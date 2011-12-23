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
 * @copyright Copyright (c) 2007-2011 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
namespace com\mohiva\test\common\io;

use com\mohiva\common\io\ResourceStatistics;
use com\mohiva\common\io\events\ResourceStatisticsChangeEvent;

/**
 * Unit test case for the `ResourceStatistics` class.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Test
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2011 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class ResourceStatisticsTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * Test the `setCreationTime` and `getCreationTime()` accessors.
	 */
	public function testCreationTimeAccessors() {
		
		$time = time();
		$stat = new ResourceStatistics();
		$stat->setCreationTime($time);
		
		$this->assertSame($time, $stat->getCreationTime());
	}
	
	/**
	 * Test the `setModificationTime` and `getModificationTime()` accessors.
	 */
	public function testModificationTimeAccessors() {
		
		$time = time();
		$stat = new ResourceStatistics();
		$stat->setModificationTime($time);
		
		$this->assertSame($time, $stat->getModificationTime());
	}
	
	/**
	 * Test the `setAccessTime` and `getAccessTime()` accessors.
	 */
	public function testAccessTimeAccessors() {
		
		$time = time();
		$stat = new ResourceStatistics();
		$stat->setAccessTime($time);
		
		$this->assertSame($time, $stat->getAccessTime());
	}
	
	/**
	 * Test the `setSite` and `getSite()` accessors.
	 */
	public function testSizeAccessors() {
		
		$size = time();
		$stat = new ResourceStatistics();
		$stat->setSize($size);
		
		$this->assertSame($size, $stat->getSize());
	}
	
	/**
	 * Test if the `setCreationTime` method dispatches the 
	 * `ResourceStatisticsChangeEvent` event.
	 */
	public function testSetCreationTimeDispatchesEvent() {
		
		$dispatched = false;
		$listener = function(ResourceStatisticsChangeEvent $event) use (&$dispatched) {
			$dispatched = true;
		};
		
		$stat = new ResourceStatistics();
		$stat->addEventListener(ResourceStatisticsChangeEvent::CREATION_TIME_CHANGED, $listener);
		$stat->setCreationTime(time());
		
		$this->assertTrue($dispatched);
	}
	
	/**
	 * Test if the `setModificationTime` method dispatches the 
	 * `ResourceStatisticsChangeEvent` event.
	 */
	public function testSetModificationTimeDispatchesEvent() {
		
		$dispatched = false;
		$listener = function(ResourceStatisticsChangeEvent $event) use (&$dispatched) {
			$dispatched = true;
		};
		
		$stat = new ResourceStatistics();
		$stat->addEventListener(ResourceStatisticsChangeEvent::MODIFICATION_TIME_CHANGED, $listener);
		$stat->setModificationTime(time());
		
		$this->assertTrue($dispatched);
	}
	
	/**
	 * Test if the `setAccessTime` method dispatches the 
	 * `ResourceStatisticsChangeEvent` event.
	 */
	public function testSetAccessTimeDispatchesEvent() {
		
		$dispatched = false;
		$listener = function(ResourceStatisticsChangeEvent $event) use (&$dispatched) {
			$dispatched = true;
		};
		
		$stat = new ResourceStatistics();
		$stat->addEventListener(ResourceStatisticsChangeEvent::ACCESS_TIME_CHANGED, $listener);
		$stat->setAccessTime(time());
		
		$this->assertTrue($dispatched);
	}
	
	/**
	 * Test if the `setSize` method dispatches the 
	 * `ResourceStatisticsChangeEvent` event.
	 */
	public function testSetSizeDispatchesEvent() {
		
		$dispatched = false;
		$listener = function(ResourceStatisticsChangeEvent $event) use (&$dispatched) {
			$dispatched = true;
		};
		
		$stat = new ResourceStatistics();
		$stat->addEventListener(ResourceStatisticsChangeEvent::SIZE_CHANGED, $listener);
		$stat->setSize(1);
		
		$this->assertTrue($dispatched);
	}
}
