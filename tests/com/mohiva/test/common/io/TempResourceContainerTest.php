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

use com\mohiva\common\io\TempResourceContainer;
use com\mohiva\common\io\DefaultResourceLoader;
use com\mohiva\common\io\TempFileResource;

/**
 * Unit test case for the `TempResourceContainer` class.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Test
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class TempResourceContainerTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * Test the `setResourceLoader` and `getResourceLoader` accessors.
	 */
	public function testResourceLoaderAccessors() {
		
		$container = new TempResourceContainer(TempFileResource::TYPE);
		
		// Test the default loader, if no one was set before
		$this->assertInstanceOf('\com\mohiva\common\io\DefaultResourceLoader', $container->getResourceLoader());
		
		// Test the accessors with a stub implementation of the ResourceLoader interface
		/** @var $stub \com\mohiva\common\io\ResourceLoader */
		$stub = $this->getMock('\com\mohiva\common\io\ResourceLoader');
		$container->setResourceLoader($stub);
		$this->assertSame($stub, $container->getResourceLoader());
	}
	
	/**
	 * Test if the `exists` method return false if a temporary resource doesn't exists.
	 */
	public function testExistsReturnFalse() {
		
		$container = new TempResourceContainer(TempFileResource::TYPE);
		
		$this->assertFalse($container->exists('php://temp/test'));
	}
	
	/**
	 * Test if the `exists` method return true if a temporary resource exists.
	 */
	public function testExistsReturnTrue() {
		
		$container = new TempResourceContainer(TempFileResource::TYPE);	
		$container->create('php://temp/test', 'test');
		
		$this->assertTrue($container->exists('php://temp/test'));
	}
	
	/**
	 * Test if the `create` method can create a new resource.
	 */
	public function testCreateResource() {
		
		$container = new TempResourceContainer(TempFileResource::TYPE);
		$resource = $container->create('php://temp/test', 'test');
		
		$this->assertSame('test', $resource->read());
	}
	
	/**
	 * Test if the `get` method return a new resource.
	 */
	public function testGetReturnsNewResource() {
		
		$container = new TempResourceContainer(TempFileResource::TYPE);
		$resource = $container->get('php://temp/test');
		
		$this->assertInstanceOf('\com\mohiva\common\io\TempFileResource', $resource);
		$this->assertSame('', $resource->read());
	}
	
	/**
	 * Test if the `get` method return a previous set resource.
	 */
	public function testGetReturnsPreviousSetResource() {
		
		$container = new TempResourceContainer(TempFileResource::TYPE);
		$container->create('php://temp/test', 'test');
		$resource = $container->get('php://temp/test');
		
		$this->assertInstanceOf('\com\mohiva\common\io\TempFileResource', $resource);
		$this->assertSame('test', $resource->read());
	}
	
	/**
	 * Test if the `remove` return false if the resource doesn't exists.
	 */
	public function testRemoveReturnFalseOnNonExistingResource() {
		
		$container = new TempResourceContainer(TempFileResource::TYPE);
		
		$this->assertFalse($container->remove('php://temp/test'));
	}
	
	/**
	 * Test if the `remove` return true if the resource was removed.
	 */
	public function testRemoveReturnTrueIfTheResourceWasRemoved() {
		
		$container = new TempResourceContainer(TempFileResource::TYPE);
		$container->create('php://temp/test', 'test');
		
		$this->assertTrue($container->remove('php://temp/test'));
	}
	
	/**
	 * Test if the `remove` method throws an exception if
	 * the resource doesn't exists.
	 * 
	 * @expectedException \com\mohiva\common\io\exceptions\RemoveException
	 */
	public function testRemoveThrowsRemoveException() {
		
		$container = new TempResourceContainer(TempFileResource::TYPE);
		$container->remove('php://temp/test', false);
	}
	
	/**
	 * Test if the `current` method returns the current resource.
	 */
	public function testCurrentReturnCurrentResource() {
		
		$container = new TempResourceContainer(TempFileResource::TYPE);
		$container->create('php://temp/test', 'test');
		$container->rewind();
		
		$expected = $container->get('php://temp/test');
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
		
		$container = new TempResourceContainer(TempFileResource::TYPE);
		
		$this->assertNull($container->current());
	}
	
	/**
	 * Test if the `key` method returns the current pathname.
	 */
	public function testKeyReturnCurrentPathname() {
		
		$container = new TempResourceContainer(TempFileResource::TYPE);
		$container->create('php://temp/test', 'test');
		$container->rewind();
		
		$this->assertSame(
			'php://temp/test',
			$container->key()
		);
	}
	
	/**
	 * Test if the `key` method return null if no resource exists.
	 */
	public function testKeyReturnNullOnEmptyDirectory() {
		
		$container = new TempResourceContainer(TempFileResource::TYPE);
		
		$this->assertNull($container->key());
	}
	
	/**
	 * Test if the `next` method moves to the next resource.
	 */
	public function testNextMoveForwardToNextResource() {
		
		$container = new TempResourceContainer(TempFileResource::TYPE);
		$container->create('php://temp/test1', 'test');
		$container->create('php://temp/test2', 'test');
		$container->rewind();
		$container->next();
		
		$this->assertSame(
			'php://temp/test2',
			$container->key()
		);
	}
	
	/**
	 * Test if the `rewind` moves back to the first resource.
	 */
	public function testRewindMoveToTheFirstResource() {
		
		$container = new TempResourceContainer(TempFileResource::TYPE);
		$container->create('php://temp/test1', 'test');
		$container->create('php://temp/test2', 'test');
		$container->rewind();
		
		$this->assertSame(
			'php://temp/test1',
			$container->key()
		);
	}
	
	/**
	 * Test if the `valid` method returns false if no resource exists.
	 */
	public function testValidReturnsFalse() {
		
		$container = new TempResourceContainer(TempFileResource::TYPE);
		
		$this->assertFalse($container->valid());
	}
	
	/**
	 * Test if the `valid` method returns true if a resource exists.
	 */
	public function testValidReturnsTrue() {
		
		$container = new TempResourceContainer(TempFileResource::TYPE);
		$container->create('php://temp/test', 'test');
		$container->rewind();
		
		$this->assertTrue($container->valid());
	}
}
