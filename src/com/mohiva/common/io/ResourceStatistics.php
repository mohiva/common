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

use com\mohiva\common\util\Event;
use com\mohiva\common\util\DefaultEventDispatcher;
use com\mohiva\common\io\events\ResourceStatisticsChangeEvent;

/**
 * An object containing statistics information about a resource.
 * 
 * This class dispatches events to support the modification of all statistical values.
 * The associated resource can then register event listeners which handles those events.
 * Not every resource can handle all modifications. For a description of all allowed 
 * modifications, visit the documentation of the resource.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/IO
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 * TODO Inject the event dispatcher object as dependency instead of extending the class
 */
class ResourceStatistics extends DefaultEventDispatcher {
	
	/**
	 * The creation time of a resource.
	 * 
	 * @var int
	 */
	private $creationTime = null;
	
	/**
	 * The last access time of a resource.
	 * 
	 * @var int
	 */
	private $accessTime = null;
	
	/**
	 * The modification time of a resource.
	 * 
	 * @var int
	 */
	private $modificationTime = null;
	
	/**
	 * The size of the resource in bytes.
	 * 
	 * @var int
	 */
	private $size = null;
	
	/**
	 * Sets the creation time of a resource.
	 * 
	 * @param int $creationTime The creation time of a resource.
	 */
	public function setCreationTime($creationTime) {
		
		$this->creationTime = $creationTime;
		$this->dispatchEvent(new ResourceStatisticsChangeEvent(
			ResourceStatisticsChangeEvent::CREATION_TIME_CHANGED
		));
	}
	
	/**
	 * Gets the creation time of a resource.
	 * 
	 * @return int The creation time of a resource.
	 */
	public function getCreationTime() {
		
		return $this->creationTime;
	}
	
	/**
	 * Sets the access time of a resource.
	 * 
	 * @param int $accessTime The last access time of a resource.
	 */
	public function setAccessTime($accessTime) {
		
		$this->accessTime = $accessTime;
		$this->dispatchEvent(new ResourceStatisticsChangeEvent(
			ResourceStatisticsChangeEvent::ACCESS_TIME_CHANGED
		));
	}
	
	/**
	 * Gets the access time of a resource.
	 * 
	 * @return int The last access time of a resource.
	 */
	public function getAccessTime() {
		
		return $this->accessTime;
	}
	
	/**
	 * Sets the modification time of a resource.
	 * 
	 * @param int $modifyTime The modification time of a resource.
	 */
	public function setModificationTime($modifyTime) {
		
		$this->modificationTime = $modifyTime;
		$this->dispatchEvent(new ResourceStatisticsChangeEvent(
			ResourceStatisticsChangeEvent::MODIFICATION_TIME_CHANGED
		));
	}
	
	/**
	 * Gets the modification time of a resource.
	 * 
	 * @return int The modification time of a resource.
	 */
	public function getModificationTime() {
		
		return $this->modificationTime;
	}
	
	/**
	 * Sets the size of the resource.
	 * 
	 * @param int $size The size of the resource in bytes.
	 */
	public function setSize($size) {
		
		$this->size = $size;
		$this->dispatchEvent(new ResourceStatisticsChangeEvent(
			ResourceStatisticsChangeEvent::SIZE_CHANGED
		));
	}
	
	/**
	 * Gets the size of the resource.
	 * 
	 * @return int The size of the resource in bytes.
	 */
	public function getSize() {
		
		return $this->size;
	}
}
