<?php

namespace com\mohiva\test\resources\common\lang\annotations;

use com\mohiva\common\lang\annotations\Annotation;
use stdClass;

class ObjectValue implements Annotation {

	const NAME = 'ObjectValue';

	private $param1;

	public function __construct(stdClass $param1) {

		$this->param1 = $param1;
	}

	public function getName() {

		return self::NAME;
	}

	public function getParam1() {

		return $this->param1;
	}
}
