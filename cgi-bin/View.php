<?php

require_once('View/AjaxResponse.php');
require_once('View/Chat.php');

class View {
	private $model, $user_id;

	private $ajr, $chat;

	function __construct ($model, $user_id) {
		$this->model = $model;
		$this->user_id = $user_id;
	}

	public function processInstruction ($instr) {
		switch ($instr['func']) {
			case 'pollChat':
				return $this->getAjaxResponse()->getChatUpdate($instr['args']);
				;;
			case 'pollGame':
				return $this->getAjaxResponse()->getBoardUpdateInstructions($instr['args']);
				;;

		}
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

	function __get ($name) {
		switch ($name) {
			case 'ajr':
				return $this->getAjaxResponse();
				break;
				;;
		}
	}
}
