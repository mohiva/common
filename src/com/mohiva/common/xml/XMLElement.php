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

use com\mohiva\common\xml\exceptions\XMLException;

/**
 * Provides an SimpleXML like api on top of the DOMElement implementation.
 *
 * @category  Mohiva/Common
 * @package   Mohiva/Common/XML
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class XMLElement extends \DOMElement implements \ArrayAccess {

	/**
	 * Execute a xpath query.
	 *
	 * As default behavior this method returns always a node list. But if the query is
	 * prefixed with the # character then return the first node of the result
	 * or null if no result exists.
	 *
	 * @param string $query The query string to process.
	 * @return \DOMNodeList | XMLElement | XMLAttribute | null The result of the xpath query.
	 * @throws XMLException if the element isn't a child of a DOMDocument.
	 */
	public function __invoke($query) {

		if ($this->ownerDocument === null) {
			throw new XMLException('XMLElement has no owner document');
		}

		if ($query[0] === '#') {
			$query = substr($query, 1);
			$node = $this->ownerDocument->xpath->query($query, $this);
			if ($node->length) {
				return $node->item(0);
			}

			return null;
		}

		return $this->ownerDocument->xpath->query($query, $this);
	}

	/**
	 * Return the value as string.
	 *
	 * @return string The element value casted as string.
	 */
	public function __toString() {

		return $this->toString();
	}

	/**
	 * Creates a XMLElement with the give name and append it to the document.
	 *
	 * @param string $name The name of the child element to add.
	 * @param mixed $value If specified, the value of the child element.
	 * @param string $namespace If specified, the namespace to which the child element belongs.
	 * @return XMLElement The created element to providing a fluent interface.
	 * @throws XMLException if the element isn't a child of a DOMDocument.
	 */
	public function child($name, $value = null, $namespace = null) {

		if ($this->ownerDocument === null) {
			throw new XMLException('XMLElement has no owner document');
		}

		$value = !is_bool($value) ? $value : ($value ? 'true' : 'false');
		if ($namespace === null) {
			$element = $this->ownerDocument->createElement($name, $value);
		} else {
			$prefix = explode(':', $name)[0];
			$this->ownerDocument->xpath->registerNamespace($prefix, $namespace);
			$element = $this->ownerDocument->createElementNS($namespace, $name, $value);
		}

		$element = $this->appendChild($element);

		return $element;
	}

	/**
	 * Sets an attribute for the this element.
	 *
	 * @param string $name The name of the child attribute to add.
	 * @param mixed $value If specified, the value of the attribute.
	 * @param string $namespace If specified, the namespace to which the attribute belongs.
	 * @return XMLElement This element to providing a fluent interface.
	 * @throws XMLException if the element isn't a child of a DOMDocument.
	 */
	public function attribute($name, $value, $namespace = null) {

		if ($this->ownerDocument === null) {
			throw new XMLException('XMLElement has no owner document');
		}

		$value = !is_bool($value) ? $value : ($value ? 'true' : 'false');
		if ($namespace === null) {
			$this->setAttribute($name, $value);
		} else {
			$prefix = explode(':', $name)[0];
			$this->ownerDocument->xpath->registerNamespace($prefix, $namespace);
			$this->setAttributeNS($namespace, $name, $value);
		}

		return $this;
	}

	/**
	 * Remove all children within this element.
	 */
	public function removeChilds() {

		while ($this->childNodes->length) {
			$this->removeChild($this->firstChild);
		}
	}

	/**
	 * Return the node value as boolean.
	 *
	 * @return bool The node value casted as boolean.
	 */
	public function toBool() {

		if ($this->nodeValue === 'true') {
			return true;
		} else if ($this->nodeValue === 'false') {
			return false;
		} else {
			return (bool) $this->nodeValue;
		}
	}

	/**
	 * Return the value as int.
	 *
	 * @return int The node value casted as int.
	 */
	public function toInt() {

		return (int) $this->nodeValue;
	}

	/**
	 * Return the value as float.
	 *
	 * @return float The node value casted as float.
	 */
	public function toFloat() {

		return (float) $this->nodeValue;
	}

	/**
	 * Return the value as string.
	 *
	 * @return string The node value casted as string.
	 */
	public function toString() {

		return (string) $this->nodeValue;
	}

	/**
	 * Return element as XML string.
	 *
	 * @return string The element as XML string.
	 * @throws XMLException if the element isn't a child of a DOMDocument.
	 */
	public function toXML() {

		if ($this->ownerDocument === null) {
			throw new XMLException('XMLElement has no owner document');
		}

		return $this->ownerDocument->saveXML($this);
	}

	/**
	 * Get attribute for the given name.
	 *
	 * @param string $name The name of the attribute.
	 * @return XMLAttribute The attribute object for the given name or null.
	 */
	public function offsetGet($name) {

		if (!$this->offsetExists($name)) {
			return null;
		}

		return $this->getAttributeNode($name);
	}

	/**
	 * Set a new attribute.
	 *
	 * @param string $name The name of the attribute.
	 * @param string $value The value of the attribute.
	 */
	public function offsetSet($name, $value) {

		$value = !is_bool($value) ? $value : ($value ? 'true' : 'false');
		$this->setAttribute($name, $value);
	}

	/**
	 * Check if attribute with the given name exists.
	 *
	 * @param string $name The name of the attribute to check.
	 * @return bool True if an attribute with the given name exists, false otherwise.
	 */
	public function offsetExists($name) {

		return $this->hasAttribute($name);
	}

	/**
	 * Remove attribute for the given name.
	 *
	 * @param string $name The name of the attribute to remove.
	 */
	public function offsetUnset($name) {

		$this->removeAttribute($name);
	}
}
