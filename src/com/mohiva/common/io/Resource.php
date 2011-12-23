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
 * This interface describes a resource and the actions to do with it. 
 * It is an abstract layer for physical resources(file, database, ..) 
 * which supports CRUD like actions.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/IO
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2011 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
interface Resource {
	
	/**
	 * The class constructor.
	 *
	 * @param \SplFileInfo $fileInfo The file info object associated with the resource.
	 */
	public function __construct(SplFileInfo $fileInfo);
	
	/**
	 * Return the object handle of the resource.
	 * 
	 * @return mixed The object handle of the resource.
	 */
	public function getHandle();
	
	/**
	 * Return the `ResourceStatistics` object for the resource.
	 * 
	 * @return \com\mohiva\common\io\ResourceStatistics An object containing statistics information 
	 * about a resource.
	 */
	public function getStat();
	
	/**
	 * Indicates if this resource exists or not.
	 * 
	 * @return boolean True if the resource exists, false otherwise.
	 */
	public function exists();
	
	/**
	 * Read the data of the resource and return it.
	 * 
	 * @return mixed The data of the resource.
	 */
	public function read();
	
	/**
	 * Write the given data to the resource.
	 * 
	 * Attention if the resource already exists then this method 
	 * will overwrite it with the given data.
	 * 
	 * @param string $data The data to write.
	 */
	public function write($data);
	
	/**
	 * Remove the resource.
	 * 
	 * @param bool $checkExistence True if should be checked if the resource exists before deleting it, false otherwise.
	 * @return bool True if the resource was removed, false if the resource doesn't exists.
	 */
	public function remove($checkExistence = true);
}
