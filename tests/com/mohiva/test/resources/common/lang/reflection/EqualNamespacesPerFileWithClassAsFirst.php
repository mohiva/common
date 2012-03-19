<?php

namespace com\mohiva\test\resources\common\lang\reflection {
	use com\mohiva\common\lang\ReflectionClass as Class1, com\mohiva\common\lang\ReflectionProperty as Class2;
	use com\mohiva\common\lang\ReflectionMethod as Class3;

	class EqualNamespacesPerFileWithClassAsFirst {}
}

namespace com\mohiva\test\resources\common\lang\reflection {
	use com\mohiva\common\lang\ReflectionClass as Class4, com\mohiva\common\lang\ReflectionProperty as Class5;
	use com\mohiva\common\lang\ReflectionMethod as Class6;
}
