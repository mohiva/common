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
namespace com\mohiva\test\common\io;

use com\mohiva\test\common\Bootstrap;
use com\mohiva\common\io\DefaultClassLoader;
use com\mohiva\common\io\exceptions\ClassNotFoundException;
use com\mohiva\common\io\exceptions\MalformedNameException;

/**
 * Unit test case for the `DefaultClassLoaderResource` class.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Test
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class DefaultClassLoaderTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * Path to the fixtures to test.
	 * 
	 * @var string
	 */
	const VALID_CLASS        = '\com\mohiva\test\resources\common\io\ValidClassFixture';
	const VALID_INTERFACE    = '\com\mohiva\test\resources\common\io\ValidInterfaceFixture';
	const NOT_EXISTING_CLASS = '\com\mohiva\test\resources\common\io\NotExistingFixture';
	const NOT_READABLE_CLASS = '\com\mohiva\test\resources\common\io\NotReadableClassFixture';
	const NOT_DECLARED       = '\com\mohiva\test\resources\common\io\NotDeclaredFixture';
	const MALFORMED_CLASS    = '../../etc/passwd';
	
	/**
	 * Test if can load a class from include path.
	 */
	public function testLoadClass() {
		
		try {
			$loader = new DefaultClassLoader();
			$class = $loader->load(self::VALID_CLASS);
			$this->assertInstanceOf('\com\mohiva\common\lang\ReflectionClass', $class);
		} catch (\Exception $e) {
			$this->fail($e->getMessage());
		}
	}
	
	/**
	 * Test if can load a interface from include path.
	 */
	public function testLoadInterface() {
		
		try {
			$loader = new DefaultClassLoader();
			$class = $loader->load(self::VALID_INTERFACE);
			$this->assertInstanceOf('\com\mohiva\common\lang\ReflectionClass', $class);
		} catch (\Exception $e) {
			$this->fail($e->getMessage());
		}
	}
	
	/**
	 * Test if can load classes in the form `Pre_Namespace_ClassFixture`.
	 */
	public function testLoadPreNamespaceClassFromPath() {
		
		try {
			$loader = new DefaultClassLoader();
			$class = $loader->load('\com_mohiva_test_resources_common_io_Pre_Namespace_ClassFixture');
			$this->assertInstanceOf('\com\mohiva\common\lang\ReflectionClass', $class);
		} catch (\Exception $e) {
			$this->fail($e->getMessage());
		}
	}
	
	/**
	 * Test if a `MalformedNameException` will be thrown on invalid class name.
	 * 
	 * @expectedException \com\mohiva\common\io\exceptions\MalformedNameException
	 */
	public function testForMalformedNameException() {
		
		$loader = new DefaultClassLoader();
		$loader->load(self::MALFORMED_CLASS);
	}
	
	/**
	 * Test if a `FileNotFoundException` will be thrown on not existing file.
	 */
	public function testForFileNotFoundException() {
		
		try {
			$loader = new DefaultClassLoader();
			$loader->load(self::NOT_EXISTING_CLASS);
			$this->fail('ClassNotFoundException was expected but never thrown');
		} catch (ClassNotFoundException $e) {
			$this->assertInstanceOf('com\mohiva\common\io\exceptions\FileNotFoundException', $e->getPrevious());
		} catch (\Exception $e) {
			$this->fail($e->getMessage());
		}
	}
	
	/**
	 * Test if a `FileNotFoundException` will be thrown on not readable file.
	 */
	public function testForFileNotFoundExceptionOnNotReadableFile() {
		
		clearstatcache();
		$file = Bootstrap::$resourceDir . '/common/io/NotReadableFileFixture.php';
		$oldPerms = fileperms($file);
		chmod($file, 0333);
		
		// Check if can change permissions for the test
		clearstatcache();
		if (substr(sprintf('%o', fileperms($file)), -4) !== '0333') {
			$this->markTestSkipped('Cannot change file permissions');
		}
		
		try {
			$loader = new DefaultClassLoader();
			$loader->load(self::NOT_READABLE_CLASS, false);
			chmod($file, $oldPerms);
			$this->fail('ClassNotFoundException was expected but never thrown');
		} catch (ClassNotFoundException $e) {
			chmod($file, $oldPerms);
			$this->assertInstanceOf('com\mohiva\common\io\exceptions\FileNotFoundException', $e->getPrevious());
		} catch (\Exception $e) {
			chmod($file, $oldPerms);
			$this->fail($e->getMessage());
		}
	}
	
	/**
	 * Test if a `MissingDeclarationException` will be thrown on not declared class.
	 */
	public function testForMissingDeclarationException() {
		
		try {
			$loader = new DefaultClassLoader();
			$loader->load(self::NOT_DECLARED, false);
		} catch (ClassNotFoundException $e) {
			$this->assertInstanceOf('com\mohiva\common\io\exceptions\MissingDeclarationException', 
				$e->getPrevious()
			);
		} catch (\Exception $e) {
			$this->fail($e->getMessage());
		}
	}
}
