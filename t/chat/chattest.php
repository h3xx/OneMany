<?php
session_start();

# pretend we're logged in
require_once('../../cgi-bin/Controller.php');
require_once('../../cgi-bin/View.php');
require_once('../../cgi-bin/Model.php');

$uid = 1;
$gid = 2;

$mdl = new Model($gid);
$view = new View($mdl);
$ctr = new Controller($mdl, $uid);

$what = @$_GET['method'];
$func = @$_GET['func'];
$args = @$_GET['args'];

$buff = ['func' => $func, 'args' => $args];

$res = null;
switch ($what) {
	case 'ask':
		$res = $view->processInstruction($buff);
		break;
		;;
	case 'tell':
		$res = $ctr->processInstruction($buff);
		break;
		;;

}


var_dump($res);
