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
namespace com\mohiva\test\common\lang;

use com\mohiva\common\lang\AnnotationContext;

/**
 * Unit test case for the `AnnotationContext` class.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Test
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2011 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class AnnotationContextTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * Test all getters for the values set with the constructor.
	 */
	public function testConstructorAccessors() {
		
		$namespace = sha1(microtime(true));
		$useStatements = array(sha1(microtime(true)));
		$class = sha1(microtime(true));
		$location = sha1(microtime(true));
		
		$context = new AnnotationContext(
			$namespace,
			$useStatements,
			$class,
			$location
		);
		
		$this->assertSame($namespace, $context->getNamespace());
		$this->assertSame($useStatements, $context->getUseStatements());
		$this->assertSame($class, $context->getClass());
		$this->assertSame($location, $context->getLocation());
		
	}
}
