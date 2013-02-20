<?php

class ViewAjaxResponse {
	private $model, $user_id;

	function __construct ($model, $user_id) {
		$this->model = $model;
		$this->user_id = $user_id;
	}

	public function getBoardUpdateInstructions ($game_state) {

		$jsondata = $this->model->game->getGameUpdates($game_state);
		return json_encode($jsondata, JSON_UNESCAPED_UNICODE);
	}

	public function getChatUpdate ($chat_last) {

		$jsondata = $this->model->chat->getChatUpdates($chat_last);
		return json_encode($jsondata, JSON_UNESCAPED_UNICODE);

	}
}
