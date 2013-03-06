<?php
session_start();

require_once('../cgi-bin/Model.php');
require_once('../cgi-bin/Controller.php');
require_once('../cgi-bin/View.php');

$uid = @$_SESSION['user_id'];
$gid = @$_SESSION['game_id'];
$gid = 4;

$mdl = new Model($gid);

$what = @$_REQUEST['method'];
$func = @$_REQUEST['func'];
$args = @$_REQUEST['args'];

$buff = ['func' => $func, 'args' => $args];

$res = null;
switch ($what) {
	case 'ask':
		$view = new View($mdl, $uid);
		$res = $view->processInstruction($buff);
		break;
		;;
	case 'tell':
		$ctr = new Controller($mdl, $uid);
		$res = $ctr->processInstruction($buff);
		break;
		;;

}

if (isset($res)) {
	header('Content-Type: application/json; charset=utf8');
	print($res);
}
