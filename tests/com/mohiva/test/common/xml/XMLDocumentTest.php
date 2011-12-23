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
use com\mohiva\common\xml\exceptions\XMLException;

/**
 * Unit test case for the `XMLDocument` class.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Test
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2011 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class XMLDocumentTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * Test if can load a XML file.
	 */
	public function testLoadXMLFile() {
		
		try {
			$config = new XMLDocument();
			$config->load(Bootstrap::$resourceDir . '/common/xml/config.xml');
		} catch (\Exception $e) {
			$this->fail($e->getMessage());
		}
		
		$this->assertInstanceOf('com\mohiva\common\xml\XMLDocument', $config);
	}
	
	/**
	 * Test if can load XML from a string.
	 */
	public function testLoadXMLFromString() {
		
		try {
			$config = new XMLDocument();
			$config->loadXML('<config><node></node></config>');
		} catch (\Exception $e) {
			$this->fail($e->getMessage());
		}
		
		$this->assertInstanceOf('com\mohiva\common\xml\XMLDocument', $config);
	}
	
	/**
	 * Test if default encoding is UTF-8 if the XML file has no encoding defined.
	 */
	public function testDefaultEncodingFromFile() {
		
		$config = new XMLDocument();
		$config->load(Bootstrap::$resourceDir . '/common/xml/no_encoding.xml');
		
		$this->assertSame(XMLDocument::XML_ENCODING, $config->xmlEncoding);
	}
	
	/**
	 * Test if default encoding is UTF-8 if the XML string has no encoding defined.
	 */
	public function testDefaultEncodingFromString() {
		
		$config = new XMLDocument();
		$config->loadXML('<config><node></node></config>');
		
		$this->assertSame(XMLDocument::XML_ENCODING, $config->xmlEncoding);
	}
	
	/**
	 * Test if fails on non existing file.
	 * 
	 * @expectedException \com\mohiva\common\io\exceptions\IOException
	 */
	public function testIfFailsOnNonExistingFile() {
		
		$config = new XMLDocument();
		$config->load('/not/existing.xml');
	}
	
	/**
	 * Test if fails on invalid XML string.
	 * 
	 * @expectedException \com\mohiva\common\xml\exceptions\XMLException
	 */
	public function testIfFailsOnInvalidString() {
		
		$config = new XMLDocument();
		$config->loadXML('<config><config>');
	}
	
	/**
	 * Test if throws an `XMLException` if setting the root node twice.
	 * 
	 * @expectedException \com\mohiva\common\xml\exceptions\XMLException
	 */
	public function testIfFailsOnSettingRootNodeTwice() {
		
		$config = new XMLDocument();
		$config->root('first');
		$config->root('second');
	}
	
	/**
	 * Test if throws an `XMLException` if try to operate on a non existing root node.
	 */
	public function testIfFailsOnNonExistingRootNode() {
		
		$config = new XMLDocument();
		
		try {
			$config->attribute('test', 1);
			$this->fail('XMLException was expected but never thrown');
		} catch (\Exception $e) {
			$this->assertInstanceOf('\com\mohiva\common\xml\exceptions\XMLException', $e);
		}
		
		try {
			$config['test'];
			$this->fail('XMLException was expected but never thrown');
		} catch (\Exception $e) {
			$this->assertInstanceOf('\com\mohiva\common\xml\exceptions\XMLException', $e);
		}
		
		try {
			$config['test'] = 1;
			$this->fail('XMLException was expected but never thrown');
		} catch (\Exception $e) {
			$this->assertInstanceOf('\com\mohiva\common\xml\exceptions\XMLException', $e);
		}
		
		try {
			isset($config['test']);
			$this->fail('XMLException was expected but never thrown');
		} catch (\Exception $e) {
			$this->assertInstanceOf('\com\mohiva\common\xml\exceptions\XMLException', $e);
		}
		
		try {
			unset($config['test']);
			$this->fail('XMLException was expected but never thrown');
		} catch (\Exception $e) {
			$this->assertInstanceOf('\com\mohiva\common\xml\exceptions\XMLException', $e);
		}
	}
	
	/**
	 * Test to create a document.
	 */
	public function testCreateDocument() {
		
		$config = new XMLDocument;
		$this->assertEquals(trim($config->saveXML()), '<?xml version="1.0" encoding="UTF-8"?>');
		
		$config = new XMLDocument('1.1', 'ISO-8859-1');
		$this->assertEquals(trim($config->saveXML()), '<?xml version="1.1" encoding="ISO-8859-1"?>');
	}
	
	/**
	 * Test the `root` method without a namespace.
	 */
	public function testRootMethodWithoutNS() {
		
		$config = new XMLDocument;
		$config->root('config', 'mohiva');
		$this->assertInstanceOf('com\mohiva\common\xml\XMLElement', $config('#/config'));
		$this->assertSame($config('#/config')->toString(), 'mohiva');
		$this->assertSame($config('/config')->length, 1);
	}
	
	/**
	 * Test the `attribute` method without a namespace.
	 */
	public function testAttributeMethodWithoutNS() {
		
		$config = new XMLDocument;
		$config->root('config');
		$config->attribute('number', 1000);
		
		$this->assertInstanceOf('com\mohiva\common\xml\XMLAttribute', $config('#/config/attribute::number'));
		$this->assertSame($config('#/config/attribute::number')->toInt(), 1000);
		$this->assertSame($config('/config/attribute::number')->length, 1);
	}
	
	/**
	 * Test the `root` method with a namespace.
	 */
	public function testRootMethodWithNS() {
		
		$config = new XMLDocument;
		$config->root('test:config', 'mohiva', 'http://framework.mohiva.com/test');
		$this->assertInstanceOf('com\mohiva\common\xml\XMLElement', $config('#/test:config'));
		$this->assertSame($config('#/test:config')->toString(), 'mohiva');
		$this->assertSame($config('/test:config')->length, 1);
	}
	
	/**
	 * Test the `attribute` method with a namespace.
	 */
	public function testAttributeMethodWithNS() {
		
		$config = new XMLDocument;
		$config->root('config');
		$config->attribute('test:number', 1000, 'http://framework.mohiva.com/test');
		
		$this->assertInstanceOf('com\mohiva\common\xml\XMLAttribute', $config('#/config/attribute::test:number'));
		$this->assertSame($config('#/config/attribute::test:number')->toInt(), 1000);
		$this->assertSame($config('/config/attribute::test:number')->length, 1);
	}
	
	/**
	 * Test the `__invoke` method.
	 */
	public function testInvokeMethod() {
		
		$config = new XMLDocument;
		$config->root('config');
		$config->attribute('test1', true);
		$config->attribute('test2', false);
		
		$this->assertInstanceOf('com\mohiva\common\xml\XMLElement', $config('#/config'));
		$this->assertTrue($config('#//config/attribute::test1')->toBool());
		$this->assertFalse($config('#//config/attribute::test2')->toBool());
	}
	
	/**
	 * Test if can get all namespaces defined in a document.
	 */
	public function testGetNamespacesWithoutXinclude() {
		
		$namespaces = array(
			'xml' => 'http://www.w3.org/XML/1998/namespace',
			'xi' => 'http://www.w3.org/2001/XInclude',
			'test' => 'urn:com.mohiva.test.framework.xml.files.helpers',
			'mx' => 'urn:com.mohiva.framework.xml.helpers'
		);
		
		$config = new XMLDocument();
		$config->load(Bootstrap::$resourceDir . '/common/xml/config.xml');
		$this->assertSame($namespaces, $config->getNamespaces());
		$this->assertSame(array(), $config->getNamespaces(array_values($namespaces)));
	}
	
	/**
	 * Test if can get all namespaces defined in a document, including all namespaces from a included file.
	 */
	public function testGetNamespacesWithXinclude() {
		
		$namespaces = array(
			'xml' => 'http://www.w3.org/XML/1998/namespace',
			'xi' => 'http://www.w3.org/2001/XInclude',
			'test' => 'urn:com.mohiva.test.framework.xml.files.helpers',
			'mx' => 'urn:com.mohiva.framework.xml.helpers',
			'custom' => 'urn:com.mohiva.www.xml.files.helpers'
		);
		
		$config = new XMLDocument();
		$config->load(Bootstrap::$resourceDir . '/common/xml/config.xml');
		$config->xinclude();
		$this->assertSame($namespaces, $config->getNamespaces());
		$this->assertSame(array(), $config->getNamespaces(array_values($namespaces)));
	}
	
	/**
	 * Test if can get an attribute with the `ArrayAccess` interface.
	 */
	public function testOffsetGet() {
		
		$config = new XMLDocument();
		$config->root('config');
		$config->attribute('attr1', true);
		$config->attribute('attr2', false);
		$config->attribute('attr3', 'mohiva');
		
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
	 * Test if can serialize an XML document.
	 */
	public function testSerializing() {
		
		$config = new XMLDocument();
		$config->load(Bootstrap::$resourceDir . '/common/xml/config.xml');
		
		$serialized = serialize($config);
		$unserialized = unserialize($serialized);
		
		$this->assertXmlStringEqualsXmlString($config->saveXML(), $unserialized->saveXML());
	}
	
	/**
	 * Test if can serialize an empty XML document.
	 */
	public function testSerializingEmptyDocument() {
		
		$empty = new XMLDocument();
		$serialized = serialize($empty);
		
		$unserialized = unserialize($serialized);
		$this->assertEquals($empty->saveXML(), $unserialized->saveXML());
	}
	
	/**
	 * Test if all comments will be removed.
	 */
	public function testRemoveComments() {
		
		$config = new XMLDocument();
		$config->load(Bootstrap::$resourceDir . '/common/xml/config.xml');
		$config->removeComments();
		
		$this->assertEquals(0, $config('.//comment()')->length);
	}
	
	/**
	 * Test if can clone an empty document.
	 */
	public function testCloneEmptyDocument() {
		
		$config = new XMLDocument();
		
		$config1 = clone $config;
		$config1->root('config');
		
		$config2 = clone $config;
		$config2->root('config');
	}
	
	/**
	 * Test if can clone an document.
	 */
	public function testCloneDocument() {
		
		$config = new XMLDocument();
		$config->root('config')->child('child');
		
		$clone = clone $config;
		$placeholder = $clone->createElement('__node__', 'id');
		$node = $clone('#/config/child');
		$node->parentNode->replaceChild($placeholder, $node);
		
		$clone = clone $config;
		$placeholder = $clone->createElement('__node__', 'id');
		$node = $clone('#/config/child');
		$node->parentNode->replaceChild($placeholder, $node);
	}
}
