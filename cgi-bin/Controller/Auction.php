<?php

class ControllerAuction {
	private $model, $user_id;

	function __construct ($model, $user_id) {
		$this->model = $model;
		$this->user_id = $user_id;
	}

	public function startAuction ($space_id) {
		if (!isset($space_id)) {
			$space_id = $this->model->game->getUserOnSpace($this->user_id);
			if (!is_numeric($space_id)) {
				return [
					'result'=> false,
					'msg'	=> 'Failed to determine your location.',
				];
			}
		}

		# FIXME : implement
	}

	public function addBid ($space_id, $bid) {
		# FIXME : implement

	}
}
