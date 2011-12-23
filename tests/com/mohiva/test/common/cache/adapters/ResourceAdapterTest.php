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
namespace com\mohiva\test\common\cache\adapters;

use com\mohiva\common\cache\HashKey;
use com\mohiva\common\cache\adapters\ResourceAdapter;
use com\mohiva\common\io\TempResourceContainer;
use com\mohiva\common\io\TempFileResource;
use com\mohiva\common\io\exceptions\IOException;
use com\mohiva\common\crypto\Hash;

/**
 * Unit test case for the `ResourceAdapter` class.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Test
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2011 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class ResourceAdapterTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * Test the `setLifetime` and `getLifetime()` accessors.
	 */
	public function testLifetimeAccessors() {
		
		$container = new TempResourceContainer(TempFileResource::TYPE);
		$adapter = new ResourceAdapter($container);
		$adapter->setLifetime(1234);
		
		$this->assertSame(1234, $adapter->getLifetime());
	}
	
	/**
	 * Check if the `exists` method return true if a key is cached.
	 */
	public function testExistsReturnTrue() {
		
		$key = new HashKey(Hash::ALGO_SHA1, 'php://temp/');
		$container = new TempResourceContainer(TempFileResource::TYPE);
		$container->create((string) $key, 'A value');
		
		$adapter = new ResourceAdapter($container);
		
		$this->assertTrue($adapter->exists($key));
	}
	
	/**
	 * Check if the `exists` method return false if a key isn't cached.
	 */
	public function testExistsReturnFalse() {
		
		$key = new HashKey(Hash::ALGO_SHA1, 'php://temp/');
		$container = new TempResourceContainer(TempFileResource::TYPE);
		$adapter = new ResourceAdapter($container);
		
		$this->assertFalse($adapter->exists($key));
	}
	
	/**
	 * Test if the `fetch` method return a previous cached value.
	 */
	public function testFetchReturnValue() {
		
		$value = 'A value';
		$key = new HashKey(Hash::ALGO_SHA1, 'php://temp/');
		$container = new TempResourceContainer(TempFileResource::TYPE);
		$container->create((string) $key, $value);
		
		$adapter = new ResourceAdapter($container);
		
		$this->assertSame($value, $adapter->fetch($key));
	}
	
	/**
	 * Test if the fetch method return null if no value is cached for the specified key.
	 */
	public function testFetchReturnNull() {
		
		$key = new HashKey(Hash::ALGO_SHA1, 'php://temp/');
		$container = new TempResourceContainer(TempFileResource::TYPE);
		$adapter = new ResourceAdapter($container);
		
		$this->assertNull($adapter->fetch($key));
	}
	
	/**
	 * Test if the `store` method stores a value.
	 */
	public function testStoreValueInCache() {
		
		$value = 'A value';
		$key = new HashKey(Hash::ALGO_SHA1, 'php://temp/');
		$container = new TempResourceContainer(TempFileResource::TYPE);
		$adapter = new ResourceAdapter($container);
		$adapter->store($key, $value);
		
		$this->assertSame($value, $container->get((string) $key)->read());
	}
	
	/**
	 * Test if the `store` method throws an exception if
	 * the process cannot store a process in cache.
	 * 
	 * @expectedException \com\mohiva\common\io\exceptions\IOException
	 */
	public function testStoreThrowsIOException() {
		
		/* @var \com\mohiva\common\cache\adapters\ResourceContainer $stub */
		$stub = $this->getMock('ResourceContainer', array(), array());
		$stub->expects($this->any())
			->method('create')
			->will($this->throwException(new IOException()));
		
		$key = new HashKey(Hash::ALGO_SHA1, 'php://temp/');
		$adapter = new ResourceAdapter($stub);
		$adapter->store($key, 'A value');
	}
	
	/**
	 * Test if the `remove` return false if the resource doesn't exists.
	 */
	public function testRemoveReturnFalseOnNonExistingResource() {
		
		$key = new HashKey(Hash::ALGO_SHA1, 'php://temp/');
		$container = new TempResourceContainer(TempFileResource::TYPE);
		$adapter = new ResourceAdapter($container);
		
		$this->assertFalse($adapter->remove($key));
	}
	
	/**
	 * Test if the `remove` return true if the resource was removed.
	 */
	public function testRemoveReturnTrueIfTheResourceWasRemoved() {
		
		$key = new HashKey(Hash::ALGO_SHA1, 'php://temp/');
		$container = new TempResourceContainer(TempFileResource::TYPE);
		$adapter = new ResourceAdapter($container);
		$adapter->store($key, 'A value');
		
		$this->assertTrue($adapter->remove($key));
	}
	
	/**
	 * Test if the `remove` method throws an exception if
	 * the process cannot remove a resource from cache.
	 * 
	 * @expectedException \com\mohiva\common\io\exceptions\IOException
	 */
	public function testRemoveThrowsIOException() {
		
		/* @var \com\mohiva\common\cache\adapters\ResourceContainer $stub */
		$stub = $this->getMock('ResourceContainer', array(), array());
		$stub->expects($this->any())
			->method('remove')
			->will($this->throwException(new IOException()));
		
		$key = new HashKey(Hash::ALGO_SHA1, 'php://temp/');
		$adapter = new ResourceAdapter($stub);
		$adapter->remove($key);
	}
	
	/**
	 * Test if the `clean` method removes all expired resources.
	 */
	public function testCleanRemovesExpiredResources() {
		
		$container = new TempResourceContainer(TempFileResource::TYPE);
		$container->create('php://temp/test1', 'A string')->getStat()->setModificationTime(time() - 5);
		$container->create('php://temp/test2', 'A string')->getStat()->setModificationTime(time() - 5);
		$container->create('php://temp/test3', 'A string')->getStat()->setModificationTime(time() - 5);
		$container->create('php://temp/test4', 'A string');
		$container->create('php://temp/test5', 'A string');
		
		$adapter = new ResourceAdapter($container);
		$adapter->setLifetime(1);
		$adapter->clean();
		
		$cnt = 0;
		$container->rewind();
		while ($container->valid()) {
			$container->next();
			$cnt++;
		}
		
		$this->assertSame(2, $cnt);
	}
}
