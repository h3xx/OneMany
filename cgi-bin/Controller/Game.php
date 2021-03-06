<?php

require_once('Board.php');
require_once('Auction.php');

class ControllerGame {
	private $model, $user_id;

	function __construct ($model, $user_id) {
		$this->model = $model;
		$this->user_id = $user_id;
	}

	public function processGameInstruction ($instruction) {
		$buff = preg_split('/:/', $instruction);

		switch ($buff[0]) {
			case 'join':
				if (!isset($buff[1]) || !is_numeric($buff[1])) {
					return [
						'result'=> false,
						'msg'	=> 'Usage: join:GAME_ID',
					];
				}
				return $this->joinGame($buff[1]);
				break;
				;;
			case 'buy':
				$result = $this->board->buyPropertyYoureOn();
				if ($result['result']) {
					return $this->turnIsOver($result);
				}
				return $result;
				break;
				;;
			case 'sell':
				return $this->board->sellProperty(@$buff[1]);
				break;
				;;
			case 'buyHouse':
				# note: buff[2] defaults to 1
				return $this->board->buyHouses(@$buff[1], @$buff[2]);
				break;
				;;
			case 'sellHouse':
				# note: buff[2] defaults to 1
				return $this->board->sellHouses(@$buff[1], @$buff[2]);
				break;
				;;
			case 'noBuy':
				return $this->auction->startAuction(@$buff[1]);
				break;
				;;
			case 'bid':
				return $this->auction->addBid(@$buff[1]);
				break;
				;;
			case 'mortgage':
				return $this->board->mortgageProperty(@$buff[1]);
				break;
				;;
			case 'unmortgage':
				return $this->board->unmortgageProperty(@$buff[1]);
				break;
				;;
			case 'roll':
				return $this->rollDice();
				break;
				;;
			case 'payBail':
				return $this->payBail();
				break;
				;;
			case 'useGojf':
				return $this->useGojf();
				break;
				;;
			default:
				return [
					'result'=> false,
					'msg'	=> 'Invalid instruction.',
				];
				break;
				;;
		}
	}

	private function joinGame ($game_id) {
		# TODO : send a request to the owner of the game?
		if (!$this->model->game->joinGame($this->user_id, $game_id)) {
			# failed to join game
			# TODO : tell user why? [IDGAF]
			return [
				'result'=> false,
				'msg'	=> 'Failed to join game (is it full?).',
			];
		}

		# use session variables
		$_SESSION['game_id'] = $game_id;

		return [
			'result'=> true,
			'msg'	=> 'Successfully joined game.',
		];
	}

	private function rollDice () {
		# check whether it's their turn
		$whose_turn = $this->model->game->whoseTurn();
		if ($whose_turn !== $this->user_id) {
			return [
				'result'=> false,
				'msg'	=> 'Not your turn.',
			];
		}

		# what to return upon successful roll
		$success = [
			'result'=> true,
			'msg'	=> 'Successfully rolled dice.',
		];

		$roll = $this->model->game->doRoll($this->user_id, 2);

		if (!is_array($roll)) {
			return [
				'result'=> false,
				'msg'	=> 'Failed to roll dice. [WTF]',
			];
		}

		if ($this->model->user->isInJail($this->user_id)) {
			# user is in jail
			if ($roll[0] === $roll[1]) {
				# doubles - get out of jail
				$this->model->user->setInJail($this->user_id, false);
				$this->model->user->resetDoubles($this->user_id);
			}
			# TODO : only be able to roll three times to get freed from jail ?
			return $this->turnIsOver($success);
		}

		# not in jail
		if ($roll[0] === $roll[1]) {
			# doubles
			$num_doubles = $this->model->user->incrementDoubles($this->user_id);
			if (!is_numeric($num_doubles)) {
				return [
					'result'=> false,
					'msg'	=> 'Failed to set the number of doubles. [WTF]',
				];
			}
			$jail_doubles = $this->model->rules->getRuleValue('jail_doubles', 3);

			if ($jail_doubles !== 0 && $num_doubles >= $jail_doubles) {
				# doubles-induced jail
				return $this->throwUserInJail($success);
			}

			# you rolled doubles, but you're not going to
			# jail for it - still your turn after this
			$this->model->game->giveExtraTurn($this->user_id);
		} else {
			# no doubles - your turn is over after this
			$this->model->user->resetDoubles($this->user_id);
		}

		$new_loc = ($this->model->game->getUserOnSpace($this->user_id) + $roll[0] + $roll[1]);
		if ($new_loc > 39) {
			# passed GO
			$this->addGoMoney();
			$new_loc = $new_loc % 40;
		}

		$result = $this->landOnSpace($new_loc, $roll);

		if ($roll[0] == $roll[1]) {
			$result['msg'] .= ' You rolled doubles. Go again.';
		}

		return $result;
	}

