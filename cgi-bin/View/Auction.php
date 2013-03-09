<?php

class ViewAuction {
	private $model, $user_id;

	function __construct ($model, $user_id) {
		$this->model = $model;
		$this->user_id = $user_id;
	}

	public function getAuctionInfo ($args) {
		if ($args === 'time') {
			return $this->model->auction->getAuctionTimeleft();
		}

		# all data
		return $this->model->auction->getAuctionInfo();
	}
}
