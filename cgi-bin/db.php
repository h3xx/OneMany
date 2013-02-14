<?php

# set up the database connection

class Database {
	private $ini;
	private $link;


	function __construct () {
		$this->ini = $this->getIni();
	}
/*
	function __construct ($ini) {
		$this->ini = $ini;
	}
*/
	private function getLink () {
		if ($this->link) {
			return $this->link;
		}

		$parse = parse_ini_file($this->ini, true);

		$driver = @$parse['database']['db_driver'];
		$dsn = $driver . ':';
		$user = @$parse['database']['db_user'];
		$password = @$parse['database']['db_password'];
		$options = @$parse['database']['db_options'];
		$attributes = @$parse['database']['db_attributes'];

		foreach ($parse['database']['dsn'] as $k => $v) {
			$dsn .= "${k}=${v};" ;
		}

		$this->link = new PDO($dsn, $user, $password, $options) ;

		if (isset($attributes)) {
			foreach ($attributes as $k => $v) {
				$this->link->setAttribute(
						constant("PDO::{$k}")
						, constant ( "PDO::{$v}" ) ) ;
			}
		}

		return $this->link;
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
			case 'link':
				return $this->getLink();
			;;
		}
	}

/*
	public static function __callStatic ( $name, $args ) {
		$callback = array ( self :: getLink ( ), $name ) ;
		return call_user_func_array ( $callback , $args ) ;
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


