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

use ArrayObject;
use com\mohiva\common\lang\annotations\Annotation;

/**
 * Represents a list containing annotations. Every annotation of the same type will be 
 * bundled in a separate `ArrayObject`. So it is possible to iterate over 
 * an annotation group with the `ArrayIterator` iterator.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Util
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2011 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class AnnotationList extends ArrayObject {
	
	/**
	 * Add a new annotation to the list.
	 *
	 * @param \com\mohiva\common\lang\annotations\Annotation $annotation The annotation to add.
	 */
	public function addAnnotation(Annotation $annotation) {
		
		$name = $annotation->getName();
		if (!$this->offsetExists($name)) {
			$this->offsetSet($name, new ArrayObject());
		}
		
		/* @var \ArrayObject $annotations */
		$annotations = $this->offsetGet($name);
		$annotations->append($annotation);
	}
	
	/**
	 * Return all annotations of the given name.
	 * 
	 * @param string $annotation The name of the annotation.
	 * @return \ArrayObject All annotations of the given name or an empty object if no annotations exists.
	 */
	public function getAnnotations($annotation) {
		
		if (!$this->offsetExists($annotation)) {
			return new ArrayObject();
		}
		
		return $this->offsetGet($annotation);
	}
	
	/**
	 * Check if annotations of the given name exists in this list.
	 * 
	 * @param string $annotation The name of the annotation to check.
	 * @return boolean True if annotations exists, false otherwise.
	 */
	public function hasAnnotations($annotation) {
		
		return $this->offsetExists($annotation);
	}
}
