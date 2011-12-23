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

use com\mohiva\common\cache\Key;

/**
 * Cache adapter interface.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Cache/Adapters
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2011 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
interface Adapter {
	
	/**
	 * Set the lifetime of the cache in seconds.
	 * 
	 * @param int $lifetime The lifetime in seconds or 0 for no expiry.
	 */
	public function setLifetime($lifetime);
	
	/**
	 * Get the lifetime of the cache in seconds.
	 * 
	 * @return int The lifetime in seconds or 0 for no expiry.
	 */
	public function getLifetime();
	
	/**
	 * Check if a key exists in cache.
	 * 
	 * @param \com\mohiva\common\cache\Key $key The key for the cached value.
	 * @return boolean True if a value fore the given key exists, false otherwise.
	 */
	public function exists(Key $key);
	
	/**
	 * Fetch the value from adapter for the given key.
	 * 
	 * @param \com\mohiva\common\cache\Key $key The key for the cached value.
	 * @return string|null The cached value or null if no value for the given id exists.
	 */
	public function fetch(Key $key);
	
	/**
	 * Store the key => value pair in the cache.
	 * 
	 * @param \com\mohiva\common\cache\Key $key The key for the cached value.
	 * @param string $value The value to cache.
	 * @throws IOException if the value cannot be stored for the given key.
	 */
	public function store(Key $key, $value);
	
	/**
	 * Remove the key and its value from cache.
	 * 
	 * @param \com\mohiva\common\cache\Key $key The key to remove.
	 * @param bool $checkExistence True if should be checked if the key exists before deleting it, false otherwise.
	 * @return bool True if the key was removed, false if the key doesn't exists.
	 * @throws IOException if the value cannot be removed for the given key.
	 */
	public function remove(Key $key, $checkExistence = true);
	
	/**
	 * Remove all expired entries from cache.
	 */
	public function clean();
}
