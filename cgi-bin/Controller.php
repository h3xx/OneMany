<?php

require_once('Tools.php');
require_once('Controller/Chat.php');
require_once('Controller/Game.php');
require_once('Controller/Login.php');
require_once('Controller/Signup.php');

class Controller {
	private $model, $user_id;

	function __construct ($model, $user_id) {
		$this->model = $model;
		$this->user_id = $user_id;
	}

	public function processInstruction ($instr) {
		switch ($instr['func']) {
			case 'chat':
				$jsonresponse = $this->chat->postChatMessage($instr['args']);
				break;
				;;
			case 'game':
				$jsonresponse = $this->game->processGameInstruction($instr['args']);
				break;
				;;
			case 'login':
				$jsonresponse = $this->login->processInstruction($instr['args']);
				break;
				;;
			case 'signup':
				$jsonresponse = $this->signup->processInstruction($instr['args']);
				break;
				;;
			default:
				$jsonresponse = [
					'result'=> false,
					'msg'	=> 'Invalid function.',
				];
				break;
				;;
		}

		return Tools::encodeJson($jsonresponse);
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

	private function getLogin () {
		if (!isset($this->login)) {
			$this->login = new ControllerLogin($this->model);
		}
		return $this->login;
	}

	private function getSignup () {
		if (!isset($this->signup)) {
			$this->signup = new ControllerSignup($this->model);
		}
		return $this->signup;
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
			case 'login':
				return $this->getLogin();
				break;
				;;
			case 'signup':
				return $this->getSignup();
				break;
				;;
		}
	}
}
