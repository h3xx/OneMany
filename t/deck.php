<pre>
<?php

require_once('../cgi-bin/Database.php');
require_once('../cgi-bin/ChanceDeck.php');

$dbh = new Database();
$cd = new ChanceDeck($dbh);

var_dump($cd->foo);