	public function addGoMoney () {
		return $this->model->user->addUserCash($this->user_id, $this->model->rules->getRuleValue('go_salary', 200));
	}

	public function landOnSpace ($space_id, $dice, $prevmsg='') {
		$dice_total = array_sum($dice);
		$sinfo = $this->model->game->board->getSpaceAndOwnershipInfo($space_id);
		if (!empty($prevmsg)) {
			$landed_msg = $prevmsg;
		} else {
			$user_name = $this->model->user->resolveUserId($this->user_id);
			$landed_msg = $user_name . ' landed on ' . $sinfo['name'] . '.';
		}

		if (!$this->model->user->moveToSpace($this->user_id, $space_id)) {
			return [
				'result'=> false,
				'msg'	=> 'Something went horribly wrong moving your piece.',
			];
		}

		$owner = $sinfo['owner'];
		if ($owner === $this->user_id) {
			return $this->turnIsOver([
				'result'=> true,
				'msg'	=> $landed_msg . ' You own that space, nothing to do.',
			]);
		} else if (isset($owner)) {
			$rent = $this->model->game->board->rentForSpace($space_id, $dice_total);
			if ($rent === 0) {
				return $this->turnIsOver([
					'result'=> true,
					'msg'	=> $landed_msg . ' No rent.',
				]);
			}
			$this->model->user->addUserCash($owner, $rent);
			$this->model->user->addUserCash($this->user_id, -$rent);

			# turn is over
			return $this->turnIsOver([
				'result'=> true,
				'msg'	=> $landed_msg . ' Paid rent of $' . $rent . ' to ' . $sinfo['oname'],
			]);
		#} else if ($this->model->game->board->isOwnable($space_id)) {
		} else if ($sinfo['cost'] > 0) { # ownable
			$this->model->game->askBuy($this->user_id, $space_id);
			# no owner, is buyable
			return [
				'result'=> true,
				'msg'	=> $landed_msg . ' It\'s unowned. We need to know if you want to buy this.',
			];
		} else {
			# other type of space
			switch ($sinfo['group']) {
				#case 'U':
				#	// Utility -- shouldn't be handled here
				#case 'RR':
				#	// Railroad -- shouldn't be handled here
				case 'LT':
					# luxury tax
					$tax = $this->model->rules->getRuleValue('luxurytax', 75);
					$this->model->user->addUserCash($this->user_id, -$tax);
					return $this->turnIsOver([
						'result'=> true,
						'msg'	=> $landed_msg . ' Paid tax of $' . $tax,
					]);
					break;
				case 'IT':
					# income tax
					$tax_f = $this->model->rules->getRuleValue('incometax_flat', 200);
					$tax_p = (
						$this->model->user->getUserCash($this->user_id) *
						$this->model->rules->getRuleValue('incometax_perc', 10) / 100
					);

					# choose lowest tax
					$tax = (
						($tax_f < $tax_p) ? [$tax_f, 'flat'] : [$tax_p, 'percentage']
					);

					$this->model->user->addUserCash($this->user_id, -$tax[0]);
					return $this->turnIsOver([
						'result'=> true,
						'msg'	=> $landed_msg . ' Paid ['.$tax[1].'] tax of $' . $tax[0],
					]);
					break;
				case 'C':
					# Chance
					$card = $this->model->game->chance->drawCard($this->user_id);
					$landed_msg = $landed_msg . ' - '.$card['text'];
					if (is_numeric($card['action'])) {
						# paid
						$this->model->user->addUserCash($this->user_id, $card['action']);
					} else {
						switch ($card['action']) {
							case 'G0':
								# to to GO
								$this->addGoMoney();
								return $this->landOnSpace(0, $dice, $landed_msg);
								break;
							case 'G17':
								# go to Illinois Ave
								if ($space_id >= 17) {
									$this->addGoMoney();
								}
								return $this->landOnSpace(17, $dice, $landed_msg);
								break;
							case 'G11':
								# go to St. Charles Place
								if ($space_id >= 11) {
									$this->addGoMoney();
								}
								return $this->landOnSpace(11, $dice, $landed_msg);
								break;
							case 'G5':
								# go to Reading Railroad
								if ($space_id >= 5) {
									$this->addGoMoney();
								}
								return $this->landOnSpace(5, $dice, $landed_msg);
								break;
							case 'G39':
								# go to Reading Railroad
								if ($space_id >= 39) {
									$this->addGoMoney();
								}
								return $this->landOnSpace(39, $dice, $landed_msg);
								break;
							case 'G3':
								# go back three spaces
								$sp = $space_id - 3;
								if ($sp < 0) {
									$sp += 40;
								}
								return $this->landOnSpace($sp, $dice, $landed_msg);
								break;

							case 'G2':
								# GO TO JAIL
								return $this->throwUserInJail([
									'result'=> true,
									'msg'	=> $landed_msg,
								]);

								break;

							case 'GOJF':
								# get out of jail free card
								if (!$this->model->game->setGojf($this->user_id, true)) {
									return [
										'result'=> false,
										'msg'	=> 'Failed to give you a GOJF card. [WTF]',
									];
								}
								# caveat: turn is ended below
								break;

							case 'PA50':
								# pay $50 to all players
								$ids = $this->model->game->allUidsInGame();
								foreach ($ids as $i) {
									$this->model->game->transferCash($this->user_id, $i);
								}
								break;
						}
					}
					return $this->turnIsOver([
						'result'=> true,
						'msg'	=> $landed_msg,
					]);
					break;
				case 'CC':
					# Community Chest
					$card = $this->model->game->commchest->drawCard($this->user_id);
					$landed_msg = $landed_msg . ' - '.$card['text'];
					if (is_numeric($card['action'])) {
						# paid
						$this->model->user->addUserCash($this->user_id, $card['action']);
					} else {
						switch ($card['action']) {
							case 'G0':
								# to to GO
								$this->addGoMoney();
								return $this->landOnSpace(0, $dice, $landed_msg);
								break;
							case 'G2':
								# GO TO JAIL
								return $this->throwUserInJail([
									'result'=> true,
									'msg'	=> $landed_msg,
								]);
								break;
							case 'GOJF':
								# get out of jail free card
								if (!$this->model->game->setGojf($this->user_id, true)) {
									return [
										'result'=> false,
										'msg'	=> 'Failed to give you a GOJF card. [WTF]',
									];
								}
								# caveat: turn is ended below
								break;
							case 'CA50':
								# collect $50 from all players
								$ids = $this->model->game->allUidsInGame();
								foreach ($ids as $i) {
									$this->model->game->transferCash($i, $this->user_id);
								}
								break;
						}
					}
					return $this->turnIsOver([
						'result'=> true,
						'msg'	=> $landed_msg,
					]);
					break;
				case 'G2':
					# Go to jail!
					return $this->throwUserInJail([
						'result'=> true,
						'msg'	=> $landed_msg,
					]);
					break;
				case 'FP':
					# Free Parking
					if (!$this->model->game->awardFreeParking($this->user_id)) {
						return [
							'result'=> false,
							'msg'	=> 'Failed to give you Free Parking. [WTF]',
						];
					}
					return $this->turnIsOver([
						'result'=> true,
						'msg'	=> $landed_msg,
					]);
					break;
				# zero-action spaces
				case 'JV':
					# just visiting jail
				case 'GO':
					# GO
					return $this->turnIsOver([
						'result'=> true,
						'msg'	=> $landed_msg,
					]);
					break;
				#default:
				#	return [
				#		'result'=> false,
				#		'msg'	=> $landed_msg . ' Unhandled space.',
				#	];
			}
		}

		return [
			'result'=> false,
			'msg'	=> $landed_msg . ' Unhandled space.',
		];
	}

