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

use ReflectionProperty as InternalReflectionProperty;
use com\mohiva\common\cache\containers\AnnotationContainer;

/**
 * The `ReflectionProperty` class reports information about a property.
 *
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Util
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class ReflectionProperty extends InternalReflectionProperty implements AnnotationReflector {

	/**
	 * An instance of the `ReflectionDocComment` class.
	 *
	 * @var ReflectionDocComment
	 */
	private $docComment = null;

	/**
	 * Gets declaring class for the reflected property.
	 *
	 * @return ReflectionClass A `ReflectionClass` object of the class that the reflected
	 * property is part of.
	 */
	public function getDeclaringClass() {

		$class = parent::getDeclaringClass();

		return new ReflectionClass($class->getName());
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

		return $this->getClassContext() . '::$' . $this->getName();
	}
}
