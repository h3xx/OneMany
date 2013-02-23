<?php

class ModelUpdate {
	private $model, $game_id;

	function __construct ($model, $game_id) {
		$this->model = $model;
		$this->game_id = $game_id;
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

	public function pushUpdate ($update_json) {
		$sth = $this->model->prepare(
			'insert into "game_update" ("game_id", "game_change") '.
			'values (:gid, :chn)'
			#.' returning "game_newstate"' # implement later?
		);

		if (is_array($update_json)) {
			$update_json = json_encode($update_json, JSON_UNESCAPED_UNICODE);
		}

		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);
		$sth->bindParam(':chn', $update_json, PDO::PARAM_STR);

		if (!$sth->execute()) {
			return false;
		}

		return true;
	}

}
