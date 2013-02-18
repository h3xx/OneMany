<?php
session_start();
# microtime(as_float=true) is time as float (int = seconds)
$now = microtime(true);
$interval = 10; # 10 seconds
if (@$_GET['start'] || !isset($_SESSION['tstart'])) {
    $_SESSION['tstart'] = $now;
    $progress = 0;
} else {
    $progress = ($now - $_SESSION['tstart']) / $interval;
}

header('Content-Type: application/json');
print json_encode(['progress' => $progress * 100]);
