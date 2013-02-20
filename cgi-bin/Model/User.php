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

		$sth->execute();

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

		$sth->execute();

		$result = $sth->fetch(PDO::FETCH_NUM);

		return $result[0];
	}

	public function resolveUserId ($user_id) {
		$sth = $this->model->prepare(
			'select "user_name" from "user" '.
			'where "user_id" = :uid '.
			'limit 1'
		);

		$sth->bindParam(':uid', $user_id, PDO::PARAM_INT);

		$sth->execute();

		$result = $sth->fetch(PDO::FETCH_NUM);

		return $result[0];
	}

	function __get ($name) {
		switch ($name) {
		}
	}
}