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
namespace com\mohiva\test\common\cache\adapters;

use com\mohiva\common\cache\HashKey;
use com\mohiva\common\cache\adapters\APCAdapter;

/**
 * Unit test case for the `APCAdapter` class.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Test
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class APCAdapterTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * Setup the test case.
	 */
	public function setup() {
		
		if (!$this->isApcEnabled()) {
			$this->markTestSkipped('The APC extension is not available');
		} else if (!$this->isCliCacheEnabled()) {
			$this->markTestSkipped('The apc.enable_cli setting must be enabled');
		}
		
		apc_clear_cache('user');
	}
	
	/**
	 * Tear down the test case.
	 */
	public function tearDown() {
		
		if ($this->isApcEnabled()) {
			apc_clear_cache('user');
		}
	}
	
	/**
	 * Test the `setLifetime` and `getLifetime()` accessors.
	 */
	public function testLifetimeAccessors() {
		
		$adapter = new APCAdapter();
		$adapter->setLifetime(1234);
		
		$this->assertSame(1234, $adapter->getLifetime());
	}
	
	/**
	 * Check if the `exists` method return true if a key is cached.
	 */
	public function testExistsReturnTrue() {
		
		$key = new HashKey();
		$adapter = new APCAdapter();
		
		apc_store((string) $key, 'A value');
		
		$this->assertTrue($adapter->exists($key));
	}
	
	/**
	 * Check if the `exists` method return false if a key isn't cached.
	 */
	public function testExistsReturnFalse() {
		
		$key = new HashKey();
		$adapter = new APCAdapter();
		
		$this->assertFalse($adapter->exists($key));
	}
	
	/**
	 * Test if the `fetch` method return a previous cached value.
	 */
	public function testFetchReturnValue() {
		
		$value = 'A value';
		$key = new HashKey();
		$adapter = new APCAdapter();
		
		apc_store((string) $key, $value);
		
		$this->assertSame($value, $adapter->fetch($key));
	}
	
	/**
	 * Test if the fetch method return null if no value is cached for the specified key.
	 */
	public function testFetchReturnNull() {
		
		$key = new HashKey();
		$adapter = new APCAdapter();
		
		$this->assertNull($adapter->fetch($key));
	}
	
	/**
	 * Test if the `store` method stores a value.
	 */
	public function testStoreValueInCache() {
		
		$value = 'A value';
		$key = new HashKey();
		$adapter = new APCAdapter();
		$adapter->store($key, $value);
		
		$this->assertSame($value, apc_fetch((string) $key));
	}
	
	/**
	 * Test if the `store` method stores the TTL in cache.
	 */
	public function testStoreValueWithTTLInCache() {
		
		$ttl = 1234;
		$key = new HashKey();
		$adapter = new APCAdapter();
		$adapter->setLifetime($ttl);
		$adapter->store($key, 'A value');
		
		$info = apc_cache_info('user');
		$this->assertEquals($ttl, $info['cache_list'][0]['ttl']);
	}
	
	/**
	 * Test if the `remove` return false if the resource doesn't exists.
	 */
	public function testRemoveReturnFalseOnNonExistingResource() {
		
		$key = new HashKey();
		$adapter = new APCAdapter();
		
		$this->assertFalse($adapter->remove($key));
	}
	
	/**
	 * Test if the `remove` return true if the resource was removed.
	 */
	public function testRemoveReturnTrueIfTheResourceWasRemoved() {
		
		$key = new HashKey();
		$adapter = new APCAdapter();
		$adapter->store($key, 'A value');
		
		$this->assertTrue($adapter->remove($key));
	}
	
	/**
	 * Test if the `remove` method throws an exception if
	 * the process cannot remove the resource.
	 * 
	 * @expectedException \com\mohiva\common\io\exceptions\IOException
	 */
	public function testRemoveThrowsRemoveException() {
		
		$key = new HashKey();
		$adapter = new APCAdapter();
		
		$adapter->remove($key, false);
	}
	
	/**
	 * Check if the APC extension is enabled.
	 * 
	 * @return boolean True if the APC extension is enabled, false otherwise.
	 */
	private function isApcEnabled() {
		
		if (!extension_loaded('apc')) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * Check if the apc cli cache is enabled.
	 * 
	 * @return boolean True if the cli cache is enabled, false otherwise.
	 */
	private function isCliCacheEnabled() {
		
		$iniValue = strtolower(ini_get('apc.enable_cli'));
		if ($iniValue == 1 || $iniValue == 'on') {
			return true;
		}
		
		return false;
	}
}
