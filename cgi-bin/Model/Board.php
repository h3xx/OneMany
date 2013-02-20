<?php

class ModelBoard {
	private $model, $game_name, $game_id;

	function __construct ($model, $game_id) {
		$this->model = $model;
		$this->game_id = $game_id;
	}

	public function getSpaceInfo ($space_id) {
		$sth = $this->model->prepare(
			'select '.
				'"space_group",'.
				'"space_name",'.
				'"cost",'.
				'"rent",'.
				'"rent1",'.
				'"rent2",'.
				'"rent3",'.
				'"rent4",'.
				'"rent5",'.
				'"housecost",'.
				'"mortgage"'.
			' from "space" where "space_id" = :sid'
		);

		$sth->bindParam(':sid', $space_id, PDO::PARAM_INT);

		$sth->execute();

		$result = $sth->fetch(PDO::FETCH_ASSOC);

		return $result;
	}

	public function isSpaceOwned ($space_id) {
		$sth = $this->model->prepare(
			'select "owner_id" from "c_game_space" '.
			'where "game_id" = :gid and "space_id" = :sid'
		);

		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);
		$sth->bindParam(':sid', $space_id, PDO::PARAM_INT);

		$sth->execute();

		$result = $sth->fetch(PDO::FETCH_NUM);

		return isset($result[0]);

	}

}
