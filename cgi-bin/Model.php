<?php

require_once('Model/Database.php');
require_once('Model/Game.php');
require_once('Model/Chat.php');
require_once('Model/User.php');

class Model {
	private $dbi;

	private $game, $chat, $user, $game_id;

	function __construct ($game_id) {
		$this->game_id = $game_id;
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

	function __get ($name) {
		switch ($name) {
			case 'game':
				return $this->getGame();
				;;
			case 'chat':
				return $this->getChat();
				;;
			case 'dbi':
				# XXX : debugging
				return $this->getDbi();
				;;
		}
	}
}
