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

use com\mohiva\common\lang\ReflectionMethod;

/**
 * Unit test case for the `ReflectionMethod` class.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Test
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2011 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class ReflectionMethodTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * The fixture namespace.
	 */
	const FIXTURE_NS = 'com\mohiva\test\resources\common\lang\reflection';
	
	/**
	 * Test if the `getDeclaringClass` return a `ReflectionClass` object.
	 */
	public function testGetDeclaringClass() {
		
		$method = new ReflectionMethod(self::FIXTURE_NS . '\ClassWithMethods', 'foo');
		
		$this->assertInstanceOf('com\mohiva\common\lang\ReflectionClass', $method->getDeclaringClass());
	}
	
	/**
	 * Test if the `getPrototype` method return a `ReflectionMethod` object
	 * if a method has a prototype.
	 */
	public function testGetPrototypeReturnReflectionMethod() {
		
		$method = new ReflectionMethod(self::FIXTURE_NS . '\MethodWithPrototype', 'foo');
		
		$this->assertInstanceOf('com\mohiva\common\lang\ReflectionMethod', $method->getPrototype());
	}
	
	/**
	 * Test if the `getPrototype` method throws an exception if a method has no prototype.
	 * 
	 * @expectedException \ReflectionException
	 */
	public function testGetPrototypeThrowsException() {
		
		$method = new ReflectionMethod(self::FIXTURE_NS . '\ClassWithMethods', 'foo');
		$method->getPrototype();
	}
	
	/**
	 * Test if the `getNamespace` method return a `ReflectionClassNamespace` object.
	 */
	public function testGetNamespace() {
		
		$method = new ReflectionMethod(self::FIXTURE_NS . '\ClassWithMethods', 'foo');
		
		$this->assertInstanceOf('com\mohiva\common\lang\ReflectionClassNamespace', $method->getNamespace());
	}
	
	/**
	 * Test if the `getClassContext` method returns a class name.
	 */
	public function testGetClassContext() {
		
		$method = new ReflectionMethod(self::FIXTURE_NS . '\ClassWithMethods', 'foo');
		
		$this->assertEquals(self::FIXTURE_NS . '\ClassWithMethods', $method->getClassContext());
	}
	
	/**
	 * Test if the `getClassContext` method returns a class name with the method.
	 */
	public function testGetParseContext() {
		
		$method = new ReflectionMethod(self::FIXTURE_NS . '\ClassWithMethods', 'foo');
		
		$this->assertEquals(self::FIXTURE_NS . '\ClassWithMethods::foo()', $method->getParseContext());
	}
	
	/**
	 * Test if the `getAnnotationList` method return a list of all annotations in a doc comment.
	 */
	public function testGetAnnotationList() {
		
		$method = new ReflectionMethod('\com\mohiva\test\resources\common\lang\AnnotationTest', 'value');
		
		$this->assertInstanceOf('com\mohiva\common\lang\AnnotationList', $method->getAnnotationList());
	}
}
