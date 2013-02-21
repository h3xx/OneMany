<?php

require_once('ChanceDeck.php');
require_once('Board.php');

class ModelGame {
	private $model, $game_name, $game_id;

	private $chance, $commchest, $board;

	function __construct ($model, $game_id) {
		$this->model = $model;
		$this->game_id = $game_id;
	}

	private function newGame () {
		
		$sth = $this->model->prepare(
			'insert into game ("game_name") values (:name) '.
			'returning "game_id"' # return the last inserted row id as the result set
		);
		$sth->bindParam(':name', $this->game_name, PDO::PARAM_STR);
		if (!$sth->execute()) {
			return false;
		}
		
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

	private function getBoard () {
		if (!isset($this->board)) {
			$this->board = new ModelBoard($this->model, $this->game_id);
		}
		return $this->board;
	}

	public function getGameUpdates ($game_state) {
		$sth = $this->model->prepare(
			'select "game_change" as "change", '.
				'"game_newstate" '.
			'from "game_update" '.
			'where "game_id" = :gid and "game_newstate" > :gst '.
			'order by "game_newstate" asc'
		);

		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);
		$sth->bindParam(':gst', $game_state, PDO::PARAM_INT);

		# gets set to the (1-indexed) column last fetched row
		$newstate = 0;
		$sth->bindColumn(2, $newstate);

		if (!$sth->execute()) {
			return false;
		}

		$instructions = $sth->fetchAll(PDO::FETCH_COLUMN, 0);

		$data = [
			'instructions'	=> $instructions,
			'newstate'	=> $newstate,
		];

		return $data;
	}

	public function getGameData () {
		# FIXME
	}

	public function setGameState ($game_state) {
		$sth = $this->model->prepare(
			'update "game" '.
			'set "game_state" = :gst '.
			'where "game_id" = :gid'
		);

		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);
		$sth->bindParam(':gst', $game_state, PDO::PARAM_INT);

		return $sth->execute();
	}

	function __get ($name) {
		switch ($name) {
			case 'chance':
				return $this->getChance();
				break;
				;;
			case 'commchest':
				return $this->getCommChest();
				break;
				;;
			case 'board':
				return $this->getBoard();
				break;
				;;
			#case 'game_id':
			#	return $this->game_id;
			#	;;
		}
	}
}
