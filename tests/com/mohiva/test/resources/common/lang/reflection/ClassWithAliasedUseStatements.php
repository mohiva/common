<?php

namespace com\mohiva\test\resources\common\lang\reflection;

use com\mohiva\common\lang as lang;
use lang\ReflectionClass as Class1, lang\ReflectionProperty as Class2;
use lang\ReflectionMethod as Class3;

class ClassWithAliasedUseStatements {}

// Should not be recognized
use ArrayIterator;
