<?php
namespace com\mohiva\test\resources\common\io;

class SplFileObjectReadMock extends \SplFileObject {

	public function fgets() {
		
		throw new \RuntimeException();
	}
}
