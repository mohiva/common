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

/**
 * Provides an SimpleXML like api on top  of the DOMAttr implementation.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/XML
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class XMLAttribute extends \DOMAttr {
	
	/**
	 * Return the value as string.
	 * 
	 * @return string The attribute value casted as string.
	 */
	public function __toString() {
		
		return $this->toString();
	}
	
	/**
	 * Return the value as boolean.
	 * 
	 * @return bool The attribute value casted as boolean.
	 */
	public function toBool() {
		
		if ($this->value === 'true') {
			return true;
		} else if ($this->value === 'false') {
			return false;
		} else {
			return (bool) $this->value;
		}
	}
	
	/**
	 * Return the value as int.
	 * 
	 * @return int The attribute value casted as int.
	 */
	public function toInt() {
		
		return (int) $this->value;
	}
	
	/**
	 * Return the value as float.
	 * 
	 * @return float The attribute value casted as float.
	 */
	public function toFloat() {
		
		return (float) $this->value;
	}

	/**
	 * Return the value as string.
	 * 
	 * @return string The attribute value casted as string.
	 */
	public function toString() {
		
		return (string) $this->value;
	}
}
