<?php

class ModelChat {
	private $model, $game_id;

	function __construct ($model, $game_id) {
		$this->model = $model;
		$this->game_id = $game_id;
	}

	function init () {
		# XXX : method stub
		#       ( Nothing to do here! )
		#       v
		#    /\o
		#     /\/
		#    /\
		#   /  \
		#  LOL LOL
	}

	# initial creation inside the database
	function create () {
	}

	public function addChatMessage ($user_id, $chat_text) {
		$sth = $this->model->prepare(
			'insert into "chat" ('.
				'"user_id", "game_id", "chat_text"'.
			') values (:uid, :gid, :ctx)'
		);

		$sth->bindParam(':uid', $user_id, PDO::PARAM_INT);
		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);
		$sth->bindParam(':ctx', $chat_text, PDO::PARAM_STR);

		$sth->execute();
	}

	public function getChatUpdates ($chat_last) {
		$sth = $this->model->prepare(
			'select "user_name" as "user", '.
				'"chat_text" as "text", '.
				'"chat_time" as "time", '.
				'"chat_id" from ('.

				'"chat" left join "user" on ('.
					'"user"."user_id" = "chat"."user_id"'.
				')'.
			') '.
			'where "game_id" = :gid and "chat_id" > :cid '.
			'order by "chat_time" asc'
		);

		$sth->bindParam(':gid', $this->game_id, PDO::PARAM_INT);
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

		$data = [
			'instructions'	=> $instructions,
			'newstate'	=> $newstate,
		];

		return $data;
	}

	function __get ($name) {
		switch ($name) {
		}
	}
}
