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

use Exception;
use com\mohiva\common\io\exceptions\MalformedNameException;
use com\mohiva\common\io\exceptions\MissingDefinitionException;
use com\mohiva\common\io\exceptions\ClassNotFoundException;
use com\mohiva\common\io\exceptions\FileNotFoundException;

require_once 'AbstractClassLoader.php';

/**
 * `ClassLoader` implementation which loads classes which matches the registered namespaces.
 *
 * This is a full PSR-0 compatible class loader implementation as proposed by
 * the Framework Interoperability Group. Fore more information visit the proposal
 * page: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md
 *
 * @category  Mohiva/Common
 * @package   Mohiva/Common/IO
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class NamespaceClassLoader extends AbstractClassLoader {

	/**
	 * The registered namespaces and their associated paths.
	 *
	 * @var array
	 */
	private $namespaces = [];

	/**
	 * Registers a namespace/path pair.
	 *
	 * A namespace should not be defined fully qualified. This means it should be defined without the leading
	 * namespace separator.
	 *
	 * @param string $namespace The namespace for which the path should be registered.
	 * @param string $path The lookup path for the classes which matches the given namespace.
	 */
	public function register($namespace, $path) {

		$namespace = ltrim($namespace, '\\');
		$this->namespaces[$namespace] = $path;
	}

	/**
	 * Loads the given class from include path.
	 *
	 * @param string $fqn The fully qualified name of the class to load.
	 * @throws MalformedNameException if the class name contains illegal characters.
	 * @throws ClassNotFoundException if the class cannot be found.
	 */
	public function load($fqn) {

		if (class_exists($fqn, false) || interface_exists($fqn, false)) {
			return;
		} else if (!$this->isValid($fqn)) {
			require_once __DIR__ . '/../exceptions/MohivaException.php';
			require_once __DIR__ . '/../exceptions/SecurityException.php';
			require_once 'exceptions/MalformedNameException.php';
			throw new MalformedNameException("The class name `{$fqn}` contains illegal characters");
		}

		try {
			$path = $this->getNamespacePath($fqn);
			$fileName = $this->toFileName($fqn);
			$fileName = $this->getClassFileFromPath($path, $fileName);
			$this->loadClassFromFile($fqn, $fileName);
		} catch (Exception $e) {
			require_once __DIR__ . '/../exceptions/MohivaException.php';
			require_once 'exceptions/IOException.php';
			require_once 'exceptions/ClassNotFoundException.php';
			throw new ClassNotFoundException("The class `{$fqn}` cannot be found", 0, $e);
		}
	}

	/**
	 * Gets the path for the namespace which matches the given FQN.
	 *
	 * @param string $fqn A fully qualified name.
	 * @return string The lookup path for the given class.
	 * @throws MissingDefinitionException if the FQN doesn't match a namespace.
	 */
	private function getNamespacePath($fqn) {

		$fqn = ltrim($fqn, '\\');
		foreach ($this->namespaces as $namespace => $path) {
			if (strpos($fqn, $namespace) === 0) {
				return $path;
			}
		}

		require_once __DIR__ . '/../exceptions/MohivaException.php';
		require_once 'exceptions/MissingDeclarationException.php';
		throw new MissingDefinitionException("No namespace for class or interface `{$fqn}` defined");
	}

	/**
	 * This method checks if the given file exists in the given path.
	 *
	 * @param string $path The path from which the file should be loaded.
	 * @param string $fileName The name of the file to load.
	 * @return string The absolute path to the file.
	 * @throws FileNotFoundException if the given file cannot be found in the given path or it isn't readable.
	 */
	private function getClassFileFromPath($path, $fileName) {

		$file = $path . DIRECTORY_SEPARATOR . $fileName;
		if (!is_readable($file)) {
			require_once __DIR__ . '/../exceptions/MohivaException.php';
			require_once 'exceptions/IOException.php';
			require_once 'exceptions/FileNotFoundException.php';
			throw new FileNotFoundException(
				"The file `{$fileName}` doesn't exists in path `{$path}` or it isn't readable"
			);
		}

		return $file;
	}
}
