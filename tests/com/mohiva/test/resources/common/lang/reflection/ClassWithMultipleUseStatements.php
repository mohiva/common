<?php

namespace com\mohiva\test\resources\common\lang\reflection;

use com\mohiva\common\lang\ReflectionClass, com\mohiva\common\lang\ReflectionProperty;
use com\mohiva\common\lang\ReflectionMethod;

class ClassWithMultipleUseStatements {

	public $foo = null;

	public function foo() {}
}

// Should not be recognized
use ArrayIterator;
