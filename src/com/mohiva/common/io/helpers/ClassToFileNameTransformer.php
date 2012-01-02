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
 * A helper trait that transforms class names into file names.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/IO/Helper
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
trait ClassToFileNameTransformer {
	
	/**
	 * Transforms the given fully qualified name into a file name, based 
	 * on the reference implementation of the PHP Standards Working Group.
	 * 
	 * @param string $fqn A fully qualified name.
	 * @return string The path to the class file.
	 * @see http://groups.google.com/group/php-standards/web/psr-0-final-proposal
	 */
	private function toPSR0FileName($fqn) {
		
		$fileName = '';
		$className = ltrim($fqn, '\\');
		$lastNsPos = strripos($className, '\\');
		if ($lastNsPos) {
			$namespace = substr($className, 0, $lastNsPos);
			$className = substr($className, $lastNsPos + 1);
			$fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
		}
		$fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
		
		return $fileName;
	}
}
