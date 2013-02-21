<?php

class ViewChat {
	private $model, $user_id;

	function __construct ($model, $user_id) {
		$this->model = $model;
		$this->user_id = $user_id;
	}

	public function getChatUpdate ($chat_last) {

		$json_data = $this->model->chat->getChatUpdates($chat_last);
		return $json_data;

	}
}
