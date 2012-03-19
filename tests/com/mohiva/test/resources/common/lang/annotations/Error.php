<?php

namespace com\mohiva\test\resources\common\lang\annotations;

use com\mohiva\common\lang\annotations\Annotation;

class Error implements Annotation {

	const NAME = 'Error';

	public function __construct($param1, $param2, $param3) {}

	public function getName() {

		return self::NAME;
	}
}
