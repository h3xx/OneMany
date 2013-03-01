<?php

require_once('Board.php');

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

	public function processGameInstruction ($instruction) {
		$buff = preg_split('/:/', $instruction);

		switch ($buff[0]) {
			case 'buy':
				return $this->board->buyPropertyYoureOn();
				break;
				;;
			case 'mortgage':
				return $this->board->mortgageProperty($buff[1]);
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
				'msg'	=> 'Failed to roll dice.',
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
			return $success;
		} else {
			if ($roll[0] === $roll[1]) {
				# doubles
				$num_doubles = $this->model->user->incrementDoubles($this->user_id);
				if (!is_numeric($num_doubles)) {
					return [
						'result'=> false,
						'msg'	=> 'Failed to set the number of doubles.',
					];
				}
				$jail_doubles = $this->model->rules->getRuleValue('jail_doubles', 3);

				if ($jail_doubles !== 0 && $num_doubles >= $jail_doubles) {
					# doubles-induced jail
					return $this->throwUserInJail($success);
				}

				# you rolled doubles, but you're not going to
				# jail for it - still your turn after this
				# FIXME : implement
			} else {
				# no doubles - your turn is over after this
				# FIXME : implement

				$this->model->user->resetDoubles($this->user_id);
			}
		}

		# FIXME : check turn, move piece, etc.
		return $success;
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
		}
	}

}

