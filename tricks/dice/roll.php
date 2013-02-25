<?php
session_start();

function doRoll ($dice = 1, $min = 1, $max = 6) {
    $rolls = [];
    $tot = 0;
    for ($x = 0; $x < $dice; ++$x) {
	$r = rand($min, $max);
	$rolls []= $r;
	$total += $r;
    }
}

# pretend we're logged in
$uid = 1;
$gid = 2;

$what = @$_REQUEST['method'];
$func = @$_REQUEST['func'];
$args = @$_REQUEST['args'];

$res = null;
switch ($what) {
	case 'ask':
		$res = 
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
} # else {
    # well, I don't really see why I should give a shit.
#}
