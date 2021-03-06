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
 * @package   Mohiva/Common/Exceptions
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
namespace com\mohiva\common\exceptions;

/**
 * Signals that a syntax error occurred.
 *
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Exceptions
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class SyntaxErrorException extends \Exception implements MohivaException {

	/**
	 * The line number in which the syntax error was found.
	 *
	 * @var int
	 */
	private $lineNo = null;

	/**
	 * Sets the line number in which the syntax error was found.
	 *
	 * @param int $lineNo The line number in which the syntax error was found.
	 */
	public function setLineNo($lineNo) {

		$this->lineNo = $lineNo;
	}

	/**
	 * Gets the line number in which the syntax error was found.
	 *
	 * @return int The line number in which the syntax error was found.
	 */
	public function getLineNo() {

		return $this->lineNo;
	}
}
