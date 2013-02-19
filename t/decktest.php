<pre>
<?php
require_once('../cgi-bin/Model.php');

$mdl = new Model(23);
#$gam = new Game($mdl, 23);
#$gam->create('shit');
var_dump($mdl->game->chance->deck);
$mdl->game->chance->moveCardToBack(3);

#var_dump($dbh->errorCode());
