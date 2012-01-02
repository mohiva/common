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
 * @package   Mohiva/Common/IO
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
namespace com\mohiva\common\io;

use ArrayIterator;
use com\mohiva\common\io\exceptions\RemoveException;

/**
 * This is an implementation of the `ResourceContainer` interface, which supports
 * temporary resources such like `TempFileResource`. This container can be used 
 * for testing.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/IO
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class TempResourceContainer implements ResourceContainer {
	
	/**
	 * The resource loader instance used to load the resources.
	 * 
	 * @var ResourceLoader
	 */
	private $resourceLoader = null;
	
	/**
	 * The type of the resource to load.
	 * 
	 * @var string
	 */
	private $resourceType = null;
	
	/**
	 * The iterator instance to use to traverse the resources.
	 * 
	 * @var \ArrayIterator
	 */
	private $iterator = null;
	
	/**
	 * The class constructor.
	 * 
	 * @param string $resourceType The type of the resource to load.
	 */
	public function __construct($resourceType) {
		
		$this->iterator = new ArrayIterator(array());
		$this->resourceType = $resourceType;
	}
	
	/**
	 * Sets the resource loader instance used to load the resources.
	 * 
	 * @param \com\mohiva\common\io\ResourceLoader $resourceLoader The resource loader instance used to load 
	 * the resources.
	 */
	public function setResourceLoader(ResourceLoader $resourceLoader) {
		
		$this->resourceLoader = $resourceLoader;
	}
	
	/**
	 * Gets the resource loader instance used to load the resources.
	 * 
	 * @return \com\mohiva\common\io\ResourceLoader The resource loader instance used to load the resources.
	 */
	public function getResourceLoader() {
		
		if ($this->resourceLoader === null) {
			$this->resourceLoader = new DefaultResourceLoader();
		}
		
		return $this->resourceLoader;
	}
	
	/**
	 * Check if a resource exists.
	 * 
	 * @param string $resourcePath The path to the temporary resource.
	 * @return boolean True if the resource exists, false otherwise.
	 */
	public function exists($resourcePath) {
		
		return isset($this->iterator[$resourcePath]);
	}
	
	/**
	 * Creates a new resource.
	 * 
	 * @param string $resourcePath The path to the temporary resource.
	 * @param mixed $data The data to store as content of the resource.
	 * @return \com\mohiva\common\io\Resource The created resource.
	 */
	public function create($resourcePath, $data) {
		
		$resource = $this->getResourceLoader()->getResourceByType($resourcePath, $this->resourceType);
		$resource->write($data);
		
		$this->iterator[$resourcePath] = $resource;
		
		return $resource;
	}
	
	/**
	 * Return the resource for the given file name.
	 * 
	 * @param string $resourcePath The path to the temporary resource.
	 * @return \com\mohiva\common\io\Resource A resource instance.
	 */
	public function get($resourcePath) {
		
		if (isset($this->iterator[$resourcePath])) {
			return $this->iterator[$resourcePath];
		}
		
		$resource = $this->getResourceLoader()->getResourceByType($resourcePath, $this->resourceType);
		
		return $resource;
	}
	
	/**
	 * Removes a resource.
	 * 
	 * @param string $resourcePath The path to the temporary resource.
	 * @param bool $checkExistence True if should be checked if the key exists before deleting it, false otherwise.
	 * @return bool True if the resource was removed, false if the resource doesn't exists.
	 * @throws RemoveException if the resource doesn't exists.
	 */
	public function remove($resourcePath, $checkExistence = true) {
		
		if ($checkExistence && !$this->exists($resourcePath)) {
			return false;
		}
		
		try {
			unset($this->iterator[$resourcePath]);
		} catch (\Exception $e) {
			throw new RemoveException("Resource `{$resourcePath}` doesn't exists");
		}
		
		return true;
	}
	
	/**
	 * Return the current resource.
	 * 
	 * @return \com\mohiva\common\io\Resource The current `Resource` object or null if no resource 
	 * exists.
	 */
	public function current() {
		
		if (!$this->valid()) {
			return null;
		}
		
		return $this->iterator->current();
	}
	
	/**
	 * Return the pathname for the current resource.
	 * 
	 * @return string The path to the temporary resource or null if no resource exists.
	 */
	public function key() {
		
		if (!$this->valid()) {
			return null;
		}
		
		return $this->iterator->key();
	}
	
	/**
	 * Move forward to the next resource.
	 */
	public function next() {
		
		$this->iterator->next();
	}
	
	/**
	 * Rewind the Iterator to the first resource.
	 */
	public function rewind() {
		
		$this->iterator->rewind();
	}
	
	/**
	 * Checks if the current position is valid.
	 * 
	 * @return boolean Returns true on success or false on failure.
	 */
	public function valid() {
		
		return $this->iterator->valid();
	}
}
