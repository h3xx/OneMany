<?php

class ControllerAuction {
	private $model, $user_id;

	function __construct ($model, $user_id) {
		$this->model = $model;
		$this->user_id = $user_id;
	}

	public function startAuction ($space_id) {
		if (!$this->model->rules->getRuleValue('auctions')) {
			return [
				'result'=> false,
				'msg'	=> 'Auctions are not enabled for this game.',
			];
		}

		$whose_turn = $this->model->game->whoseTurn();
		if ($whose_turn !== $this->user_id) {
			return [
				'result'=> false,
				'msg'	=> 'Not your turn.',
			];
		}

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

		return [
			'result'=> false,
			'msg'	=> 'FIXME: auctions.',
		];
		# FIXME : implement
	}

	public function addBid ($bid) {
		if (!$this->model->rules->getRuleValue('auctions')) {
			return [
				'result'=> false,
				'msg'	=> 'Auctions are not enabled for this game.',
			];
		}

		$ainfo = $this->model->game->getAuctionInfoNoExpired();
		if (empty($ainfo)) {
			return [
				'result'=> false,
				'msg'	=> 'Space is not up for auction.',
			];
		}

		$space_id = $ainfo['aspace'];

		return [
			'result'=> true,
			'msg'	=> 'Successfully placed bid.',
		];

		# FIXME : implement

	}
}
