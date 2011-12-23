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
 * @copyright Copyright (c) 2007-2011 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
namespace com\mohiva\common\lang;

use ReflectionClass as InternalReflectionClass;
use com\mohiva\common\cache\containers\AnnotationContainer;

/**
 * The `ReflectionClass` class reports information about a class. 
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Util
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2011 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class ReflectionClass extends InternalReflectionClass implements AnnotationReflector {
	
	/**
	 * An instance of the `ReflectionDocComment` class.
	 * 
	 * @var ReflectionDocComment
	 */
	private $docComment = null;
	
	/**
	 * Gets the constructor from a class.
	 * 
	 * @return ReflectionMethod A `ReflectionMethod` object or null if the class has no constructor.
	 */
	public function getConstructor() {
		
		/* @var \ReflectionClass $method */
		$method = parent::getConstructor();
		if (!$method) {
			return null;
		}
		
		return new ReflectionMethod($this->getName(), $method->getName());
	}
	
	/**
	 * Get all implemented interfaces.
	 * 
	 * @return ReflectionClass[] An associative `array` of interfaces, with keys as interface names and 
	 * the array values as `ReflectionClass` objects.
	 */
	public function getInterfaces() {
		
		$interfaces = array();
		foreach (parent::getInterfaces() as $name => $interface) {
			$interfaces[$name] = new ReflectionClass($interface);
		}
		
		return $interfaces;
	}
	
	/**
	 * Returns the name of the first found constant for the given value. The prefix 
	 * can be used to restrict the constants to check for. This is an advantage if 
	 * multiple constants with the same value exists.
	 * 
	 * @param mixed $value The value of the constant to find.
	 * @param string $prefix A string prefix.
	 * @return string The name of the constant.
	 */
	public function getConstantByValue($value, $prefix = null) {
		
		$constants = $this->getConstants();
		foreach ($constants as $constName => $constValue) {
			$pos = $prefix ? strpos($constName, $prefix) : 0;
			if ($pos === 0 && $constValue === $value) {
				return $constName;
			}
		}
		
		return null;
	}
	
	/**
	 * Gets a `ReflectionMethod` object for the given name.
	 * 
	 * @param string $name The name of the method.
	 * @return ReflectionMethod A `ReflectionMethod` object.
	 */
	public function getMethod($name) {
		
		return new ReflectionMethod($this->getName(), $name);
	}
	
	/**
	 * Gets a list of methods.
	 * 
	 * @param int $filter The optional filter, for filtering desired method types. It's configured 
	 * using the `ReflectionMethod` constants.
	 * 
	 * @return ReflectionMethod[] An array of `ReflectionMethod` objects.
	 */
	public function getMethods($filter = null) {
		
		$methods = array();
		foreach (parent::getMethods($filter) as $method) {
			/* @var \ReflectionMethod $method */
			$methods[] = new ReflectionMethod($method->class, $method->name);
		}
		
		return $methods;
	}
	
	/**
	 * Gets parent class.
	 * 
	 * @return ReflectionClass A `ReflectionClass` object or null if the class has no parent.
	 */
	public function getParentClass() {
		
		$parent = parent::getParentClass();
		if (!$parent) {
			return null;
		}
		
		return new ReflectionClass($parent->getName());
	}
	
	/**
	 * Gets a `ReflectionProperty` object for the given name.
	 * 
	 * @param string $name The name of the property.
	 * @return ReflectionProperty A `ReflectionProperty` object.
	 */
	public function getProperty($name) {
		
		return new ReflectionProperty($this->getName(), $name);
	}
	
	/**
	 * Gets a list of properties.
	 * 
	 * @param int $filter The optional filter, for filtering desired property types. It's configured 
	 * using the `ReflectionProperty` constants.
	 * 
	 * @return ReflectionProperty[] An array of `ReflectionProperty` objects. 
	 */
	public function getProperties($filter = null) {
		
		$properties = array();
		foreach (parent::getProperties($filter) as $property) {
			/* @var \ReflectionProperty $property */
			$properties[] = new ReflectionProperty($property->class, $property->name);
		}
		
		return $properties;
	}
	
	/**
	 * Gets a `ReflectionClassNamespace` object.
	 * 
	 * @return ReflectionClassNamespace A `ReflectionClassNamespace` object.
	 */
	public function getNamespace() {
		
		return new ReflectionClassNamespace($this);
	}
	
	/**
	 * Return the annotation list.
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
	 * Returns the fully qualified class name for the class,
	 * property or method on which the doc comment is located.
	 *
	 * @return string A fully qualified class name.
	 */
	public function getClassContext() {
		
		return $this->getName();
	}
	
	/**
	 * Return the name of the class, property or method on which the
	 * doc comment is located.
	 *
	 * @return string A class, property or method name.
	 */
	public function getParseContext() {
		
		return $this->getName();
	}
}
