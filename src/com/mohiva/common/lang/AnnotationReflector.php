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

/**
 * Interface for all reflection classes which can handle annotations.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Util
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
interface AnnotationReflector {
	
	/**
	 * Return the annotation list.
	 * 
	 * @return AnnotationList A list containing annotation instances.
	 */
	public function getAnnotationList();
	
	/**
	 * Return the doc comment.
	 * 
	 * @return string The doc comment if exists, false otherwise
	 */
	public function getDocComment();
	
	/**
	 * Gets a `ReflectionClassNamespace` object for the associated reflector.
	 * 
	 * @return ReflectionClassNamespace A `ReflectionClassNamespace` object.
	 */
	public function getNamespace();
	
	/**
	 * Returns the fully qualified class name for the class, 
	 * property or method on which the doc comment is located.
	 * 
	 * @return string A fully qualified class name.
	 */
	public function getClassContext();
	
	/**
	 * Return the name of the class, property or method on which the 
	 * doc comment is located.
	 * 
	 * @return string A class, property or method name.
	 */
	public function getParseContext();
}
