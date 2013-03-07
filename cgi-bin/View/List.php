<?php

class ViewList {
	private $model, $user_id;

	function __construct ($model, $user_id) {
		$this->model = $model;
		$this->user_id = $user_id;
	}

	public function listGames () {

		$json_data = $this->model->game->getGamesList();
		return $json_data;
	}
}
