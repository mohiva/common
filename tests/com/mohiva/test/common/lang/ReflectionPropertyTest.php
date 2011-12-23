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
 * @copyright Copyright (c) 2007-2011 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
namespace com\mohiva\test\common\lang;

use com\mohiva\common\lang\ReflectionProperty;

/**
 * Unit test case for the `ReflectionProperty` class.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Test
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2011 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class ReflectionPropertyTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * The fixture namespace.
	 */
	const FIXTURE_NS = 'com\mohiva\test\resources\common\lang\reflection';
	
	/**
	 * Test if the `getDeclaringClass` return a `ReflectionClass` object.
	 */
	public function testGetDeclaringClass() {
		
		$property = new ReflectionProperty(self::FIXTURE_NS . '\ClassWithProperties', 'foo');
		
		$this->assertInstanceOf('com\mohiva\common\lang\ReflectionClass', $property->getDeclaringClass());
	}
	
	/**
	 * Test if the `getNamespace` method return a `ReflectionClassNamespace` object.
	 */
	public function testGetNamespace() {
		
		$property = new ReflectionProperty(self::FIXTURE_NS . '\ClassWithProperties', 'foo');
		
		$this->assertInstanceOf('com\mohiva\common\lang\ReflectionClassNamespace', $property->getNamespace());
	}
	
	/**
	 * Test if the `getClassContext` method returns a class name.
	 */
	public function testGetClassContext() {
		
		$property = new ReflectionProperty(self::FIXTURE_NS . '\ClassWithProperties', 'foo');
		
		$this->assertEquals(self::FIXTURE_NS . '\ClassWithProperties', $property->getClassContext());
	}
	
	/**
	 * Test if the `getClassContext` method returns a class name with the property.
	 */
	public function testGetParseContext() {
		
		$property = new ReflectionProperty(self::FIXTURE_NS . '\ClassWithProperties', 'foo');
		
		$this->assertEquals(self::FIXTURE_NS . '\ClassWithProperties::$foo', $property->getParseContext());
	}
	
	/**
	 * Test if the `getAnnotationList` method return a list of all annotations in a doc comment.
	 */
	public function testGetAnnotationList() {
		
		$property = new ReflectionProperty('\com\mohiva\test\resources\common\lang\AnnotationTest', 'nested');
		
		$this->assertInstanceOf('com\mohiva\common\lang\AnnotationList', $property->getAnnotationList());
	}
}
