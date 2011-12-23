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
 * @package   Mohiva/Common/IO
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2011 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
namespace com\mohiva\common\io;

/**
 * Interface to be implemented by objects that can load classes.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/IO
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2011 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
interface ClassLoader {
	
	/**
	 * Return a `ReflectionClass` instance for the given class.
	 * 
	 * @param string $fqn The fully qualified name of the class to load.
	 * @param boolean $returnRef True if the class reference should be returned, false otherwise.
	 * @return \com\mohiva\common\lang\ReflectionClass The resulting `ReflectionClass` object or null if the return 
	 * is disabled.
	 * 
	 * @throws ClassNotFoundException if the class cannot be found.
	 */
	public function loadClass($fqn, $returnRef = true);
	
	/**
	 * Return a `ReflectionClass` instance for the given class.
	 * 
	 * This method searches the class in the given path.
	 * 
	 * @param string $fqn The fully qualified name of the class to load.
	 * @param string $path The path in which the class will be searched.
	 * @param boolean $returnRef True if the class reference should be returned, false otherwise.
	 * @return \com\mohiva\common\lang\ReflectionClass The resulting `ReflectionClass` object or null if the return 
	 * is disabled.
	 * 
	 * @throws ClassNotFoundException if the class cannot be found.
	 */
	public function loadClassFromPath($fqn, $path, $returnRef = true);
}
