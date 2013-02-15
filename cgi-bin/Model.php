<?php

require_once('Model/Database.php');
require_once('Model/Game.php');

class Model {
	private $dbi;

	private $game, $game_id;

	function __construct ($game_id) {
		$this->game_id = $game_id;
		$this->init();
	}

	function init () {
		# lazy create
		if ($this->game_id < 0) {
			$this->game_id = $this->getGame()->create();
		}
	}

	function prepare ($sql) {
		return $this->getDbi()->prepare($sql);
	}

	private function getDbi () {
		if (!isset($this->dbi)) {
			$this->dbi = new Database();
		}
		return $this->dbi;
	}

	private function getGame () {
		if (!isset($this->game)) {
			$this->game = new Game($this, $this->game_id);
		}
		return $this->game;
	}

	function __get ($name) {
		switch ($name) {
			case 'game':
				return $this->getGame();
				;;
		}
	}
}
