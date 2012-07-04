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

use Exception;
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
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class XMLElementTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Test if the `__invoke` method throws an `XMLException` if the `ownerDocument`
	 * property is null.
	 *
	 * @expectedException \com\mohiva\common\xml\exceptions\XMLException
	 */
	public function testInvokeThrowsXMLExceptionIfOwnerDocumentDoesNotExists() {

		$element = new XMLElement('config');
		$element('.');
	}

	/**
	 * Test if the `__invoke` method returns a single item from a node list.
	 */
	public function testInvokeReturnsSingleItem() {

		$doc = new XMLDocument;
		$config = $doc->root('config');
		$config->child('email');

		$element = $config('#email');

		$this->assertInstanceOf('\DOMNode', $element);
	}

	/**
	 * Test if the `__invoke` method returns `null` if a requested single element does not exists.
	 */
	public function testInvokeReturnNullIfSingleElementDoesNotExists() {

		$doc = new XMLDocument;
		$config = $doc->root('config');
		$element = $config('#email');

		$this->assertNull( $element);
	}

	/**
	 * Test the `__invoke` method returns a node list when sending a normal XPath query.
	 */
	public function testInvokeReturnsNodeList() {

		$doc = new XMLDocument;
		$config = $doc->root('config');
		$config->child('email');
		$element = $config('email');

		$this->assertInstanceOf('\DOMNodeList', $element);
	}

	/**
	 * Test if the `__toString` method returns a node value as string.
	 */
	public function testMagicToString() {

		$doc = new XMLDocument;
		$config = $doc->root('config');
		$config->child('email', 'user@domain.com');

		$this->assertSame((string) $config('#email'), 'user@domain.com');
	}

	/**
	 * Test if the `child` method throws an `XMLException` if the `ownerDocument`
	 * property is null.
	 *
	 * @expectedException \com\mohiva\common\xml\exceptions\XMLException
	 */
	public function testChildMethodThrowsXMLExceptionIfOwnerDocumentDoesNotExists() {

		(new XMLElement('config'))->child('node');
	}

	/**
	 * Test if the `child` method can create an element without a namespace.
	 */
	public function testChildMethodCreatesElementWithoutNs() {

		$doc = new XMLDocument;
		$doc->root('config')->child('email', 'user@domain.com');

		$this->assertSame('user@domain.com', $doc('#/config/email')->toString());
	}

	/**
	 * Test if the `child` method can create an element with a namespace.
	 */
	public function testChildMethodCreatesElementWithNs() {

		$doc = new XMLDocument;
		$doc->root('config')->child('test:email', 'user@domain.com', 'http://elixir.mohiva.com/test');

		$this->assertSame('user@domain.com', $doc('#/config/test:email')->toString());
	}

	/**
	 * Test if the `child` method can set a boolean `true` as element value.
	 */
	public function testChildMethodCanHandleBooleanTrue() {

		$doc = new XMLDocument;
		$doc->root('config')->child('value', true);

		$this->assertTrue($doc('#/config/value')->toBool());
	}

	/**
	 * Test if the `child` method can set a boolean `false` as element value.
	 */
	public function testChildMethodCanHandleBooleanFalse() {

		$doc = new XMLDocument;
		$doc->root('config')->child('value', false);

		$this->assertFalse($doc('#/config/value')->toBool());
	}

	/**
	 * Test if the `attribute` method throws an `XMLException` if the `ownerDocument`
	 * property is null.
	 *
	 * @expectedException \com\mohiva\common\xml\exceptions\XMLException
	 */
	public function testAttributeMethodThrowsXMLExceptionIfOwnerDocumentDoesNotExists() {

		(new XMLElement('config'))->attribute('node', true);
	}

	/**
	 * Test if the `attribute` method can create an attribute without a namespace.
	 */
	public function testAttributeMethodCreatesAttributeWithoutNs() {

		$doc = new XMLDocument;
		$doc->root('config')->child('email')->attribute('method', 'smtp');

		$this->assertSame('smtp', $doc('#/config/email/@method')->toString());
	}

	/**
	 * Test if the `attribute` method can create an attribute with a namespace.
	 */
	public function testAttributeMethodCreatesAttributeWithNs() {

		$doc = new XMLDocument;
		$doc->root('config')->child('email')->attribute('test:method', 'smtp', 'http://elixir.mohiva.com/test');

		$this->assertSame('smtp', $doc('#/config/email/attribute::test:method')->toString());
	}

	/**
	 * Test if the `attribute` method can set a boolean `true` as attribute value.
	 */
	public function testAttributeMethodCanHandleBooleanTrue() {

		$doc = new XMLDocument;
		$doc->root('config')->attribute('value', true);

		$this->assertTrue($doc('#/config/@value')->toBool());
	}

	/**
	 * Test if the `attribute` method can set a boolean `false` as attribute value.
	 */
	public function testAttributeMethodCanHandleBooleanFalse() {

		$doc = new XMLDocument;
		$doc->root('config')->attribute('value', false);

		$this->assertFalse($doc('#/config/@value')->toBool());
	}

	/**
	 * Test if can remove all nodes within a element.
	 */
	public function testRemoveChildren() {

		$doc = new XMLDocument;
		$doc->root('config')
			->child('resource')
				->attribute('name', 'SessionResource')
				->attribute('type', 'com\mohiva\common\bootstrap\resources\SessionResource')
			->parentNode->child('resource')
				->attribute('name', 'DatabaseResource')
				->attribute('type', 'com\mohiva\common\bootstrap\resources\DatabaseResource');

		$doc->documentElement->removeChildren();

		$this->assertFalse($doc->documentElement->hasChildNodes());
	}

	/**
	 * Test if the `toBool` method returns a boolean `true` if the attribute contains the string representation
	 * of a boolean `true`.
	 */
	public function testToBoolReturnsTrueOnBooleanTrue() {

		$doc = new XMLDocument;
		$doc->root('config', 'true');

		$this->assertTrue($doc('#/config')->toBool());
	}

	/**
	 * Test if the `toBool` method returns a boolean `false` if the attribute contains the string representation
	 * of a boolean `false`.
	 */
	public function testToBoolReturnsFalseOnBooleanFalse() {

		$doc = new XMLDocument;
		$doc->root('config', 'false');

		$this->assertFalse($doc('#/config')->toBool());
	}

	/**
	 * Test if the `toBool` method returns the boolean value for a string.
	 */
	public function testToBoolReturnsBooleanValueForString() {

		$doc = new XMLDocument;
		$doc->root('config', 'test');

		$this->assertTrue($doc('#/config')->toBool());
	}

	/**
	 * Test if the `toInt` method returns a node value as int.
	 */
	public function testToIntReturnsIntegerValue() {

		$doc = new XMLDocument;
		$doc->root('config', '12345');

		$this->assertSame(12345, $doc('#/config')->toInt());
	}

	/**
	 * Test if the `toFloat` method returns a node value as float.
	 */
	public function testToFloatReturnsFloatingPointValue() {

		$doc = new XMLDocument;
		$doc->root('config', '1.12345');

		$this->assertSame(1.12345, $doc('#/config')->toFloat());
	}

	/**
	 * Test if the `toString` method returns a node value as string.
	 */
	public function testToStringReturnsStringValue() {

		$doc = new XMLDocument;
		$doc->root('config', 'user@domain.com');

		$this->assertSame('user@domain.com', $doc('#/config')->toString());
	}

	/**
	 * Test if the `toXML` method throws an `XMLException` if the `ownerDocument`
	 * property is null.
	 *
	 * @expectedException \com\mohiva\common\xml\exceptions\XMLException
	 */
	public function testToXmlThrowsXMLException() {

		(new XMLElement('config'))->toXML();
	}

	/**
	 * Test if the `toXML` method returns a node value as XML string.
	 */
	public function testToXMLReturnsXMLString() {

		$doc = new XMLDocument;
		$doc->root('config', 'test')->attribute('email', 'user@domain.com');

		$this->assertSame('<config email="user@domain.com">test</config>', $doc('#/config')->toXML());
	}

	/**
	 * Test if the `offsetGet` method returns `null` if an attribute does not exists.
	 */
	public function testOffsetGetReturnsNullIfAttributeDoesNotExists() {

		$doc = new XMLDocument;
		$config = $doc->root('config');

		$this->assertNull($config['not_existing']);
	}

	/**
	 * Test if the `offsetGet` method returns an attribute value.
	 */
	public function testOffsetGetReturnsAttributeValue() {

		$doc = new XMLDocument;
		$config = $doc->root('config');
		$config->attribute('attr', 'value');

		$this->assertSame('value', $config['attr']->toString());
	}

	/**
	 * Test if the `offsetSet` method can set an attribute.
	 */
	public function testOffsetSetCanSetAttribute() {

		$doc = new XMLDocument;
		$config = $doc->root('config');
		$config['attr'] = 'test';

		$this->assertSame('test', $config['attr']->toString());
	}

	/**
	 * Test if the `offsetSet` method can set a boolean `true` as attribute value.
	 */
	public function testOffsetSetCanHandleBooleanTrue() {

		$doc = new XMLDocument;
		$config = $doc->root('config');
		$config['attr'] = true;

		$this->assertTrue($config['attr']->toBool());
	}

	/**
	 * Test if the `offsetSet` method can set a boolean `false` as attribute value.
	 */
	public function testOffsetSetCanHandleBooleanFalse() {

		$doc = new XMLDocument;
		$config = $doc->root('config');
		$config['attr'] = false;

		$this->assertFalse($config['attr']->toBool());
	}

	/**
	 * Test if the `offsetExists` method returns `true` if an attribute exists.
	 */
	public function testOffsetExistsReturnsTrueIfAttributeExists() {

		$doc = new XMLDocument;
		$config = $doc->root('config');
		$config['attr'] = true;

		$this->assertTrue(isset($config['attr']));
	}

	/**
	 * Test if the `offsetExists` method returns `false` if an attribute does not exists.
	 */
	public function testOffsetExistsReturnsFalseIfAttributeDoesNotExists() {

		$doc = new XMLDocument;
		$config = $doc->root('config');

		$this->assertFalse(isset($config['attr']));
	}

	/**
	 * Test with the `ArrayAccess` interface if an attribute can be unset.
	 */
	public function testOffsetUnset() {

		$doc = new XMLDocument;
		$config = $doc->root('config');
		$config['attr'] = true;

		unset($config['attr']);

		$this->assertFalse(isset($config['attr']));
	}
}
