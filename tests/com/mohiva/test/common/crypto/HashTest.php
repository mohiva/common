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
 * @copyright Copyright (c) 2007-2011 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
namespace com\mohiva\test\common\crypto;

use com\mohiva\common\crypto\Hash;

/**
 * Unit test case for the `Hash` class.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Test
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2011 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class HashTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * Test if the `isSupported` method returns true if the algorithm is supported.
	 */
	public function testIfIsSupportedReturnsTrue() {
		
		$this->assertTrue(Hash::isSupported(Hash::ALGO_SHA1));
	}
	
	/**
	 * Test if the `isSupported` method returns false if the algorithm isn't supported.
	 */
	public function testIfIsSupportedReturnsFalse() {
		
		$this->assertFalse(Hash::isSupported('unsupported'));
	}
	
	/**
	 * Test if throws an exception if an unsupported algorithm is given.
	 * 
	 * @expectedException \InvalidArgumentException
	 */
	public function testIfThrowsAnExceptionForUnsupportedAlgos() {
		
		new Hash('unsupported');
	}
	
	/**
	 * Test if the `set` method overwrites a previous set value.
	 */
	public function testSetOverwritesPreviousSetValue() {
		
		$key = new Hash(Hash::ALGO_SHA1);
		$key->set('Should be overwritten');
		$key->set('Overwrites the first value');
		
		$this->assertSame(hash('sha1', 'Overwrites the first value'), $key->create());
	}
	
	/**
	 * Test if the `append` method appends a value to a previous set value.
	 */
	public function testAppendAppendsToPreviousSetValue() {
		
		$key = new Hash(Hash::ALGO_SHA1);
		$key->append('first value, ');
		$key->append('second value');
		
		$this->assertSame(hash('sha1', 'first value, second value'), $key->create());
	}
	
	/**
	 * Test if the `create` method returns a hash.
	 */
	public function testCreateReturnsHash() {
		
		$key = new Hash(Hash::ALGO_WHIRLPOOL);
		$key->set('A value');
		
		$this->assertSame(hash('whirlpool', 'A value'), $key->create());
	}
	
	/**
	 * Test if the `__toString` method returns a hash.
	 */
	public function testToStringReturnsHash() {
		
		$key = new Hash(Hash::ALGO_CRC32);
		$key->set('A value');
		
		$this->assertSame(hash('crc32', 'A value'), (string) $key);
	}
}
