<?php
/**
 * Mohiva Common
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.textile.
 * It is also available through the world-wide-web at this URL:
 * https://github.com/mohiva/common/blob/master/LICENSE.textile
 *
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Test
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
namespace com\mohiva\test\common\xml;

use com\mohiva\common\xml\XMLDocument;

/**
 * Unit test case for the `XMLAttribute` class.
 *
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Test
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class XMLAttributeTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Test if the `__toString` method returns a node value as string.
	 */
	public function testMagicToString() {

		$doc = new XMLDocument;
		$doc->root('config')->attribute('email', 'user@domain.com');

		$this->assertSame('user@domain.com', (string) $doc('#/config/@email'));
	}

	/**
	 * Test if the `toBool` method returns a boolean `true` if the attribute contains the string representation
	 * of a boolean `true`.
	 */
	public function testToBoolReturnsTrueOnBooleanTrue() {

		$doc = new XMLDocument;
		$doc->root('config')->attribute('default', 'true');

		$this->assertTrue($doc('#/config/@default')->toBool());
	}

	/**
	 * Test if the `toBool` method returns a boolean `false` if the attribute contains the string representation
	 * of a boolean `false`.
	 */
	public function testToBoolReturnsFalseOnBooleanFalse() {

		$doc = new XMLDocument;
		$doc->root('config')->attribute('default', 'false');

		$this->assertFalse($doc('#/config/@default')->toBool());
	}

	/**
	 * Test if the `toBool` method returns the boolean value for a string.
	 */
	public function testToBoolReturnsBooleanValueForString() {

		$doc = new XMLDocument;
		$doc->root('config')->attribute('default', 'test');

		$this->assertTrue($doc('#/config/@default')->toBool());
	}

	/**
	 * Test if the `toInt` method returns a node value as int.
	 */
	public function testToIntReturnsIntegerValue() {

		$doc = new XMLDocument;
		$doc->root('config')->attribute('number', '12345');

		$this->assertSame(12345, $doc('#/config/@number')->toInt());
	}

	/**
	 * Test if the `toFloat` method returns a node value as float.
	 */
	public function testToFloatReturnsFloatingPointValue() {

		$doc = new XMLDocument;
		$doc->root('config')->attribute('number', '1.12345');

		$this->assertSame(1.12345, $doc('#/config/@number')->toFloat());
	}

	/**
	 * Test if the `toString` method returns a node value as string.
	 */
	public function testToStringReturnsStringValue() {

		$doc = new XMLDocument;
		$doc->root('config')->attribute('email', 'user@domain.com');

		$this->assertSame('user@domain.com', $doc('#/config/@email')->toString());
	}
}
