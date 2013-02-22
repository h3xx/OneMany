<?php

require_once('Board.php');

class ControllerGame {
	private $model, $user_id;

	private $board;

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

	function __get ($name) {
		switch ($name) {
			case 'board':
				return $this->getBoard();
				break;
				;;
		}
	}

}

