<?php

namespace com\mohiva\test\resources\common\lang\annotations;

use com\mohiva\common\lang\annotations\Annotation;
use stdClass;

class Value implements Annotation {

	const NAME = 'Value';

	private $param1;
	private $param2;
	private $param3;
	private $param4;
	private $param5;
	private $param6;
	private $param7;
	private $param8;
	private $param9;
	private $param10;
	private $param11;
	private $param12;
	private $param13;

	public function __construct(
		$param1,
		$param2,
		$param3,
		$param4,
		$param5,
		$param6,
		$param7,
		$param8,
		$param9,
		$param10,
		stdClass $param11,
		array $param12,
		NonParameter $param13) {

		$this->param1 = $param1;
		$this->param2 = $param2;
		$this->param3 = $param3;
		$this->param4 = $param4;
		$this->param5 = $param5;
		$this->param6 = $param6;
		$this->param7 = $param7;
		$this->param8 = $param8;
		$this->param9 = $param9;
		$this->param10 = $param10;
		$this->param11 = $param11;
		$this->param12 = $param12;
		$this->param13 = $param13;
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

	public function getParam7() {

		return $this->param7;
	}

	public function getParam8() {

		return $this->param8;
	}

	public function getParam9() {

		return $this->param9;
	}

	public function getParam10() {

		return $this->param10;
	}

	public function getParam11() {

		return $this->param11;
	}

	public function getParam12() {

		return $this->param12;
	}

	public function getParam13() {

		return $this->param13;
	}
}
