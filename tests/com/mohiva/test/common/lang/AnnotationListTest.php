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
 * @package   Mohiva/Common/Test
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
namespace com\mohiva\test\common\lang;

use com\mohiva\test\resources\common\lang\annotations\ArrayValue;
use com\mohiva\test\resources\common\lang\annotations\ObjectValue;
use com\mohiva\common\lang\AnnotationList;

/**
 * Unit test case for the `AnnotationList` class.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Test
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class AnnotationListTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * Test if can add an annotation to the list.
	 */
	public function testIfCanAddAnnotation() {
		
		$list = new AnnotationList();
		$list->addAnnotation(new ArrayValue(array()));
		$list->addAnnotation(new ObjectValue(new \stdClass));
		$list->addAnnotation(new ArrayValue(array()));
		$list->addAnnotation(new ObjectValue(new \stdClass));
	}
	
	/**
	 * Test if the method `getAnnotation` returns an annotation.
	 */
	public function testGetAnnotation() {
		
		$list = new AnnotationList();
		$list->addAnnotation(new ArrayValue(array()));
		
		$annotation = $list->getAnnotations(ArrayValue::NAME)
			->getIterator()
			->current();
		
		$this->assertInstanceOf('\com\mohiva\test\resources\common\lang\annotations\ArrayValue', $annotation);
	}
	
	/**
	 * Test if can iterate over the method `getAnnotation`.
	 */
	public function testIteratorOverAnnotation() {
		
		$number = 0;
		$list = new AnnotationList();
		$list->addAnnotation(new ArrayValue(array()));
		$list->addAnnotation(new ArrayValue(array()));
		$list->addAnnotation(new ArrayValue(array()));
		$iterator = $list->getAnnotations(ArrayValue::NAME)->getIterator();
		foreach ($iterator as $annotation) {
			$this->assertInstanceOf(
				'\com\mohiva\test\resources\common\lang\annotations\ArrayValue', 
				$annotation
			);
			
			$number++;
		}
		
		$this->assertEquals($number, 3);
	}
	
	/**
	 * Test if the method `getAnnotations` returns a list 
	 * with annotations of the same type.
	 */
	public function testGetAnnotationsReturnList() {
		
		$list = new AnnotationList();
		$list->addAnnotation(new ArrayValue(array()));
		$list->addAnnotation(new ArrayValue(array()));
		$list->addAnnotation(new ArrayValue(array()));
		$list = $list->getAnnotations(ArrayValue::NAME);
		
		$this->assertInstanceOf('\ArrayObject', $list);
		$this->assertEquals($list->count(), 3);
	}
	
	/**
	 * Test if the method `hasAnnotations` returns true on existing annotation.
	 */
	public function testIfHasAnnotationsReturnsTrue() {
		
		$list = new AnnotationList();
		$list->addAnnotation(new ArrayValue(array()));
		$result = $list->hasAnnotations(ArrayValue::NAME);
		
		$this->assertTrue($result);
	}
	
	/**
	 * Test if the method `hasAnnotations` returns false on not existing annotation.
	 */
	public function testIfHasAnnotationsReturnsFalse() {
		
		$list = new AnnotationList();
		$result = $list->hasAnnotations('NotExisting');
		
		$this->assertFalse($result);
	}
}
