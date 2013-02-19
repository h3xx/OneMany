<pre>
<?php

$ini = $_SERVER['DOCUMENT_ROOT'] . '/config.ini';

var_dump(parse_ini_file($ini, true));
