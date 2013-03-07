<?php

require_once('Board.php');
require_once('Auction.php');

class ControllerGame {
	private $model, $user_id;

	function __construct ($model, $user_id) {
		$this->model = $model;
		$this->user_id = $user_id;
	}

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

	public function processGameInstruction ($instruction) {
		$buff = preg_split('/:/', $instruction);

		switch ($buff[0]) {
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
			case 'auction':
				# note: buff[1] doesn't have to be set
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
			case 'roll':
				return $this->rollDice();
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

		$new_loc = ($this->model->game->getUserOnSpace($this->user_id) + $roll[0] + $roll[1]) % 40; # XXX : assuming the size of the board is 40

		return $this->landOnSpace($new_loc, $roll[0] + $roll[1]);

		# FIXME : check turn, move piece, etc.
		#return $success;
	}

	public function landOnSpace ($space_id, $dice_total) {
		$landed_msg = 'Landed on ' . $this->model->game->board->getSpaceName($space_id) . '.';

		if (!$this->model->user->moveToSpace($this->user_id, $space_id)) {
			return [
				'result'=> false,
				'msg'	=> 'Something went horribly wrong moving your piece.',
			];
		}

		$owner = $this->model->game->board->whoOwnsSpace($space_id);
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
				'msg'	=> $landed_msg . ' Paid rent of $' . $rent . ' to ' . $this->model->user->resolveUserId($owner),
			]);
		} else if ($this->model->game->board->isOwnable($space_id)) {
			$this->model->game->askBuy($this->user_id, $space_id);
			# no owner, is buyable
			return [
				'result'=> true,
				'msg'	=> $landed_msg . ' It\' unowned. We need to know if you want to buy this.',
			];
		}

		return [
			'result'=> false,
			'msg'	=> $landed_msg . ' Unhandled space.',
		];
		# FIXME - handle, chance, commchest, go2jail, go
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
		if (!$this->model->user->setInJail($this->user_id, false)) {
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

}

