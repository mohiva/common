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
use com\mohiva\common\lang\ReflectionClass;
use com\mohiva\common\io\exceptions\MalformedNameException;
use com\mohiva\common\io\exceptions\MissingDeclarationException;
use com\mohiva\common\io\exceptions\ClassNotFoundException;
use com\mohiva\common\io\exceptions\FileNotFoundException;

require_once 'ClassLoader.php';

/**
 * Abstract `ClassLoader` implementation which provides some basic functionality that all class
 * loaders must provide.
 *
 * @category  Mohiva/Common
 * @package   Mohiva/Common/IO
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
abstract class AbstractClassLoader implements ClassLoader {

	/**
	 * Check if a FQN contains illegal characters.
	 *
	 * @param string $fqn The fully qualified name to check.
	 * @return boolean True if the fully qualified name is valid, false otherwise.
	 */
	protected function isValid($fqn) {

		if (preg_match('/^[a-zA-Z_\\\][a-zA-Z0-9_\\\]*$/', $fqn)) {
			return true;
		}

		return false;
	}

	/**
	 * Transforms the given fully qualified name into a file name, based
	 * on the reference implementation of the PHP Standards Working Group.
	 *
	 * @param string $fqn A fully qualified name.
	 * @return string The path to the class file.
	 * @see https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md
	 */
	protected function toFileName($fqn) {

		$fileName = '';
		$className = ltrim($fqn, '\\');
		$lastNsPos = strripos($className, '\\');
		if ($lastNsPos) {
			$namespace = substr($className, 0, $lastNsPos);
			$className = substr($className, $lastNsPos + 1);
			$fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
		}
		$fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

		return $fileName;
	}

	/**
	 * Includes the file and check if a class or interface declaration for the
	 * given name exists in it.
	 *
	 * @param string $fqn The fully qualified name to check for.
	 * @param string $file The file to include.
	 * @throws MissingDeclarationException if the declaration for the FQN is missing in the given file.
	 */
	protected function loadClassFromFile($fqn, $file) {

		// Include the file
		/** @noinspection PhpIncludeInspection */
		include_once($file);

		// Check if the class or interface is declared
		if (!class_exists($fqn, false) && !interface_exists($fqn, false)) {
			require_once __DIR__ . '/../exceptions/MohivaException.php';
			require_once 'exceptions/MissingDeclarationException.php';
			throw new MissingDeclarationException("Cannot find the class or interface `{$fqn}` in file `{$file}`");
		}
	}
}
