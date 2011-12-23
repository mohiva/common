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
 * @copyright Copyright (c) 2007-2011 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
namespace com\mohiva\common\io;

/**
 * Registers the implementation of the `ClassLoader` interface 
 * with the SPL autoloader stack.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/IO
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2011 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class ClassAutoloader {
	
	/**
	 * The `ClassLoader` implementation to use for autoloading.
	 * 
	 * @var ClassLoader
	 */
	private $classLoader = null;
	
	/**
	 * The callback function registered with the SPL autoloader stack.
	 * 
	 * @var Closure
	 */
	private $callback = null;
	
	/**
	 * The class constructor.
	 * 
	 * @param ClassLoader $classLoader The `ClassLoader` implementation to use for class loading 
	 * or null to use the `DefaultClassLoader` implementation.
	 */
	public function __construct(ClassLoader $classLoader = null) {
		
		if ($classLoader instanceof ClassLoader) {
			$this->classLoader = $classLoader;
		} else {
			require_once 'ClassLoader.php';
			require_once 'DefaultClassLoader.php';
			$this->classLoader = new DefaultClassLoader();
		}
	}
	
	/**
	 * Register the `loadClass` method of the `ClassLoader` 
	 * implementation with the spl autoload stack.
	 * 
	 * @param boolean $throw This parameter specifies whether `register()` should throw 
	 * exceptions on error.
	 * 
	 * @param boolean $prepend If true, `register()` will prepend the autoloader on the 
	 * autoload stack instead of appending it.
	 */
	public function register($throw = true, $prepend = true) {
		
		$classLoader = $this->classLoader;
		$this->callback = function($fqn) use ($classLoader) {
			/** @var $classLoader ClassLoader */
			$classLoader->loadClass($fqn, false);
		};
		
		spl_autoload_register($this->callback, $throw, $prepend);
	}
	
	/**
	 * Unregister the `loadClass` Method of the `ClassLoader` 
	 * implementation from the spl autoload stack.
	 */
	public function unregister() {
		
		spl_autoload_unregister($this->callback);
		$this->callback = null;
	}
	
	/**
	 * Return the `ClassLoader` instance used for autoloading.
	 * 
	 * @return ClassLoader The `ClassLoader` instance registered with this autoloader.
	 */
	public function getClassLoader() {
		
		return $this->classLoader;
	}
	
	/**
	 * Return the registered callback function or null if no callback is registered.
	 * 
	 * @return Closure The registered callback function or null if no callback is registered.
	 */
	public function getCallback() {
		
		return $this->callback;
	}
}
