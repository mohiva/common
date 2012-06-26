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
use com\mohiva\common\io\exceptions\MissingDeclarationException;
use com\mohiva\common\io\exceptions\ClassNotFoundException;
use com\mohiva\common\io\exceptions\FileNotFoundException;

require_once 'AbstractClassLoader.php';

/**
 * `ClassLoader` implementation which loads classes from include path.
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
class IncludePathClassLoader extends AbstractClassLoader {

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
			$fileName = $this->toFileName($fqn);
			$fileName = $this->getClassFileFromIncludePath($fileName);
			$this->loadClassFromFile($fqn, $fileName);
		} catch (Exception $e) {
			require_once __DIR__ . '/../exceptions/MohivaException.php';
			require_once 'exceptions/IOException.php';
			require_once 'exceptions/ClassNotFoundException.php';
			throw new ClassNotFoundException("The class `{$fqn}` cannot be found", null, $e);
		}
	}

	/**
	 * This method search the given file in include path and then return the
	 * absolute path for it.
	 *
	 * @param string $fileName The name of the file to load.
	 * @return string The absolute path to the file.
	 * @throws FileNotFoundException if the given file cannot be found in the include path or it isn't readable.
	 */
	private function getClassFileFromIncludePath($fileName) {

		$file = stream_resolve_include_path($fileName);
		if ($file === false) {
			$includePath = get_include_path();
			require_once __DIR__ . '/../exceptions/MohivaException.php';
			require_once 'exceptions/IOException.php';
			require_once 'exceptions/FileNotFoundException.php';
			throw new FileNotFoundException(
				"The file `{$fileName}` doesn't exists in include path `{$includePath}` or it isn't readable"
			);
		}

		return $file;
	}
}
