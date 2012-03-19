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

/**
 * Interface to be implemented by objects that can load resources.
 *
 * @category  Mohiva/Common
 * @package   Mohiva/Common/IO
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
interface ResourceLoader {

	/**
	 * Return a Resource handle for the specified path.
	 *
	 * The implementation must detect what type of resource handle
	 * should be loaded for the given path.
	 *
	 * @param string $path The path to the resource.
	 * @return Resource The corresponding resource handle.
	 */
	public function getResource($path);

	/**
	 * Return a Resource handle of the given type for the specified path.
	 *
	 * @param string $path The path to the resource.
	 * @param string $type The FQN of the resource handle.
	 * @return Resource The corresponding resource handle.
	 */
	public function getResourceByType($path, $type);

	/**
	 * Specify the ClassLoader to load resources with.
	 *
	 * @param ClassLoader $classLoader The `ClassLoader` to load resources with.
	 */
	public function setClassLoader(ClassLoader $classLoader);

	/**
	 * Return the `ClassLoader` to load resources with.
	 *
	 * @return ClassLoader The `ClassLoader` to load resources with.
	 */
	public function getClassLoader();
}
