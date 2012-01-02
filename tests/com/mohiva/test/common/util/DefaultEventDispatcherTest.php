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
 * @package   Mohiva/Common/Test
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
namespace com\mohiva\test\common\util;

use com\mohiva\common\util\DefaultEventDispatcher;
use com\mohiva\test\resources\common\util\TestEvent;

/**
 * Unit test case for the `DefaultEventDispatcher` class.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Test
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class DefaultEventDispatcherTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * Test if the `addEventListener` method accepts all types of callbacks.
	 */
	public function testAddEventListenerAcceptsCallbacks() {
		
		$dispatcher = new DefaultEventDispatcher();
		$dispatcher->addEventListener('onTest1', array($this, 'testAddEventListenerAcceptsCallbacks'));
		$dispatcher->addEventListener('onTest2', function() {});
		$dispatcher->addEventListener('onTest3', 'trim');
	}
	
	/**
	 * Test if the `addEventListener` method throws an exception if the listener 
	 * isn't a callback.
	 * 
	 * @expectedException \InvalidArgumentException
	 */
	public function testAddEventListenerThrowsException() {
		
		$dispatcher = new DefaultEventDispatcher();
		$dispatcher->addEventListener('onTest', array());
	}
	
	/**
	 * Test if the `addEventListener` stores multiple listeners.
	 */
	public function testAddEventListenerStoresMultipleListeners() {
		
		$dispatcher = new DefaultEventDispatcher();
		$dispatcher->addEventListener('onTest1', function() {});
		$dispatcher->addEventListener('onTest2', function() {});
		
		$this->assertTrue($dispatcher->hasEventListener('onTest1'));
		$this->assertTrue($dispatcher->hasEventListener('onTest2'));
	}
	
	/**
	 * Test if the `hasEventListener` method returns `true` if
	 * a listener for a given type is registered.
	 */
	public function testHasEventListenerReturnsTrue() {
		
		$dispatcher = new DefaultEventDispatcher();
		$dispatcher->addEventListener('onTest', function() {});
		
		$this->assertTrue($dispatcher->hasEventListener('onTest'));
	}
	
	/**
	 * Test if the `hasEventListener` method returns `false` if
	 * a listener for a given type isn't registered.
	 */
	public function testHasEventListenerReturnsFalse() {
		
		$dispatcher = new DefaultEventDispatcher();
		
		$this->assertFalse($dispatcher->hasEventListener('onTest'));
	}
	
	/**
	 * Test if the `removeEventListener` method accepts all types of callbacks.
	 */
	public function testRemoveEventListenerAcceptsCallbacks() {
		
		$dispatcher = new DefaultEventDispatcher();
		$dispatcher->removeEventListener('onTest1', array($this, 'testRemoveEventListenerAcceptsCallbacks'));
		$dispatcher->removeEventListener('onTest2', function() {});
		$dispatcher->removeEventListener('onTest3', 'trim');
	}
	
	/**
	 * Test if the `removeEventListener` method throws an exception if the listener 
	 * isn't a callback.
	 * 
	 * @expectedException \InvalidArgumentException
	 */
	public function testRemoveEventListenerThrowsException() {
		
		$dispatcher = new DefaultEventDispatcher();
		$dispatcher->removeEventListener('onTest', array());
	}
	
	/**
	 * Test if the `removeEventListener` doesn't throw an error if we try 
	 * to remove a not existing event listener for an not existing type.
	 */
	public function testRemoveEventListenerForNotExistingType() {
		
		$dispatcher = new DefaultEventDispatcher();
		$dispatcher->removeEventListener('onTest', function() {});
	}
	
	/**
	 * Test if the `removeEventListener` doesn't throw an error if we try 
	 * to remove a not existing event listener for an existing type.
	 */
	public function testRemoveNotExistingListenerForExistingType() {
		
		$dispatcher = new DefaultEventDispatcher();
		$dispatcher->addEventListener('onTest', function() {});
		$dispatcher->removeEventListener('onTest', function() {});
	}
	
	/**
	 * Test if the `removeEventListener` method removes a single listener.
	 */
	public function testRemoveEventListenerRemovesSingleListener() {
		
		$listener = function() {};
		$dispatcher = new DefaultEventDispatcher();
		$dispatcher->addEventListener('onTest', $listener);
		$dispatcher->removeEventListener('onTest', $listener);
		
		$this->assertFalse($dispatcher->hasEventListener('onTest'));
	}
	
	/**
	 * Test if the `removeEventListener` method removes multiple listeners.
	 */
	public function testRemoveEventListenerRemovesMultipleListeners() {
		
		$listener1 = function() {};
		$listener2 = function() {};
		$dispatcher = new DefaultEventDispatcher();
		$dispatcher->addEventListener('onTest', $listener1);
		$dispatcher->addEventListener('onTest', $listener2);
		$dispatcher->removeEventListener('onTest', $listener1);
		$dispatcher->removeEventListener('onTest', $listener2);
		
		$this->assertFalse($dispatcher->hasEventListener('onTest'));
	}
	
	/**
	 * Test if the `removeEventListener` method removes only a single listener, even
	 * if multiple listeners for the same type are registered.
	 */
	public function testRemoveEventListenerRemovesSingleListenerForSameType() {
		
		$listener1 = function() {};
		$listener2 = function() {};
		$dispatcher = new DefaultEventDispatcher();
		$dispatcher->addEventListener('onTest', $listener1);
		$dispatcher->addEventListener('onTest', $listener2);
		$dispatcher->removeEventListener('onTest', $listener1);
		
		$this->assertTrue($dispatcher->hasEventListener('onTest'));
	}
	
	/**
	 * Test if the `dispatchEvent` method does not throw an error if we try to 
	 * dispatch a not existing type.
	 */
	public function testDispatchNotExistingType() {
		
		$dispatcher = new DefaultEventDispatcher();
		$dispatcher->dispatchEvent(new TestEvent('onTest'));
	}
	
	/**
	 * Test if the `dispatchEvent` method can dispatch an `array` listener.
	 */
	public function testDispatchArrayListener() {
		
		$event = new TestEvent('onTest');
		
		$this->assertFalse($event->dispatched);
		
		$dispatcher = new DefaultEventDispatcher();
		$dispatcher->addEventListener('onTest', array($this, 'onTest'));
		$dispatcher->dispatchEvent($event);
		
		$this->assertTrue($event->dispatched);
	}
	
	/**
	 * Test if the `dispatchEvent` method can dispatch a `Closure` listener.
	 */
	public function testDispatchClosureListener() {
		
		$event = new TestEvent('onTest');
		
		$this->assertFalse($event->dispatched);
		
		$dispatcher = new DefaultEventDispatcher();
		$dispatcher->addEventListener('onTest', function(TestEvent $event) { $event->dispatched = true; });
		$dispatcher->dispatchEvent($event);
		
		$this->assertTrue($event->dispatched);
	}
	
	/**
	 * Test if the `dispatchEvent` method can dispatch an `string` listener.
	 */
	public function testDispatchStringListener() {
		
		function onTest(TestEvent $event) { $event->dispatched = true; }
		
		$event = new TestEvent('onTest');
		
		$this->assertFalse($event->dispatched);
		
		$dispatcher = new DefaultEventDispatcher();
		$dispatcher->addEventListener('onTest', 'com\mohiva\test\common\util\onTest');
		$dispatcher->dispatchEvent($event);
		
		$this->assertTrue($event->dispatched);
	}
	
	/**
	 * Test if the `dispatchEvent` method dispatches the listeners by the set priority.
	 */
	public function testDispatchesByPriority() {
		
		$event = new TestEvent('onTest');
		
		$dispatcher = new DefaultEventDispatcher();
		$dispatcher->addEventListener('onTest', function(TestEvent $event) { $event->dispatchList[] = 3;      }, 3);
		$dispatcher->addEventListener('onTest', function(TestEvent $event) { $event->dispatchList[] = 1;      }, 1);
		$dispatcher->addEventListener('onTest', function(TestEvent $event) { $event->dispatchList[] = -6.1;   }, -6);
		$dispatcher->addEventListener('onTest', function(TestEvent $event) { $event->dispatchList[] = 9;      }, 9);
		$dispatcher->addEventListener('onTest', function(TestEvent $event) { $event->dispatchList[] = 7;      }, 7);
		$dispatcher->addEventListener('onTest', function(TestEvent $event) { $event->dispatchList[] = 4.1;    }, 4);
		$dispatcher->addEventListener('onTest', function(TestEvent $event) { $event->dispatchList[] = 2;      }, 2);
		$dispatcher->addEventListener('onTest', function(TestEvent $event) { $event->dispatchList[] = 4.2;    }, 4);
		$dispatcher->addEventListener('onTest', function(TestEvent $event) { $event->dispatchList[] = -6.2;   }, -6);
		$dispatcher->dispatchEvent($event);
		
		$this->assertEquals(array(9, 7, 4.1, 4.2, 3, 2, 1, -6.1, -6.2), $event->dispatchList);
	}
	
	/**
	 * Test if the `dispatchEvent` method dispatches the listeners by the insertion order.
	 */
	public function testDispatchBySamePriority() {
		
		$event = new TestEvent('onTest');
		
		$dispatcher = new DefaultEventDispatcher();
		$dispatcher->addEventListener('onTest', function(TestEvent $event) { $event->dispatchList[] = 1; });
		$dispatcher->addEventListener('onTest', function(TestEvent $event) { $event->dispatchList[] = 2; });
		$dispatcher->addEventListener('onTest', function(TestEvent $event) { $event->dispatchList[] = 3; });
		$dispatcher->addEventListener('onTest', function(TestEvent $event) { $event->dispatchList[] = 4; });
		$dispatcher->addEventListener('onTest', function(TestEvent $event) { $event->dispatchList[] = 5; });
		$dispatcher->addEventListener('onTest', function(TestEvent $event) { $event->dispatchList[] = 6; });
		$dispatcher->addEventListener('onTest', function(TestEvent $event) { $event->dispatchList[] = 7; });
		$dispatcher->dispatchEvent($event);
		
		$this->assertEquals(array(1, 2, 3, 4, 5, 6, 7), $event->dispatchList);
	}
	
	/**
	 * Test if the `removeEventListener` method keeps the priority for the registered listeners.
	 * 
	 * The problem is that the `SplPriorityQueue` class doesn't support a remove method. 
	 * To work around we must copy one queue to another and leaving out the value to remove. During
	 * the copy process the priority must be kept.
	 */
	public function testRemoveKeepsPriority() {
		
		$event = new TestEvent('onTest');
		$removable = function(TestEvent $event) {};
		
		$dispatcher = new DefaultEventDispatcher();
		$dispatcher->addEventListener('onTest', function(TestEvent $event) { $event->dispatchList[] = 3;   }, 3);
		$dispatcher->addEventListener('onTest', function(TestEvent $event) { $event->dispatchList[] = 1;   }, 1);
		$dispatcher->addEventListener('onTest', function(TestEvent $event) { $event->dispatchList[] = 6;   }, 6);
		$dispatcher->addEventListener('onTest', function(TestEvent $event) { $event->dispatchList[] = 9;   }, 9);
		$dispatcher->addEventListener('onTest', function(TestEvent $event) { $event->dispatchList[] = 7;   }, 7);
		$dispatcher->addEventListener('onTest', function(TestEvent $event) { $event->dispatchList[] = 4.1; }, 4);
		$dispatcher->addEventListener('onTest', function(TestEvent $event) { $event->dispatchList[] = 2;   }, 2);
		$dispatcher->addEventListener('onTest', function(TestEvent $event) { $event->dispatchList[] = 4.2; }, 4);
		$dispatcher->addEventListener('onTest', $removable, 5);
		$dispatcher->removeEventListener('onTest', $removable);
		$dispatcher->dispatchEvent($event);
		
		$this->assertEquals(array(9, 7, 6, 4.1, 4.2, 3, 2, 1), $event->dispatchList);
	}
	
	/**
	 * Test if the `dispatchEvent` method set the "target" property of the `Event` object.
	 */
	public function testDispatchEventSetEventTarget() {
		
		$event = new TestEvent('onTest');
		$dispatcher = new DefaultEventDispatcher();
		$dispatcher->addEventListener('onTest', function(TestEvent $event) {});
		$dispatcher->dispatchEvent($event);
		
		$this->assertSame($dispatcher, $event->getTarget());
	}
	
	/**
	 * Event listener method.
	 * 
	 * @param \com\mohiva\test\resources\common\util\TestEvent $event
	 */
	public function onTest(TestEvent $event) {
		
		$event->dispatched = true;
	}
}
