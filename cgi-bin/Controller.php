<?php

require_once('Tools.php');
require_once('Controller/Chat.php');
require_once('Controller/Game.php');
require_once('Controller/User.php');
require_once('Controller/Signup.php');
require_once('Controller/Verify.php');

class Controller {
	private $model, $user_id;

	function __construct ($model, $user_id) {
		$this->model = $model;
		$this->user_id = $user_id;
	}

	public function processInstruction ($instr) {
		# the user must be logged in, or logging in, or requesting public info
		if (!isset($this->user_id) && !preg_match('/^(user|signup|verify)$/', @$instr['func'])) {
			$jsonresponse = [
				'result'=> false,
				'msg'	=> 'You are not logged in.',
				'redirect'=> Tools::loginUrl(),
			];
		} else {
			switch (@$instr['func']) {
				case 'chat':
					$jsonresponse = $this->chat->postChatMessage($instr['args']);
					break;
					;;
				case 'game':
					$jsonresponse = $this->game->processGameInstruction($instr['args']);
					break;
					;;
				case 'user':
					$jsonresponse = $this->user->processInstruction($instr['args']);
					break;
					;;
				case 'signup':
					$jsonresponse = $this->signup->processInstruction($instr['args']);
					break;
					;;
				case 'verify':
					$jsonresponse = $this->verify->processInstruction($instr['args']);
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

	private function getUser () {
		if (!isset($this->user)) {
			$this->user = new ControllerUser($this->model);
		}
		return $this->user;
	}

	private function getSignup () {
		if (!isset($this->signup)) {
			$this->signup = new ControllerSignup($this->model);
		}
		return $this->signup;
	}

	private function getVerify () {
		if (!isset($this->verify)) {
			$this->verify = new ControllerVerify($this->model);
		}
		return $this->verify;
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
			case 'user':
				return $this->getUser();
				break;
				;;
			case 'signup':
				return $this->getSignup();
				break;
				;;
			case 'verify':
				return $this->getVerify();
				break;
				;;
		}
	}
}
