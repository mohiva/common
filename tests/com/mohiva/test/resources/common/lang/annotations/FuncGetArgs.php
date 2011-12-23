<?php

namespace com\mohiva\test\resources\common\lang\annotations;

use com\mohiva\common\lang\annotations\Annotation;

class FuncGetArgs implements Annotation {
	
	const NAME = 'FuncGetArgs';
	
	private $values = array();
	
	public function __construct() {
		
		$this->values = func_get_args();
	}
	
	public function getName() {
		
		return self::NAME;
	}
	
	public function getValues() {
		
		return $this->values;
	}
}
