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

		$roll = $this->model->game->doRoll($this->user_id, 2);

		if (!is_array($roll)) {
			return [
				'result'=> false,
				'msg'	=> 'Failed to roll dice.',
			];
		}

		# FIXME : move piece
		return [
			'result'=> true,
			'msg'	=> 'Successfully rolled dice.',
		];
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

