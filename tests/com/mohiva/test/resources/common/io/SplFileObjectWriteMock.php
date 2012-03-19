<?php
namespace com\mohiva\test\resources\common\io;

class SplFileObjectWriteMock extends \SplFileObject {

	public function fwrite($str, $length = 0) {

		return false;
	}
}
