<?php

class Foo {
	private $baz;
	public $blarg;

	function __construct () {}

	private function getBar () {
		print("Running getBar()\n");
		if (!isset($this->bar)) {
			$this->bar = 5;
		}
		return $this->bar;
	}

	private function getBaz () {
		print("==Running getBaz()\n");
		if (!isset($this->baz)) {
			$this->baz = 5;
		}
		return $this->baz;
	}

	private function getBlarg () {
		print("==Running getBlarg()\n");
		if (!isset($this->blarg)) {
			$this->blarg = 5;
		}
		return $this->blarg;
	}

	public function pubfunc1() {
		print("==Running pubfunc1()\n");
		print('==bar is ' . $this->bar . "\n");
	}

	public function pubfunc2() {
		print("==Running pubfunc2()\n");
		print('==baz is ' . $this->baz . "\n");
	}

	public function pubfunc3() {
		print("==Running pubfunc3()\n");
		print('==blarg is ' . $this->blarg . "\n");
	}

	function __get ($name) {
		print("==Running __get($name)\n");
		switch ($name) {
			case 'bar':
				return $this->getBar();
				;;
			case 'baz':
				return $this->getBaz();
				;;
			case 'blarg':
				return $this->getBlarg();
				;;
		}
	}
}

$f = new Foo();

# test 1 : non-declared member
print("test1: Two subsequent calls:\n");
$f->pubfunc1();
$f->pubfunc1();

print("\ntest2: Private members only set when accessed publically:\n");
$f->pubfunc2();
print("test2: done.\n\n");
print("test2: publically, baz is " . $f->baz . "\n");
$f->pubfunc2();

print("\ntest3: Public members never by accessor:\n");
$f->pubfunc3();
print("test2: publically, blarg is " . $f->blarg . "\n");
$f->pubfunc3();
