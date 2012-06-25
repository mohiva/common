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

use InvalidArgumentException;
use com\mohiva\common\io\exceptions\ClassNotFoundException;

/**
 * Registers the implementation of the `ClassLoader` interface
 * with the SPL autoloader stack.
 *
 * @category  Mohiva/Common
 * @package   Mohiva/Common/IO
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class ClassAutoloader {

	/**
	 * The silent policy catches all `ClassNotFoundExceptions` which could occur during
	 * the autoloader process. All other exceptions thrown by the class loader are not
	 * caught.
	 *
	 * @var int
	 */
	const POLICY_SILENT = 1;

	/**
	 * The exception policy let through all `ClassNotFoundExceptions` which could occur during
	 * the autoloader process.
	 *
	 * @var int
	 */
	const POLICY_EXCEPTION = 2;

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
	 * The policy for this autoloader.
	 *
	 * @var int
	 */
	private $policy = self::POLICY_SILENT;

	/**
	 * A list with namespace for which the autoloader is responsible.
	 *
	 * @var array
	 */
	private $namespaces = array();

	/**
	 * The number of registered namespaces.
	 *
	 * @var int
	 */
	private $namespaceCnt = 0;

	/**
	 * The class constructor.
	 *
	 * @param ClassLoader $classLoader The `ClassLoader` implementation to use for class loading
	 * or null to use the `IncludePathClassLoader` implementation.
	 */
	public function __construct(ClassLoader $classLoader = null) {

		if ($classLoader instanceof ClassLoader) {
			$this->classLoader = $classLoader;
		} else {
			require_once 'ClassLoader.php';
			require_once 'IncludePathClassLoader.php';
			$this->classLoader = new IncludePathClassLoader(false);
		}
	}

	/**
	 * Set the policy of the autoloader.
	 *
	 * @param int $policy One of the defined ClassAutoloader::POLICY_* constants.
	 * @throws InvalidArgumentException if the policy value is invalid.
	 */
	public function setPolicy($policy) {

		if ($policy !== self::POLICY_SILENT && $policy !== self::POLICY_EXCEPTION) {
			throw new InvalidArgumentException("Invalid value `{$policy}` for autoloader policy given");
		}

		$this->policy = $policy;
	}

	/**
	 * Gets the policy of the autoloader.
	 *
	 * @return int The value of one of the defined ClassAutoloader::POLICY_* constants.
	 */
	public function getPolicy() {

		return $this->policy;
	}

	/**
	 * Register a namespace for which the autoloader is responsible.
	 *
	 * Namespaces can be used to guarantee that a autoloader load only classes for a particular project.
	 *
	 * @param string $namespace A namespace which must be contained in the FQN of a class so that the autoloader
	 * can load it.
	 */
	public function registerNamespace($namespace) {

		$this->namespaces[] = $namespace;
		$this->namespaceCnt++;
	}

	/**
	 * Returns the list of registered namespaces.
	 *
	 * @return array The list of registered namespaces.
	 */
	public function getNamespaces() {

		return $this->namespaces;
	}

	/**
	 * Register the `load` method of the `ClassLoader`
	 * implementation with the spl autoload stack.
	 *
	 * @param boolean $throw This parameter specifies whether `register()` should throw
	 * exceptions on error.
	 *
	 * @param boolean $prepend If true, `register()` will prepend the autoloader on the
	 * autoload stack instead of appending it.
	 */
	public function register($throw = true, $prepend = true) {

		$this->callback = function($fqn) {

			if ($this->namespaceCnt && !$this->matchNamespace($fqn)) return;

			try {
				$this->classLoader->load($fqn);
			} catch (ClassNotFoundException $e) {
				if ($this->policy === self::POLICY_EXCEPTION) {
					throw $e;
				}
			}
		};

		spl_autoload_register($this->callback, $throw, $prepend);
	}

	/**
	 * Unregister the `load` Method of the `ClassLoader`
	 * implementation from the spl autoload stack.
	 */
	public function unregister() {

		if (!$this->isRegistered()) {
			return;
		}

		spl_autoload_unregister($this->callback);
		$this->callback = null;
	}

	/**
	 * Indicates if the autoloader is registered or not.
	 *
	 * @return boolean True if the autoloader is registered, false otherwise.
	 */
	public function isRegistered() {

		return $this->callback !== null;
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

	/**
	 * Check if the given FQN match one of the registered namespaces.
	 *
	 * @param string $fqn The FQN to match against the registered namespaces.
	 * @return boolean True if the FQN matches on of the registered namespaces, false otherwise.
	 */
	private function matchNamespace($fqn) {

		for ($i = 0; $i < $this->namespaceCnt; $i++) {
			if (strpos($fqn, $this->namespaces[$i]) !== false) {
				return true;
			}
		}

		return false;
	}
}
