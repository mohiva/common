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

use Exception;
use com\mohiva\test\common\Bootstrap;
use com\mohiva\common\io\exceptions\ClassNotFoundException;
use com\mohiva\common\io\NamespaceClassLoader;

/**
 * Unit test case for the `NamespaceClassLoader` class.
 *
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Test
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class NamespaceClassLoaderTest extends AbstractClassLoaderTest {

	/**
	 * Path to the fixtures to test.
	 *
	 * @var string
	 */
	const NOT_DEFINED = '\not\defined\namespace\for\ClassFixture';

	/**
	 * The loader instance.
	 *
	 * @var NamespaceClassLoader
	 */
	protected $loader = null;

	/**
	 * Setup the test case.
	 */
	public function setUp() {

		$this->loader = new NamespaceClassLoader();
		$this->loader->register('com\mohiva\test\resources', Bootstrap::$testDir);
		$this->loader->register('com_mohiva_test_resources', Bootstrap::$testDir);
	}

	/**
	 * Check if the loader throws a `MissingDefinitionException` if no namespace which matches the class
	 * name is defined.
	 */
	public function testLoaderThrowsMissingDefinitionException() {

		try {
			$this->loader->load(self::NOT_DEFINED);
		} catch (ClassNotFoundException $e) {
			$this->assertInstanceOf('com\mohiva\common\io\exceptions\MissingDefinitionException',
				$e->getPrevious()
			);
		} catch (Exception $e) {
			$this->fail($e->getMessage());
		}
	}
}
