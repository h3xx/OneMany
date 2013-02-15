<?php

class ModelBoard {
	private $model, $game_name, $game_id;

	private $chance, $commchest;

	function __construct ($model, $game_id) {
		$this->model = $model;
		$this->game_id = $game_id;
	}


}
