<?php

namespace com\mohiva\test\resources\common\lang\annotations;

use com\mohiva\common\lang\annotations\Annotation;

class ArrayValue implements Annotation {

	const NAME = 'ArrayValue';

	private $param1;

	public function __construct(array $param1) {

		$this->param1 = $param1;
	}

	public function getName() {

		return self::NAME;
	}

	public function getParam1() {

		return $this->param1;
	}
}
