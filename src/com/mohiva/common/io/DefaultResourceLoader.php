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
 * @copyright Copyright (c) 2007-2011 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
namespace com\mohiva\common\io;

use SplFileInfo;

/**
 * The default implementation of the `ResourceLoader` interface.
 * 
 * To avoid useless class loading(it should be programmed against the 
 * `Resource::TYPE` and `Resource::DESCRIPTOR` 
 * class constants), there are no default descriptors registered in 
 * this implementation.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/IO
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2011 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class DefaultResourceLoader implements ResourceLoader {
	
	/**
	 * The `ClassLoader` associated with this object.
	 * 
	 * @var ClassLoader
	 */
	private $classLoader = null;
	
	/**
	 * A list with registered file descriptors.
	 * 
	 * @var array
	 */
	private $descriptors = array();
	
	/**
	 * Return a Resource handle for the specified path.
	 * 
	 * Every path must be prefixed with a resource descriptor. This is 
	 * used to decide what type of resource should be loaded.
	 * 
	 * As example:
	 *    file:/path/to/file.txt - return the FileResource handle
	 *    xml:./relative/path.xml - return the XMLResource handle
	 * 
	 * For more information about the descriptor notation, look into the derived
	 * `Resource` implementations. The descriptor is defined as constant 
	 * in every implemented `Resource` class.
	 * 
	 * @param string $path The path to the resource, prefixed with a resource descriptor.
	 * @return Resource The corresponding resource handle.
	 * @throws \InvalidArgumentException if the path isn't prefixed with a registered resource descriptor.
	 */
	public function getResource($path) {
		
		$matches = array();
		$descriptors = implode('|', array_keys($this->descriptors));
		if (empty($this->descriptors) || !preg_match("@^({$descriptors})(.*)$@", $path, $matches)) {
			throw new \InvalidArgumentException(
				"The path `{$path}` isn't prefixed with a registered resource descriptor"
			);
		}
		
		$handle = $this->descriptors[$matches[1]];
		$path = $matches[2];
		
		$loader = $this->getClassLoader();
		$class = $loader->load($handle);
		$resource = $class->newInstance(new SplFileInfo($path));
		
		return $resource;
	}
	
	/**
	 * Return a Resource handle for the specified path.
	 * 
	 * This method returns the resource based on the type instead of the resource
	 * descriptor. Defining the path with a resource descriptor will fail because 
	 * of a nonexistent path.
	 * 
	 * It is highly recommend to use this method to load resources because it is faster 
	 * as the `getResource` implementation.
	 * 
	 * @param string $path The path to the resource.
	 * @param string $type The FQN of the resource handle.
	 * @return Resource The corresponding resource handle.
	 */
	public function getResourceByType($path, $type) {
		
		$loader = $this->getClassLoader();
		$class = $loader->load($type);
		$resource = $class->newInstance(new SplFileInfo($path));
		
		return $resource;
	}
	
	/**
	 * Register a new resource descriptor for a resource handle implementation.
	 * 
	 * After registering a descriptor the corresponding `Resource` can be loaded 
	 * with the `getResourceMethod`.
	 * 
	 * @param string $descriptor A unique resource descriptor.
	 * @param string $type The FQN of the resource handle.
	 */
	public function registerDescriptor($descriptor, $type) {
		
		$this->descriptors[$descriptor] = $type;
	}
	
	/**
	 * Unregister an already registered resource descriptor and its resource handle implementation.
	 * 
	 * After unregistering a descriptor the corresponding `Resource` cannot be loaded 
	 * with the `getResourceMethod`.
	 * 
	 * @param string $descriptor A unique resource descriptor.
	 */
	public function unregisterDescriptor($descriptor) {
		
		if (isset($this->descriptors[$descriptor])) {
			unset($this->descriptors[$descriptor]);
		}
	}
	
	/**
	 * Return a list with all registered descriptors and its corresponding resource handles.
	 * 
	 * @return array A list with all registered descriptors and its corresponding resource handles.
	 */
	public function getRegisteredDescriptors() {
		
		return $this->descriptors;
	}
	
	/**
	 * Specify the `ClassLoader` to load resources with.
	 * 
	 * @param ClassLoader $classLoader The `ClassLoader` to load resources with.
	 */
	public function setClassLoader(ClassLoader $classLoader) {
		
		$this->classLoader = $classLoader;
	}
	
	/**
	 * Return the `ClassLoader` to load resources with.
	 * 
	 * @return ClassLoader The `ClassLoader` to load resources with.
	 */
	public function getClassLoader() {
		
		if ($this->classLoader == null) {
			$this->classLoader = new DefaultClassLoader();
		}
		
		return $this->classLoader;
	}
}
