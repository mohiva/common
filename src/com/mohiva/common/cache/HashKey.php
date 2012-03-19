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
 * @package   Mohiva/Common/Cache
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
namespace com\mohiva\common\cache;

use com\mohiva\common\crypto\Hash;

/**
 * Hash key generator class.
 *
 * Creates an hash key.
 *
 * @category  Mohiva
 * @package   Mohiva/Cache
 * @author    Christian Kaps <akkie@framework.mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   http://framework.mohiva.com/license New BSD License
 * @link      http://framework.mohiva.com
 */
class HashKey implements Key {

	/**
	 * The instance of the hash object.
	 *
	 * @var com\mohiva\common\crypto\Hash
	 */
	private $hash = null;

	/**
	 * A string used as hash prefix.
	 *
	 * @var string
	 */
	private $prefix = null;

	/**
	 * A string used as hash postfix.
	 *
	 * @var string
	 */
	private $postfix = null;

	/**
	 * The class constructor.
	 *
	 * @param string $algorithm The hash algorithm to use.
	 * @param string $prefix A string used as hash prefix.
	 * @param string $postfix A string used as hash postfix.
	 */
	public function __construct($algorithm = Hash::ALGO_SHA1, $prefix = null, $postfix = null) {

		$this->hash = new Hash($algorithm);
		$this->prefix = $prefix;
		$this->postfix = $postfix;
	}

	/**
	 * Set the source for the key.
	 *
	 * @param string $source The source for the key to create.
	 */
	public function set($source) {

		$this->hash->set($source);
	}

	/**
	 * Append a string to the source.
	 *
	 * @param string $string The string to append.
	 */
	public function append($string) {

		$this->hash->append($string);
	}

	/**
	 * Create the key and return it as string.
	 *
	 * @return string The created key.
	 */
	public function create() {

		$key = $this->hash->create();
		$key = $this->prefix ? $this->prefix . $key : $key;
		$key = $this->postfix ? $key . $this->postfix : $key;

		return $key;
	}

	/**
	 * If the object is treated like as string
	 * then create the key and return it.
	 *
	 * @return string The created key.
	 */
	public function __toString() {

		return $this->create();
	}
}
