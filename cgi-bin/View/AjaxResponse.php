<?php

class ViewAjaxResponse {
	private $model;

	__construct ($model) {
		$this->model = $model;
	}

	public function getBoardUpdateInstructions ($game_id, $game_state) {
		$sth = $this->model->prepare(
			'select "game_change", "game_newstate" from "game_update" '.
			'where "game_id" = :gid and "game_newstate" > :gst '.
			'order by "game_newstate" asc'
		);

		$sth->bindParam(':gid', $game_id, PDO::PARAM_INT);
		$sth->bindParam(':gst', $game_state, PDO::PARAM_INT);

		# gets set to the (1-indexed) column last fetched row
		$newstate = 0;
		$sth->bindColumn(2, $newstate);

		$sth->execute();

		$instructions = $sth->fetchAll(PDO::FETCH_COLUMN, 0);

		$jsondata = [
			'instructions'	=> $instructions,
			'newstate'	=> $newstate,
		];

		return json_encode($jsondata, JSON_UNESCAPED_UNICODE);
	}

	public function getChatUpdate ($game_id, $chat_last) {
		$sth = $this->model->prepare(
			'select "user_name" as "user", '.
				'"chat_text" as "text", '.
				'"chat_time" as "time", '.
				'"chat_id" from ('.

				'"chat" left join "user" on ("user"."user_id" = "chat"."user_id")'.
			') '.
			'where "game_id" = :gid and "chat_id" > :cid '.
			'order by "chat_time" asc'
		);

		$sth->bindParam(':gid', $game_id, PDO::PARAM_INT);
		$sth->bindParam(':cid', $chat_last, PDO::PARAM_INT);

		# gets set to the (1-indexed) column last fetched row
		$newstate = 0;
		$sth->bindColumn(4, $newstate);

		$sth->execute();

		$result = $sth->fetchAll(PDO::FETCH_ASSOC);

		# limit result set
		$onlywant = ['user'=>0,'text'=>0,'time'=>0];
		$instructions = [];

		foreach ($result as $row) {
			$instructions []= array_intersect_key($row, $onlywant);
		}

		$jsondata = [
			'instructions'	=> $instructions,
			'newstate'	=> $newstate,
		];

		return json_encode($jsondata, JSON_UNESCAPED_UNICODE);

	}
}
