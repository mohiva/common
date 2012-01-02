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
 * @package   Mohiva/Common/Lang
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
namespace com\mohiva\common\lang;

use com\mohiva\common\parser\Token;

/**
 * Class which represents an annotation token.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Util
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class AnnotationToken implements Token {
	
	/**
	 * The token code.
	 * 
	 * @var int
	 */
	private $code = null;
	
	/**
	 * The token value.
	 * 
	 * @var string
	 */
	private $value = null;
	
	/**
	 * The token offset.
	 * 
	 * @var int
	 */
	private $offset = null;
	
	/**
	 * The class constructor.
	 * 
	 * @param int $code The token code.
	 * @param string $value The token offset.
	 * @param int $offset The token offset.
	 */
	public function __construct($code, $value, $offset) {
		
		$this->code = $code;
		$this->value = $value;
		$this->offset = $offset;
	}
	
	/**
	 * Returns the token code.
	 * 
	 * @return int The token code.
	 */
	public function getCode() {
		
		return $this->code;
	}
	
	/**
	 * Returns the token value.
	 * 
	 * @return string The token value.
	 */
	public function getValue() {
		
		return $this->value;
	}
	
	/**
	 * Returns thr token offset.
	 * 
	 * @return int The token offset.
	 */
	public function getOffset() {
		
		return $this->offset;
	}
}
