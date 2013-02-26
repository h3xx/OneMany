<?php

class ModelUser {
	private $model, $game_id;

	function __construct ($model, $game_id) {
		$this->model = $model;
		$this->game_id = $game_id;
	}

	public function checkLogin ($user_name, $password) {
		$sth = $this->model->prepare(
			# database-side function; returns a boolean
			'select check_login(:un, :pw)'
		);

		$sth->bindParam(':un', $user_name, PDO::PARAM_STR);
		$sth->bindParam(':pw', $password, PDO::PARAM_STR);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_NUM);

		return $result[0];
	}

	public function resolveUsername ($user_name) {

		$sth = $this->model->prepare(
			'select "user_id" from "user" '.
			'where "user_name" = :un '.
			'limit 1'
		);

		$sth->bindParam(':un', $user_name, PDO::PARAM_STR);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_NUM);

		$user_id = @$result[0];

		return $user_id;
	}

	public function resolveUserId ($user_id) {
		$sth = $this->model->prepare(
			'select "user_name" from "user" '.
			'where "user_id" = :uid '.
			'limit 1'
		);

		$sth->bindParam(':uid', $user_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_NUM);

		$user_name = @$result[0];

		return $user_name;
	}

	public function isValidUserId ($user_id) {
		$sth = $this->model->prepare(
			'select count("user_id") from "user" '.
			'where "user_id" = :uid'
		);

		$sth->bindParam(':uid', $user_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_NUM);

		return $result[0] > 0;
	}

	public function isUserInGame ($user_id) {
		$sth = $this->model->prepare(
			'select count("user_id") from "c_user_game" '.
			'where "user_id" = :uid '.
			'and "game_id" = :gid'
		);

		$sth->bindParam(':uid', $user_id, PDO::PARAM_INT);
		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_NUM);

		return $result[0];
	}

	public function getUserCash ($user_id) {
		$sth = $this->model->prepare(
			'select "cash" from "c_user_game" '.
			'where "user_id" = :uid and "game_id" = :gid'
		);

		$sth->bindParam(':uid', $user_id, PDO::PARAM_INT);
		$sth->bindParam(':gid', $game_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_NUM);

		return @$result[0];
	}

	public function getTotalPlayerWorth ($user_id) {
		$sth = $this->model->prepare(
			# see `function-player_worth.sql'
			'select player_worth(:gid, :uid)'
		);

		$sth->bindParam(':uid', $user_id, PDO::PARAM_INT);
		$sth->bindParam(':gid', $game_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_NUM);

		return @$result[0];
	}

	public function getNumDoubles ($user_id) {
		$sth = $this->model->prepare(
			'select "doubles" '.
			'from "c_user_game" '.
			'where "game_id" = :gid and "user_id" = :uid'
		);

		$sth->bindParam(':uid', $user_id, PDO::PARAM_INT);
		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_NUM);

		return @$result[0];
	}

	# simple internal function - requires no update to the view
	public function incrementDoubles ($user_id) {
		$sth = $this->model->prepare(
			'update "c_user_game" '.
			'set "doubles" = "doubles" + 1 '.
			'where "game_id" = :gid and "user_id" = :uid '.
			'returning "doubles"'
		);

		$sth->bindParam(':uid', $user_id, PDO::PARAM_INT);
		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_NUM);

		return @$result[0];

	}

	# simple internal function - requires no update to the view
	public function resetDoubles ($user_id) {
		$sth = $this->model->prepare(
			'update "c_user_game" '.
			'set "doubles" = 0 '.
			'where "game_id" = :gid and "user_id" = :uid'
		);

		$sth->bindParam(':uid', $user_id, PDO::PARAM_INT);
		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);

		return $sth->execute();

	}

	public function setUserCash ($user_id, $cash) {
		$sth = $this->model->prepare(
			'update "c_user_game" set "cash" = :csh '.
			'where "user_id" = :uid and "game_id" = :gid'
		);

		$sth->bindParam(':uid', $user_id, PDO::PARAM_INT);
		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);
		$sth->bindParam(':csh', $cash, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		# XXX : tell update module about it
		return $this->model->update->pushUpdate([
			'type'	=> 'cash',
			'id'	=> $user_id,
			'cash'	=> $cash,
		]);
	}

	public function addUserCash ($user_id, $cash_delta) {
		$sth = $this->model->prepare(
			'update "c_user_game" '.
			'set "cash" = "cash" + :cshd '.
			'where "user_id" = :uid and "game_id" = :gid '.
			'returning "cash"'	# return the new cash amount
		);

		$sth->bindParam(':uid', $user_id, PDO::PARAM_INT);
		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);
		$sth->bindParam(':cshd', $cash_delta, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_NUM);
		$cash = @$result[0];
		if (!isset($cash)) {
			return false;
		}

		# XXX : tell update module about it
		if (!$this->model->update->pushUpdate([
			'type'	=> 'cash',
			'id'	=> $user_id,
			'cash'	=> $cash,
		])) {
			return false;
		}


		return $cash;
	}

	public function joinGame ($user_id, $game_id) {
		$initial_cash = $this->model->rules->getRuleValue('starting_cash');

		$sth = $this->model->prepare(
			'insert into "c_user_game" ("user_id", "game_id", "cash") '.
			'values (:uid, :gid, :csh)'
		);

		$sth->bindParam(':uid', $user_id, PDO::PARAM_INT);
		$sth->bindParam(':gid', $game_id, PDO::PARAM_INT);
		$sth->bindParam(':csh', $initial_cash, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		# XXX : tell update module about it
		return $this->model->update->pushUpdate([
			'type'	=> 'join',
			'id'	=> $user_id,
			'name'	=> $this->resolveUserId($user_id),
			'cash'	=> $initial_cash,
		]);
	}

	public function leaveGame ($user_id, $game_id) {
		$sth = $this->model->prepare(
			'delete from "c_user_game" '.
			'where "user_id" = :uid and "game_id" = :gid'
		);

		$sth->bindParam(':uid', $user_id, PDO::PARAM_INT);
		$sth->bindParam(':gid', $game_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		# TODO : tell update module about it?
		# TODO : give up all property
		return true;
	}

	function __get ($name) {
		switch ($name) {
		}
	}
}
