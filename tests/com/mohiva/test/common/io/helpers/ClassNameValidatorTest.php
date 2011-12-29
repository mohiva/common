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
namespace com\mohiva\test\common\io\helpers;

/**
 * Unit test case for the `ClassNameValidator` trait.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Test
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2011 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class ClassNameValidatorTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * A list with valid class names.
	 * 
	 * @var array
	 */
	private $validClassNames = array(
		'com\mohiva\common\io\helpers\ClassNameValidator',
		'\com\mohiva\common\io\helpers\ClassNameValidator',
		'Pre_Namespace_Class'
	);
	
	/**
	 * A list with invalid class names.
	 * 
	 * @var array
	 */
	private $invalidClassNames = array(
		'../../etc/passwd'
	);
	
	/**
	 * Check if the `isValid` method returns true for valid class names.
	 */
	public function testIsValidReturnsTrue() {
		
		$validator = $this->getObjectForTrait('\com\mohiva\common\io\helpers\ClassNameValidator');
		
		$method = new \ReflectionMethod($validator, 'isValid');
		$method->setAccessible(true);
		
		foreach($this->validClassNames as $className) {
			$this->assertTrue($method->invoke($validator, $className));
		}
	}
	
	/**
	 * Check if the `isValid` method returns false for invalid class names.
	 */
	public function testIsValidReturnsFalse() {
		
		$validator = $this->getObjectForTrait('\com\mohiva\common\io\helpers\ClassNameValidator');
		
		$method = new \ReflectionMethod($validator, 'isValid');
		$method->setAccessible(true);
		
		foreach($this->invalidClassNames as $className) {
			$this->assertFalse($method->invoke($validator, $className));
		}
	}
}
