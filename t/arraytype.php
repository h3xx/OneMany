<pre>
<?php

# pretend we're logged in
require_once('../cgi-bin/Model.php');

$uid = 2;
$gid = 4;

$mdl = new Model($gid);

$sth = $mdl->prepare('select "last_roll" from "game" where "game_id" = 2');

if (!$sth->execute()) {
	print('Failed');
	exit;
}

$result = $sth->fetchAll(PDO::FETCH_ASSOC);
var_dump($result);
