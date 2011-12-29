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
 * Unit test case for the `ClassToFileNameTransformer` trait.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Test
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2011 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class ClassToFileNameTransformerTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * Check if the `toPSR0FileName` method returns the correct file names for a set of class names.
	 */
	public function testToPSR0FileName() {
		
		$transformer = $this->getObjectForTrait('\com\mohiva\common\io\helpers\ClassToFileNameTransformer');
		
		$method = new \ReflectionMethod($transformer, 'toPSR0FileName');
		$method->setAccessible(true);
		
		$classNames = array(
			'com\mohiva\common\io\DefaultClassLoader' => 'com/mohiva/common/io/DefaultClassLoader.php',
			'com\mohiva\common\io\Default_ClassLoader' => 'com/mohiva/common/io/Default/ClassLoader.php',
			'Pre_Namespace_Class' => 'Pre/Namespace/Class.php'
		);
		
		foreach($classNames as $className => $fileName) {
			$expected = str_replace('/', DIRECTORY_SEPARATOR, $fileName);
			$this->assertSame($expected, $method->invoke($transformer, $className));
		}
	}
}
