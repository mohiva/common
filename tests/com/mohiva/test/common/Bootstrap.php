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
namespace com\mohiva\test\common;

use com\mohiva\common\io\ClassAutoloader;
use com\mohiva\common\io\IncludePath;

/**
 * Bootstrap class for the Mohiva unit test suite.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Test
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class Bootstrap {
	
	/**
	 * The path to the root directory.
	 * 
	 * @var string
	 */
	public static $rootDir = null;
	
	/**
	 * The path to the source directory.
	 * 
	 * @var string
	 */
	public static $srcDir = null;
	
	/**
	 * The path to the tests directory.
	 * 
	 * @var string
	 */
	public static $testDir = null;
	
	/**
	 * The default include path.
	 * 
	 * @var string
	 */
	public static $includePath = null;
	
	/**
	 * The path to the test resources.
	 * 
	 * @var string
	 */
	public static $resourceDir = null;
	
	/**
	 * The default class loader.
	 * 
	 * @var \com\mohiva\common\io\ClassAutoloader
	 */
	public static $autoloader = null;
	
	/**
	 * Set error reporting and determine several directories.
	 */
	public static function run() {
		
		self::setupPHP();
		self::setupIncludePath();
		self::setupClassAutoloader();
	}
	
	/**
	 * Setup PHP specific settings.
	 */
	private static function setupPHP() {
		
		error_reporting(E_ALL | E_STRICT);
		date_default_timezone_set('UTC');
	}
	
	/**
	 * Setup the include path.
	 */
	private static function setupIncludePath() {
		
		$rootDir = realpath(dirname(__FILE__) . '/../../../../..');
		self::$srcDir = realpath("{$rootDir}/src");
		self::$testDir = realpath("{$rootDir}/tests");
		self::$resourceDir = self::$testDir . '/com/mohiva/test/resources';
		self::$rootDir = $rootDir;
		
		$path = array(
			self::$srcDir,
			self::$testDir,
			get_include_path()
		);
		self::$includePath = set_include_path(implode(PATH_SEPARATOR, $path));
	}
	
	/**
	 * Setup the class autoloader for all mohiva related files.
	 */
	private static function setupClassAutoloader() {
		
		/** @noinspection PhpIncludeInspection */
		require_once 'com/mohiva/common/io/ClassAutoloader.php';
		
		self::$autoloader = new ClassAutoloader();
		self::$autoloader->setPolicy(ClassAutoloader::POLICY_EXCEPTION);
		self::$autoloader->registerNamespace('com\mohiva');
		self::$autoloader->register();
	}
}

Bootstrap::run();
