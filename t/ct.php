<?php

class tc {
	private $foo = 5;
	function __construct () {
		var_dump($this->foo . _BASE_DIR);
	}
}

$t = new tc();
