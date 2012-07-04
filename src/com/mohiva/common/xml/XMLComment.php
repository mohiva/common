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
 * Implementation of the DOMComment class which fixes the line number bug.
 *
 * @category  Mohiva/Common
 * @package   Mohiva/Common/XML
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class XMLComment extends \DOMComment {

	/**
	 * The XMLDocument object associated with this node.
	 *
	 * @var XMLDocument
	 */
	public $ownerDocument = null;

	/**
	 * Gets line number for where the node is defined.
	 *
	 * The method `DOMNode::getLineNo()` does not return the correct line number for comment nodes. So this method
	 * can return either the original line number returned by libxml or the fixed and correct line number
	 * created by the `XMLDocument::fixLineNumbers()` method.
	 *
	 * @return int Either the original line number returned by libxml or the fixed and correct line number
	 * created by the `XMLDocument::fixLineNumbers()` method.
	 */
	public function getLineNo() {

		$container = $this->ownerDocument->getLineNumberContainer();
		if ($container && $container->contains($this)) {
			return $container->offsetGet($this);
		}

		return parent::getLineNo();
	}
}
