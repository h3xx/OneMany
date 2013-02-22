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

		# XXX : tell update module about it?
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
