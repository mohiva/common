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

use com\mohiva\common\io\exceptions\FileNotFoundException;

/**
 * Static class which can be used to manage the include path.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/IO
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class IncludePath {
	
	/**
	 * Contains the include path which were not set through the autoloader. 
	 * The initial value must be null because of lazy loading.
	 * 
	 * @var string
	 */
	private static $defaultPath = null;
	
	/**
	 * Contains all paths which were set during script runtime. 
	 * 
	 * @var array
	 */
	private static $userPaths = array();
	
	/**
	 * Return the current set include path.
	 * 
	 * @return string The current set include path.
	 */
	public static function getPath() {
		
		return get_include_path();
	}
	
	/**
	 * Set the give path as include path. All previous set paths will be reset.
	 * 
	 * @param string $path The include path to set.
	 */
	public static function setPath($path) {
		
		self::$defaultPath = $path;
		self::$userPaths = array();
		self::setIncludePath();
	}
	
	/**
	 * Add a path to the existing include path. `addPath` 
	 * will prepend the path instead of appending it. So the first path set 
	 * with this method is also the first, the second is also the second and 
	 * so on. After that comes the paths which was not set by this method.
	 * 
	 * @param string $path A path to a directory.
	 * @return string The set canonicalized absolute path name.
	 */
	public static function addPath($path) {
		
		$path = self::resolvePath($path);
		self::$userPaths[md5($path)] = $path;
		self::setIncludePath();
		
		return $path;
	}
	
	/**
	 * Remove a path from include path. This method removes only paths which were set 
	 * previously by the `addPath` method.
	 * 
	 * @param string $path A path to a directory.
	 * @return string The removed canonicalized absolute path name.
	 */
	public static function removePath($path) {
		
		$path = self::resolvePath($path);
		$hash = md5($path);
		if (isset(self::$userPaths[$hash])) {
			unset(self::$userPaths[$hash]);
			self::setIncludePath();
		}
		
		return $path;
	}
	
	/**
	 * Reset the original include path. This means it will delete all paths which was 
	 * previously set by the `addPath` method.
	 */
	public static function resetPath() {
		
		self::$userPaths = array();
		self::setIncludePath();
	}
	
	/**
	 * Get canonicalized absolute path name for the given path. If the given path 
	 * is a absolute path then check if it exists otherwise try to resolve the 
	 * absolute path.
	 *
	 * @param string $path A relative or absolute path name.
	 * @return string The canonicalized absolute path name.
	 * @throws FileNotFoundException when the path can not be resolved or the path doesn't exists in the filesystem.
	 */
	public static function resolvePath($path) {
		
		if (strpos($path, DIRECTORY_SEPARATOR) === 0 && is_dir($path)) {
			return trim($path);
		} else if (($realPath = realpath($path)) !== false) {
			return $realPath;
		}
		
		require_once 'exceptions/FileNotFoundException.php';
		throw new FileNotFoundException("Cannot resolve the absolute path for the given path `{$path}`");
	}
	
	/**
	 * Set the include path.
	 */
	private static function setIncludePath() {
		
		// Load default path
		if (self::$defaultPath === null) {
			self::$defaultPath = get_include_path();
		}
		
		// Set the include path
		$userPath = implode(PATH_SEPARATOR, self::$userPaths);
		if ($userPath === '') {
			set_include_path(self::$defaultPath);
		} else {
			set_include_path($userPath . PATH_SEPARATOR . self::$defaultPath);
		}
	}
}