	private function payBail () {
		# check whether it's their turn
		$whose_turn = $this->model->game->whoseTurn();
		if ($whose_turn !== $this->user_id) {
			return [
				'result'=> false,
				'msg'	=> 'Not your turn.',
			];
		}

		if (!$this->model->user->isInJail($this->user_id)) {
			return [
				'result'=> false,
				'msg'	=> 'You are not in jail.',
			];
		}

		$jail_bail = $this->model->rules->getRuleValue('jail_bail');
		$cash = $this->model->user->getUserCash($this->user_id);

		if ($jail_bail > $cash) {
			return [
				'result'=> false,
				'msg'	=> 'You cannot afford bail.',
			];
		}

		if (!$this->model->game->payBail($this->user_id)) {
			return [
				'result'=> false,
				'msg'	=> 'Something went horribly wrong paying bail [WTF].',
			];
		}

		return [
			'result'=> true,
			'msg'	=> 'Successfully paid bail.',
		];
	}

	private function useGojf () {
		# check whether it's their turn
		$whose_turn = $this->model->game->whoseTurn();
		if ($whose_turn !== $this->user_id) {
			return [
				'result'=> false,
				'msg'	=> 'Not your turn.',
			];
		}

		if (!$this->model->user->isInJail($this->user_id)) {
			return [
				'result'=> false,
				'msg'	=> 'You are not in jail.',
			];
		}

		if (!$this->model->game->hasGojf($this->user_id)) {
			return [
				'result'=> false,
				'msg'	=> 'You do not have a Get out of Jail Free card.',
			];
		}

		if (!$this->model->game->useGojf($this->user_id)) {
			return [
				'result'=> false,
				'msg'	=> 'Failure to use GOJF card. [WTF]',
			];
		}

		return $this->turnIsOver([
			'result'=> true,
			'msg'	=> 'Successfully paid bail.',
		]);
	}

