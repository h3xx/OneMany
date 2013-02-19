<?php

class ControllerChat {
	private $model, $user_id;

	function __construct ($model, $user_id) {
		$this->model = $model;
		$this->user_id = $user_id;
	}

	public function postChatMessage ($chat_text) {
		$this->model->chat->addChatMessage($chat_text);
	}
}
