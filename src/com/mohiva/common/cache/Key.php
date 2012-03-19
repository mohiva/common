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
 * @package   Mohiva/Common/Cache
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
namespace com\mohiva\common\cache;

/**
 * Cache key interface.
 *
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Cache
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
interface Key {

	/**
	 * Set the source for the key.
	 *
	 * @param string $source The source for the key to create.
	 */
	public function set($source);

	/**
	 * Append a string to the source.
	 *
	 * @param string $string The string to append.
	 */
	public function append($string);

	/**
	 * Create the key and return it as string.
	 *
	 * @return string The created key.
	 */
	public function create();

	/**
	 * If the object is treated like as string
	 * then create the key and return it.
	 *
	 * @return string The created key.
	 */
	public function __toString();
}
