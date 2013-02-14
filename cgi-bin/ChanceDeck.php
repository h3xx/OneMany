<?php

class ChanceDeck {
	public $foo;
	function __construct($dbh) {
		$this->foo = $dbh->getChanceCards();
	}
}
