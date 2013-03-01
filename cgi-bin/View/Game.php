<?php

class ViewGame {
	private $model, $user_id;

	function __construct ($model, $user_id) {
		$this->model = $model;
		$this->user_id = $user_id;
	}

	public function getBoardUpdateInstructions ($game_state) {

		$json_data = $this->model->update->getGameUpdates($game_state);
		return $json_data;
	}

	public function getInitialGameData () {

		$json_data = $this->model->game->exportModel();
		return $json_data;

	}
}
