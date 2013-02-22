<?php

require_once('Controller/Chat.php');
require_once('Controller/Game.php');

class Controller {
	private $model, $user_id;

	private $chat;

	function __construct ($model, $user_id) {
		$this->model = $model;
		$this->user_id = $user_id;
	}

	public function processInstruction ($instr) {
		switch ($instr['func']) {
			case 'chat':
				return $this->getChat()->postChatMessage($instr['args']);
				break;
				;;

		}
	}

	private function getChat () {
		if (!isset($this->chat)) {
			$this->chat = new ControllerChat($this->model, $this->user_id);
		}
		return $this->chat;
	}

	private function getGame () {
		if (!isset($this->game)) {
			$this->game = new ControllerGame($this->model, $this->user_id);
		}
		return $this->game;
	}

	function __get ($name) {
		switch ($name) {
			case 'chat':
				return $this->getChat();
				break;
				;;
			case 'game':
				return $this->getGame();
				break;
				;;
		}
	}
}
