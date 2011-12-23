<?php

namespace com\mohiva\test\resources\common\lang\annotations;

use com\mohiva\common\lang\annotations\Annotation;

class DoubleParameter implements Annotation {
	
	const NAME = 'DoubleParameter';
	
	private $param1;
	private $param2;
	
	public function __construct($param1, $param2) {
		
		$this->param1 = $param1;
		$this->param2 = $param2;
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
}
