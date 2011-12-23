<?php

namespace com\mohiva\test\resources\common\lang\reflection\different {
	use com\mohiva\common\lang\ReflectionClass as Class1, com\mohiva\common\lang\ReflectionProperty as Class2;
	use com\mohiva\common\lang\ReflectionMethod as Class3;
}

namespace {
	use com\mohiva\common\lang\ReflectionClass as Class4, com\mohiva\common\lang\ReflectionProperty as Class5;
	use com\mohiva\common\lang\ReflectionMethod as Class6;
}

namespace com\mohiva\test\resources\common\lang\reflection {
	use com\mohiva\common\lang\ReflectionClass as Class7, com\mohiva\common\lang\ReflectionProperty as Class8;
	use com\mohiva\common\lang\ReflectionMethod as Class9;

	class DifferentNamespacesPerFileWithClassAsLast {}
}
