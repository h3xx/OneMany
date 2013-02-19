<pre>
<?php
function getChatUpdate ($game_id, $chat_last) {
	/*
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
	*/

	$newstate = 10;
	$result = [
		[
			'user'	=> 'h3xx',
			'text'	=> 'hi',
			'time'	=> '123',
			'chat_id'	=> '1',
		],
		[
			'user'	=> 'h3xx',
			'text'	=> 'hi1',
			'time'	=> '124',
			'chat_id'	=> '2',
		],
	];

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

print getChatUpdate(0,0);
