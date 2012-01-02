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
namespace com\mohiva\test\common\cache;

use com\mohiva\common\cache\HashKey;
use com\mohiva\common\crypto\Hash;

/**
 * Unit test case for the `HashKey` class.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Test
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class HashKeyTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * Test if throws an exception if an unsupported algorithm is given.
	 * 
	 * @expectedException \InvalidArgumentException
	 */
	public function testIfThrowsAnExceptionForUnsupportedAlgos() {
		
		new HashKey('unsupported');
	}
	
	/**
	 * Test if the `set` method overwrites a previous set value.
	 */
	public function testSetOverwritesPreviousSetValue() {
		
		$key = new HashKey(Hash::ALGO_SHA1);
		$key->set('Should be overwritten');
		$key->set('Overwrites the first value');
		
		$this->assertSame(sha1('Overwrites the first value'), $key->create());
	}
	
	/**
	 * Test if the `append` method appends a value to a previous set value.
	 */
	public function testAppendAppendsToPreviousSetValue() {
		
		$key = new HashKey(Hash::ALGO_SHA1);
		$key->append('first value, ');
		$key->append('second value');
		
		$this->assertSame(sha1('first value, second value'), $key->create());
	}
	
	/**
	 * Test if the `create` method returns a hash.
	 */
	public function testCreateReturnsHash() {
		
		$key = new HashKey(Hash::ALGO_WHIRLPOOL);
		$key->set('A value');
		
		$this->assertSame(hash('whirlpool', 'A value'), $key->create());
	}
	
	/**
	 * Test if the `create` method returns a prefixed hash.
	 */
	public function testCreateReturnPrefixedHash() {
		
		$key = new HashKey(Hash::ALGO_ADLER32, 'prefix-');
		$key->set('A value');
		
		$this->assertSame('prefix-' . hash('adler32', 'A value'), $key->create());
	}
	
	/**
	 * Test if the `create` method returns a postfixed hash.
	 */
	public function testCreateReturnPostfixedHash() {
		
		$key = new HashKey(Hash::ALGO_GOST, null, '.cache');
		$key->set('A value');
		
		$this->assertSame(hash('gost', 'A value') . '.cache', $key->create());
	}
	
	/**
	 * Test if the `__toString` method returns a hash.
	 */
	public function testToStringReturnsHash() {
		
		$key = new HashKey(Hash::ALGO_CRC32);
		$key->set('A value');
		
		$this->assertSame(hash('crc32', 'A value'), (string) $key);
	}
	
	/**
	 * Test if the `__toString` method returns a prefixed hash.
	 */
	public function testToStringReturnPrefixedHash() {
		
		$key = new HashKey(Hash::ALGO_HAVAL128_5, 'prefix-');
		$key->set('A value');
		
		$this->assertSame('prefix-' . hash('haval128,5', 'A value'), (string) $key);
	}
	
	/**
	 * Test if the `__toString` method returns a postfixed hash.
	 */
	public function testToStringReturnPostfixedHash() {
		
		$key = new HashKey(Hash::ALGO_SNEFRU256, null, '.cache');
		$key->set('A value');
		
		$this->assertSame(hash('snefru256', 'A value') . '.cache', (string) $key);
	}
}
