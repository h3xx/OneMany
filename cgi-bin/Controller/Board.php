<?php

class ControllerBoard {
	private $model, $user_id;

	function __construct ($model, $user_id) {
		$this->model = $model;
		$this->user_id = $user_id;
	}

	public function buyProperty ($space_id) {
		# TODO : this is horribly inefficient

		$space_cost = $this->model->board->getBuyFromBankCost($space_id);
		if (!isset($space_cost) || $space_cost < 0) {
			return [
				'result'=> false,
				'msg'	=> 'Property is not buyable.',
			];
		}

		$owner = $this->model->board->whoOwnsSpace($space_id);
		if (isset($owner)) {
			if ($owner === $this->user_id) {
				return [
					'result'=> false,
					'msg'	=> 'You already own this property.',
				];
			}
			return [
				'result'=> false,
				'msg'	=> 'Property is owned by ' . $this->model->user->resolveUserId($owner) . '.',
			];
		}

		$cash_on_hand = $this->model->user->getUserCash($this->user_id);

		if ($cash_on_hand < $space_cost) {
			return [
				'result'=> false,
				'msg'	=> 'You do not have enough cash to buy this property.',
			];
		}

		# everything seems fine - the user can buy it

		if (!$this->model->board->setPropertyOwner($space_id, $this->user_id)) {
			return [
				'result'=> false,
				'msg'	=> 'Failed to set property owner [WTF].',
			];
		}

		if (!$this->model->user->setUserCash($this->user_id, $cash_on_hand - $space_cost)) {
			return [
				'result'=> false,
				'msg'	=> 'Failed to set user cash [WTF].',
			];
		}

		return [
			'result'=> true,
			'msg'	=> 'Successfully bought property.',
		];
	}

	public function mortgageProperty ($space_id) {
		# FIXME
	}

	public function landOnSpace ($space_id) {
		$grp = $this->model->game->board->getSpaceGroup($space_id);

		switch ($grp) {
			
		}

		# FIXME
	}

}
