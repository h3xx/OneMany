<?php
session_start();

# pretend we're logged in
require_once('../cgi-bin/Controller.php');
require_once('../cgi-bin/View.php');
require_once('../cgi-bin/Model.php');

$uid = 2;
$gid = 4;

$mdl = new Model($gid);

var_dump($mdl->game->board->rentForSpace(13, 12));
