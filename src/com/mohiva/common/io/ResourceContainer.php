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

use ArrayAccess;
use Iterator;

/**
 * A resource container can handle many resources of the same type. The container offers 
 * the possibility to traverse over all resources and provide a simple CRUD like interface 
 * to work with resources within the container.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/IO
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
interface ResourceContainer extends Iterator {
	
	/**
	 * Sets the resource loader instance used to load the resources.
	 * 
	 * @param \com\mohiva\common\io\ResourceLoader $resourceLoader The resource loader instance used to load 
	 * the resources.
	 */
	public function setResourceLoader(ResourceLoader $resourceLoader);
	
	/**
	 * Gets the resource loader instance used to load the resources.
	 * 
	 * @return \com\mohiva\common\io\ResourceLoader The resource loader instance used to load the resources.
	 */
	public function getResourceLoader();
	
	/**
	 * Check if the resource with the given path exists in the container.
	 * 
	 * @param string $resourcePath The path to the resource.
	 * @return boolean True if the resource exists, false otherwise.
	 */
	public function exists($resourcePath);
	
	/**
	 * Creates a new resource.
	 * 
	 * @param string $resourcePath The path to the resource.
	 * @param mixed $data The data to store as content of the resource.
	 * @return \com\mohiva\common\io\Resource The created resource.
	 */
	public function create($resourcePath, $data);
	
	/**
	 * Return the resource for the given path.
	 * 
	 * @param string $resourcePath The path to the resource.
	 * @return \com\mohiva\common\io\Resource A resource object.
	 */
	public function get($resourcePath);
	
	/**
	 * Remove the resource for the given path from container.
	 * 
	 * @param string $resourcePath The path to the resource.
	 * @param bool $checkExistence True if should be checked if the key exists before deleting it, false otherwise.
	 * @return bool True if the resource was removed, false if the resource doesn't exists.
	 */
	public function remove($resourcePath, $checkExistence = true);
}
