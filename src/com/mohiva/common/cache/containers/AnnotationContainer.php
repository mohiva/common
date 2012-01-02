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
 * @package   Mohiva/Common/Cache/Containers
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
namespace com\mohiva\common\cache\containers;

use com\mohiva\common\cache\Key;
use com\mohiva\common\cache\adapters\Adapter;
use com\mohiva\common\lang\AnnotationList;

/**
 * Cache container class which handles the caching of annotation lists.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Cache/Containers
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class AnnotationContainer {
	
	/**
	 * The adapter to use as backend.
	 * 
	 * @var \com\mohiva\common\cache\adapters\Adapter
	 */
	private $adapter = null;
	
	/**
	 * The key to use for the adapter.
	 * 
	 * @var \com\mohiva\common\cache\Key
	 */
	private $key = null;
	
	/**
	 * Indicates if the container is enabled or disabled for caching.
	 * 
	 * @var bool
	 */
	private $enabled = null;
	
	/**
	 * The class constructor.
	 * 
	 * @param \com\mohiva\common\cache\adapters\Adapter $adapter The adapter to use as backend.
	 * @param \com\mohiva\common\cache\Key $key The key to use for the adapter.
	 * @param bool $enabled True if the cache is enabled, false otherwise.
	 */
	public function __construct(Adapter $adapter, Key $key, $enabled = true) {
		
		$this->adapter = $adapter;
		$this->key = $key;
		$this->enabled = $enabled;
	}
	
	/**
	 * Check if a cache entry for the given doc comment exists.
	 * 
	 * @param string $comment The doc comment to check for.
	 * @return boolean True if an cache entry exists for the given doc comment, false otherwise.
	 */
	public function exists($comment) {
		
		if ($this->enabled === false) {
			return false;
		}
		
		$this->key->set($comment);
		
		return $this->adapter->exists($this->key);
	}
	
	/**
	 * Fetch the `AnnotationList` for the given doc comment.
	 * 
	 * @param string $comment The doc comment to check for.
	 * @return AnnotationList A list with annotations or null if no cache entry exists for the given comment.
	 */
	public function fetch($comment) {
		
		if ($this->enabled === false) {
			return null;
		}
		
		$this->key->set($comment);
		$value = $this->adapter->fetch($this->key);
		if (!$value) {
			return null;
		}
		
		return unserialize($value);
	}
	
	/**
	 * Store the annotation list for the given doc comment in cache.
	 * 
	 * @param string $comment The doc comment in which the annotations are located.
	 * @param \com\mohiva\common\lang\AnnotationList $annotations A list with annotations to cache.
	 */
	public function store($comment, AnnotationList $annotations) {
		
		if ($this->enabled === false) {
			return;
		}
		
		$this->key->set($comment);
		$value = serialize($annotations);
		
		$this->adapter->store($this->key, $value);
	}
	
	/**
	 * Remove the cached annotations for the given doc comment from cache.
	 * 
	 * @param string $comment The doc comment to remove.
	 */
	public function remove($comment) {
		
		if ($this->enabled === false) {
			return;
		}
		
		$this->key->set($comment);
		$this->adapter->remove($this->key);
	}
}
