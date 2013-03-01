<?php

class ModelRules {
	private $model, $game_id;

	private $cache;

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
		# use cache first
		if (isset($this->cache[$rule_name])) {
			return $this->cache[$rule_name];
		}

		$sth = $this->model->prepare(
			'select rule_or_default(:gid, :rn)'
		);

		$sth->bindParam(':rn', $rule_name, PDO::PARAM_STR);
		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);

		if (!$sth->execute()) {
			return false;
		}

		$result = $sth->fetch(PDO::FETCH_NUM);
		# store in cache
		$this->cache[$rule_name] = @$result[0];

		return @$result[0];
	}
}
