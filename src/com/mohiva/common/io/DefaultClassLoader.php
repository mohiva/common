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

use com\mohiva\common\lang\ReflectionClass;
use com\mohiva\common\io\exceptions\MalformedNameException;
use com\mohiva\common\io\exceptions\MissingDeclarationException;
use com\mohiva\common\io\exceptions\IOException;
use com\mohiva\common\io\exceptions\ClassNotFoundException;
use com\mohiva\common\io\exceptions\FileNotFoundException;

/**
 * The default implementation of the `ClassLoader` interface.
 * 
 * This is a full PSR-0 compatible class loader implementation proposed by 
 * the PHP Standards Working Group. Fore more information visit the proposal
 * page: http://groups.google.com/group/php-standards/web/psr-0-final-proposal.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/IO
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2011 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class DefaultClassLoader implements ClassLoader {
	
	/**
	 * The namespace separator.
	 *
	 * @var string
	 */
	const NAMESPACE_SEPARATOR = '\\';
	
	/**
	 * The class separator.
	 *
	 * @var string
	 */
	const CLASS_SEPARATOR = '_';
	
	/**
	 * File extension for php files to load.
	 *
	 * @var string
	 */
	const FILE_EXTENSION = '.php';
	
	/**
	 * Return a `ReflectionClass` instance for the given class.
	 * 
	 * @param string $fqn The fully qualified name of the class to load.
	 * @param boolean $returnRef True if the class reference should be returned, false otherwise.
	 * @return \com\mohiva\common\lang\ReflectionClass The resulting `ReflectionClass` object or null if the return 
	 * is disabled.
	 * 
	 * @throws ClassNotFoundException if the class cannot be found.
	 */
	public function loadClass($fqn, $returnRef = true) {
		
		// Check if the class is already loaded
		if (class_exists($fqn, false) || interface_exists($fqn, false)) {
			return $returnRef ? new ReflectionClass($fqn) : null;
		}
		
		// Try to load the class from file system
		try {
			$fileName = $this->fqnToFileName($fqn);
			$fileName = $this->getClassFileFromIncludePath($fileName);
			$this->loadClassFromFile($fqn, $fileName);
		} catch (\Exception $e) {
			require_once 'exceptions/IOException.php';
			require_once 'exceptions/ClassNotFoundException.php';
			throw new ClassNotFoundException("The class `{$fqn}` cannot be found", null, $e);
		}
		
		return $returnRef ? new ReflectionClass($fqn) : null;
	}
	
	/**
	 * Return a `ReflectionClass` instance for the given class.
	 * 
	 * This method searches the class in the given path.
	 * 
	 * @param string $fqn The fully qualified name of the class to load.
	 * @param string $path The path in which the class will be searched.
	 * @param boolean $returnRef True if the class reference should be returned, false otherwise.
	 * @return \com\mohiva\common\lang\ReflectionClass The resulting `ReflectionClass` object or null if the return 
	 * is disabled.
	 * 
	 * @throws ClassNotFoundException if the class cannot be found.
	 */
	public function loadClassFromPath($fqn, $path, $returnRef = true) {
		
		// Check if the class is already loaded
		if (class_exists($fqn, false) || interface_exists($fqn, false)) {
			return $returnRef ? new ReflectionClass($fqn) : null;
		}

		// Try to load the class from file system
		try {
			$fileName = $this->fqnToFileName($fqn);
			$fileName = $this->getClassFileFromPath($fileName, $path);
			$this->loadClassFromFile($fqn, $fileName);
		} catch (\Exception $e) {
			require_once 'exceptions/IOException.php';
			require_once 'exceptions/ClassNotFoundException.php';
			throw new ClassNotFoundException("The class `{$fqn}` cannot be found in path `{$path}`", null, $e);
		}
		
		return $returnRef ? new ReflectionClass($fqn) : null;
	}
	
	/**
	 * Check if a FQN contains illegal characters. Only names passes 
	 * this test can be loaded by this `ClassLoader`.
	 * 
	 * @param string $fqn The fully qualified name to check.
	 * @return boolean True if the fully qualified name is valid, false otherwise.
	 */
	private function isValidFqn($fqn) {
		
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
	 * @see http://groups.google.com/group/php-standards/web/psr-0-final-proposal
	 */
	private function fqnToFileName($fqn) {
		
		// Check if the FQN is valid
		if (!$this->isValidFqn($fqn)) {
			require_once 'exceptions/MalformedNameException.php';
			throw new MalformedNameException("The class name `{$fqn}` contains illegal characters");
		}
		
		// Build the file name from class name
		$fileName = '';
		$className = ltrim($fqn, self::NAMESPACE_SEPARATOR);
		$lastNsPos = strripos($className, self::NAMESPACE_SEPARATOR);
		if ($lastNsPos) {
			$namespace = substr($className, 0, $lastNsPos);
			$className = substr($className, $lastNsPos + 1);
			$fileName  = str_replace(self::NAMESPACE_SEPARATOR, DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
		}
		$fileName .= str_replace(self::CLASS_SEPARATOR, DIRECTORY_SEPARATOR, $className) . self::FILE_EXTENSION;
		
		return $fileName;
	}
	
	/**
	 * This method search the given file in include path and then return the 
	 * absolute path for it.
	 * 
	 * @param string $fileName The name of the file to load.
	 * @return string The absolute path to the included file.
	 * @throws FileNotFoundException if the given file cannot be found in the include path or it isn't readable.
	 */
	private function getClassFileFromIncludePath($fileName) {
		
		// Check if the file exists in include path
		$includableFile = stream_resolve_include_path($fileName);
		if ($includableFile === false) {
			$includePath = get_include_path();
			require_once 'exceptions/IOException.php';
			require_once 'exceptions/FileNotFoundException.php';
			throw new FileNotFoundException(
				"The file `{$fileName}` doesn't exists in include path `{$includePath}` or it isn't readable"
			);
		}
		
		return $includableFile;
	}
	
	/**
	 * This method return the absolute path for the given file and path.
	 * 
	 * @param string $fileName The name of the file to load.
	 * @param string $path The path from which the file should be loaded.
	 * @return string The absolute path to the included file.
	 * @throws FileNotFoundException if the given file cannot be found in the include path or it isn't readable.
	 */
	private function getClassFileFromPath($fileName, $path) {
		
		$path = rtrim($path, '\\/');
		$fileName = ltrim($fileName, '\\/');
		$includableFile = realpath($path . DIRECTORY_SEPARATOR . $fileName);
		
		// Check if the file exists in path
		if (!is_file($includableFile)) {
			require_once 'exceptions/IOException.php';
			require_once 'exceptions/FileNotFoundException.php';
			throw new FileNotFoundException(
				"The file `{$fileName}` doesn't exists in path `{$path}` or it isn't readable"
			);
		}
		
		return $includableFile;
	}
	
	/**
	 * Includes the file and check if a class or interface declaration for the 
	 * given name exists in it.
	 * 
	 * @param string $fqn The fully qualified name to check for.
	 * @param string $file The file to include.
	 * @throws MissingDeclarationException if the declaration for the FQN is missing in the given file.
	 */
	private function loadClassFromFile($fqn, $file) {
		
		// Include the file
		/** @noinspection PhpIncludeInspection */
		include_once($file);
		
		// Check if the class or interface is declared
		if (!class_exists($fqn, false) && !interface_exists($fqn, false)) {
			require_once 'exceptions/MissingDeclarationException.php';
			throw new MissingDeclarationException("Cannot find the class or interface `{$fqn}` in file `{$file}`");
		}
	}
}
