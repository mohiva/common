<?php

namespace com\mohiva\test\resources\common\lang\annotations;

use com\mohiva\common\lang\annotations\Annotation;

class DefaultValue implements Annotation {
	
	const NAME = 'DefaultValue';
	
	private $param1;
	private $param2;
	private $param3;
	
	public function __construct($param1 = 'param1', $param2 = 'param2', $param3 = 'param3') {
		
		$this->param1 = $param1;
		$this->param2 = $param2;
		$this->param3 = $param3;
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
}
