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

use com\mohiva\test\common\Bootstrap;
use com\mohiva\common\xml\XMLDocument;
use com\mohiva\common\xml\exceptions\XMLException;

/**
 * Unit test case for the `XMLDocument` class.
 *
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Test
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class XMLDocumentTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Test if can create an empty document with the default version and encoding.
	 */
	public function testCreateDocumentWithDefaultValues() {

		$doc = new XMLDocument;

		$this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>', trim($doc->saveXML()));
	}

	/**
	 * Test if can create an empty document with a specified version and encoding.
	 */
	public function testCreateDocumentWithVersionAndEncoding() {

		$doc = new XMLDocument('1.1', 'ISO-8859-1');
		$this->assertEquals('<?xml version="1.1" encoding="ISO-8859-1"?>', trim($doc->saveXML()));
	}

	/**
	 * Test if the `__invoke` method returns a single item from a node list.
	 */
	public function testInvokeReturnsSingleItem() {

		$doc = new XMLDocument;
		$config = $doc->root('config');
		$config->child('email');

		$element = $doc('#/config/email');

		$this->assertInstanceOf('\DOMNode', $element);
	}

	/**
	 * Test if the `__invoke` method returns `null` if a requested single element does not exists.
	 */
	public function testInvokeReturnNullIfSingleElementDoesNotExists() {

		$doc = new XMLDocument;
		$doc->root('config');
		$element = $doc('#/config/email');

		$this->assertNull($element);
	}

	/**
	 * Test the `__invoke` method returns a node list when sending a normal XPath query.
	 */
	public function testInvokeReturnsNodeList() {

		$doc = new XMLDocument;
		$config = $doc->root('config');
		$config->child('email');
		$element = $doc('/config/email');

		$this->assertInstanceOf('\DOMNodeList', $element);
	}

	/**
	 * Test if can clone an empty document.
	 */
	public function testCloneEmptyDocument() {

		$doc = new XMLDocument();

		$this->assertNotSame($doc, clone $doc);
	}

	/**
	 * Test if the clone of an empty document can be modified after cloning.
	 */
	public function testIfEmptyClonedDocumentCanBeModified() {

		$doc = new XMLDocument();

		$clone = clone $doc;
		$config = $clone->root('config', 'value');

		$this->assertSame('value', $config->toString());
	}

	/**
	 * Test if can clone a not empty document.
	 */
	public function testCloneNotEmptyDocument() {

		$doc = new XMLDocument();
		$doc->root('config')->child('child');

		$this->assertNotSame($doc, clone $doc);
	}

	/**
	 * Test if the clone of a not empty document can be modified after cloning.
	 */
	public function testIfNotEmptyClonedDocumentCanBeModified() {

		$doc = new XMLDocument();
		$doc->root('config')->child('child');

		$clone = clone $doc;
		$placeholder = $clone->createElement('__node__', 'id');
		$node = $clone('#/config/child');
		$node->parentNode->replaceChild($placeholder, $node);

		$this->assertSame('id', $clone('#/config/__node__')->toString());
	}

	/**
	 * Test if can serialize/unserialize a not empty XML document.
	 */
	public function testSerializableInterfaceForNotEmptyDocument() {

		$doc = new XMLDocument();
		$doc->load(Bootstrap::$resourceDir . '/common/xml/config.xml');

		/* @var XMLDocument $unserialized */
		$serialized = serialize($doc);
		$unserialized = unserialize($serialized);

		$this->assertXmlStringEqualsXmlString($doc->saveXML(), $unserialized->saveXML());
	}

	/**
	 * Test if can serialize/unserialize an empty XML document.
	 */
	public function testSerializableInterfaceForEmptyDocument() {

		$doc = new XMLDocument();
		$serialized = serialize($doc);

		/* @var XMLDocument $unserialized */
		$unserialized = unserialize($serialized);
		$this->assertEquals($doc->saveXML(), $unserialized->saveXML());
	}

	/**
	 * Test if can load a XML file.
	 */
	public function testLoadXMLFile() {

		try {
			$doc = new XMLDocument();
			$doc->load(Bootstrap::$resourceDir . '/common/xml/config.xml');
		} catch (\Exception $e) {
			$this->fail($e->getMessage());
		}

		$this->assertInstanceOf('com\mohiva\common\xml\XMLDocument', $doc);
	}

	/**
	 * Test if can load XML from a string.
	 */
	public function testLoadXMLFromString() {

		try {
			$doc = new XMLDocument();
			$doc->loadXML('<config><node></node></config>');
		} catch (\Exception $e) {
			$this->fail($e->getMessage());
		}

		$this->assertInstanceOf('com\mohiva\common\xml\XMLDocument', $doc);
	}

	/**
	 * Test if default encoding is UTF-8 if the XML file has no encoding defined.
	 */
	public function testDefaultEncodingFromFile() {

		$doc = new XMLDocument();
		$doc->load(Bootstrap::$resourceDir . '/common/xml/no_encoding.xml');

		$this->assertSame(XMLDocument::XML_ENCODING, $doc->xmlEncoding);
	}

	/**
	 * Test if default encoding is UTF-8 if the XML string has no encoding defined.
	 */
	public function testDefaultEncodingFromString() {

		$doc = new XMLDocument();
		$doc->loadXML('<config><node></node></config>');

		$this->assertSame(XMLDocument::XML_ENCODING, $doc->xmlEncoding);
	}

	/**
	 * Test if fails on non existing file.
	 *
	 * @expectedException \com\mohiva\common\io\exceptions\IOException
	 */
	public function testIfFailsOnNonExistingFile() {

		$doc = new XMLDocument();
		$doc->load('/not/existing.xml');
	}

	/**
	 * Test if fails on invalid XML string.
	 *
	 * @expectedException \com\mohiva\common\xml\exceptions\XMLException
	 */
	public function testIfFailsOnInvalidString() {

		$doc = new XMLDocument();
		$doc->loadXML('<config><config>');
	}

	/**
	 * Test if the `$preserveWhiteSpace` property will be set to true if the `$fixLineNumbers`
	 * property is set to true.
	 */
	public function testFixLineNumbersSetsPreserveWhiteSpaceToTrue() {

		$doc = new XMLDocument();
		$doc->preserveWhiteSpace = false;
		$doc->fixLineNumbers = true;
		$doc->load(Bootstrap::$resourceDir . '/common/xml/config.xml');

		$this->assertTrue($doc->preserveWhiteSpace);
	}

	/**
	 * Test if the `$preserveWhiteSpace` property is not affected when setting the `$fixLineNumbers`
	 * property to false.
	 */
	public function testPreserveWhiteSpaceIsNotAffectedIfFixLineNumbersIsSetToFalse() {

		$doc = new XMLDocument();
		$doc->preserveWhiteSpace = false;
		$doc->fixLineNumbers = false;
		$doc->load(Bootstrap::$resourceDir . '/common/xml/config.xml');

		$this->assertFalse($doc->preserveWhiteSpace);
	}

	/**
	 * Test if the line numbers will be fixed if the `$fixLineNumbers` property is set to `true`.
	 */
	public function testFixLineNumbersIfPropertyIsSetToTrue() {

		$config = new XMLDocument();
		$config->preserveWhiteSpace = false;
		$config->fixLineNumbers = true;
		$config->load(Bootstrap::$resourceDir . '/common/xml/line_no.xml');

		$found = [];
		$expected = [2, 2, 4, 7, 9, 9, 11, 11, 13, 13, 15, 15, 17, 25, 29, 29];
		$nodes = $config->xpath->query('//node()');
		foreach ($nodes as $node) {
			/* @var \DOMNode $node */
			$found[] = $node->getLineNo();
		}

		$this->assertEquals($expected, $found);
	}

	/**
	 * Test if the line numbers were not be fixed if the `$fixLineNumbers` property is set to `false`.
	 */
	public function testDoesNotFixLineNumbersIfPropertyIsNotSet() {

		$config = new XMLDocument();
		$config->preserveWhiteSpace = true;
		$config->fixLineNumbers = false;
		$config->load(Bootstrap::$resourceDir . '/common/xml/line_no.xml');

		$found = [];
		$expected = [2, 2, 4, 7, 9, 9, 11, 11, 13, 13, 15, 15, 17, 25, 29, 29];
		$nodes = $config->xpath->query('//node()');
		foreach ($nodes as $node) {
			/* @var \DOMNode $node */
			$found[] = $node->getLineNo();
		}

		$this->assertNotEquals($expected, $found);
	}

	/**
	 * Test if the `root` method throws an `XMLException` if trying to set the root node twice.
	 *
	 * @expectedException \com\mohiva\common\xml\exceptions\XMLException
	 */
	public function testRootThrowsXMLExceptionIfRootNodeAlreadyExists() {

		$doc = new XMLDocument();
		$doc->root('first');
		$doc->root('second');
	}

	/**
	 * Test if the `root` method can create an element without a namespace.
	 */
	public function testRootMethodCreatesElementWithoutNs() {

		$doc = new XMLDocument;
		$doc->root('config', 'mohiva');

		$this->assertSame($doc('#/config')->toString(), 'mohiva');
	}

	/**
	 * Test if the `root` method can create an element with a namespace.
	 */
	public function testRootMethodCreatesElementWithNs() {

		$doc = new XMLDocument;
		$doc->root('test:config', 'mohiva', 'http://framework.mohiva.com/test');

		$this->assertSame($doc('#/test:config')->toString(), 'mohiva');
	}

	/**
	 * Test if the `root` method can set a boolean `true` as element value.
	 */
	public function testRootMethodCanHandleBooleanTrue() {

		$doc = new XMLDocument;
		$doc->root('config', true);

		$this->assertTrue($doc('#/config')->toBool());
	}

	/**
	 * Test if the `root` method can set a boolean `false` as element value.
	 */
	public function testRootMethodCanHandleBooleanFalse() {

		$doc = new XMLDocument;
		$doc->root('config', false);

		$this->assertFalse($doc('#/config')->toBool());
	}

	/**
	 * Test if the `attribute` method throws an `XMLException` if the `documentElement`
	 * property is null.
	 *
	 * @expectedException \com\mohiva\common\xml\exceptions\XMLException
	 */
	public function testAttributeMethodThrowsXMLExceptionIfDocumentElementDoesNotExists() {

		(new XMLDocument())->attribute('node', true);
	}

	/**
	 * Test if the `attribute` method can create an attribute without a namespace.
	 */
	public function testAttributeMethodCreatesAttributeWithoutNs() {

		$doc = new XMLDocument;
		$doc->root('config');
		$doc->attribute('method', 'smtp');

		$this->assertSame('smtp', $doc('#/config/@method')->toString());
	}

	/**
	 * Test if the `attribute` method can create an attribute with a namespace.
	 */
	public function testAttributeMethodCreatesAttributeWithNs() {

		$doc = new XMLDocument;
		$doc->root('config');
		$doc->attribute('test:method', 'smtp', 'http://elixir.mohiva.com/test');

		$this->assertSame('smtp', $doc('#/config/attribute::test:method')->toString());
	}

	/**
	 * Test if the `attribute` method can set a boolean `true` as attribute value.
	 */
	public function testAttributeMethodCanHandleBooleanTrue() {

		$doc = new XMLDocument;
		$doc->root('config');
		$doc->attribute('value', true);

		$this->assertTrue($doc('#/config/@value')->toBool());
	}

	/**
	 * Test if the `attribute` method can set a boolean `false` as attribute value.
	 */
	public function testAttributeMethodCanHandleBooleanFalse() {

		$doc = new XMLDocument;
		$doc->root('config');
		$doc->attribute('value', false);

		$this->assertFalse($doc('#/config/@value')->toBool());
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

		$doc = new XMLDocument();
		$doc->load(Bootstrap::$resourceDir . '/common/xml/config.xml');

		$this->assertSame($namespaces, $doc->getNamespaces());
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

		$doc = new XMLDocument();
		$doc->load(Bootstrap::$resourceDir . '/common/xml/config.xml');
		$doc->xinclude();

		$this->assertSame($namespaces, $doc->getNamespaces());
	}

	/**
	 * Test if the `getNamespace` method omits a list of namespaces.
	 */
	public function testGetNamespacesOmitsNamespaces() {

		$namespaces = array(
			'xml' => 'http://www.w3.org/XML/1998/namespace',
			'xi' => 'http://www.w3.org/2001/XInclude',
			'test' => 'urn:com.mohiva.test.framework.xml.files.helpers',
			'mx' => 'urn:com.mohiva.framework.xml.helpers'
		);

		$doc = new XMLDocument();
		$doc->load(Bootstrap::$resourceDir . '/common/xml/config.xml');

		$this->assertSame(array(), $doc->getNamespaces(array_values($namespaces)));
	}

	/**
	 * Test if the `removeComments` remove all comments inside the document.
	 */
	public function testRemoveComments() {

		$doc = new XMLDocument();
		$doc->load(Bootstrap::$resourceDir . '/common/xml/config.xml');
		$doc->removeComments();

		$this->assertEquals(0, $doc('.//comment()')->length);
	}

	/**
	 * Test if the `offsetGet` method throws an `XMLException` if the `documentElement`
	 * property is null.
	 *
	 * @expectedException \com\mohiva\common\xml\exceptions\XMLException
	 */
	public function testOffsetGetThrowsXMLExceptionIfDocumentElementDoesNotExists() {

		$doc = new XMLDocument;
		$doc['test'];
	}

	/**
	 * Test if the `offsetGet` method returns `null` if an attribute does not exists.
	 */
	public function testOffsetGetReturnsNullIfAttributeDoesNotExists() {

		$doc = new XMLDocument;
		$doc->root('config');

		$this->assertNull($doc['not_existing']);
	}

	/**
	 * Test if the `offsetGet` method returns an attribute value.
	 */
	public function testOffsetGetReturnsAttributeValue() {

		$doc = new XMLDocument;
		$doc->root('config');
		$doc->attribute('attr', 'value');

		$this->assertSame('value', $doc['attr']->toString());
	}

	/**
	 * Test if the `offsetSet` method throws an `XMLException` if the `documentElement`
	 * property is null.
	 *
	 * @expectedException \com\mohiva\common\xml\exceptions\XMLException
	 */
	public function testOffsetSetThrowsXMLExceptionIfDocumentElementDoesNotExists() {

		$doc = new XMLDocument;
		$doc['test'] = 'value';
	}

	/**
	 * Test if the `offsetSet` method can set an attribute.
	 */
	public function testOffsetSetCanSetAttribute() {

		$doc = new XMLDocument;
		$doc->root('config');
		$doc['attr'] = 'test';

		$this->assertSame('test', $doc['attr']->toString());
	}

	/**
	 * Test if the `offsetSet` method can set a boolean `true` as attribute value.
	 */
	public function testOffsetSetCanHandleBooleanTrue() {

		$doc = new XMLDocument;
		$doc->root('config');
		$doc['attr'] = true;

		$this->assertTrue($doc['attr']->toBool());
	}

	/**
	 * Test if the `offsetSet` method can set a boolean `false` as attribute value.
	 */
	public function testOffsetSetCanHandleBooleanFalse() {

		$doc = new XMLDocument;
		$doc->root('config');
		$doc['attr'] = false;

		$this->assertFalse($doc['attr']->toBool());
	}

	/**
	 * Test if the `offsetExists` method throws an `XMLException` if the `documentElement`
	 * property is null.
	 *
	 * @expectedException \com\mohiva\common\xml\exceptions\XMLException
	 */
	public function testOffsetExistsThrowsXMLExceptionIfDocumentElementDoesNotExists() {

		$doc = new XMLDocument;
		/** @noinspection PhpExpressionResultUnusedInspection */
		isset($doc['test']);
	}

	/**
	 * Test if the `offsetExists` method returns `true` if an attribute exists.
	 */
	public function testOffsetExistsReturnsTrueIfAttributeExists() {

		$doc = new XMLDocument;
		$doc->root('config');
		$doc['attr'] = true;

		$this->assertTrue(isset($doc['attr']));
	}

	/**
	 * Test if the `offsetExists` method returns `false` if an attribute does not exists.
	 */
	public function testOffsetExistsReturnsFalseIfAttributeDoesNotExists() {

		$doc = new XMLDocument;
		$doc->root('config');

		$this->assertFalse(isset($doc['attr']));
	}

	/**
	 * Test if the `offsetUnset` method throws an `XMLException` if the `documentElement`
	 * property is null.
	 *
	 * @expectedException \com\mohiva\common\xml\exceptions\XMLException
	 */
	public function testOffsetUnsetThrowsXMLExceptionIfDocumentElementDoesNotExists() {

		$doc = new XMLDocument;
		unset($doc['test']);
	}

	/**
	 * Test with the `ArrayAccess` interface if an attribute can be unset.
	 */
	public function testOffsetUnset() {

		$doc = new XMLDocument;
		$doc->root('config');
		$doc['attr'] = true;

		unset($doc['attr']);

		$this->assertFalse(isset($doc['attr']));
	}
}
