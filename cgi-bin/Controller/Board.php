<?php

class ControllerBoard {
	private $model, $user_id;

	function __construct ($model, $user_id) {
		$this->model = $model;
		$this->user_id = $user_id;
	}

	public function buyProperty ($space_id) {
		# TODO : this is horribly inefficient

		$space_cost = $this->model->game->board->getBuyFromBankCost($space_id);
		if (!isset($space_cost) || $space_cost < 0) {
			return [
				'result'=> false,
				'msg'	=> 'Property is not buyable.',
			];
		}

		$owner = $this->model->game->board->whoOwnsSpace($space_id);
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
				'msg'	=> 'You do not have enough cash to buy this property. ',
			];
		}

		# everything seems fine - the user can buy it

		if (!$this->model->game->board->setPropertyOwner($space_id, $this->user_id)) {
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

	public function sellProperty ($space_id) {
		return [
			'result'=> false,
			'msg'	=> 'Successfully bought property.',
		];
	}

	public function buyPropertyYoureOn () {
		$space_id = $this->model->game->getUserOnSpace($this->user_id);

		if (!is_numeric($space_id)) {
			return [
				'result'=> false,
				'msg'	=> 'Failed to determine your location.',
			];
		}

		return $this->buyProperty($space_id);
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
				'msg'	=> 'Failed to mortgage property.',
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

	private function _sellHousesNonParallel ($space_id, $houses_to_sell) {
		# caveat: must have done all checking beforehand
		$numhouses = $this->model->game->board->housesOnSpace($space_id);
		if (!isset($numhouses)) {
			return [
				'result'=> false,
				'msg'	=> 'Something went horribly wrong finding the number of houses [WTF].',
			];
		}

		if ($numhouses === 0) {
			return [
				'result'=> false,
				'msg'	=> 'There are no houses on that property to sell.',
			];
		}

		if ($houses_to_sell > $numhouses) {
			return [
				'result'=> false,
				'msg'	=> 'You cannot sell that many houses.',
			];
		}

		if (!$this->model->game->board->setNumHouses($space_id, ($numhouses - $houses_to_sell))) {
			return [
				'result'=> false,
				'msg'	=> 'Something went horribly wrong setting the number of houses [WTF].',
			];
		}


		return [
			'result'=> true,
			'msg'	=> 'Successfully sold houses.',
		];
	}

	public function sellHouses ($space_id, $houses_to_sell) {
		if (!isset($houses_to_sell)) {
			$houses_to_sell = 1;
		}

		if (!is_numeric($houses_to_sell) || $houses_to_sell < 0) {
			return [
				'result'=> false,
				'msg'	=> 'Invalid number of houses to sell.',
			];
		}

		$owner_id = $this->model->game->whoOwnsSpace($space_id);
		if (!isset($owner_id) || $owner_id !== $this->user_id) {
			return [
				'result'=> false,
				'msg'	=> 'You do not own that property.',
			];
		}

		# retrieve rule
		$parallel = $this->model->rules->getRuleValue('parallel_improvement');

		$cash_delta =
			($parallel ?
				$this->model->game->board->getGroupHouseCost($space_id) :
				$this->model->game->board->getHouseCost($space_id)
			);
		if (!is_numeric($cash_delta)) {
			return [
				'result'=> false,
				'msg'	=> 'Something went horribly wrong getting a house cost [WTF].',
			];
		}

		if ($parallel) {

			$sids = $this->model->game->board->getSpaceIdsInSameGroup($space_id);
			if (!isset($sids) || !is_array($sids)) {
				return [
					'result'=> false,
					'msg'	=> 'Something went horribly wrong getting space ids in same group [WTF].',
				];
			}

			foreach ($sids as $sid) {
				$res = $this->_sellHousesNonParallel($sid, $houses_to_sell);
				if (!$res['result']) {
					# FUUUUUUU!!!!
					return $res;
				}
			}

			# nothing failed...
			$res = [
				'result'=> true,
				'msg'	=> 'Success.',
			];
		} else {
			$res = $this->_sellHousesNonParallel($sid, $houses_to_sell);
			if (!$res['result']) {
				return $res;
			}

		}

		$new_cash = $this->model->user->addUserCash($this->user_id, $cash_delta);
		if (!is_numeric($new_cash)) {
			return [
				'result'=> false,
				'msg'	=> 'Something went horribly wrong setting the user cash [WTF].',
			];
		}

		# nothing failed...
		return $res;
	}

	private function _buyHousesNonParallel ($space_id, $houses_to_buy) {
		# caveat: must have done all checking beforehand
		$numhouses = $this->model->game->board->housesOnSpace($space_id);
		if (!isset($numhouses)) {
			return [
				'result'=> false,
				'msg'	=> 'Something went horribly wrong finding the number of houses [WTF].',
			];
		}

		if ($numhouses === 5) {
			return [
				'result'=> false,
				'msg'	=> 'There is already a hotel on the property.',
			];
		}

		if ($houses_to_buy + $numhouses > 5) {
			return [
				'result'=> false,
				'msg'	=> 'You cannot buy that many houses.',
			];
		}

		if (!$this->model->game->board->setNumHouses($space_id, ($numhouses + $houses_to_buy))) {
			return [
				'result'=> false,
				'msg'	=> 'Something went horribly wrong setting the number of houses [WTF].',
			];
		}

		return [
			'result'=> true,
			'msg'	=> 'Successfully bought houses.',
		];
	}

	public function buyHouses ($space_id, $houses_to_buy) {
		if (!isset($houses_to_buy)) {
			$houses_to_buy = 1;
		}

		if (!is_numeric($houses_to_buy) || $houses_to_buy < 0) {
			return [
				'result'=> false,
				'msg'	=> 'Invalid number of houses to buy.',
			];
		}

		$owner_id = $this->model->game->board->whoOwnsSpace($space_id);
		if (!isset($owner_id) || $owner_id !== $this->user_id) {
			return [
				'result'=> false,
				'msg'	=> 'You do not own that property.',
			];
		}

		# retrieve rule
		$parallel = $this->model->rules->getRuleValue('parallel_improvement');

		$cash_delta =
			-($parallel ?
				$this->model->game->board->getGroupHouseCost($space_id) :
				$this->model->game->board->getHouseCost($space_id)
			);
		if (!is_numeric($cash_delta)) {
			return [
				'result'=> false,
				'msg'	=> 'Something went horribly wrong getting a house cost [WTF].',
			];
		}

		$usercash = $this->model->user->getUserCash($this->user_id);
		if (!is_numeric($usercash)) {
			return [
				'result'=> false,
				'msg'	=> 'Something went horribly wrong getting the amount of user cash [WTF].',
			];
		}
		if ($usercash + $cash_delta < 0) {
			return [
				'result'=> false,
				'msg'	=> 'Not enough cash to make that improvement.',
			];
		}

		if ($parallel) {
			# must improve each property in the group
			if (!$this->model->game->board->hasMonopoly($this->user_id, $space_id)) {
				return [
					'result'=> false,
					'msg'	=> 'You must have a monopoly before you can buy improvements.',
				];
			}

			$sids = $this->model->game->board->getSpaceIdsInSameGroup($space_id);
			if (!isset($sids) || !is_array($sids)) {
				return [
					'result'=> false,
					'msg'	=> 'Something went horribly wrong getting space ids in same group [WTF].',
				];
			}

			foreach ($sids as $sid) {
				$res = $this->_buyHousesNonParallel($sid, $houses_to_buy);
				if (!$res['result']) {
					# FUUUUUUU!!!!
					return $res;
				}
			}

			# nothing failed...
			$res = [
				'result'=> true,
				'msg'	=> 'Success.',
			];
		} else {
			$res = $this->_buyHousesNonParallel($space_id, $houses_to_buy);
			if (!$res['result']) {
				return $res;
			}
		}

		$new_cash = $this->model->user->addUserCash($this->user_id, $cash_delta);
		if (!is_numeric($new_cash)) {
			return [
				'result'=> false,
				'msg'	=> 'Something went horribly wrong setting the user cash [WTF].',
			];
		}

		# nothing failed...
		return $res;
	}

}
