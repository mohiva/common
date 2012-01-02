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

use com\mohiva\test\common\Bootstrap;
use com\mohiva\common\io\ClassLoader;
use com\mohiva\common\io\ClassAutoloader;
use com\mohiva\common\io\DefaultClassLoader;

/**
 * Unit test case for the `ClassAutoloader` class.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Test
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class ClassAutoloaderTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * Contains the registered autoloader for this test case.
	 * 
	 * @var \com\mohiva\common\io\ClassAutoloader
	 */
	private $autoLoader = null;
	
	/**
	 * Setup the test case.
	 */
	public function setUp() {
		
		Bootstrap::$autoloader->unregister();
	}
	
	/**
	 * Tear down the test case.
	 */
	public function tearDown() {
		
		if ($this->autoLoader) $this->autoLoader->unregister();
		Bootstrap::$autoloader->register();
	}
	
	/**
	 * Test all getters for the values set with the constructor.
	 */
	public function testConstructorAccessors() {
		
		$classLoader = new DefaultClassLoader();
		$autoLoader = new ClassAutoloader($classLoader);
		
		$this->assertSame($classLoader, $autoLoader->getClassLoader());
	}
	
	/**
	 * Test the `setPolicy` and `getPolicy` accessors.
	 */
	public function testPolicyAccessors() {
		
		$autoLoader = new ClassAutoloader();
		$autoLoader->setPolicy(ClassAutoloader::POLICY_EXCEPTION);
		$autoLoader->setPolicy(ClassAutoloader::POLICY_SILENT);
		
		$this->assertSame(ClassAutoloader::POLICY_SILENT, $autoLoader->getPolicy());
	}
	
	/**
	 * Test if the `setPolicy` method throws an exception if an invalid value is given.
	 * 
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetPolicyThrowsException() {
		
		$autoLoader = new ClassAutoloader();
		$autoLoader->setPolicy(microtime(true));
	}
	
	/**
	 * Test the `registerNamespace` and `getNamespaces` accessors.
	 */
	public function testNamespaceAccessors() {
		
		$autoLoader = new ClassAutoloader();
		$autoLoader->registerNamespace('com\mohiva');
		
		$this->assertSame(array('com\mohiva'), $autoLoader->getNamespaces());
	}
	
	/**
	 * Check if the autoloader can be registered.
	 */
	public function testIfAutloaderCanBeRegistered() {
		
		$this->autoLoader = new ClassAutoloader();
		$this->autoLoader->register();
		
		$found = false;
		$class = get_class($this->autoLoader);
		$loaders = spl_autoload_functions();
		$loaders = $loaders !== false ? $loaders : array();
		foreach ($loaders as $loader) {
			if ($loader === $this->autoLoader->getCallback()) {
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
		$this->autoLoader = new ClassAutoloader();
		$this->autoLoader->register();
		
		// Unregister autoloader
		$this->autoLoader->unregister();
		
		$found = false;
		$class = get_class($this->autoLoader);
		$loaders = spl_autoload_functions();
		$loaders = $loaders !== false ? $loaders : array();
		foreach ($loaders as $loader) {
			if ($loader === $this->autoLoader->getCallback()) {
				$found = true;
			}
		}
		
		$this->assertFalse($found, "Failed to unregister {$class} with spl_autoload_unregister");
	}
	
	/**
	 * Test if the `isRegistered` method returns true if the autoloader is registered.
	 */
	public function testIsRegisteredReturnsTrue() {
		
		$this->autoLoader = new ClassAutoloader();
		$this->autoLoader->register();
		
		$this->assertTrue($this->autoLoader->isRegistered());
	}
	
	/**
	 * Test if the `isRegistered` method returns false if the autoloader isn't registered.
	 */
	public function testIsRegisteredReturnsFalseIfIsNotRegistered() {
		
		$autoLoader = new ClassAutoloader();
		
		$this->assertFalse($autoLoader->isRegistered());
	}
	
	/**
	 * Test if the `isRegistered` method returns false if the autoloader is unregistered.
	 */
	public function testIsRegisteredReturnsFalseIfIsUnregistered() {
		
		$this->autoLoader = new ClassAutoloader();
		$this->autoLoader->register();
		$this->autoLoader->unregister();
		
		$this->assertFalse($this->autoLoader->isRegistered());
	}
	
	/**
	 * Check if can get the registered callback function.
	 */
	public function testGetCallback() {
		
		$this->autoLoader = new ClassAutoloader();
		$this->autoLoader->register();
		
		$this->assertTrue($this->autoLoader->getCallback() instanceof \Closure);
	}
	
	/**
	 * Check if can get the registered ClassLoader instance.
	 */
	public function testGetClassLoader() {
		
		$autoLoader = new ClassAutoloader();
		
		$this->assertTrue($autoLoader->getClassLoader() instanceof ClassLoader);
	}
	
	/**
	 * Test if the autoloader doesn't throw an exception if the `silent` policy is set.
	 */
	public function testPolicySilent() {
		
		$this->autoLoader = new ClassAutoloader();
		$this->autoLoader->register();
		
		try {
			class_exists('NotExistingClass', true);
		} catch (\Exception $e) {
			$this->fail("Autoloader mustn't throw an exception");
		}
	}
	
	/**
	 * Test if the autoloader throws an exception if the `exception` policy is set.
	 * 
	 * @expectedException \com\mohiva\common\io\exceptions\ClassNotFoundException
	 */
	public function testPolicyException() {
		
		$this->autoLoader = new ClassAutoloader();
		$this->autoLoader->setPolicy(ClassAutoloader::POLICY_EXCEPTION);
		$this->autoLoader->register();
		
		class_exists('NotExistingClass', true);
	}
	
	/**
	 * Test if the autoloader loads any class if no namespace is registered.
	 */
	public function testLoadsClassIfNoNamespaceIsRegistered() {
		
		$this->autoLoader = new ClassAutoloader();
		$this->autoLoader->register();
		
		$this->assertTrue(class_exists('com\mohiva\test\resources\common\io\ClassWithoutRegisteredNamespaces', true));
	}
	
	/**
	 * Test if the autoloader loads a class which matches the registered namespaces.
	 */
	public function testLoadsClassInRegisteredNamespaces() {
		
		$this->autoLoader = new ClassAutoloader();
		$this->autoLoader->registerNamespace('com\mohiva\test\resources\common\io');
		$this->autoLoader->register();
		
		$this->assertTrue(class_exists('com\mohiva\test\resources\common\io\ClassInRegisteredNamespaces', true));
	}
	
	/**
	 * Test if the autoloader skips a class which doesn't match the registered namespaces.
	 */
	public function testSkipsClassNotInRegisteredNamespaces() {
		
		$this->autoLoader = new ClassAutoloader();
		$this->autoLoader->registerNamespace('com\mohiva\test\resources\common\not\existing');
		$this->autoLoader->register();
		
		$this->assertFalse(class_exists('com\mohiva\test\resources\common\io\ClassNotInRegisteredNamespaces', true));
	}
}
