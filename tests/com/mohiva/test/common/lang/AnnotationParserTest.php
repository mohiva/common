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

use com\mohiva\test\resources\common\lang\AnnotationTest;
use com\mohiva\test\resources\common\lang\annotations\ArrayValue;
use com\mohiva\test\resources\common\lang\annotations\Constant;
use com\mohiva\test\resources\common\lang\annotations\DefaultValue;
use com\mohiva\test\resources\common\lang\annotations\DoubleParameter;
use com\mohiva\test\resources\common\lang\annotations\Error;
use com\mohiva\test\resources\common\lang\annotations\FuncGetArgs;
use com\mohiva\test\resources\common\lang\annotations\Nested;
use com\mohiva\test\resources\common\lang\annotations\NonParameter;
use com\mohiva\test\resources\common\lang\annotations\ObjectValue;
use com\mohiva\test\resources\common\lang\annotations\SingleParameter;
use com\mohiva\test\resources\common\lang\annotations\Value;
use com\mohiva\common\exceptions\SyntaxErrorException;
use com\mohiva\common\lang\AnnotationParser;
use com\mohiva\common\lang\ReflectionClass;
use com\mohiva\common\lang\ReflectionProperty;
use com\mohiva\common\lang\ReflectionMethod;
use com\mohiva\common\lang\exceptions\UndefinedParameterValueException;
use PHPUnit_Framework_Constraint_IsType as PHPUnitType;

/**
 * Unit test case for the `AnnotationParser` class.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Test
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 * TODO Test the AnnotationParser direct instead of using the reflection classes
 */
class AnnotationParserTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * The path to the class containing the annotations.
	 * 
	 * @var string
	 */
	const TEST_CLASS = '\com\mohiva\test\resources\common\lang\AnnotationTest';
	
	/**
	 * Test if an exception will be thrown for an undefined constant.
	 * 
	 * @expectedException \com\mohiva\common\lang\exceptions\UndefinedConstantException
	 */
	public function testUndefinedConstant() {
		
		$reflection = new ReflectionMethod(self::TEST_CLASS, 'undefinedConstant');
		$reflection->getAnnotationList();
	}
	
	/**
	 * Test if an exception will be thrown for an undefined class constant.
	 * 
	 * @expectedException \com\mohiva\common\lang\exceptions\UndefinedConstantException
	 */
	public function testUndefinedClassConstant() {
		
		$reflection = new ReflectionMethod(self::TEST_CLASS, 'undefinedClassConstant');
		$reflection->getAnnotationList();
	}
	
	/**
	 * Test if an exception will be thrown if a class for a class constant cannot be found.
	 * 
	 * @expectedException \com\mohiva\common\io\exceptions\ClassNotFoundException
	 */
	public function testClassForClassConstantNotFound() {
		
		$reflection = new ReflectionMethod(self::TEST_CLASS, 'classForClassConstantNotFound');
		$reflection->getAnnotationList();
	}
	
	/**
	 * Test if an exception will be thrown if the annotation class cannot be found.
	 * 
	 * @expectedException \com\mohiva\common\io\exceptions\ClassNotFoundException
	 */
	public function testClassForAnnotationNotFound() {
		
		$reflection = new ReflectionMethod(self::TEST_CLASS, 'classForAnnotationNotFound');
		$reflection->getAnnotationList();
	}
	
	/**
	 * Test if an exception will be thrown when defining an annotation with named and unnamed parameters.
	 * 
	 * @expectedException \com\mohiva\common\exceptions\SyntaxErrorException
	 */
	public function testMixingNamedAndUnnamedParams() {
		
		$reflection = new ReflectionMethod(self::TEST_CLASS, 'mixingNamedAndUnnamedParams');
		$reflection->getAnnotationList();
	}
	
	/**
	 * Test if an exception will be thrown when using undefined parameters in an annotation.
	 * 
	 * @expectedException \com\mohiva\common\lang\exceptions\UndefinedParameterException
	 */
	public function testUndefinedParam() {
		
		$reflection = new ReflectionMethod(self::TEST_CLASS, 'undefinedParam');
		$reflection->getAnnotationList();
	}
	
	/**
	 * Test if an exception will be thrown for undefined parameter values.
	 */
	public function testUndefinedParameterValue() {
		
		for ($i = 1; $i <= 3; $i++) {
			try {
				$reflection = new ReflectionMethod(self::TEST_CLASS, 'undefinedParameterValue' . $i);
				$reflection->getAnnotationList();
				$this->fail('UndefinedParameterValueException was expected but never thrown');
			} catch (UndefinedParameterValueException $e) {}
		}
	}
	
	/**
	 * Test for syntax errors.
	 */
	public function testSyntaxError() {
		
		for ($i = 1; $i <= 20; $i++) {
			try {
				$reflection = new ReflectionMethod(self::TEST_CLASS, 'syntaxError' . $i);
				$reflection->getAnnotationList();
				$this->fail('SyntaxErrorException was expected but never thrown');
			} catch (SyntaxErrorException $e) {}
		}
	}
	
	/**
	 * Test if the `ArrayValueAnnotation` class has correct values.
	 */
	public function testArrayValueAnnotation() {
		
		$array = array('key' => 'value', 1, 3, 4, 'test', 1 => 3, false, null);
		
		$class = 'com\mohiva\test\resources\common\lang\annotations\ArrayValue';
		$reflection = new ReflectionProperty(self::TEST_CLASS, 'arrayValue');
		$annotation = $reflection
			->getAnnotationList()
			->getAnnotations(ArrayValue::NAME)
			->getIterator()
			->current();
		
		/* @var \com\mohiva\test\resources\common\lang\annotations\ArrayValue $annotation */
		$this->assertInstanceOf($class, $annotation);
		$this->assertInternalType(PHPUnitType::TYPE_ARRAY, $annotation->getParam1());
		$this->assertSame($annotation->getParam1(), $array);
	}
	
	/**
	 * Test if the `ConstantAnnotation` class has correct values.
	 */
	public function testConstantAnnotation() {
		
		$class = 'com\mohiva\test\resources\common\lang\annotations\Constant';
		$reflection = new ReflectionProperty(self::TEST_CLASS, 'constant');
		$annotation = $reflection
			->getAnnotationList()
			->getAnnotations(Constant::NAME)
			->getIterator()
			->current();
		
		/* @var \com\mohiva\test\resources\common\lang\annotations\Constant $annotation */
		$this->assertInstanceOf($class, $annotation);
		$this->assertInternalType(PHPUnitType::TYPE_INT, $annotation->getParam1());
		$this->assertInternalType(PHPUnitType::TYPE_INT, $annotation->getParam2());
		$this->assertInternalType(PHPUnitType::TYPE_INT, $annotation->getParam3());
		$this->assertInternalType(PHPUnitType::TYPE_INT, $annotation->getParam4());
		$this->assertInternalType(PHPUnitType::TYPE_INT, $annotation->getParam5());
		$this->assertInternalType(PHPUnitType::TYPE_INT, $annotation->getParam6());
		$this->assertEquals(AnnotationTest::PARAM1, $annotation->getParam1());
		$this->assertEquals(AnnotationTest::PARAM2, $annotation->getParam2());
		$this->assertEquals(AnnotationTest::PARAM3, $annotation->getParam3());
		$this->assertEquals(AnnotationTest::PARAM4, $annotation->getParam4());
		$this->assertEquals(AnnotationTest::PARAM5, $annotation->getParam5());
		$this->assertEquals(T_BREAK, $annotation->getParam6());
	}
	
	/**
	 * Test if the `DefaultValueAnnotation` class has correct values.
	 */
	public function testDefaultValueAnnotation() {
		
		$class = 'com\mohiva\test\resources\common\lang\annotations\DefaultValue';
		$reflection = new ReflectionProperty(self::TEST_CLASS, 'defaultValue');
		$iterator = $reflection
			->getAnnotationList()
			->getAnnotations(DefaultValue::NAME)
			->getIterator();
		
		/* @var \com\mohiva\test\resources\common\lang\annotations\DefaultValue $annotation */
		$annotation = $iterator->current();
		$this->assertInstanceOf($class, $annotation);
		$this->assertInternalType(PHPUnitType::TYPE_STRING, $annotation->getParam1());
		$this->assertInternalType(PHPUnitType::TYPE_STRING, $annotation->getParam2());
		$this->assertInternalType(PHPUnitType::TYPE_STRING, $annotation->getParam3());
		$this->assertEquals($annotation->getParam1(), 'param1');
		$this->assertEquals($annotation->getParam2(), 'param2');
		$this->assertEquals($annotation->getParam3(), 'param3');
		
		$iterator->next();
		$annotation = $iterator->current();
		$this->assertInstanceOf($class, $annotation);
		$this->assertInternalType(PHPUnitType::TYPE_STRING, $annotation->getParam1());
		$this->assertInternalType(PHPUnitType::TYPE_STRING, $annotation->getParam2());
		$this->assertInternalType(PHPUnitType::TYPE_STRING, $annotation->getParam3());
		$this->assertEquals($annotation->getParam1(), 'param1');
		$this->assertEquals($annotation->getParam2(), 'param2');
		$this->assertEquals($annotation->getParam3(), 'param3');
		
		$iterator->next();
		$annotation = $iterator->current();
		$this->assertInstanceOf($class, $annotation);
		$this->assertInternalType(PHPUnitType::TYPE_STRING, $annotation->getParam1());
		$this->assertInternalType(PHPUnitType::TYPE_STRING, $annotation->getParam2());
		$this->assertInternalType(PHPUnitType::TYPE_STRING, $annotation->getParam3());
		$this->assertEquals($annotation->getParam1(), 'param1');
		$this->assertEquals($annotation->getParam2(), 'param2');
		$this->assertEquals($annotation->getParam3(), 'param3');
	}
	
	/**
	 * Test if the `DoubleParameterAnnotation` class has correct values.
	 */
	public function testDoubleParameterAnnotation() {
		
		$class = 'com\mohiva\test\resources\common\lang\annotations\DoubleParameter';
		$reflection = new ReflectionClass(self::TEST_CLASS);
		$iterator = $reflection
			->getAnnotationList()
			->getAnnotations(DoubleParameter::NAME)
			->getIterator();
		
		/* @var \com\mohiva\test\resources\common\lang\annotations\DoubleParameter $annotation */
		$annotation = $iterator->current();
		$this->assertInstanceOf($class, $annotation);
		$this->assertInternalType(PHPUnitType::TYPE_STRING, $annotation->getParam1());
		$this->assertInternalType(PHPUnitType::TYPE_STRING, $annotation->getParam2());
		$this->assertEquals($annotation->getParam1(), "pa\"r\"am1\"");
		$this->assertEquals($annotation->getParam2(), "param\"2");
		
		$iterator->next();
		$annotation = $iterator->current();
		$this->assertInstanceOf($class, $annotation);
		$this->assertInternalType(PHPUnitType::TYPE_STRING, $annotation->getParam1());
		$this->assertInternalType(PHPUnitType::TYPE_STRING, $annotation->getParam2());
		$this->assertEquals($annotation->getParam1(), "pa\\'r\\'am1\"");
		$this->assertEquals($annotation->getParam2(), "param\\'2");
		
		$iterator->next();
		$annotation = $iterator->current();
		$this->assertInstanceOf($class, $annotation);
		$this->assertInternalType(PHPUnitType::TYPE_STRING, $annotation->getParam1());
		$this->assertInternalType(PHPUnitType::TYPE_STRING, $annotation->getParam2());
		$this->assertEquals($annotation->getParam1(), "pa'r'am1");
		$this->assertEquals($annotation->getParam2(), 'param"2');
		
		$iterator->next();
		$annotation = $iterator->current();
		$this->assertInstanceOf($class, $annotation);
		$this->assertInternalType(PHPUnitType::TYPE_STRING, $annotation->getParam1());
		$this->assertInternalType(PHPUnitType::TYPE_STRING, $annotation->getParam2());
		$this->assertEquals($annotation->getParam1(), 'pa\'r\'am1\'');
		$this->assertEquals($annotation->getParam2(), 'param\'2');
		
		$iterator->next();
		$annotation = $iterator->current();
		$this->assertInstanceOf($class, $annotation);
		$this->assertInternalType(PHPUnitType::TYPE_STRING, $annotation->getParam1());
		$this->assertInternalType(PHPUnitType::TYPE_STRING, $annotation->getParam2());
		$this->assertEquals($annotation->getParam1(), 'pa\"r\"am1\'');
		$this->assertEquals($annotation->getParam2(), 'param\"2');
	}
	
	/**
	 * Test if the `FuncGetArgsAnnotation` class has correct values.
	 */
	public function testFuncGetArgsAnnotation() {
		
		$class = 'com\mohiva\test\resources\common\lang\annotations\FuncGetArgs';
		$reflection = new ReflectionProperty(self::TEST_CLASS, 'funcGetArgs');
		$iterator = $reflection
			->getAnnotationList()
			->getAnnotations(FuncGetArgs::NAME)
			->getIterator();
		
		/* @var \com\mohiva\test\resources\common\lang\annotations\FuncGetArgs $annotation */
		$annotation = $iterator->current();
		$this->assertInstanceOf($class, $annotation);
		$this->assertInternalType(PHPUnitType::TYPE_ARRAY, $annotation->getValues());
		$this->assertSame($annotation->getValues(), range(1, 10));
		
		$iterator->next();
		$annotation = $iterator->current();
		$this->assertInstanceOf($class, $annotation);
		$this->assertInternalType(PHPUnitType::TYPE_ARRAY, $annotation->getValues());
		$this->assertSame($annotation->getValues(), range(1, 5));
	}
	
	/**
	 * Test if the `NestedAnnotation` class has correct values.
	 */
	public function testNestedAnnotation() {
		
		$class = 'com\mohiva\test\resources\common\lang\annotations\Nested';
		$nonParameterClass = 'com\mohiva\test\resources\common\lang\annotations\NonParameter';
		$defaultValueClass = 'com\mohiva\test\resources\common\lang\annotations\DefaultValue';
		$arrayValueClass = 'com\mohiva\test\resources\common\lang\annotations\ArrayValue';
		$objectValueClass = 'com\mohiva\test\resources\common\lang\annotations\ObjectValue';
		
		$reflection = new ReflectionProperty(self::TEST_CLASS, 'nested');
		$iterator = $reflection
			->getAnnotationList()
			->getAnnotations(Nested::NAME)
			->getIterator();
		
		/* @var \com\mohiva\test\resources\common\lang\annotations\Nested $annotation */
		$annotation = $iterator->current();
		$this->assertInstanceOf($class, $annotation);
		$this->assertInstanceOf($nonParameterClass, $annotation->getParam1());
		$this->assertInstanceOf($defaultValueClass, $annotation->getParam2());
		$this->assertInstanceOf($arrayValueClass, $annotation->getParam3());
		$this->assertInstanceOf($objectValueClass, $annotation->getParam4());
		$this->assertInternalType(PHPUnitType::TYPE_ARRAY, $annotation->getParam5());
		$this->assertInstanceOf('\stdClass', $annotation->getParam6());
		$this->assertTrue($annotation->getParam7());
		$this->assertFalse($annotation->getParam8());
		$this->assertNull($annotation->getParam9());
		
		// Test array parameter
		$this->assertSame($annotation->getParam5(), range(1, 5));
		
		// Test object parameter
		$object = new \stdClass;
		$object->key1 = 1;
		$object->key2 = 2;
		$object->key3 = 3;
		$this->assertEquals($annotation->getParam6(), $object);
		
		// Test nested DefaultValue annotations
		$defaultValue = $annotation->getParam2();
		/* @var \com\mohiva\test\resources\common\lang\annotations\DefaultValue $defaultValue */
		$this->assertEquals($defaultValue->getParam1(), 'param1');
		$this->assertEquals($defaultValue->getParam2(), 'param2');
		$this->assertEquals($defaultValue->getParam3(), 'param3');
		
		// Test nested ArrayValue annotations
		$array = array('key' => 'value', 1, 3, 4, 'test', 1 => 3, false, null);
		$arrayValue = $annotation->getParam3();
		/* @var \com\mohiva\test\resources\common\lang\annotations\ArrayValue $arrayValue */
		$this->assertSame($arrayValue->getParam1(), $array);
		
		// Test nested ObjectValue annotations
		$object = new \stdClass();
		$object->key1 = 'value1';
		$object->key2 = 2;
		$object->key3 = true;
		$object->key4 = false;
		$object->key5 = null;
		$objectValue = $annotation->getParam4();
		/* @var \com\mohiva\test\resources\common\lang\annotations\ObjectValue $objectValue */
		$this->assertEquals($objectValue->getParam1(), $object);
	}
	
	/**
	 * Test if the `NonParameterAnnotation` class has correct values.
	 */
	public function testNonParameterAnnotation() {
		
		$class = 'com\mohiva\test\resources\common\lang\annotations\NonParameter';
		$reflection = new ReflectionProperty(self::TEST_CLASS, 'nonParameter');
		$annotation = $reflection
			->getAnnotationList()
			->getAnnotations(NonParameter::NAME)
			->getIterator()
			->current();
		
		$this->assertInstanceOf($class, $annotation);
	}
	
	/**
	 * Test if the `ObjectValueAnnotation` class has correct values.
	 */
	public function testObjectValueAnnotation() {
		
		$object = new \stdClass;
		$object->key1 = 'value1';
		$object->key2 = 2;
		$object->key3 = true;
		$object->key4 = false;
		$object->key5 = null;
		
		$class = 'com\mohiva\test\resources\common\lang\annotations\ObjectValue';
		$reflection = new ReflectionProperty(self::TEST_CLASS, 'objectValue');
		$annotation = $reflection
			->getAnnotationList()
			->getAnnotations(ObjectValue::NAME)
			->getIterator()
			->current();
		
		/* @var \com\mohiva\test\resources\common\lang\annotations\ObjectValue $annotation */
		$this->assertInstanceOf($class, $annotation);
		$this->assertInstanceOf('\stdClass', $annotation->getParam1());
		$this->assertEquals($object, $annotation->getParam1());
	}
	
	/**
	 * Test if the `SingleParameterAnnotation` class has correct values.
	 */
	public function testSingleParameterAnnotation() {
		
		$class = 'com\mohiva\test\resources\common\lang\annotations\SingleParameter';
		$reflection = new ReflectionProperty(self::TEST_CLASS, 'singleParameter');
		$iterator = $reflection
			->getAnnotationList()
			->getAnnotations(SingleParameter::NAME)
			->getIterator();
		
		/* @var \com\mohiva\test\resources\common\lang\annotations\SingleParameter $annotation */
		$annotation = $iterator->current();
		$this->assertInstanceOf($class, $annotation);
		$this->assertSame($annotation->getParam1(), 'param1');
		
		$iterator->next();
		$annotation = $iterator->current();
		$this->assertInstanceOf($class, $annotation);
		$this->assertSame($annotation->getParam1(), 'param1');
	}
	
	/**
	 * Test if the `ValueAnnotation` class has correct values.
	 */
	public function testValueAnnotation() {
				
		$nonParameter = 'com\mohiva\test\resources\common\lang\annotations\NonParameter';
		
		$class = 'com\mohiva\test\resources\common\lang\annotations\Value';
		$reflection = new ReflectionMethod(self::TEST_CLASS, 'value');
		$annotation = $reflection
			->getAnnotationList()
			->getAnnotations(Value::NAME)
			->getIterator()
			->current();
		
		/* @var \com\mohiva\test\resources\common\lang\annotations\Value $annotation */
		$this->assertInstanceOf($class, $annotation);
		$this->assertTrue($annotation->getParam1());
		$this->assertFalse($annotation->getParam2());
		$this->assertNull($annotation->getParam3());
		$this->assertSame($annotation->getParam4(), -1);
		$this->assertSame($annotation->getParam5(), 1);
		$this->assertSame($annotation->getParam6(), -1.1);
		$this->assertSame($annotation->getParam7(), 1.1);
		$this->assertSame($annotation->getParam8(), 'test');
		$this->assertSame($annotation->getParam9(), "test\"test");
		$this->assertSame($annotation->getParam10(), 'test\'test');
		$this->assertEquals($annotation->getParam11(), new \stdClass);
		$this->assertSame($annotation->getParam12(), array());
		$this->assertInstanceOf($nonParameter, $annotation->getParam13());
	}
	
	/**
	 * Test if the parser can load annotations with the help of the class namespace:
	 * `
	 * namespace com\mohiva\test\resources\core;
	 *
	 * @annotations\FuncGetArgs(1, 2, 3, 4, 5, 6, 7, 8, 9, 10)
	 * `
	 */
	public function testClassNamespace() {
		
		$class = 'com\mohiva\test\resources\common\lang\annotations\FuncGetArgs';
		
		$reflection = new ReflectionMethod(self::TEST_CLASS, 'classNamespace');
		$annotation = $reflection
			->getAnnotationList()
			->getAnnotations(FuncGetArgs::NAME)
			->getIterator()
			->current();
		
		/* @var \com\mohiva\test\resources\common\lang\annotations\FuncGetArgs $annotation */
		$this->assertInstanceOf($class, $annotation);
		$this->assertInternalType(PHPUnitType::TYPE_ARRAY, $annotation->getValues());
		$this->assertSame($annotation->getValues(), range(1, 10));
	}
	
	/**
	 * Test if the parser can load annotations with aliased namespaces in the form:
	 * `
	 * use com\mohiva\test\resources\common\lang\annotations as nsAlias;
	 *
	 * @nsAlias\FuncGetArgs(1, 2, 3, 4, 5, 6, 7, 8, 9, 10)
	 * `
	 */
	public function testNamespaceAlias() {
		
		$class = 'com\mohiva\test\resources\common\lang\annotations\FuncGetArgs';
		
		$reflection = new ReflectionMethod(self::TEST_CLASS, 'namespaceAlias');
		$annotation = $reflection
			->getAnnotationList()
			->getAnnotations(FuncGetArgs::NAME)
			->getIterator()
			->current();
		
		/* @var \com\mohiva\test\resources\common\lang\annotations\FuncGetArgs $annotation */
		$this->assertInstanceOf($class, $annotation);
		$this->assertInternalType(PHPUnitType::TYPE_ARRAY, $annotation->getValues());
		$this->assertSame($annotation->getValues(), range(1, 10));
	}
	
	/**
	 * Test if the parser can load aliased annotations in the form:
	 * `
	 * use com\mohiva\test\resources\common\lang\annotations\FuncGetArgs as FuncGetArgsAlias;
	 *
	 * @FuncGetArgsAlias(1, 2, 3, 4, 5, 6, 7, 8, 9, 10)
	 * `
	 */
	public function testClassAlias() {
		
		$class = 'com\mohiva\test\resources\common\lang\annotations\FuncGetArgs';
		
		$reflection = new ReflectionMethod(self::TEST_CLASS, 'classAlias');
		$annotation = $reflection
			->getAnnotationList()
			->getAnnotations(FuncGetArgs::NAME)
			->getIterator()
			->current();
		
		/* @var \com\mohiva\test\resources\common\lang\annotations\FuncGetArgs $annotation */
		$this->assertInstanceOf($class, $annotation);
		$this->assertInternalType(PHPUnitType::TYPE_ARRAY, $annotation->getValues());
		$this->assertSame($annotation->getValues(), range(1, 10));
	}
	
	/**
	 * Test if an annotation with a fully qualified name can be loaded. 
	 */
	public function testFullyQualifiedName() {
		
		$class = 'com\mohiva\test\resources\common\lang\annotations\FuncGetArgs';
		
		$reflection = new ReflectionMethod(self::TEST_CLASS, 'fullyQualifiedName');
		$annotation = $reflection
			->getAnnotationList()
			->getAnnotations(FuncGetArgs::NAME)
			->getIterator()
			->current();
		
		/* @var \com\mohiva\test\resources\common\lang\annotations\FuncGetArgs $annotation */
		$this->assertInstanceOf($class, $annotation);
		$this->assertInternalType(PHPUnitType::TYPE_ARRAY, $annotation->getValues());
		$this->assertSame($annotation->getValues(), range(1, 10));
	}	
}
