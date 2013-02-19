<pre>
<?php
require_once('../cgi-bin/Model.php');

$mdl = new Model(23);

$dbh = $mdl->dbi->dbh;

$sth = $dbh->prepare(
		'select "TEXT", "RECORDID" from chance order by "RECORDID" asc'
		);

#$sth->bindParam(':gid', $game_id, PDO::PARAM_INT);
#$sth->bindParam(':gst', $game_state, PDO::PARAM_INT);

$rid = 0;
$sth->bindColumn(2, $rid);

$sth->execute();

$result = $sth->fetchAll(PDO::FETCH_COLUMN, 0);

$data = [
	'instructions'	=> $result,
	'newstate'	=> $rid,
];

var_dump($data);
print json_encode($data, JSON_UNESCAPED_UNICODE);
