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
namespace com\mohiva\test\common\io;

use com\mohiva\common\io\DefaultResourceLoader;
use com\mohiva\common\io\FileResource;

/**
 * Unit test case for the `DefaultResourceLoader` class.
 *
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Test
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class DefaultResourceLoaderTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Test if can get a resource with a registered descriptor.
	 */
	public function testGetResourceWithRegisteredDescriptor() {

		$loader = new DefaultResourceLoader();
		$loader->registerDescriptor(FileResource::DESCRIPTOR, FileResource::TYPE);
		$resource = $loader->getResource('file:' . __FILE__);

		$this->assertInstanceOf(FileResource::TYPE, $resource);
	}

	/**
	 * Test if throws an InvalidArgumentException if no resource descriptor isn't defined in path.
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testGetResourceWithNotDefinedDescriptor() {

		$loader = new DefaultResourceLoader();
		$loader->getResource(__FILE__);
	}

	/**
	 * Test if throws an InvalidArgumentException if the given resource descriptor isn't registered.
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testGetResourceWithNotRegisteredDescriptor() {

		$loader = new DefaultResourceLoader();
		$loader->getResource('notexisting:xml:' . __FILE__);
	}

	/**
	 * Test if can get a resource by type.
	 */
	public function testGetResourceByType() {

		$loader = new DefaultResourceLoader();
		$resource = $loader->getResourceByType(__FILE__, FileResource::TYPE);

		$this->assertInstanceOf(FileResource::TYPE, $resource);
	}

	/**
	 * Test if throws a ClassNotFoundException if using a not existing resource type.
	 *
	 * @expectedException \com\mohiva\common\io\exceptions\ClassNotFoundException
	 */
	public function testGetResourceByTypeWithNotExistingType() {

		$loader = new DefaultResourceLoader();
		$loader->getResourceByType(__FILE__, '\com\mohiva\common\io\NotExistingResource');
	}

	/**
	 * Test if can register a resource descriptor.
	 */
	public function testRegisterDescriptor() {

		$loader = new DefaultResourceLoader();
		$loader->registerDescriptor('test:', 'ResourceClass');

		$this->assertArrayHasKey('test:', $loader->getRegisteredDescriptors());
	}

	/**
	 * Test if can unregister a previous registered descriptor.
	 */
	public function testUnregisterDescriptor() {

		$loader = new DefaultResourceLoader();
		$loader->registerDescriptor('test:', 'ResourceClass');
		$loader->unregisterDescriptor('test:');

		$this->assertArrayNotHasKey('test:', $loader->getRegisteredDescriptors());
	}
}
