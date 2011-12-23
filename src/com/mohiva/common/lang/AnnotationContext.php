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

/**
 * Contains information about the context of an annotation.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Lang
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2011 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class AnnotationContext {
	
	/**
	 * @var string
	 */
	private $namespace = null;
	
	/**
	 * @var array
	 */
	private $useStatements = array();
	
	/**
	 * @var string
	 */
	private $class = null;
	
	/**
	 * @var string
	 */
	private $location = null;
	
	/**
	 * @param string $namespace The namespace in which the annotation was found.
	 * @param array $useStatements A list with use statements within the namespace.
	 * @param string $class Relates to the fully qualified class name in which the annotation was found.
	 * @param string $location The name of the class, property or method on which the annotation was found.
	 */
	public function __construct($namespace, array $useStatements, $class, $location) {
		
		$this->namespace = $namespace;
		$this->useStatements = $useStatements;
		$this->class = $class;
		$this->location = $location;
	}
	
	/**
	 * @return string The namespace in which the annotation was found.
	 */
	public function getNamespace() {
		
		return $this->namespace;
	}
	
	/**
	 * @return array A list with use statements within the namespace.
	 */
	public function getUseStatements() {
		
		return $this->useStatements;
	}
	
	/**
	 * @return string Relates to the fully qualified class name in which the annotation was found.
	 */
	public function getClass() {
		
		return $this->class;
	}
	
	/**
	 * @return string The name of the class, property or method on which the annotation was found.
	 */
	public function getLocation() {
		
		return $this->location;
	}
}
