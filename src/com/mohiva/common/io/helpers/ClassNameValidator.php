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
 * @package   Mohiva/Common/IO/Helper
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
namespace com\mohiva\common\io\helpers;

/**
 * Helper trait to validate class names.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/IO/Helper
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
trait ClassNameValidator {
	
	/**
	 * Check if a FQN contains illegal characters.
	 * 
	 * @param string $fqn The fully qualified name to check.
	 * @return boolean True if the fully qualified name is valid, false otherwise.
	 */
	private function isValid($fqn) {
		
		if (preg_match('/^[a-zA-Z_\\\][a-zA-Z0-9_\\\]*$/', $fqn)) {
			return true;
		}
		
		return false;
	}
}
