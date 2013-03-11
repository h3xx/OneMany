<?php

require_once('Model/Database.php');
require_once('Model/Game.php');
require_once('Model/Chat.php');
require_once('Model/User.php');
require_once('Model/Update.php');
require_once('Model/Rules.php');

class Model {
	private $_game_id;

	function __construct ($game_id) {
		$this->_game_id = $game_id;
	}

	function prepare ($sql) {
		return $this->dbi->prepare($sql);
	}

	private function getDbi () {
		if (!isset($this->dbi)) {
			$this->dbi = new Database();
		}
		return $this->dbi;
	}

	private function getGame () {
		if (!isset($this->game)) {
			$this->game = new ModelGame($this, $this->game_id);
		}
		return $this->game;
	}

	private function getChat () {
		if (!isset($this->chat)) {
			$this->chat = new ModelChat($this, $this->game_id);
		}
		return $this->chat;
	}

	private function getUser () {
		if (!isset($this->user)) {
			$this->user = new ModelUser($this, $this->game_id);
		}
		return $this->user;
	}

	private function getUpdate () {
		if (!isset($this->update)) {
			$this->update = new ModelUpdate($this, $this->game_id);
		}
		return $this->update;
	}

	private function getRules () {
		if (!isset($this->rules)) {
			$this->rules = new ModelRules($this, $this->game_id);
		}
		return $this->rules;
	}

	function __get ($name) {
		switch ($name) {
			case 'game_id':
				return isset($this->_game_id) ? $this->_game_id : $_SESSION['game_id'];
				break;
				;;
			case 'game':
				return $this->getGame();
				break;
				;;
			case 'chat':
				return $this->getChat();
				break;
				;;
			case 'user':
				return $this->getUser();
				break;
				;;
			case 'update':
				return $this->getUpdate();
				break;
				;;
			case 'rules':
				return $this->getRules();
				break;
				;;
			case 'dbi':
				# XXX : debugging
				return $this->getDbi();
				break;
				;;
		}
	}
}
