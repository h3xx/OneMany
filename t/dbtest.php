<?php
require_once('../cgi-bin/Database.php');
$dbh = new Database();

$stmt = $dbh->prepare('create extension pgcrypto');
$stmt->execute();

var_dump($dbh->errorInfo());

#var_dump ( $stmt -> fetchAll ( ) ) ;
#$stmt -> closeCursor ( ) ;
