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

use com\mohiva\common\io\IncludePath;
use com\mohiva\common\io\exceptions\FileNotFoundException;

/**
 * Unit test case for the `IncludePath` class.
 *
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Test
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class IncludePathTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Stores the original include path.
	 *
	 * @var string
	 */
	private $includePath;

	/**
	 * Setup the test case.
	 */
	public function setUp() {

		// Store the original include path
		$this->includePath = get_include_path();
	}

	/**
	 * Tear down the test case.
	 */
	public function tearDown() {

		// Restore the original include path
		IncludePath::resetPath();
		set_include_path($this->includePath);
	}

	/**
	 * Test if can get the include path.
	 */
	public function testGetIncludePath() {

		$this->assertEquals($this->includePath, IncludePath::getPath());
	}

	/**
	 * Test if can set the include path.
	 */
	public function testSetIncludePath() {

		$beforeIncludePath = get_include_path();
		set_include_path($beforeIncludePath . PATH_SEPARATOR . './test');
		IncludePath::setPath($beforeIncludePath);

		$this->assertEquals($beforeIncludePath, get_include_path());
	}

	/**
	 * Check if a existing realtive path can be added to an include path.
	 *
	 * @return string The relative include path.
	 */
	public function testIfCanAddValidRealtiveIncludePath() {

		$addedPath = null;
		$includePath = '.';
		$beforeIncludePath = get_include_path();

		try {
			$addedPath = IncludePath::addPath($includePath);
		} catch (\Exception $e) {
			$this->fail($e->getMessage());
		}

		$afterIncludePath = get_include_path();
		$this->assertEquals($afterIncludePath, $addedPath . PATH_SEPARATOR . $beforeIncludePath);

		return $includePath;
	}

	/**
	 * Check if a `DirectoryNotFoundException` will
	 * be thrown when adding a non existing relative path.
	 */
	public function testIfFailsOnAddingInvalidRealtiveIncludePath() {

		$includePath = './notExistingPath';
		$beforeIncludePath = get_include_path();

		try {
			IncludePath::addPath($includePath);
			$this->fail('FileNotFoundException was expected but never thrown');
		} catch (FileNotFoundException $e) {
			$afterIncludePath = get_include_path();
			$this->assertEquals($afterIncludePath, $beforeIncludePath);
		}
	}

	/**
	 * Check if a existing absolute path can be added to an include path.
	 *
	 * @return The absolute include path.
	 */
	public function testIfCanAddValidAbsoluteIncludePath() {

		$addedPath = null;
		$includePath = __DIR__;
		$beforeIncludePath = get_include_path();

		try {
			$addedPath = IncludePath::addPath($includePath);
		} catch (\Exception $e) {
			$this->fail($e->getMessage());
		}

		$afterIncludePath = get_include_path();
		$this->assertEquals($afterIncludePath, $addedPath . PATH_SEPARATOR . $beforeIncludePath);

		return $includePath;
	}

	/**
	 * Check if a `DirectoryNotFoundException` will
	 * be thrown when adding a non existing relative path.
	 */
	public function testIfFailsOnAddingInvalidAbsoluteIncludePath() {

		$includePath = __DIR__ . '/notExistingPath';
		$beforeIncludePath = get_include_path();

		try {
			IncludePath::addPath($includePath);
			$this->fail('FileNotFoundException was expected but never thrown');
		} catch (FileNotFoundException $e) {
			$afterIncludePath = get_include_path();
			$this->assertEquals($afterIncludePath, $beforeIncludePath);
		}
	}

	/**
	 * Check if a existing realtive path can be removed from include path.
	 */
	public function testIfCanRemoveValidRealtiveIncludePath() {

		$addedPath = null;
		$includePath = $this->testIfCanAddValidRealtiveIncludePath();
		$beforeIncludePath = get_include_path();

		try {
			$addedPath = IncludePath::removePath($includePath);
		} catch (\Exception $e) {
			$this->fail($e->getMessage());
		}

		$afterIncludePath = get_include_path();
		$this->assertEquals($addedPath . PATH_SEPARATOR . $afterIncludePath, $beforeIncludePath);
	}

	/**
	 * Check if a `DirectoryNotFoundException` will
	 * be thrown when removing a non existing relative path.
	 */
	public function testIfFailsOnRemovingInvalidRealtiveIncludePath() {

		$includePath = './notExistingPath';
		$beforeIncludePath = get_include_path();

		try {
			IncludePath::removePath($includePath);
			$this->fail('FileNotFoundException was expected but never thrown');
		} catch (FileNotFoundException $e) {
			$afterIncludePath = get_include_path();
			$this->assertEquals($afterIncludePath, $beforeIncludePath);
		}
	}

	/**
	 * Check if a existing absolute path can be removed from include path.
	 */
	public function testIfCanRemoveValidAbsoluteIncludePath() {

		$addedPath = null;
		$includePath = $this->testIfCanAddValidAbsoluteIncludePath();
		$beforeIncludePath = get_include_path();

		try {
			$addedPath = IncludePath::removePath($includePath);
		} catch (\Exception $e) {
			$this->fail($e->getMessage());
		}

		$afterIncludePath = get_include_path();
		$this->assertEquals($addedPath . PATH_SEPARATOR . $afterIncludePath, $beforeIncludePath);
	}

	/**
	 * Check if a `DirectoryNotFoundException` will
	 * be thrown when removing a non existing absolute path.
	 */
	public function testIfFailsOnRemovingInvalidAbsoluteIncludePath() {

		$includePath = __DIR__ . '/notExistingPath';
		$beforeIncludePath = get_include_path();

		try {
			IncludePath::removePath($includePath);
			$this->fail('FileNotFoundException was expected but never thrown');
		} catch (FileNotFoundException $e) {
			$afterIncludePath = get_include_path();
			$this->assertEquals($afterIncludePath, $beforeIncludePath);
		}
	}

	/**
	 * Test if real paths can be resolved.
	 */
	public function testIfCanResolveRealPaths() {

		$firstPath = null;
		$secondPath = null;
		try {
			$firstPath = IncludePath::resolvePath(__DIR__);
			$secondPath = IncludePath::resolvePath('.');
		} catch (\Exception $e) {
			$this->fail($e->getMessage());
		}

		$this->assertTrue($firstPath === __DIR__);
		$this->assertTrue($secondPath === realpath($secondPath));
	}

	/**
	 * Test if a the method `resolvePath` throws an
	 * `DirectoryNotFoundException` with invalid paths.
	 */
	public function testIfResolvePathFailsOnInvalidPaths() {

		try {
			IncludePath::resolvePath(__DIR__ . '/notExistingPath');
			$this->fail('FileNotFoundException was expected but never thrown');
		} catch (FileNotFoundException $e) {}

		try {
			IncludePath::resolvePath('./notExistingPath');
			$this->fail('FileNotFoundException was expected but never thrown');
		} catch (FileNotFoundException $e) {}
	}
}
