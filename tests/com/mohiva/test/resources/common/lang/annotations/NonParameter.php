<?php

namespace com\mohiva\test\resources\common\lang\annotations;

use com\mohiva\common\lang\annotations\Annotation;

class NonParameter implements Annotation {
	
	const NAME = 'NonParameter';
	
	public function __construct() {}
	
	public function getName() {
		
		return self::NAME;
	}
}
