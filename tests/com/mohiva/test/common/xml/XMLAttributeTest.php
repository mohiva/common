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
 * @copyright Copyright (c) 2007-2011 Christian Kaps (http://www.mohiva.com)
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
 * @copyright Copyright (c) 2007-2011 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class XMLAttributeTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * Test if a node value can be converted to boolean.
	 */
	public function testToBool() {
		
		$config = new XMLDocument;
		$config->root('config')->attribute('default', true);
		
		$this->assertTrue($config('#/config/attribute::default')->toBool());
	}
	
	/**
	 * Test if a node value can be converted to int.
	 */
	public function testToInt() {
		
		$config = new XMLDocument;
		$config->root('config')->attribute('number', 12345);
		
		$this->assertSame($config('#/config/attribute::number')->toInt(), 12345);
	}
	
	/**
	 * Test if a node value can be converted to float.
	 */
	public function testToFloat() {
		
		$config = new XMLDocument;
		$config->root('config')->attribute('number', 1.12345);
		
		$this->assertSame($config('#/config/attribute::number')->toFloat(), 1.12345);
	}
	
	/**
	 * Test if a node value can be converted to string.
	 */
	public function testToString() {
		
		$config = new XMLDocument;
		$config->root('config')->attribute('email', 'user@domain.com');
		
		$this->assertSame($config('#/config/attribute::email')->toString(), 'user@domain.com');
	}
}
