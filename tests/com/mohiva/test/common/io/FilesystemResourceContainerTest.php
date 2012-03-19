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

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use com\mohiva\common\io\FilesystemResourceContainer;
use com\mohiva\common\io\DefaultResourceLoader;
use com\mohiva\common\io\FileResource;

/**
 * Unit test case for the `FilesystemResourceContainer` class.
 *
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Test
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class FilesystemResourceContainerTest extends \PHPUnit_Framework_TestCase {

	/**
	 * The path to the temporary directory.
	 */
	private $tempDir = null;

	/**
	 * Setup the test case.
	 */
	public function setUp() {

		$this->tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'mohiva-FilesystemResourceContainerTest';
		if (is_dir($this->tempDir)) $this->removeTempDir($this->tempDir);
		mkdir($this->tempDir, 0777, true);
	}

	/**
	 * Tear down the test case.
	 */
	public function tearDown() {

		if (is_dir($this->tempDir)) $this->removeTempDir($this->tempDir);
	}

	/**
	 * Test the `setResourceLoader` and `getResourceLoader` accessors.
	 */
	public function testResourceLoaderAccessors() {

		$container = new FilesystemResourceContainer($this->tempDir, FileResource::TYPE);

		// Test the default loader, if no one was set before
		$this->assertInstanceOf('\com\mohiva\common\io\DefaultResourceLoader', $container->getResourceLoader());

		// Test the accessors with a stub implementation of the ResourceLoader interface
		/** @var $stub \com\mohiva\common\io\ResourceLoader */
		$stub = $this->getMock('\com\mohiva\common\io\ResourceLoader');
		$container->setResourceLoader($stub);
		$this->assertSame($stub, $container->getResourceLoader());
	}

	/**
	 * Test if the `exists` method return false if a doesn't resource exists.
	 */
	public function testExistsReturnFalse() {

		$container = new FilesystemResourceContainer($this->tempDir, FileResource::TYPE);

		$this->assertFalse($container->exists('test.txt'));
	}

	/**
	 * Test if the `exists` method return true if a resource exists.
	 */
	public function testExistsReturnTrue() {

		$container = new FilesystemResourceContainer($this->tempDir, FileResource::TYPE);

		$file = $this->tempDir . DIRECTORY_SEPARATOR . 'test.txt';
		file_put_contents($file, 'test');

		$this->assertTrue($container->exists('test.txt'));
	}

	/**
	 * Test if the `create` method can create a new resource.
	 */
	public function testCreateResource() {

		$container = new FilesystemResourceContainer($this->tempDir, FileResource::TYPE);
		$resource = $container->create('test.txt', 'test');

		$this->assertSame('test', $resource->read());
	}

	/**
	 * Test if the `get` method returns a new resource.
	 */
	public function testGetReturnsNewResource() {

		$container = new FilesystemResourceContainer($this->tempDir, FileResource::TYPE);
		$resource = $container->get('test.txt');

		$this->assertInstanceOf('\com\mohiva\common\io\FileResource', $resource);
	}

	/**
	 * Test if the `get` method returns an existing resource.
	 */
	public function testGetReturnsExistingResource() {

		$file = $this->tempDir . DIRECTORY_SEPARATOR . 'test.txt';
		file_put_contents($file, 'test');

		$container = new FilesystemResourceContainer($this->tempDir, FileResource::TYPE);
		$resource = $container->get('test.txt');

		$this->assertInstanceOf('\com\mohiva\common\io\FileResource', $resource);
		$this->assertSame('test', $resource->read());
	}

	/**
	 * Test if the `remove` method removes a resource.
	 */
	public function testRemoveResource() {

		$file = $this->tempDir . DIRECTORY_SEPARATOR . 'test.txt';
		file_put_contents($file, 'test');

		$this->assertTrue(file_exists($file));

		$container = new FilesystemResourceContainer($this->tempDir, FileResource::TYPE);
		$container->remove('test.txt');

		$this->assertFalse(file_exists($file));
	}

	/**
	 * Test if the `current` method returns the current resource.
	 */
	public function testCurrentReturnCurrentResource() {

		$container = new FilesystemResourceContainer($this->tempDir, FileResource::TYPE);
		$container->create('test.txt', 'test');
		$container->rewind();

		/* @var \com\mohiva\common\io\FileResource $expected */
		/* @var \com\mohiva\common\io\FileResource $actual */
		$expected = $container->get('test.txt');
		$actual = $container->current();

		$this->assertSame(
			$expected->getHandle()->getPathname(),
			$actual->getHandle()->getPathname()
		);
	}

	/**
	 * Test if the `current` method return null if no resource exists.
	 */
	public function testCurrentReturnNullOnEmptyDirectory() {

		$container = new FilesystemResourceContainer($this->tempDir, FileResource::TYPE);

		$this->assertNull($container->current());
	}

	/**
	 * Test if the `key` method returns the current pathname.
	 */
	public function testKeyReturnCurrentPathname() {

		$container = new FilesystemResourceContainer($this->tempDir, FileResource::TYPE);
		$container->create('test.txt', 'test');
		$container->rewind();

		$this->assertSame(
			$this->tempDir . DIRECTORY_SEPARATOR . 'test.txt',
			$container->key()
		);
	}

	/**
	 * Test if the `key` method return null if no resource exists.
	 */
	public function testKeyReturnNullOnEmptyDirectory() {

		$container = new FilesystemResourceContainer($this->tempDir, FileResource::TYPE);

		$this->assertNull($container->key());
	}

	/**
	 * Test if the `next` method moves to the next resource.
	 */
	public function testNextMoveForwardToNextResource() {

		$container = new FilesystemResourceContainer($this->tempDir, FileResource::TYPE);
		$container->create('test1.txt', 'test');
		$container->create('test2.txt', 'test');
		$container->rewind();
		$current = $container->key();
		$container->next();

		$this->assertNotSame(
			$current,
			$container->key()
		);
	}

	/**
	 * Test if the `rewind` moves back to the first resource.
	 */
	public function testRewindMoveToTheFirstResource() {

		$container = new FilesystemResourceContainer($this->tempDir, FileResource::TYPE);
		$container->create('test1.txt', 'test');
		$container->create('test2.txt', 'test');
		$container->rewind();

		// If pointer is set to the first then it must iterate over 2 files
		$cnt = 0;
		foreach ($container as $value) {
			$cnt++;
		}

		$this->assertSame(2, $cnt);
	}

	/**
	 * Test if the `valid` method returns false if no resource exists.
	 */
	public function testValidReturnsFalse() {

		$container = new FilesystemResourceContainer($this->tempDir, FileResource::TYPE);

		$this->assertFalse($container->valid());
	}

	/**
	 * Test if the `valid` method returns true if a resource exists.
	 */
	public function testValidReturnsTrue() {

		$container = new FilesystemResourceContainer($this->tempDir, FileResource::TYPE);
		$container->create('test1.txt', 'test');
		$container->rewind();

		$this->assertTrue($container->valid());
	}

	/**
	 * Remove the temporary test directory.
	 *
	 * @param string $dir The dir to remove.
	 */
	private function removeTempDir($dir) {

		$dirIt = new RecursiveDirectoryIterator($dir);
		$recIt = new RecursiveIteratorIterator($dirIt, RecursiveIteratorIterator::CHILD_FIRST);
		foreach ($recIt as $file) { /* @var \SplFileInfo $file */
			if ($file->getFilename() == '.' || $file->getFilename() == '..') {
				continue;
			}

			if ($file->isDir()) {
				rmdir($file->getPathname());
			} else {
				unlink($file->getPathname());
			}
		}
		rmdir($dir);
	}
}
