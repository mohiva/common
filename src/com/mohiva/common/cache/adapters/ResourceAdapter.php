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
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
namespace com\mohiva\common\cache\adapters;

use com\mohiva\common\io\ResourceContainer;
use com\mohiva\common\io\exceptions\IOException;
use com\mohiva\common\cache\Key;

/**
 * Resource cache adapter class.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Cache/Adapters
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class ResourceAdapter implements Adapter {
	
	/**
	 * The resource container which contains the resources.
	 * 
	 * @var com\mohiva\common\io\ResourceLoader
	 */
	private $resourceContainer = null;
	
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
	 * @param \com\mohiva\common\io\ResourceContainer $resourceContainer The resource container which contains 
	 * the resources.
	 */
	public function __construct(ResourceContainer $resourceContainer) {
		
		$this->resourceContainer = $resourceContainer;
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
		
		return $this->resourceContainer->exists((string) $key);
	}
	
	/**
	 * Fetch the value from adapter for the given key.
	 * 
	 * @param \com\mohiva\common\cache\Key $key The key for the cached value.
	 * @return string The cached value or null if no value for the given id exists.
	 */
	public function fetch(Key $key) {
		
		$resource = $this->resourceContainer->get((string) $key);
		$content = $resource->read();
		if (!$content) {
			return null;
		}
		
		return $content;
	}
	
	/**
	 * Save the key => value pair in the cache.
	 * 
	 * @param \com\mohiva\common\cache\Key $key The key for the cached value.
	 * @param string $value The value to cache.
	 * @throws IOException if the value cannot be stored for the given key.
	 */
	public function store(Key $key, $value) {
		
		try {
			$this->resourceContainer->create((string) $key, $value);
		} catch (\Exception $e) {
			throw new IOException("The value for key `{$key}` cannot be stored ", null, $e);
		}
	}
	
	/**
	 * Remove the key and its value from cache.
	 * 
	 * @param \com\mohiva\common\cache\Key $key The key to remove.
	 * @param bool $checkExistence True if should be checked if the key exists before deleting it, false otherwise.
	 * @return bool True if the key was removed, false if the key doesn't exists.
	 * @throws IOException if the value cannot be removed for the given key.
	 */
	public function remove(Key $key, $checkExistence = true) {
		
		try {
			$result = $this->resourceContainer->remove((string) $key, $checkExistence);
		} catch (\Exception $e) {
			throw new IOException("The value for the key `{$key}` cannot be removed", null, $e);
		}
		
		return $result;
	}
	
	/**
	 * Remove all expired entries from cache.
	 */
	public function clean() {
		
		$this->resourceContainer->rewind();
		while($this->resourceContainer->valid()) {
			/* @var \com\mohiva\common\io\Resource $resource */
			$resource = $this->resourceContainer->current();
			$path = $this->resourceContainer->key();
			$this->resourceContainer->next();
			
			/* @var \com\mohiva\common\io\ResourceStatistics $stat */
			$stat = $resource->getStat();
			if ($stat->getModificationTime() <= time() - $this->lifetime) {
				$this->resourceContainer->remove($path);
			}
		}
	}
}
