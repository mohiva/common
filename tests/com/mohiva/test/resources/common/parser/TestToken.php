<?php

namespace com\mohiva\test\resources\common\parser;

use com\mohiva\common\parser\Token;

class TestToken implements Token {

	private $code = null;

	public function __construct($code) {

		$this->code = $code;
	}

	public function getCode() {

		return $this->code;
	}
}
