<?php

class ModelRules {
	private $model, $game_id;

	function __construct ($model, $game_id) {
		$this->model = $model;
		$this->game_id = $game_id;
	}

	/*
	public function getDefaultRules () {
		$sth = $this->model->prepare(
			'select * from rules'
		);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetchAll(PDO::FETCH_ASSOC);

		return $result;
	}
	*/

	public function getRuleValue ($rule_name) {
		$sth = $this->model->prepare(
			'select "rule_value" from "c_game_rules" '.
			'where "rule_name" = :rn and "game_id" = :gid'
		);

		$sth->bindParam(':rn', $rule_name, PDO::PARAM_STR);
		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_NUM);

		return @$result[0];
	}
}
