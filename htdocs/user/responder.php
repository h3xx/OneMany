<?php
session_start();

# pretend we're logged in
require_once('../../cgi-bin/Controller.php');
require_once('../../cgi-bin/View.php');
require_once('../../cgi-bin/Model.php');

$uid = 1;
$gid = 2;

$mdl = new Model($gid);
$view = new View($mdl, $uid);
$ctr = new Controller($mdl, $uid);

$what = @$_REQUEST['method'];
$func = @$_REQUEST['func'];
$args = @$_REQUEST['args'];

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

if (isset($res)) {
	header('Content-Type: application/json; charset=utf8');
	print($res);
}
