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

use ReflectionMethod as InternalReflectionMethod;
use ReflectionProperty as InternalReflectionProperty;
use com\mohiva\common\lang\ReflectionClass;

/**
 * Unit test case for the `ReflectionClass` class.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Test
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class ReflectionClassTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * The fixture namespace.
	 */
	const FIXTURE_NS = 'com\mohiva\test\resources\common\lang\reflection';
	
	/**
	 * Test constants
	 */
	const T_COMMA = 1;
	const T_POINT = 2;
	const T_COLON = 3;
	const O_EQUAL = 1;
	const O_PLUS  = 2;
	
	/**
	 * Test if the `getConstructor` method return a `ReflectionMethod` object
	 * if a class has a constructor.
	 */
	public function testGetConstructorReturnReflectionMethod() {
		
		$class = new ReflectionClass(self::FIXTURE_NS . '\ClassWithConstructor');
		
		$this->assertInstanceOf('com\mohiva\common\lang\ReflectionMethod', $class->getConstructor());
	}
	
	/**
	 * Test if the `getConstructor` method return null if a class has no constructor.
	 */
	public function testGetConstructorReturnNull() {
		
		$class = new ReflectionClass(self::FIXTURE_NS . '\ClassWithoutConstructor');
		
		$this->assertNull($class->getConstructor());
	}
	
	/**
	 * Test if the `getInterfaces` method returns a list with all implemented interfaces.
	 */
	public function testGetInterfaces() {
		
		$class = new ReflectionClass(self::FIXTURE_NS . '\ClassWithInterfaces');
		$interfaces = $class->getInterfaces();
		
		$this->assertArrayHasKey(self::FIXTURE_NS . '\Foo', $interfaces);
		$this->assertArrayHasKey(self::FIXTURE_NS . '\Bar', $interfaces);
		$this->assertInstanceOf('com\mohiva\common\lang\ReflectionClass', $interfaces[self::FIXTURE_NS . '\Foo']);
		$this->assertInstanceOf('com\mohiva\common\lang\ReflectionClass', $interfaces[self::FIXTURE_NS . '\Bar']);
	}
	
	/**
	 * Test if the `getConstantByValue` method returns the name of the constant.
	 */
	public function testGetConstantByValueReturnsNameOfConstant() {
		
		$class = new ReflectionClass(__CLASS__);
		
		$this->assertSame('O_EQUAL', $class->getConstantByValue(self::O_EQUAL, 'O'));
		$this->assertSame('T_COLON', $class->getConstantByValue(self::T_COLON));
	}
	
	/**
	 * Test if the `getConstantByValue` method returns null if no constant with this value exists.
	 */
	public function testGetConstantByValueReturnsNull() {
		
		$class = new ReflectionClass(__CLASS__);
		
		$this->assertNull($class->getConstantByValue(100));
	}
	
	/**
	 * Test if the `getMethod` method return a `ReflectionMethod` object
	 * if a method with the given name exists in class.
	 */
	public function testGetMethodReturnReflectionMethod() {
		
		$class = new ReflectionClass(self::FIXTURE_NS . '\ClassWithMethods');
		
		$this->assertInstanceOf('com\mohiva\common\lang\ReflectionMethod', $class->getMethod('foo'));
	}
	
	/**
	 * Test if the `getMethod` method throws an exception if no method with the given 
	 * name exists in class.
	 * 
	 * @expectedException \ReflectionException
	 */
	public function testGetMethodThrowsException() {
		
		$class = new ReflectionClass(self::FIXTURE_NS . '\ClassWithMethods');
		$class->getMethod('baz');
	}
	
	/**
	 * Test if the `getMethods` method returns a list of methods within a class.
	 */
	public function testGetMethods() {
		
		$class = new ReflectionClass(self::FIXTURE_NS . '\ClassWithMethods');
		$methods = $class->getMethods(InternalReflectionMethod::IS_PUBLIC);
		
		$this->assertInstanceOf('com\mohiva\common\lang\ReflectionMethod', $methods[0]);
		$this->assertInstanceOf('com\mohiva\common\lang\ReflectionMethod', $methods[1]);
		$this->assertEquals('foo', $methods[0]->name);
		$this->assertEquals('bar', $methods[1]->name);
	}
	
	/**
	 * Test if the `getParentClass` method returns a `ReflectionClass` object
	 * if the class has a parent.
	 */
	public function testGetParentClassReturnReflectionClass() {
		
		$class = new ReflectionClass(self::FIXTURE_NS . '\ClassWithParent');
		
		$this->assertInstanceOf('com\mohiva\common\lang\ReflectionClass', $class->getParentClass());
	}
	
	/**
	 * Test if the `getParentClass` method returns null if the class has no parent.
	 */
	public function testGetParentClassReturnNull() {
		
		$class = new ReflectionClass(self::FIXTURE_NS . '\ClassWithoutParent');
		
		$this->assertNull($class->getParentClass());
	}
	
	/**
	 * Test if the `getProperty` method return a `ReflectionProperty` object
	 * if a property with the given name exists in class.
	 */
	public function testGetPropertyReturnReflectionProperty() {
		
		$class = new ReflectionClass(self::FIXTURE_NS . '\ClassWithProperties');
		
		$this->assertInstanceOf('com\mohiva\common\lang\ReflectionProperty', $class->getProperty('foo'));
	}
	
	/**
	 * Test if the `getProperty` method throws an exception if no property with the given 
	 * name exists in class.
	 * 
	 * @expectedException \ReflectionException
	 */
	public function testGetPropertyThrowsException() {
		
		$class = new ReflectionClass(self::FIXTURE_NS . '\ClassWithProperties');
		$class->getProperty('baz');
	}
	
	/**
	 * Test if the `getProperties` method returns a list of properties within a class.
	 */
	public function testGetProperties() {
		
		$class = new ReflectionClass(self::FIXTURE_NS . '\ClassWithProperties');
		$properties = $class->getProperties(InternalReflectionProperty::IS_PUBLIC);
		
		$this->assertInstanceOf('com\mohiva\common\lang\ReflectionProperty', $properties[0]);
		$this->assertInstanceOf('com\mohiva\common\lang\ReflectionProperty', $properties[1]);
		$this->assertEquals('foo', $properties[0]->name);
		$this->assertEquals('bar', $properties[1]->name);
	}
	
	/**
	 * Test if the `getNamespace` method return a `ReflectionClassNamespace` object.
	 */
	public function testGetNamespace() {
		
		$class = new ReflectionClass(__CLASS__);
		
		$this->assertInstanceOf('com\mohiva\common\lang\ReflectionClassNamespace', $class->getNamespace());
	}
	
	/**
	 * Test if the `getClassContext` method returns a class name.
	 */
	public function testGetClassContext() {
		
		$className = self::FIXTURE_NS . '\ClassWithMultipleUseStatements';
		$class = new ReflectionClass($className);
		
		$this->assertEquals($className, $class->getClassContext());
	}
	
	/**
	 * Test if the `getClassContext` method returns a class name.
	 */
	public function testGetParseContext() {
		
		$className = self::FIXTURE_NS . '\ClassWithMultipleUseStatements';
		$class = new ReflectionClass($className);
		
		$this->assertEquals($className, $class->getParseContext());
	}
	
	/**
	 * Test if the `getAnnotationList` method return a list of all annotations in a doc comment.
	 */
	public function testGetAnnotationList() {
		
		$class = new ReflectionClass('\com\mohiva\test\resources\common\lang\AnnotationTest');
		
		$this->assertInstanceOf('com\mohiva\common\lang\AnnotationList', $class->getAnnotationList());
	}
}
