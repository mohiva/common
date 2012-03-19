<?php

namespace com\mohiva\test\resources\common\lang\annotations;

use com\mohiva\common\lang\annotations\Annotation;

class Constant implements Annotation {

	const NAME = 'Constant';

	private $param1;
	private $param2;
	private $param3;
	private $param4;
	private $param5;
	private $param6;

	public function __construct($param1, $param2, $param3, $param4, $param5, $param6) {

		$this->param1 = $param1;
		$this->param2 = $param2;
		$this->param3 = $param3;
		$this->param4 = $param4;
		$this->param5 = $param5;
		$this->param6 = $param6;
	}

	public function getName() {

		return self::NAME;
	}

	public function getParam1() {

		return $this->param1;
	}

	public function getParam2() {

		return $this->param2;
	}

	public function getParam3() {

		return $this->param3;
	}

	public function getParam4() {

		return $this->param4;
	}

	public function getParam5() {

		return $this->param5;
	}

	public function getParam6() {

		return $this->param6;
	}
}
