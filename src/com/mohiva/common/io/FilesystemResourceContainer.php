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

use FilesystemIterator;

/**
 * This is an implementation of the `ResourceContainer` interface, which supports 
 * a directory of filesystem `Resource` classes like `FileResource`
 * or `XMLResource`.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/IO
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class FilesystemResourceContainer implements ResourceContainer {
	
	/**
	 * The resource loader instance used to load the resources.
	 * 
	 * @var ResourceLoader
	 */
	private $resourceLoader = null;
	
	/**
	 * The path to the base directory in which the resources are located.
	 * 
	 * @var string
	 */
	private $baseDir = null;
	
	/**
	 * The type of the resource to load.
	 * 
	 * @var string
	 */
	private $resourceType = null;
	
	/**
	 * The iterator instance to use to traverse the resources.
	 * 
	 * @var \DirectoryIterator
	 */
	private $iterator = null;
	
	/**
	 * The class constructor.
	 * 
	 * @param string $baseDir The path to the base directory in which the resources are located.
	 * @param string $resourceType The type of the resource to load.
	 */
	public function __construct($baseDir, $resourceType) {
		
		$this->baseDir = rtrim($baseDir, '\//');
		$this->iterator = new FilesystemIterator($this->baseDir,
			FilesystemIterator::CURRENT_AS_PATHNAME |
			FilesystemIterator::KEY_AS_PATHNAME |
			FilesystemIterator::SKIP_DOTS
		);
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
	 * @param string $fileName The name of the file relative to the containers base path.
	 * @return boolean True if the resource exists, false otherwise.
	 */
	public function exists($fileName) {
		
		$path = $this->baseDir . DIRECTORY_SEPARATOR . $fileName;
		$resource = $this->getResourceLoader()->getResourceByType($path, $this->resourceType);
		
		return $resource->exists();
	}
	
	/**
	 * Creates a new resource.
	 * 
	 * @param string $fileName The name of the file relative to the containers base path.
	 * @param mixed $data The data to store as content of the resource.
	 * @return \com\mohiva\common\io\Resource The created resource.
	 */
	public function create($fileName, $data) {
		
		$path = $this->baseDir . DIRECTORY_SEPARATOR . $fileName;
		$resource = $this->getResourceLoader()->getResourceByType($path, $this->resourceType);
		$resource->write($data);
		
		return $resource;
	}
	
	/**
	 * Return the resource for the given file name.
	 * 
	 * @param string $fileName The name of the file relative to the containers base path.
	 * @return \com\mohiva\common\io\Resource A resource instance.
	 */
	public function get($fileName) {
		
		$path = $this->baseDir . DIRECTORY_SEPARATOR . $fileName;
		$resource = $this->getResourceLoader()->getResourceByType($path, $this->resourceType);
		
		return $resource;
	}
	
	/**
	 * Removes a resource.
	 * 
	 * @param string $fileName The name of the file relative to the containers base path.
	 * @param bool $checkExistence True if should be checked if the key exists before deleting it, false otherwise.
	 * @return bool True if the resource was removed, false if the resource doesn't exists.
	 */
	public function remove($fileName, $checkExistence = true) {
		
		$path = $this->baseDir . DIRECTORY_SEPARATOR . $fileName;
		$resource = $this->getResourceLoader()->getResourceByType($path, $this->resourceType);
		
		return $resource->remove($checkExistence);
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
		
		$resource = $this->getResourceLoader()->getResourceByType(
			$this->iterator->current(),
			$this->resourceType
		);
		
		return $resource;
	}
	
	/**
	 * Return the pathname for the current resource.
	 * 
	 * @return string The pathname of the resource or null if no resource exists.
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
