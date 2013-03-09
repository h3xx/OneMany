<?php

class ViewAuction {
	private $model, $user_id;

	function __construct ($model, $user_id) {
		$this->model = $model;
		$this->user_id = $user_id;
	}

	public function getAuctionInfo ($args) {
		if ($args === 'time') {
			$inf = $this->model->game->auction->getAuctionTimeleft();
		} else {
			# all data
			$inf = $this->model->game->auction->getAuctionInfo();
		}

		# XXX : hack! expire auction if it's observed to be expired
		if ($inf['aseconds'] <= 0) {
			$this->model->game->auction->closeAuction();
		}

		return $inf;
	}
}
