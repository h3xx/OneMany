<?php

class ViewRooms {
	private $model, $user_id;

	function __construct ($model, $user_id) {
		$this->model = $model;
		$this->user_id = $user_id;
	}

	public function processRoomInstruction ($instruction) {
		# FIXME : implement
		return [
			'result'=> false,
			'msg'	=> 'Not implemented yet.',
		];
	}

	public function getRoomList () {

	}
}
