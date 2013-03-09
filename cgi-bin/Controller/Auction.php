<?php

class ControllerAuction {
	private $model, $user_id;

	function __construct ($model, $user_id) {
		$this->model = $model;
		$this->user_id = $user_id;
	}

	# note: also handles declining to buy if auctions are not enabled
	public function startAuction ($space_id) {
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

		if (!$this->model->rules->getRuleValue('auctions')) {
			$this->model->game->noBuy($this->user_id, $space_id);
			return [
				'result'=> true,
				'msg'	=> 'Declined to buy property.',
			];
		}

		if (!$this->model->game->board->isOwnable($space_id)) {
			return [
				'result'=> false,
				'msg'	=> 'Space is not ownable.',
			];
		}

		# ok, we're really going to auction it now
		$buying_price = $this->model->game->board->getBuyFromBankCost($space_id);
		$auction_startbid = $buying_price * $this->model->rules->getRuleValue('auction_startbid_perc') / 100;

		if (!$this->model->game->auction->startAuction($space_id, $this->user_id, $auction_startbid)) {
			return [
				'result'=> false,
				'msg'	=> 'Failed to start an auction. [WTF]',
			];
		}

		return [
			'result'=> true,
			'msg'	=> 'Auctioning property.',
		];
	}

	public function addBid ($bid) {
		if (!$this->model->rules->getRuleValue('auctions')) {
			return [
				'result'=> false,
				'msg'	=> 'Auctions are not enabled for this game.',
			];
		}

		$ainfo = $this->model->game->auction->getAuctionInfoNoExpired();
		if (empty($ainfo)) {
			return [
				'result'=> false,
				'msg'	=> 'There is no auction currently in progress.',
			];
		}

		if ($bid <= $ainfo['abid']) {
			return [
				'result'=> false,
				'msg'	=> 'Bid must be higher than current winning bid. [' . $bid . ':' . $ainfo['abid'] . ']',
			];
		}

		$cash = $this->model->user->getUserCash($this->user_id);
		if ($bid > $cash) {
			return [
				'result'=> false,
				'msg'	=> 'You cannot afford to make that bid.',
			];
		}

		if (!$this->model->game->auction->setAuctionBid($this->user_id, $bid)) {
			return [
				'result'=> false,
				'msg'	=> 'Failed to place bid. [WTF]',
			];
		}

		return [
			'result'=> true,
			'msg'	=> 'Successfully placed bid.',
		];
	}
}
