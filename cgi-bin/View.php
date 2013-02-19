<?php

require_once('View/AjaxResponse.php');

class View {
	private $model;

	private $ajr;

	function __construct ($model) {
		$this->model = $model;
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
			$this->ajr = new ViewAjaxResponse($this->model);
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
