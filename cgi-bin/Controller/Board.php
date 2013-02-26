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
		$owner_id = $this->model->game->whoOwnsSpace($space_id);
		if (!isset($owner_id) || $owner_id !== $this->user_id) {
			return [
				'result'=> false,
				'msg'	=> 'You do not own that property.',
			];
		}

		$is_mortgaged = $this->model->game->isPropertyMortgaged($space_id);
		if ($is_mortgaged) {
			return [
				'result'=> false,
				'msg'	=> 'Property is already mortgaged.',
			];
		}

		$numhouses = $this->model->game->board->housesOnSpace($space_id);
		if (!isset($numhouses)) {
			return [
				'result'=> false,
				'msg'	=> 'Something went horribly wrong finding the number of houses [WTF].',
			];
		}

		if ($numhouses > 0) {
			return [
				'result'=> false,
				'msg'	=> 'Cannot mortgage property - sell houses/hotels first.',
			];
		}

		if (!$this->model->game->board->setPropertyMortgaged($space_id, true)) {
			return [
				'result'=> false,
				'msg'	=> 'Failed to mortgage property',
			];
		}

		$mortgage = $this->model->game->board->getMortgageCost($space_id);
		if (!isset($mortgage)) {
			return [
				'result'=> false,
				'msg'	=> 'Something went horribly wrong finding the mortgage cost [WTF].',
			];
		}

		$new_cash = $this->model->user->addUserCash($this->user_id, $mortgage);
		if (!is_numeric($new_cash)) {
			return [
				'result'=> false,
				'msg'	=> 'Something went horribly wrong setting the user cash [WTF].',
			];
		}

		return [
			'result'=> true,
			'msg'	=> 'Sucessfully mortgaged property.',
		];
	}

	public function landOnSpace ($space_id) {
		$grp = $this->model->game->board->getSpaceGroup($space_id);

		switch ($grp) {
			
		}

		# FIXME
	}

}
