<?php

require_once('ChanceDeck.php');

class ModelGame {
	private $model, $game_name, $game_id;

	private $chance, $commchest;

	function __construct ($model, $game_id) {
		$this->model = $model;
		$this->game_id = $game_id;
	}

	function init () {
		$this->getChance()->init();
	}

	# initial creation inside the database
	function create ($game_name) {
		$this->game_name = $game_name;
		$this->newGame();
		$this->getChance()->create();
		return $this->game_id;
	}

	private function newGame () {
		
		$sth = $this->model->prepare(
			'insert into game ("game_name") values (:name) '.
			'returning "game_id"' # return the last inserted row id as the result set
		);
		$sth->bindParam(':name', $this->game_name, PDO::PARAM_STR);
		$sth->execute();
		
		# grab our game_id from the result set
		$res = $sth->fetchAll(PDO::FETCH_NUM);
		$this->game_id = $res[0][0];

		#logger("Game: inserted game named `{$this->game_name}' : game_id : {$this->game_id}");
	}

	private function getChance () {
		if (!isset($this->chance)) {
			$this->chance = new ModelChanceDeck($this->model, $this->game_id);
		}
		return $this->chance;
	}

	private function getCommChest () {
		if (!isset($this->commchest)) {
			#$this->commchest = new ModelCommChestDeck($this->model, $this->game_id);
		}
		return $this->commchest;
	}

	function __get ($name) {
		switch ($name) {
			case 'chance':
				return $this->getChance();
				;;
			case 'commchest':
				return $this->getCommChest();
				;;
			#case 'game_id':
			#	return $this->game_id;
			#	;;
		}
	}
}
