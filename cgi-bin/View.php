<?php

require_once('View/AjaxResponse.php');

class View {
	private $model, $user_id;

	private $ajr;

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

	function __get ($name) {
		switch ($name) {
			case 'ajr':
				return $this->getAjaxResponse();
				break;
				;;
		}
	}
}
