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

use ReflectionMethod as InternalReflectionMethod;
use com\mohiva\common\cache\containers\AnnotationContainer;

/**
 * The `ReflectionMethod` class reports information about a method. 
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Util
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class ReflectionMethod extends InternalReflectionMethod implements AnnotationReflector {
	
	/**
	 * An instance of the `ReflectionDocComment` class.
	 * 
	 * @var ReflectionDocComment
	 */
	private $docComment = null;
	
	/**
	 * Gets declaring class for the reflected method.
	 * 
	 * @return ReflectionClass A `ReflectionClass` object of the class that the reflected 
	 * method is part of.
	 */
	public function getDeclaringClass() {
		
		$class = parent::getDeclaringClass();
		
		return new ReflectionClass($class->getName());
	}
	
	/**
	 * Gets the method prototype (if there is one).
	 * 
	 * @return A `ReflectionMethod` instance of the method prototype.
	 * @throws ReflectionException if the method does not have a prototype.
	 */
	public function getPrototype() {
		
		/* @var \ReflectionMethod $prototype */
		/* @var \ReflectionClass $class */
		$prototype = parent::getPrototype();
		$class = $prototype->getDeclaringClass();
		
		return new self($class->getName(), $prototype->getName());
	}
	
	/**
	 * Gets the annotation list.
	 * 
	 * @return AnnotationList A list containing annotation instances.
	 */
	public function getAnnotationList() {
		
		if ($this->docComment == null) {
			$this->docComment = new ReflectionDocComment($this);
		}
		
		return $this->docComment->getAnnotationList();
	}
	
	/**
	 * Gets a `ReflectionClassNamespace` object.
	 * 
	 * @return ReflectionClassNamespace A `ReflectionClassNamespace` object.
	 */
	public function getNamespace() {
		
		return new ReflectionClassNamespace($this->getDeclaringClass());
	}
	
	/**
	 * Gets the fully qualified class name for the class,
	 * property or method on which the doc comment is located.
	 *
	 * @return string A fully qualified class name.
	 */
	public function getClassContext() {
		
		return parent::getDeclaringClass()->getName();
	}
	
	/**
	 * Gets the name of the class, property or method on which the
	 * doc comment is located.
	 *
	 * @return string A class, property or method name.
	 */
	public function getParseContext() {
		
		return $this->getClassContext() . '::' . $this->getName() . '()';
	}
}
