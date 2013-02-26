<?php

require_once('View/AjaxResponse.php');
require_once('View/Chat.php');
require_once('View/Board.php');
require_once('View/PropertyCard.php');

class View {
	private $model, $user_id;

	function __construct ($model, $user_id) {
		$this->model = $model;
		$this->user_id = $user_id;
	}

	private static function encodeJson ($json_data) {
		return json_encode($json_data, JSON_UNESCAPED_UNICODE);
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

		}

		return self::encodeJson($jsonresponse);
	}

	private function getAjaxResponse () {
		if (!isset($this->ajr)) {
			$this->ajr = new ViewAjaxResponse($this->model, $this->user_id);
		}
		return $this->ajr;
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
			case 'ajr':
				return $this->getAjaxResponse();
				break;
				;;
		}
	}
}
