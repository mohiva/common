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

use ReflectionMethod as InternalReflectionMethod;
use ReflectionProperty as InternalReflectionProperty;
use com\mohiva\common\lang\ReflectionObject;

/**
 * Unit test case for the `ReflectionObject` class.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Test
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2011 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class ReflectionObjectTest extends \PHPUnit_Framework_TestCase {
	
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
		
		$className = self::FIXTURE_NS . '\ClassWithConstructor';
		$object = new ReflectionObject(new $className);
		
		$this->assertInstanceOf('com\mohiva\common\lang\ReflectionMethod', $object->getConstructor());
	}
	
	/**
	 * Test if the `getConstructor` method return null if a class has no constructor.
	 */
	public function testGetConstructorReturnNull() {
		
		$className = self::FIXTURE_NS . '\ClassWithoutConstructor';
		$object = new ReflectionObject(new $className);
		
		$this->assertNull($object->getConstructor());
	}
	
	/**
	 * Test if the `getInterfaces` method returns a list with all implemented interfaces.
	 */
	public function testGetInterfaces() {
		
		$className = self::FIXTURE_NS . '\ClassWithInterfaces';
		$object = new ReflectionObject(new $className);
		$interfaces = $object->getInterfaces();
		
		$this->assertArrayHasKey(self::FIXTURE_NS . '\Foo', $interfaces);
		$this->assertArrayHasKey(self::FIXTURE_NS . '\Bar', $interfaces);
		$this->assertInstanceOf('com\mohiva\common\lang\ReflectionClass', $interfaces[self::FIXTURE_NS . '\Foo']);
		$this->assertInstanceOf('com\mohiva\common\lang\ReflectionClass', $interfaces[self::FIXTURE_NS . '\Bar']);
	}
	
	/**
	 * Test if the `getConstantByValue` method returns the name of the constant.
	 */
	public function testGetConstantByValueReturnsNameOfConstant() {
		
		$className = __CLASS__;
		$object = new ReflectionObject(new $className);
		
		$this->assertSame('O_EQUAL', $object->getConstantByValue(self::O_EQUAL, 'O'));
		$this->assertSame('T_COLON', $object->getConstantByValue(self::T_COLON));
	}
	
	/**
	 * Test if the `getConstantByValue` method returns null if no constant with this value exists.
	 */
	public function testGetConstantByValueReturnsNull() {
		
		$className = __CLASS__;
		$object = new ReflectionObject(new $className);
		
		$this->assertNull($object->getConstantByValue(100));
	}
	
	/**
	 * Test if the `getMethod` method return a `ReflectionMethod` object
	 * if a method with the given name exists in class.
	 */
	public function testGetMethodReturnReflectionMethod() {
		
		$className = self::FIXTURE_NS . '\ClassWithMethods';
		$object = new ReflectionObject(new $className);
		
		$this->assertInstanceOf('com\mohiva\common\lang\ReflectionMethod', $object->getMethod('foo'));
	}
	
	/**
	 * Test if the `getMethod` method throws an exception if no method with the given 
	 * name exists in class.
	 * 
	 * @expectedException \ReflectionException
	 */
	public function testGetMethodThrowsException() {
		
		$className = self::FIXTURE_NS . '\ClassWithMethods';
		$object = new ReflectionObject(new $className);
		$object->getMethod('baz');
	}
	
	/**
	 * Test if the `getMethods` method returns a list of methods within a class.
	 */
	public function testGetMethods() {
		
		$className = self::FIXTURE_NS . '\ClassWithMethods';
		$object = new ReflectionObject(new $className);
		$methods = $object->getMethods(InternalReflectionMethod::IS_PUBLIC);
		
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
		
		$className = self::FIXTURE_NS . '\ClassWithParent';
		$object = new ReflectionObject(new $className);
		
		$this->assertInstanceOf('com\mohiva\common\lang\ReflectionClass', $object->getParentClass());
	}
	
	/**
	 * Test if the `getParentClass` method returns null if the class has no parent.
	 */
	public function testGetParentClassReturnNull() {
		
		$className = self::FIXTURE_NS . '\ClassWithoutParent';
		$object = new ReflectionObject(new $className);
		
		$this->assertNull($object->getParentClass());
	}

	/**
	 * Test if the `getProperty` method return a `ReflectionProperty` object
	 * if a property with the given name exists in class.
	 */
	public function testGetPropertyReturnReflectionProperty() {
		
		$className = self::FIXTURE_NS . '\ClassWithProperties';
		$object = new ReflectionObject(new $className);
		
		$this->assertInstanceOf('com\mohiva\common\lang\ReflectionProperty', $object->getProperty('foo'));
	}
	
	/**
	 * Test if the `getProperty` method throws an exception if no property with the given 
	 * name exists in class.
	 * 
	 * @expectedException \ReflectionException
	 */
	public function testGetPropertyThrowsException() {
		
		$className = self::FIXTURE_NS . '\ClassWithProperties';
		$object = new ReflectionObject(new $className);
		$object->getProperty('baz');
	}
	
	/**
	 * Test if the `getProperties` method returns a list of properties within a class.
	 */
	public function testGetProperties() {
		
		$className = self::FIXTURE_NS . '\ClassWithProperties';
		$object = new ReflectionObject(new $className);
		$properties = $object->getProperties(InternalReflectionProperty::IS_PUBLIC);
		
		$this->assertInstanceOf('com\mohiva\common\lang\ReflectionProperty', $properties[0]);
		$this->assertInstanceOf('com\mohiva\common\lang\ReflectionProperty', $properties[1]);
		$this->assertEquals('foo', $properties[0]->name);
		$this->assertEquals('bar', $properties[1]->name);
	}
	
	/**
	 * Test if the `getNamespace` method return a `ReflectionClassNamespace` object.
	 */
	public function testGetNamespace() {
		
		$object = new ReflectionObject($this);
		
		$this->assertInstanceOf('com\mohiva\common\lang\ReflectionClassNamespace', $object->getNamespace());
	}
	
	/**
	 * Test if the `getClassContext` method returns a class name.
	 */
	public function testGetClassContext() {
		
		$className = self::FIXTURE_NS . '\ClassWithMultipleUseStatements';
		$object = new ReflectionObject(new $className);
		
		$this->assertEquals($className, $object->getClassContext());
	}
	
	/**
	 * Test if the `getClassContext` method returns a class name.
	 */
	public function testGetParseContext() {
		
		$className = self::FIXTURE_NS . '\ClassWithMultipleUseStatements';
		$object = new ReflectionObject(new $className);
		
		$this->assertEquals($className, $object->getParseContext());
	}
	
	/**
	 * Test if the `getAnnotationList` method return a list of all annotations in a doc comment.
	 */
	public function testGetAnnotationList() {
		
		$className = '\com\mohiva\test\resources\common\lang\AnnotationTest';
		$object = new ReflectionObject(new $className);
		
		$this->assertInstanceOf('com\mohiva\common\lang\AnnotationList', $object->getAnnotationList());
	}
}
