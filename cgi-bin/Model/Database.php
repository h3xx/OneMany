<?php

# set up the database connection

class Database {

	function __construct () {
	}
/*
	function __construct ($ini) {
		$this->ini = $ini;
	}
*/

	function __call ($name, $args) {
		switch ($name) {
			case 'commit':
			case 'rollBack':
			case 'exec':
			case 'prepare':
			case 'query':
			case 'errorInfo':
			case 'errorCode':
				$callback = [ $this->dbh, $name ];
				return call_user_func_array($callback, $args);
			;;
		}
	}

	private function getDbh () {
		if (isset($this->dbh)) {
			return $this->dbh;
		}

		$parse = parse_ini_file($this->ini, true);

		$driver = @$parse['database']['db_driver'];
		$dsn = $driver . ':';
		$user = @$parse['database']['db_user'];
		$password = @$parse['database']['db_password'];
		$options = @$parse['database']['db_options'];
		$attributes = @$parse['database']['db_attributes'];
		$initcmds = @$parse['database']['db_initcmd'];

		if (is_array($parse['database']['dsn'])) {
			foreach ($parse['database']['dsn'] as $k => $v) {
				$dsn .= "${k}=${v};";
			}
		} else {
			$dsn .= $parse['database']['dsn'];
		}

		$this->dbh = new PDO($dsn, $user, $password, $options);

		if (isset($attributes)) {
			foreach ($attributes as $k => $v) {
				$this->dbh->setAttribute(
						constant("PDO::{$k}")
						, constant ("PDO::{$v}"));
			}
		}

		if (isset($initcmds)) {
			if (is_array($initcmds)) {
				foreach ($initcmds as $cmd) {
					$this->dbh->exec($cmd);
				}
			} else {
				$this->dbh->exec($initcmds);
			}
		}

		return $this->dbh;
	}

	private function getIni () {
		if (!isset($this->ini)) {
			$this->ini = $_SERVER['DOCUMENT_ROOT'] . '/config.ini';
		}
		return $this->ini;
	}

	public function __get ($name) {
		switch ($name) {
			case 'ini':
				return $this->getIni();
			;;
			case 'dbh':
				return $this->getDbh();
			;;
		}
	}

/*
	public static function __callStatic ($name, $args) {
		$callback = [ self :: getDbh ( ), $name ];
		return call_user_func_array($callback, $args);
	}
*/

}

// examples
/*
$stmt = Database :: prepare ( "SELECT 'something' ;" ) ;
$stmt -> execute ( ) ;
var_dump ( $stmt -> fetchAll ( ) ) ;
$stmt -> closeCursor ( ) ;
*/


