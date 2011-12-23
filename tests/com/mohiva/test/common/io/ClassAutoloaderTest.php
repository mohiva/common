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

use com\mohiva\common\io\ClassLoader;
use com\mohiva\common\io\ClassAutoloader;

/**
 * Unit test case for the `ClassAutoloader` class.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Test
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2011 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class ClassAutoloaderTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * Contains all registered loaders.
	 * 
	 * @var array
	 */
	private $loaders = array();
	
	/**
	 * Setup the test case.
	 */
	public function setUp() {
		
		// Store all default loaders
		$loaders = spl_autoload_functions();
		$this->loaders = $loaders !== false ? $loaders : array();
		
		// Unregister all default loaders(except phpunit_autoload)
		foreach ($this->loaders as $loader) {
			if ($loader == 'phpunit_autoload') {
				continue;
			}
			spl_autoload_unregister($loader);
		}
	}
	
	/**
	 * Tear down the test case.
	 */
	public function tearDown() {
		
		// Reset all autoloaders which were set during tests(except phpunit_autoload)
		$loaders = spl_autoload_functions();
		$loaders = $loaders !== false ? $loaders : array();
		foreach ($loaders as $loader) {
			if ($loader == 'phpunit_autoload') {
				continue;
			}
			spl_autoload_unregister($loader);
		}
		
		// Restore all default loaders(except phpunit_autoload)
		foreach ($this->loaders as $loader) {
			spl_autoload_register($loader);
		}
	}
	
	/**
	 * Check if the autoloader can be registered.
	 */
	public function testIfAutloaderCanBeRegistered() {
		
		$autoLoader = new ClassAutoloader();
		$autoLoader->register();
		
		$found = false;
		$class = get_class($autoLoader);
		$loaders = spl_autoload_functions();
		$loaders = $loaders !== false ? $loaders : array();
		foreach ($loaders as $loader) {
			if ($loader === $autoLoader->getCallback()) {
				$found = true;
			}
		}
		
		$this->assertTrue($found, "Failed to register {$class} with spl_autoload_register");
	}
	
	/**
	 * Check if the autoloader can be unregistered.
	 */
	public function testIfAutloaderCanBeUnregistered() {
		
		// Register the autoloader first
		$autoLoader = new ClassAutoloader();
		$autoLoader->register();
		
		// Unregister autoloader
		$autoLoader->unregister();
		
		$found = false;
		$class = get_class($autoLoader);
		$loaders = spl_autoload_functions();
		$loaders = $loaders !== false ? $loaders : array();
		foreach ($loaders as $loader) {
			if ($loader === $autoLoader->getCallback()) {
				$found = true;
			}
		}
		
		$this->assertFalse($found, "Failed to unregister {$class} with spl_autoload_unregister");
	}
	
	/**
	 * Check if can get the registered callback function.
	 */
	public function testGetCallback() {
		
		$autoLoader = new ClassAutoloader();
		$autoLoader->register();
		
		$this->assertTrue($autoLoader->getCallback() instanceof \Closure);
	}
	
	/**
	 * Check if can get the registered ClassLoader instance.
	 */
	public function testGetClassLoader() {
		
		$autoLoader = new ClassAutoloader();
		
		$this->assertTrue($autoLoader->getClassLoader() instanceof ClassLoader);
	}
}
