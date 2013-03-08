<?php

class ModelUser {
	private $model, $game_id;

	function __construct ($model, $game_id) {
		$this->model = $model;
		$this->game_id = $game_id;
	}

	public function exportModel () {
		$sth = $this->model->prepare(
			'select '.
				'"c_user_game"."user_id" as "id", '.
				'"user_name" as "name", '.
				'"cash", '.
				'"on_space", '.
				'"in_jail" as "jail" '.
			'from "c_user_game" '.
			'left join "user" on ("c_user_game"."user_id" = "user"."user_id") '.
			'where "game_id" = :gid '.
			'order by "sequence" asc'
		);

		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetchAll(PDO::FETCH_ASSOC);

		for ($i = 0; $i < count($result); ++$i) {
			# set the user with the lowest "sequence" value to being the
			# one whose turn it is, and the rest to false.
			$result[$i]['turn'] = ($i == 0);

			# XXX : overcome PDO bug re: all column types being strings
			$result[$i]['id'] = (int)$result[$i]['id'];
			$result[$i]['cash'] = (int)$result[$i]['cash'];
			$result[$i]['on_space'] = (int)$result[$i]['on_space'];
			$result[$i]['jail'] = (boolean)$result[$i]['jail'];
		}

		return $result;
	}

	public function checkLogin ($user_name, $password) {
		$sth = $this->model->prepare(
			# database-side function; returns an integer, or null
			# in the event of an invalid login
			'select check_login(:un, :pw)'
		);

		$sth->bindParam(':un', $user_name, PDO::PARAM_STR);
		$sth->bindParam(':pw', $password, PDO::PARAM_STR);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_NUM);

		return @$result[0];
	}

	public function newLogin ($user_name, $email, $password) {
		$sth = $this->model->prepare(
			# database-side function; returns a boolean
			'select new_login(:un, :em, :pw)'
		);

		$sth->bindParam(':un', $user_name, PDO::PARAM_STR);
		$sth->bindParam(':em', $email, PDO::PARAM_STR);
		$sth->bindParam(':pw', $password, PDO::PARAM_STR);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_NUM);

		return @$result[0];
	}

	public function verifyLogin ($user_id, $verify_string) {
		$sth = $this->model->prepare(
			# database-side function; returns a boolean
			'select verify_user(:uid, :vfy)'
		);

		$sth->bindParam(':uid', $user_id, PDO::PARAM_INT);
		$sth->bindParam(':vfy', $verify_string, PDO::PARAM_STR);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_NUM);

		return @$result[0];
	}

	public function getUserInfo ($user_id) {
		$sth = $this->model->prepare(
			'select '.
				'"user_id" as "id", '.
				'"user_name" as "name", '.
				'"user_email" as "email" '.
			'from "user" '.
			'where "user_id" = :uid '.
			'and "verified"'
		);

		$sth->bindParam(':uid', $user_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		return $sth->fetch(PDO::FETCH_ASSOC);
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

	public function resolveUserEmail ($user_email) {
		$sth = $this->model->prepare(
			'select "user_id" from "user" '.
			'where "user_email" = :uem '.
			'limit 1'
		);

		$sth->bindParam(':uem', $user_email, PDO::PARAM_STR);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_NUM);

		return @$result[0];
	}

	public function addPwResetRequest ($user_id) {
		$sth = $this->model->prepare(
			'select set_user_reset(:uid)'
		);

		$sth->bindParam(':uid', $user_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_NUM);

		return @$result[0];
	}

	public function doPwReset ($user_id, $reset_string, $new_pass) {
		$sth = $this->model->prepare(
			'select take_user_reset(:uid, :rst, :pw)'
		);

		$sth->bindParam(':uid', $user_id, PDO::PARAM_INT);
		$sth->bindParam(':rst', $reset_string, PDO::PARAM_STR);
		$sth->bindParam(':pw', $new_pass, PDO::PARAM_STR);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_NUM);

		return @$result[0];
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

	public function getUserCash ($user_id) {
		$sth = $this->model->prepare(
			'select "cash" from "c_user_game" '.
			'where "user_id" = :uid and "game_id" = :gid'
		);

		$sth->bindParam(':uid', $user_id, PDO::PARAM_INT);
		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);

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

	public function isInJail ($user_id) {
		$sth = $this->model->prepare(
			'select "in_jail" '.
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

	public function setInJail ($user_id, $in_jail) {
		# caveat : does not put the user's piece in the "in jail" spot; use other function for that
		$sth = $this->model->prepare(
			'update "c_user_game" '.
			'set "in_jail" = :ij '.
			'where "user_id" = :uid and "game_id" = :gid'
		);

		$sth->bindParam(':uid', $user_id, PDO::PARAM_INT);
		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);
		$sth->bindParam(':ij', $in_jail, PDO::PARAM_BOOL);

		if (!$sth->execute()) {
			return false;
		}

		# XXX : tell update module about it
		return $this->model->update->pushUpdate([
			'type'	=> 'jail',
			'id'	=> $user_id,
			'in_jail'=> $in_jail,
		]);
	}

	public function moveToSpace ($user_id, $space_id) {
		$sth = $this->model->prepare(
			'update "c_user_game" '.
			'set "on_space" = :sid '.
			'where "user_id" = :uid and "game_id" = :gid'
		);

		$sth->bindParam(':uid', $user_id, PDO::PARAM_INT);
		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);
		$sth->bindParam(':sid', $space_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		# XXX : tell update module about it
		return $this->model->update->pushUpdate([
			'type'	=> 'move',
			'id'	=> $user_id,
			'space'	=> $space_id,
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
