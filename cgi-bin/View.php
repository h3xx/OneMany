<?php

require_once('Tools.php');
require_once('View/Chat.php');
require_once('View/Board.php');
require_once('View/Game.php');
require_once('View/PropertyCard.php');

class View {
	private $model, $user_id;

	function __construct ($model, $user_id) {
		$this->model = $model;
		$this->user_id = $user_id;
	}


	public function processInstruction ($instr) {
		switch ($instr['func']) {
			case 'pollChat':
				$jsonresponse = $this->chat->getChatUpdate($instr['args']);
				break;
				;;
			case 'pollGame':
				$jsonresponse = $this->game->getBoardUpdateInstructions($instr['args']);
				break;
				;;
			case 'propcardInfo':
				$jsonresponse = $this->propcard->getPropertyCardData($instr['args']);
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
			$this->chat = new ViewChat($this->model, $this->user_id);
		}
		return $this->chat;
	}

	private function getGame () {
		if (!isset($this->game)) {
			$this->game = new ViewGame($this->model, $this->user_id);
		}
		return $this->game;
	}

	private function getPropCard () {
		if (!isset($this->propcard)) {
			$this->propcard = new ViewPropertyCard($this->model);
		}
		return $this->propcard;
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
			case 'propcard':
				return $this->getPropCard();
				break;
				;;
		}
	}
}
