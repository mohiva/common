<?php

namespace com\mohiva\test\resources\common\lang\annotations;

use com\mohiva\common\lang\annotations\Annotation;

class SingleParameter implements Annotation {
	
	const NAME = 'SingleParameter';
		
	private $param1;
	
	public function __construct($param1) {
		
		$this->param1 = $param1;
	}
	
	public function getName() {
		
		return self::NAME;
	}
	
	public function getParam1() {
		
		return $this->param1;
	}
}
