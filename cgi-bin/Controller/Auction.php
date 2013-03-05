<?php

class ControllerAuction {
	private $model, $user_id;

	function __construct ($model, $user_id) {
		$this->model = $model;
		$this->user_id = $user_id;
	}

	public function startAuction ($space_id) {
		# FIXME : check turn
		if (!isset($space_id)) {
			$space_id = $this->model->game->getUserOnSpace($this->user_id);
			if (!is_numeric($space_id)) {
				return [
					'result'=> false,
					'msg'	=> 'Failed to determine your location.',
				];
			}
		}

		if (!$this->model->game->board->isOwnable($space_id)) {
			return [
				'result'=> false,
				'msg'	=> 'Space is not ownable.',
			];
		}

		# FIXME : implement
	}

	public function addBid ($space_id, $bid) {
		if (!$this->model->game->board->isOwnable($space_id)) {
			return [
				'result'=> false,
				'msg'	=> 'Space is not ownable.',
			];
		}
		$ainfo = $this->model->game->getAuctionInfoNoExpired();
		if (empty($ainfo) || $ainfo['aspace'] !== $space_id) {
			return [
				'result'=> false,
				'msg'	=> 'Space is not up for auction.',
			];
		}

		# FIXME : implement

	}
}
