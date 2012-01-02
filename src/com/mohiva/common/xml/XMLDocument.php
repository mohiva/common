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
 * @package   Mohiva/Common/XML
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
namespace com\mohiva\common\xml;

use com\mohiva\common\io\exceptions\IOException;
use com\mohiva\common\xml\exceptions\XMLException;

/**
 * Provides an SimpleXML like api on top of the DOMDocument implementation.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/XML
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class XMLDocument extends \DOMDocument implements \ArrayAccess, \Serializable {
	
	/**
	 * Default xml version.
	 *
	 * @var string
	 */
	const XML_VERSION = '1.0';
	
	/**
	 * Default xml encoding.
	 *
	 * @var string
	 */
	const XML_ENCODING = 'UTF-8';
	
	/**
	 * The xpath object for this document.
	 *
	 * @var \DOMXpath
	 */
	public $xpath = null;
	
	/**
	 * Bitwise OR of the libxml option constants used for this document.
	 * 
	 * @var int
	 */
	protected $options = 0;
	
	/**
	 * The class constructor.
	 *
	 * @param string $version The version number of the document as part of the XML declaration.
	 * @param string $encoding The encoding of the document as part of the XML declaration.
	 */
	public function __construct($version = self::XML_VERSION, $encoding = self::XML_ENCODING) {
		
		parent::__construct($version, $encoding);
		
		$this->registerNodeClass('DOMElement', __NAMESPACE__ . '\XMLElement');
		$this->registerNodeClass('DOMAttr', __NAMESPACE__ . '\XMLAttribute');
		
		$this->setupXPath();
	}
	
	/**
	 * Execute a xpath query.
	 * 
	 * As default behavior this method returns always a node list. But if the query is 
	 * prefixed with the # character then return the first node of the result
	 * or null if no result exists.
	 *
	 * @param string $query The query string to process.
	 * @return \DOMNodeList | XMLElement | XMLAttribute | null The result of the xpath query.
	 */
	public function __invoke($query) {
		
		if ($query[0] === '#') {
			$query = substr($query, 1);
			$node = $this->xpath->query($query);
			if ($node->length) {
				return $node->item(0);
			}
			
			return null;
		}
		
		return $this->xpath->query($query);
	}
	
	/**
	 * Clone the document.
	 */
	public function __clone() {
		
		if (!$this->documentElement) {
			return;
		}
		
		$this->loadXML($this->saveXml(), $this->options);
	}
	
	/**
	 * Return a string representation of this class.
	 * 
	 * @return string A string containing the XML content of this class.
	 */
	public function serialize() {
		
		$data['encoding'] = $this->encoding;
		$data['version'] = $this->version;
		$data['options'] = $this->options;
		$data['xml'] = $this->documentElement ? $this->saveXML() : null;
			
		return serialize($data);
	}
	
	/**
	 * Unserialize this class.
	 * 
	 * @param string $serialized A string containing serialized data.
	 */
	public function unserialize($serialized) {
		
		$data = unserialize($serialized);
		
		$this->__construct($data['version'], $data['encoding']);
		
		if ($data['xml'] !== null) $this->loadXML($data['xml'], $data['options']);
	}
	
	/**
	 * Load the given xml file.
	 * 
	 * @param string $file Path to the XML file to load.
	 * @param int $options Bitwise OR of the libxml option constants.
	 * @throws IOException if the file cannot be loaded.
	 */
	public function load($file, $options = 0) {
		
		$this->options = $options ? $options :
			LIBXML_COMPACT |
			LIBXML_DTDATTR |
			LIBXML_NONET;
		
		try {
			parent::load($file, $this->options);
		} catch (\Exception $e) {
			throw new IOException("Cannot load xml file `{$file}`", null, $e);
		}
		
		$this->encoding = $this->xmlEncoding ?: self::XML_ENCODING;
		$this->setupXPath();
	}
	
	/**
	 * Load XML from a string.
	 * 
	 * @param string $source The string containing the XML. 
	 * @param int $options Bitwise OR of the libxml option constants.
	 * @throws XMLException if the XML cannot be loaded from the given source.
	 */
	public function loadXML($source, $options = 0) {
		
		$this->options = $options ? $options :
			LIBXML_COMPACT |
			LIBXML_DTDATTR |
			LIBXML_NONET;
		
		try {
			parent::loadXML($source, $this->options);
		} catch (\Exception $e) {
			throw new XMLException("Cannot load XML from the given source `{$source}`", null, $e);
		}
		
		$this->encoding = $this->xmlEncoding ?: self::XML_ENCODING;
		$this->setupXPath();
	}
	
	/**
	 * Create the root node of this document.
	 * 
	 * @param string $name The name of the child element to add.
	 * @param mixed $value If specified, the value of the child element.
	 * @param string $namespace If specified, the namespace to which the child element belongs.
	 * @return XMLElement The created root node.
	 * @throws XMLException if already a root node exists.
	 */
	public function root($name, $value = null, $namespace = null) {
		
		if ($this->documentElement !== null) {
			throw new XMLException('This document has already a root node');
		}
		
		$value = !is_bool($value) ? $value : ($value ? 'true' : 'false');
		if ($namespace === null) {
			$element = $this->createElement($name, $value);
		} else {
			$prefix = explode(':', $name);
			$prefix = $prefix[0];
			$this->xpath->registerNamespace($prefix, $namespace);
			$element = $this->createElementNS($namespace, $name, $value);
		}
		
		$this->appendChild($element);
		
		return $this->documentElement;
	}
	
	/**
	 * Set an attribute for the root node.
	 * 
	 * @param string $name The name of the child attribute to set.
	 * @param mixed $value If specified, the value of the attribute.
	 * @param string $namespace If specified, the namespace to which the attribute belongs.
	 * @return XMLElement The root node to providing a fluent interface.
	 * @throws XMLException if no root node exists.
	 */
	public function attribute($name, $value, $namespace = null) {
		
		if ($this->documentElement === null) {
			throw new XMLException('This document has no root node');
		}
		
		$value = !is_bool($value) ? $value : ($value ? 'true' : 'false');
		if ($namespace === null) {
			$this->documentElement->setAttribute($name, $value);
		} else {
			$prefix = explode(':', $name);
			$prefix = $prefix[0];
			$this->xpath->registerNamespace($prefix, $namespace);
			$this->documentElement->setAttributeNS($namespace, $name, $value);
		}
		
		return $this->documentElement;
	}
	
	/**
	 * Return all namespaces declared in this document.
	 * 
	 * @param array $omit A list with namespace URIs to omit.
	 * @return array An array of namespace prefixes with their associated URIs.
	 */
	public function getNamespaces(array $omit = null) {
		
		$omit = $omit === null ? array() : $omit;
		$namespaces = array();
		$nodes = $this->xpath->query('//namespace::*');
		foreach ($nodes as $node) {
			/** @var $node \DOMNode */
			if (in_array($node->nodeValue, $omit)) {
				continue;
			}
			
			$namespaces[$node->prefix] = $node->nodeValue;
		}
		
		return $namespaces;
	}
	
	/**
	 * Remove all comments from this document.
	 */
	public function removeComments() {
		
		$comments = $this->xpath->query('.//comment()');
		for ($i = 0; $i < $comments->length; $i++) {
			$comments->item($i)->parentNode->removeChild($comments->item($i));
		}
	}
	
	/**
	 * Get attribute for the given name.
	 *
	 * @param string $name The name of the attribute.
	 * @return XMLAttribute The attribute object for the given name or null.
	 * @throws XMLException if no root node exists.
	 */
	public function offsetGet($name) {
		
		if ($this->documentElement === null) {
			throw new XMLException('This document has no root node');
		} else if (!$this->offsetExists($name)) {
			return null;
		}
		
		return $this->documentElement->getAttributeNode($name);
	}
	
	/**
	 * Set a new attribute.
	 * 
	 * @param string $name The name of the attribute.
	 * @param string $value The value of the attribute.
	 * @throws XMLException if no root node exists.
	 */
	public function offsetSet($name, $value) {
		
		if ($this->documentElement === null) {
			throw new XMLException('This document has no root node');
		}
		
		$value = !is_bool($value) ? $value : ($value ? 'true' : 'false');
		$this->documentElement->setAttribute($name, $value);
	}
	
	/**
	 * Check if attribute with the given name exists.
	 * 
	 * @param string $name The name of the attribute to check.
	 * @return bool True if an attribute with the given name exists, false otherwise.
	 * @throws XMLException if no root node exists.
	 */
	public function offsetExists($name) {
		
		if ($this->documentElement === null) {
			throw new XMLException('This document has no root node');
		}
		
		return $this->documentElement->hasAttribute($name);
	}
	
	/**
	 * Remove attribute for the given name.
	 * 
	 * @param string $name The name of the attribute to remove.
	 * @throws XMLException if no root node exists.
	 */
	public function offsetUnset($name) {
		
		if ($this->documentElement === null) {
			throw new XMLException('This document has no root node');
		}
		
		$this->documentElement->removeAttribute($name);
	}
	
	/**
	 * Setup the XPath object.
	 */
	protected function setupXPath() {
		
		$this->xpath = new \DOMXPath($this);
		$this->xpath->registerNamespace("php", "http://php.net/xpath");
		$this->xpath->registerPHPFunctions();
	}
}
