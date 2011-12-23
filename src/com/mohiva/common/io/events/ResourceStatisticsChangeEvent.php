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
 * @package   Mohiva/Common/IO/Events
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2011 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
namespace com\mohiva\common\io\events;

use com\mohiva\common\util\Event;

/**
 * An event that signals the change of resource statistics.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/IO/Events
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2011 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class ResourceStatisticsChangeEvent extends Event {
	
	const CREATION_TIME_CHANGED     = 'onCreationTimeChanged';
	const ACCESS_TIME_CHANGED       = 'onAccessTimeChanged';
	const MODIFICATION_TIME_CHANGED = 'onModificationTimeChanged';
	const SIZE_CHANGED              = 'onSizeChanged';
}
