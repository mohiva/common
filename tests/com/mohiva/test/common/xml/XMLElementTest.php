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

use com\mohiva\test\common\Bootstrap;
use com\mohiva\common\xml\XMLDocument;
use com\mohiva\common\xml\XMLElement;
use com\mohiva\common\xml\exceptions\XMLException;

/**
 * Unit test case for the `XMLElement` class.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Test
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2011 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class XMLElementTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * Test the `child` method without a namespace.
	 */
	public function testChildMethodWithoutNS() {
		
		$config = new XMLDocument;
		$config->root('config')->child('email', 'user@domain.com');
		$this->assertInstanceOf('com\mohiva\common\xml\XMLElement', $config('#/config/email'));
		$this->assertSame($config('#/config/email')->toString(), 'user@domain.com');
		$this->assertSame($config('/config/email')->length, 1);
	}
	
	/**
	 * Test the `attribute` method without a namespace.
	 */
	public function testAttributeMethodWithoutNS() {
		
		$config = new XMLDocument;
		$config->root('config')->child('email')->attribute('method', 'smtp');
		
		$this->assertInstanceOf('com\mohiva\common\xml\XMLAttribute', $config('#/config/email/attribute::method'));
		$this->assertSame($config('#/config/email/attribute::method')->toString(), 'smtp');
		$this->assertSame($config('/config/email/attribute::method')->length, 1);
	}
	
	/**
	 * Test the `child` method with a namespace.
	 */
	public function testChildMethodWithNS() {
		
		$config = new XMLDocument;
		$config->root('config')->child('test:email', 'user@domain.com', 'http://framework.mohiva.com/test');
		$this->assertInstanceOf('com\mohiva\common\xml\XMLElement', $config('#/config/test:email'));
		$this->assertSame($config('#/config/test:email')->toString(), 'user@domain.com');
		$this->assertSame($config('/config/test:email')->length, 1);
	}
	
	/**
	 * Test the `attribute` method with a namespace.
	 */
	public function testAttributeMethodWithNS() {
		
		$config = new XMLDocument;
		$config->root('config')->child('email')->attribute('test:method', 'smtp', 'http://framework.mohiva.com/test');
		
		$this->assertInstanceOf('com\mohiva\common\xml\XMLAttribute', $config('#/config/email/attribute::test:method'));
		$this->assertSame($config('#/config/email/attribute::test:method')->toString(), 'smtp');
		$this->assertSame($config('/config/email/attribute::test:method')->length, 1);
	}
	
	/**
	 * Test if can remove all nodes within a alement.
	 */
	public function testRemoveChilds() {
		
		$config = new XMLDocument;
		$config->root('config')
			->child('resource')
				->attribute('name', 'SessionResource')
				->attribute('type', 'com\mohiva\common\bootstrap\resources\SessionResource')
			->parentNode->child('resource')
				->attribute('name', 'DatabaseResource')
				->attribute('type', 'com\mohiva\common\bootstrap\resources\DatabaseResource');
				
		$config->documentElement->removeChilds();
		
		$this->assertFalse($config('#/config')->hasChildNodes());
	}
	
	/**
	 * Test the `__invoke` method.
	 */
	public function testInvokeMethod() {
		
		$config = new XMLDocument;
		$config->root('config')->child('email')
			->attribute('method', 'smtp')
			->attribute('default', false);
		
		/** @var $element \com\mohiva\common\xml\XMLElement */
		$element = $config('#/config');
		
		$this->assertInstanceOf('com\mohiva\common\xml\XMLElement', $element);
		$this->assertSame($element('#email/attribute::method')->toString(), 'smtp');
		$this->assertFalse($element('#email/attribute::default')->toBool());
	}
	
	/**
	 * Test if a node value can be converted to boolean.
	 */
	public function testToBool() {
		
		$config = new XMLDocument;
		$config->root('config', true);
		
		$this->assertTrue($config('#/config')->toBool());
	}
	
	/**
	 * Test if a node value can be converted to int.
	 */
	public function testToInt() {
		
		$config = new XMLDocument;
		$config->root('config', 12345);
		
		$this->assertSame($config('#/config')->toInt(), 12345);
	}
	
	/**
	 * Test if a node value can be converted to float.
	 */
	public function testToFloat() {
		
		$config = new XMLDocument;
		$config->root('config', 1.12345);
		
		$this->assertSame($config('#/config')->toFloat(), 1.12345);
	}
	
	/**
	 * Test if a node value can be converted to string.
	 */
	public function testToString() {
		
		$config = new XMLDocument;
		$config->root('config', 'user@domain.com');
		
		$this->assertSame($config('#/config')->toString(), 'user@domain.com');
	}
	
	/**
	 * Test if a node value can be converted to XML.
	 */
	public function testToXML() {
		
		$config = new XMLDocument;
		$config->root('config')->attribute('email', 'user@domain.com');
		
		$this->assertSame($config('#/config')->toXML(), '<config email="user@domain.com"></config>');
	}
	
	/**
	 * Test if can get an attribute with the `ArrayAccess` interface.
	 */
	public function testOffsetGet() {
		
		$config = new XMLDocument();
		$config->root('config')
			->attribute('attr1', true)
			->attribute('attr2', false)
			->attribute('attr3', 'mohiva');
		
		$this->assertTrue($config['attr1']->toBool());
		$this->assertFalse($config['attr2']->toBool());
		$this->assertSame($config['attr3']->toString(), 'mohiva');
	}
	
	/**
	 * Test if can set an attribute with the `ArrayAccess` interface.
	 */
	public function testOffsetSet() {
		
		$config = new XMLDocument();
		$config->root('config');
		$config['attr1'] = true;
		$config['attr2'] = false;
		$config['attr3'] = 'mohiva';
		
		$this->assertTrue($config['attr1']->toBool());
		$this->assertFalse($config['attr2']->toBool());
		$this->assertSame($config['attr3']->toString(), 'mohiva');
	}
	
	/**
	 * Test with the `ArrayAccess` interface if an attribute exists.
	 */
	public function testOffsetExists() {
		
		$config = new XMLDocument();
		$config->root('config');
		$config['attr1'] = true;
		$config['attr2'] = false;
		$config['attr3'] = 'mohiva';
		
		$this->assertTrue(isset($config['attr1']));
		$this->assertTrue(isset($config['attr2']));
		$this->assertTrue(isset($config['attr3']));
		$this->assertFalse(isset($config['attr4']));
	}
	
	/**
	 * Test with the `ArrayAccess` interface if an attribute can be unset.
	 */
	public function testOffsetUnset() {
		
		$config = new XMLDocument();
		$config->root('config');
		$config['attr1'] = true;
		$config['attr2'] = false;
		$config['attr3'] = 'mohiva';
		
		unset($config['attr1']);
		unset($config['attr2']);
		unset($config['attr3']);
		
		$this->assertFalse(isset($config['attr1']));
		$this->assertFalse(isset($config['attr2']));
		$this->assertFalse(isset($config['attr3']));
	}
	
	/**
	 * Test if throws an `XMLException` if the element isn't a child of a `XMLDocument`.
	 */
	public function testIfFailsWhenElementNotChildOfDocument() {
		
		$element = new XMLElement('config');
		
		try {
			$element('.');
			$this->fail('XMLException was expected but never thrown');
		} catch (\Exception $e) {
			$this->assertInstanceOf('com\mohiva\common\xml\exceptions\XMLException', $e);
		}
		
		try {
			$element->child('node');
			$this->fail('XMLException was expected but never thrown');
		} catch (\Exception $e) {
			$this->assertInstanceOf('com\mohiva\common\xml\exceptions\XMLException', $e);
		}
		
		try {
			$element->attribute('node', true);
			$this->fail('XMLException was expected but never thrown');
		} catch (\Exception $e) {
			$this->assertInstanceOf('com\mohiva\common\xml\exceptions\XMLException', $e);
		}
		
		try {
			$element->toXML();
			$this->fail('XMLException was expected but never thrown');
		} catch (\Exception $e) {
			$this->assertInstanceOf('com\mohiva\common\xml\exceptions\XMLException', $e);
		}
	}
}
