<?php

class ControllerChat {
	private $model, $user_id;

	function __construct ($model, $user_id) {
		$this->model = $model;
		$this->user_id = $user_id;
	}

	public function postChatMessage ($chat_text) {
		# FIXME : check if user is in game
		if ($this->model->user->isValidUserId($this->user_id)) {

			$chat_id = $this->model->chat->addChatMessage($this->user_id, $chat_text);
			$result = [
				'result'	=> true,
				'newstate'	=> $chat_id,
			];
		} else {
			$result = [
				'result'	=> false,
				'newstate'	=> -1,
			];
		}

		return json_encode($result, JSON_UNESCAPED_UNICODE);
	}
}
