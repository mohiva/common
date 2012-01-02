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
 * The `EventDispatcher` interface defines methods for adding or removing 
 * event listeners, checks whether specific types of event listeners are registered, 
 * and dispatches events. It's inspired by Adobe's AS3 implementation.
 *
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Util
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
interface EventDispatcher {
	
	/**
	 * Registers an event listener object with an `EventDispatcher` object so 
	 * that the listener receives notification of an event.
	 * 
	 * After you successfully register an event listener, you cannot change its priority 
	 * through additional calls to `addEventListener()`. To change a listener's
	 * priority, you must first call `removeEventListener()`. Then you can register
	 * the listener again with the new priority level.
	 * 
	 * @param string $type The type of event.
	 * 
	 * @param mixed $listener The listener function that processes the event. This function 
	 * must accept an event object as its only parameter and must return nothing.
	 * 
	 * @param int $priority The priority level of the event listener. The higher the number, 
	 * the higher the priority. All listeners with priority n are processed before listeners
	 * of priority n-1. If two or more listeners share the same priority, they are processed 
	 * in the order in which they were added. The default priority is 0.
	 */
	public function addEventListener($type, $listener, $priority = 0);
	
	/**
	 * Checks whether the `EventDispatcher` object has any listeners 
	 * registered for a specific type of event.
	 * 
	 * @param string $type The type of event.
	 * @return boolean True if a listener of the specified type is registered, false otherwise.
	 */
	public function hasEventListener($type);
	
	/**
	 * Removes a listener from the `EventDispatcher` object. If there is no 
	 * matching listener registered with the `EventDispatcher` object, a call
	 * to this method has no effect. 
	 * 
	 * @param string $type The type of event.
	 * @param mixed $listener The listener object to remove.
	 */
	public function removeEventListener($type, $listener);
	
	/**
	 * Dispatches an event into the event flow. The event target is the `EventDispatcher` 
	 * object upon which `dispatchEvent()` is called. 
	 * 
	 * @param Event $event The event object dispatched into the event flow.
	 */
	public function dispatchEvent(Event $event);
}