	private function throwUserInJail ($success_return) {
		# figure out where we need to send the user
		$jail_ids = $this->model->game->board->getSpaceIdsInGroup('JV');
		if (!is_array($jail_ids) || !is_numeric($jail_ids[0])) {
			return [
				'result'=> false,
				'msg'	=> 'Trying to throw you in jail, but no idea where jail is [WTF].',
			];
		}

		$jail_id = $jail_ids[0];

		# move the user to jail
		if (!$this->model->user->moveToSpace($this->user_id, $jail_id)) {
			return [
				'result'=> false,
				'msg'	=> 'Failed to move you to jail [WTF].',
			];
		}
		# throw the user in jail
		if (!$this->model->user->setInJail($this->user_id, true)) {
			return [
				'result'=> false,
				'msg'	=> 'Failed to put you in jail [WTF].',
			];
		}

		return $this->turnIsOver($success_return);
	}
	
	private function turnIsOver ($success_return) {
		# your turn is over
		if (!is_numeric($this->model->game->rotateTurn())) {
			return [
				'result'=> false,
				'msg'	=> 'Failed to rotate turn [WTF].',
			];
		}

		return $success_return;
	}

# member getters {{{

	private function getBoard () {
		if (!isset($this->board)) {
			$this->board = new ControllerBoard($this->model, $this->user_id);
		}
		return $this->board;
	}

	private function getAuction () {
		if (!isset($this->auction)) {
			$this->auction = new ControllerAuction($this->model, $this->user_id);
		}
		return $this->auction;
	}


	function __get ($name) {
		switch ($name) {
			case 'board':
				return $this->getBoard();
				break;
				;;
			case 'auction':
				return $this->getAuction();
				break;
				;;
		}
	}

# member getters }}}

}

# vi: fdm=marker
