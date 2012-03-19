<?php

namespace com\mohiva\test\resources\common\util;

use com\mohiva\common\util\Event;

class TestEvent extends Event {

	public $dispatched = false;

	public $dispatchList = array();
}
