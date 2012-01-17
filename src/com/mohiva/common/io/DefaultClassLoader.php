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

require_once 'helpers/ClassNameValidator.php';
require_once 'helpers/ClassToFileNameTransformer.php';

use com\mohiva\common\lang\ReflectionClass;
use com\mohiva\common\io\exceptions\MalformedNameException;
use com\mohiva\common\io\exceptions\MissingDeclarationException;
use com\mohiva\common\io\exceptions\ClassNotFoundException;
use com\mohiva\common\io\exceptions\FileNotFoundException;
use com\mohiva\common\io\helpers\ClassNameValidator;
use com\mohiva\common\io\helpers\ClassToFileNameTransformer;

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
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class DefaultClassLoader implements ClassLoader {
	
	use ClassNameValidator;
	use ClassToFileNameTransformer;
	
	/**
	 * Indicates if a `ReflectionClass` instance or null should be returned for the loaded class.
	 * 
	 * @var bool
	 */
	private $returnRef = true;
	
	/**
	 * The class constructor.
	 * 
	 * @param boolean $returnRef True if the class reference should be returned, false otherwise.
	 */
	public function __construct($returnRef = true) {
		
		$this->returnRef = $returnRef;
	}
	
	/**
	 * Return a `ReflectionClass` instance for the given class.
	 * 
	 * @param string $fqn The fully qualified name of the class to load.
	 * @return \com\mohiva\common\lang\ReflectionClass The resulting `ReflectionClass` object or null if the return 
	 * is disabled.
	 * 
	 * @throws MalformedNameException if the class name contains illegal characters.
	 * @throws ClassNotFoundException if the class cannot be found.
	 */
	public function load($fqn) {
		
		if (class_exists($fqn, false) || interface_exists($fqn, false)) {
			return $this->returnRef ? new ReflectionClass($fqn) : null;
		} else if (!$this->isValid($fqn)) {
			require_once __DIR__ . '/../exceptions/MohivaException.php';
			require_once __DIR__ . '/../exceptions/SecurityException.php';
			require_once 'exceptions/MalformedNameException.php';
			throw new MalformedNameException("The class name `{$fqn}` contains illegal characters");
		}
		
		try {
			$fileName = $this->toPSR0FileName($fqn);
			$fileName = $this->getClassFileFromIncludePath($fileName);
			$this->loadClassFromFile($fqn, $fileName);
		} catch (\Exception $e) {
			require_once __DIR__ . '/../exceptions/MohivaException.php';
			require_once 'exceptions/IOException.php';
			require_once 'exceptions/ClassNotFoundException.php';
			throw new ClassNotFoundException("The class `{$fqn}` cannot be found", null, $e);
		}
		
		return $this->returnRef ? new ReflectionClass($fqn) : null;
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
		
		$includableFile = stream_resolve_include_path($fileName);
		if ($includableFile === false) {
			$includePath = get_include_path();
			require_once __DIR__ . '/../exceptions/MohivaException.php';
			require_once 'exceptions/IOException.php';
			require_once 'exceptions/FileNotFoundException.php';
			throw new FileNotFoundException(
				"The file `{$fileName}` doesn't exists in include path `{$includePath}` or it isn't readable"
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
			require_once __DIR__ . '/../exceptions/MohivaException.php';
			require_once 'exceptions/MissingDeclarationException.php';
			throw new MissingDeclarationException("Cannot find the class or interface `{$fqn}` in file `{$file}`");
		}
	}
}
