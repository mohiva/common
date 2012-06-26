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

use Exception;
use com\mohiva\test\common\Bootstrap;
use com\mohiva\common\io\exceptions\ClassNotFoundException;
use com\mohiva\common\io\exceptions\MalformedNameException;

/**
 * Abstract unit test case for the `AbstractClassLoader` class.
 *
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Test
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
abstract class AbstractClassLoaderTest extends \PHPUnit_Framework_TestCase {

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
	const MIXED_NS_CLASS     = '\com\mohiva\test\resources\common\io\Pre_ClassFixture';
	const PRE_NS_CLASS       = '\com_mohiva_test_resources_common_io_Pre_Namespace_ClassFixture';

	/**
	 * The loader instance.
	 *
	 * @var \com\mohiva\common\io\ClassLoader
	 */
	protected $loader = null;

	/**
	 * A list with invalid class names.
	 *
	 * @var array
	 */
	private $invalidClassNames = array(
		'../../etc/passwd'
	);

	/**
	 * Check if the `load` method throws a `MalformedNameException` if the class name is invalid.
	 */
	public function testLoadThrowsMalformedNameException() {

		foreach($this->invalidClassNames as $className) {
			try {
				$this->loader->load($className);
				$this->fail('MalformedNameException expected');
			} catch (MalformedNameException $e) {
				$this->assertFalse(class_exists($className, false));
			}
		}
	}

	/**
	 * Check if the loader is PSR-0 compatible.
	 */
	public function testPsr0Compatibility() {

		$classNames = array(
			self::VALID_CLASS,
			self::MIXED_NS_CLASS,
			self::PRE_NS_CLASS
		);

		foreach($classNames as $className) {
			try {
				$this->loader->load($className);
				$this->assertTrue(class_exists($className, false));
			} catch (Exception $e) {
				$message = $e->getPrevious() ? $e->getPrevious()->getMessage() : $e->getMessage();
				$this->fail($message);
			}
		}
	}

	/**
	 * Test if can load a class from include path.
	 */
	public function testLoadClass() {

		try {
			$this->loader->load(self::VALID_CLASS);
			$this->assertTrue(class_exists(self::VALID_CLASS, false));
		} catch (Exception $e) {
			$this->fail($e->getMessage());
		}
	}

	/**
	 * Test if can load a interface from include path.
	 */
	public function testLoadInterface() {

		try {
			$this->loader->load(self::VALID_INTERFACE);
			$this->assertTrue(interface_exists(self::VALID_INTERFACE, false));
		} catch (Exception $e) {
			$this->fail($e->getMessage());
		}
	}

	/**
	 * Test if the loader throws a `FileNotFoundException` if the file which matches the class name cannot be
	 * found in file system.
	 */
	public function testForFileNotFoundException() {

		try {
			$this->loader->load(self::NOT_EXISTING_CLASS);
			$this->fail('ClassNotFoundException was expected but never thrown');
		} catch (ClassNotFoundException $e) {
			$this->assertInstanceOf('com\mohiva\common\io\exceptions\FileNotFoundException', $e->getPrevious());
		} catch (Exception $e) {
			$this->fail($e->getMessage());
		}
	}

	/**
	 * Test if the loader throws a `FileNotFoundException` if the file which matches the class name isn't readable.
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
			$this->loader->load(self::NOT_READABLE_CLASS);
			chmod($file, $oldPerms);
			$this->fail('ClassNotFoundException was expected but never thrown');
		} catch (ClassNotFoundException $e) {
			chmod($file, $oldPerms);
			$this->assertInstanceOf('com\mohiva\common\io\exceptions\FileNotFoundException', $e->getPrevious());
		} catch (Exception $e) {
			chmod($file, $oldPerms);
			$this->fail($e->getMessage());
		}
	}

	/**
	 * Test if the loader throws a `MissingDeclarationException` if the class isn't declared in the file
	 * which matches the class name.
	 */
	public function testForMissingDeclarationException() {

		try {
			$this->loader->load(self::NOT_DECLARED);
		} catch (ClassNotFoundException $e) {
			$this->assertInstanceOf('com\mohiva\common\io\exceptions\MissingDeclarationException',
				$e->getPrevious()
			);
		} catch (Exception $e) {
			$this->fail($e->getMessage());
		}
	}
}
