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
namespace com\mohiva\test\common\cache\containers;

use com\mohiva\common\lang\AnnotationParser;
use com\mohiva\common\lang\ReflectionDocComment;
use com\mohiva\common\io\DefaultClassLoader;
use com\mohiva\common\io\TempResourceContainer;
use com\mohiva\common\io\TempFileResource;
use com\mohiva\common\crypto\Hash;
use com\mohiva\common\cache\HashKey;
use com\mohiva\common\cache\adapters\ResourceAdapter;
use com\mohiva\common\cache\containers\AnnotationContainer;

/**
 * Unit test case for the `AnnotationContainer` class.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Test
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2011 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class AnnotationContainerTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * The path to the class containing the annotations.
	 * 
	 * @var string
	 */
	const TEST_CLASS = '\com\mohiva\test\resources\common\lang\AnnotationTest';
	
	/**
	 * Test if can store an annotation list.
	 */
	public function testStoreAnnotationList() {
		
		/* @var \com\mohiva\common\lang\AnnotationReflector $class */
		$loader = new DefaultClassLoader();
		$class = $loader->loadClass(self::TEST_CLASS);
		$docReflector = new ReflectionDocComment($class);
		$annotationList = $docReflector->getAnnotationList();
		
		$key = new HashKey(Hash::ALGO_SHA1, 'php://temp');
		$adapter = new ResourceAdapter(new TempResourceContainer(TempFileResource::TYPE));
		$container = new AnnotationContainer($adapter, $key);
		$container->store($class->getDocComment(), $annotationList);
		
		$this->assertTrue($adapter->exists($key));
	}
	
	/**
	 * Test if a previous stored annotation list exists.
	 */
	public function testAnnotationListExists() {
		
		/* @var \com\mohiva\common\lang\AnnotationReflector $class */
		$loader = new DefaultClassLoader();
		$class = $loader->loadClass(self::TEST_CLASS);
		$docReflector = new ReflectionDocComment($class);
		$annotationList = $docReflector->getAnnotationList();
		
		$key = new HashKey(Hash::ALGO_SHA1, 'php://temp');
		$adapter = new ResourceAdapter(new TempResourceContainer(TempFileResource::TYPE));
		$container = new AnnotationContainer($adapter, $key);
		$container->store($class->getDocComment(), $annotationList);
		
		$this->assertTrue($container->exists($class->getDocComment()));
	}
	
	/**
	 * Test if can fetch a previous stored annotation list.
	 */
	public function testFetchAnnotationList() {
		
		/* @var \com\mohiva\common\lang\AnnotationReflector $class */
		$loader = new DefaultClassLoader();
		$class = $loader->loadClass(self::TEST_CLASS);
		$docReflector = new ReflectionDocComment($class);
		$annotationList = $docReflector->getAnnotationList();
		
		$key = new HashKey(Hash::ALGO_SHA1, 'php://temp');
		$adapter = new ResourceAdapter(new TempResourceContainer(TempFileResource::TYPE));
		$container = new AnnotationContainer($adapter, $key);
		$container->store($class->getDocComment(), $annotationList);
		
		$this->assertEquals($container->fetch($class->getDocComment()), $annotationList);
	}
	
	/**
	 * Test if can remove a previous stored annotation list.
	 */
	public function testRemoveAnnotationList() {
		
		/* @var \com\mohiva\common\lang\AnnotationReflector $class */
		$loader = new DefaultClassLoader();
		$class = $loader->loadClass(self::TEST_CLASS);
		$docReflector = new ReflectionDocComment($class);
		$annotationList = $docReflector->getAnnotationList();
		
		$key = new HashKey(Hash::ALGO_SHA1, 'php://temp');
		$adapter = new ResourceAdapter(new TempResourceContainer(TempFileResource::TYPE));
		$container = new AnnotationContainer($adapter, $key);
		$container->store($class->getDocComment(), $annotationList);
		$container->remove($class->getDocComment());
		
		$this->assertFalse($container->exists($class->getDocComment()));
	}
}
