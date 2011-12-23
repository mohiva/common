<?php

namespace com\mohiva\test\resources\common\lang\reflection;

use \com\mohiva\common\lang\ReflectionClass as Class1, \com\mohiva\common\lang\ReflectionProperty as Class2;
use \com\mohiva\common\lang\ReflectionMethod as Class3;

class ClassWithFullyQualifiedUseStatements {}

// Should not be recognized
use ArrayIterator;
