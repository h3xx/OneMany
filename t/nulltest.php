<?php

class Foo {
	function __construct () {}

	private function getBar () {
		print("Running getBar()\n");
		if (!isset($this->bar)) {
			$this->bar = 5;
		}
		return $this->bar;
	}

	public function poop() {
		print("Running poop()\n");
		print('bar is ' . $this->bar . "\n");
	}

	function __get ($name) {
		print("Running __get($name)\n");
		switch ($name) {
			case 'bar':
				return $this->getBar();
				;;
		}
	}
}

$f = new Foo();

$f->poop();
