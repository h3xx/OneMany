<?php

class ViewAuction {
	private $model, $user_id;

	function __construct ($model, $user_id) {
		$this->model = $model;
		$this->user_id = $user_id;
	}

	public function getAuctionInfo ($args) {
		if ($args === 'time') {
			return $this->model->game->auction->getAuctionTimeleft();
		}

		# all data
		return $this->model->game->auction->getAuctionInfo();
	}
}
