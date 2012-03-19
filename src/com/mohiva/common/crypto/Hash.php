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
 * @package   Mohiva/Common/Crypto
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
namespace com\mohiva\common\crypto;

use InvalidArgumentException;

/**
 * Creates an hash for a given algorithm. This class supports all algorithms provided by
 * the hash extension. For a list of supported algorithms use the hash_algos() function.
 *
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Crypto
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class Hash {

	/**
	 * All available hash algorithms.
	 *
	 * @see http://www.php.net/manual/de/function.hash-algos.php
	 * @var string
	 */
	const ALGO_MD2        = 'md2';
	const ALGO_MD4        = 'md4';
	const ALGO_MD5        = 'md5';
	const ALGO_SHA1       = 'sha1';
	const ALGO_SHA224     = 'sha224';
	const ALGO_SHA256     = 'sha256';
	const ALGO_SHA384     = 'sha384';
	const ALGO_SHA512     = 'sha512';
	const ALGO_RIPEMD128  = 'ripemd128';
	const ALGO_RIPEMD160  = 'ripemd160';
	const ALGO_RIPEMD256  = 'ripemd256';
	const ALGO_RIPEMD320  = 'ripemd320';
	const ALGO_WHIRLPOOL  = 'whirlpool';
	const ALGO_TIGER128_3 = 'tiger128,3';
	const ALGO_TIGER160_3 = 'tiger160,3';
	const ALGO_TIGER192_3 = 'tiger192,3';
	const ALGO_TIGER128_4 = 'tiger128,4';
	const ALGO_TIGER160_4 = 'tiger160,4';
	const ALGO_TIGER192_4 = 'tiger192,4';
	const ALGO_SNEFRU     = 'snefru';
	const ALGO_SNEFRU256  = 'snefru256';
	const ALGO_GOST       = 'gost';
	const ALGO_ADLER32    = 'adler32';
	const ALGO_CRC32      = 'crc32';
	const ALGO_CRC32b     = 'crc32b';
	const ALGO_SALSA10    = 'salsa10';
	const ALGO_SALSA20    = 'salsa20';
	const ALGO_HAVAL128_3 = 'haval128,3';
	const ALGO_HAVAL160_3 = 'haval160,3';
	const ALGO_HAVAL192_3 = 'haval192,3';
	const ALGO_HAVAL224_3 = 'haval224,3';
	const ALGO_HAVAL256_3 = 'haval256,3';
	const ALGO_HAVAL128_4 = 'haval128,4';
	const ALGO_HAVAL160_4 = 'haval160,4';
	const ALGO_HAVAL192_4 = 'haval192,4';
	const ALGO_HAVAL224_4 = 'haval224,4';
	const ALGO_HAVAL256_4 = 'haval256,4';
	const ALGO_HAVAL128_5 = 'haval128,5';
	const ALGO_HAVAL160_5 = 'haval160,5';
	const ALGO_HAVAL192_5 = 'haval192,5';
	const ALGO_HAVAL224_5 = 'haval224,5';
	const ALGO_HAVAL256_5 = 'haval256,5';

	/**
	 * The hash algorithm to use.
	 *
	 * @var int
	 */
	private $algorithm = null;

	/**
	 * The source for the hash to create.
	 *
	 * @var string
	 */
	private $source = null;

	/**
	 * The cached hash.
	 *
	 * @var string
	 */
	private $cached = null;

	/**
	 * Check if the given algorithm is supported or not.
	 *
	 * @param string $algorithm The hash algorithm to check for.
	 * @return boolean True if the algorithm is supported, false otherwise.
	 */
	public static function isSupported($algorithm) {

		if (!in_array($algorithm, hash_algos())) {
			return false;
		}

		return true;
	}

	/**
	 * The class constructor.
	 *
	 * @param string $algorithm The hash algorithm to use.
	 * @throws InvalidArgumentException if the algorithm isn't supported by PHP.
	 */
	public function __construct($algorithm = self::ALGO_SHA1) {

		if (!self::isSupported($algorithm)) {
			throw new InvalidArgumentException(
				"The algorithm `{$algorithm}` isn't supported, call hash_algos() for a list of supported algorithms"
			);
		}

		$this->algorithm = $algorithm;
	}

	/**
	 * Set the source for the hash.
	 *
	 * @param string $source The source for the hash to create.
	 */
	public function set($source) {

		$this->source = $source;
		$this->cached = null;
	}

	/**
	 * Append a string to the source.
	 *
	 * @param string $string The string to append.
	 */
	public function append($string) {

		$this->source .= $string;
		$this->cached = null;
	}

	/**
	 * Create the hash and return it as string.
	 *
	 * @return string The created hash.
	 */
	public function create() {

		if ($this->cached) {
			return $this->cached;
		}

		$hash = hash($this->algorithm, $this->source);
		$this->cached = $hash;

		return $hash;
	}

	/**
	 * If the object is treated like as string
	 * then create the hash and return it.
	 *
	 * @return string The created hash.
	 */
	public function __toString() {

		return $this->create();
	}
}
