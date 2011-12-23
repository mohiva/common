<?php

namespace com\mohiva\test\resources\common\lang\annotations;

use com\mohiva\common\lang\annotations\Annotation;
use stdClass;

class Nested implements Annotation {
	
	const NAME = 'Nested';
	
	private $param1;
	private $param2;
	private $param3;
	private $param4;
	private $param5;
	private $param6;
	private $param7;
	private $param8;
	private $param9;
	
	public function __construct(
		NonParameter $param1,
		DefaultValue $param2,
		ArrayValue $param3,
		ObjectValue $param4,
		array $param5,
		stdClass $param6,
		$param7,
		$param8,
		$param9) {
		
		$this->param1 = $param1;
		$this->param2 = $param2;
		$this->param3 = $param3;
		$this->param4 = $param4;
		$this->param5 = $param5;
		$this->param6 = $param6;
		$this->param7 = $param7;
		$this->param8 = $param8;
		$this->param9 = $param9;
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
}
