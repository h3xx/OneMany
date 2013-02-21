<?php

class ControllerBoard {
	private $model, $user_id;

	function __construct ($model, $user_id) {
		$this->model = $model;
		$this->user_id = $user_id;
	}

	public function buyProperty ($space_id) {
		if ($this->model->board->isBuyable($space_id)) {
			# FIXME
		} else {
			return false;
		}
	}

}
