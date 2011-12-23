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
 * @package   Mohiva/Common/Cache/Adapters
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2011 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
namespace com\mohiva\common\cache\adapters;

use RuntimeException;
use APCIterator;
use com\mohiva\common\io\exceptions\IOException;
use com\mohiva\common\cache\Key;

/**
 * APC cache adapter class.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Cache/Adapters
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2011 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class APCAdapter implements Adapter {
	
	/**
	 * The lifetime of the cached value in seconds. If set to 0,
	 * the cache has no expiry.
	 * 
	 * @var int
	 */
	private $lifetime = 0;
	
	/**
	 * The class constructor.
	 * 
	 * @throws RuntimeException if the apc extension isn't loaded.
	 */
	public function __construct() {
		
		if (!extension_loaded('apc')) {
			throw new RuntimeException('The apc extension must be loaded to use this adapter');
		}
	}
	
	/**
	 * Sets the lifetime in seconds or 0 for no expiry.
	 * 
	 * @param int $lifetime The lifetime in seconds or 0 for no expiry.
	 */
	public function setLifetime($lifetime) {
		
		$this->lifetime = $lifetime;
	}
	
	/**
	 * Gets the lifetime in seconds or 0 for no expiry.
	 * 
	 * @return int The lifetime in seconds or 0 for no expiry.
	 */
	public function getLifetime() {
		
		return $this->lifetime;
	}
	
	/**
	 * Check if a key exists in cache.
	 * 
	 * @param \com\mohiva\common\cache\Key $key The key for the cached value.
	 * @return boolean True if a value for the given key exists, false otherwise.
	 */
	public function exists(Key $key) {
		
		if (function_exists('apc_exists')) {
			return apc_exists((string) $key);
		} else {
			return apc_fetch((string) $key) !== false;
		}
	}
	
	/**
	 * Fetch the value from adapter for the given key.
	 * 
	 * @param \com\mohiva\common\cache\Key $key The key for the cached value.
	 * @return string The cached value or null if no value for the given id exists.
	 */
	public function fetch(Key $key) {
		
		$value = apc_fetch((string) $key);
		if ($value === false) {
			return null;
		}
		
		return $value;
	}
	
	/**
	 * Store the key => value pair in the cache.
	 * 
	 * @param \com\mohiva\common\cache\Key $key The key for the cached value.
	 * @param string $value The value to cache.
	 * @throws IOException if the value cannot be stored.
	 */
	public function store(Key $key, $value) {
		
		$result = apc_store((string) $key, $value, $this->lifetime);
		if (!$result) {
			throw new IOException("The value for key `{$key}` cannot be stored");
		}
	}
	
	/**
	 * Remove the key and its value from cache.
	 * 
	 * @param \com\mohiva\common\cache\Key $key The key to remove.
	 * @param bool $checkExistence True if should be checked if the key exists before deleting it, false otherwise.
	 * @return bool True if the key was removed, false if the key doesn't exists.
	 * @throws IOException if the value cannot be removed.
	 */
	public function remove(Key $key, $checkExistence = true) {
		
		if ($checkExistence && !$this->exists($key)) {
			return false;
		}
		
		$result = apc_delete((string) $key);
		if (!$result) {
			throw new IOException("The value for the key `{$key}` cannot be deleted");
		}
		
		return true;
	}
	
	/**
	 * Remove all expired entries from cache.
	 * 
	 * @codeCoverageIgnore the time-to-live-feature does not work within the same 
	 * request (@see http://pecl.php.net/bugs/bug.php?id=13331).
	 */
	public function clean() {
		
		$expired = new APCIterator('user', null, APC_ITER_VALUE, 100, APC_LIST_DELETED);
		apc_delete($expired);
	}
}
