<?php

class ViewAjaxResponse {
	private $model;

	__construct ($model) {
		$this->model = $model;
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
