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

use Closure;
use SplPriorityQueue;
use InvalidArgumentException;

/**
 * The default implementation of the `EventDispatcher` interface.
 *
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Util
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class DefaultEventDispatcher implements EventDispatcher {

	/**
	 * A serial used to maintain the insertion order for elements of equal priority.
	 *
	 * @var int
	 */
	private $serial = PHP_INT_MAX;

	/**
	 * Contains all registered event listeners.
	 *
	 * @var \SplPriorityQueue[]
	 */
	private $listeners = array();

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
	 * @param callable $listener The listener function that processes the event. This function
	 * must accept an event object as its only parameter and must return nothing.
	 *
	 * @param int $priority The priority level of the event listener. The higher the number,
	 * the higher the priority. All listeners with priority n are processed before listeners
	 * of priority n-1. If two or more listeners share the same priority, they are processed
	 * in the order in which they were added. The default priority is 0.
	 */
	public function addEventListener($type, callable $listener, $priority = 0) {

		if (!isset($this->listeners[$type])) {
			$this->listeners[$type] = new SplPriorityQueue();
		}

		$this->listeners[$type]->insert($listener, array($priority, $this->serial--));
	}

	/**
	 * Checks whether the `EventDispatcher` object has any listeners
	 * registered for a specific type of event.
	 *
	 * @param string $type The type of event.
	 * @return boolean True if a listener of the specified type is registered, false otherwise.
	 */
	public function hasEventListener($type) {

		return isset($this->listeners[$type]);
	}

	/**
	 * Removes a listener from the `EventDispatcher` object. If there is no
	 * matching listener registered with the `EventDispatcher` object, a call
	 * to this method has no effect.
	 *
	 * @param string $type The type of event.
	 * @param callable  $listener The listener object to remove.
	 */
	public function removeEventListener($type, callable $listener) {

		if (!$this->hasEventListener($type)) {
			return;
		}

		$newQueue = new SplPriorityQueue();
		$oldQueue = $this->listeners[$type];
		$oldQueue->setExtractFlags(SplPriorityQueue::EXTR_BOTH);
		while ($oldQueue->valid()) {
			$current = $oldQueue->current();
			$oldQueue->next();
			if ($current['data'] === $listener) {
				continue;
			}

			$newQueue->insert($current['data'], $current['priority']);
		}

		if ($newQueue->isEmpty()) {
			unset($this->listeners[$type]);
		} else {
			$this->listeners[$type] = $newQueue;
		}
	}

	/**
	 * Dispatches an event into the event flow. The event target is the `EventDispatcher`
	 * object upon which `dispatchEvent()` is called.
	 *
	 * @param Event $event The event object dispatched into the event flow.
	 */
	public function dispatchEvent(Event $event) {

		$type = $event->getType();
		if (!$this->hasEventListener($type)) {
			return;
		}

		$event->setTarget($this);
		$queue = clone $this->listeners[$type];
		while ($queue->valid()) {
			/* @var $listener */
			$listener = $queue->current();
			$queue->next();
			if (is_array($listener)) {
				call_user_func($listener, $event);
			} else {
				$listener($event);
			}
		}
	}
}
