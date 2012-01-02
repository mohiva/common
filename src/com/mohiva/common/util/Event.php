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
 * @package   Mohiva/Common/Util
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
namespace com\mohiva\common\util;

/**
 * The Event class is used as the base class for the creation of Event objects, which are passed 
 * as parameters to event listeners when an event occurs.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Util
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class Event {
	
	/**
	 * The type of the event.
	 * 
	 * @var string
	 */
	private $type = null;
	
	/**
	 * The event target.
	 * 
	 * @var object
	 */
	private $target = null;
	
	/**
	 * Creates an Event object to pass as a parameter to event listeners.
	 * 
	 * @param string $type The type of the event.
	 */
	public function __construct($type) {
		
		$this->type = $type;
	}
	
	/**
	 * Gets the event type.
	 *
	 * @return string The type of the event.
	 */
	public function getType() {
		
		return $this->type;
	}
	
	/**
	 * Sets the event target.
	 * 
	 * @param object $target The event target.
	 */
	public function setTarget($target) {
		
		$this->target = $target;
	}
	
	/**
	 * Gets the event target.
	 * 
	 * @return object The event target.
	 */
	public function getTarget() {
		
		return $this->target;
	}
}
