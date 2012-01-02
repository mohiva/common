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
namespace com\mohiva\test\common\lang;

use com\mohiva\common\lang\ReflectionDocComment;
use com\mohiva\common\lang\ReflectionClass;
use com\mohiva\common\lang\ReflectionMethod;

/**
 * Unit test case for the `ReflectionDocComment` class.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Test
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class ReflectionDocCommentTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * The path to the class containing the annotations.
	 * 
	 * @var string
	 */
	const TEST_CLASS = '\com\mohiva\test\resources\common\lang\AnnotationTest';
	
	/**
	 * Test if no exception occurs when processing doc comment with PHPDocumentor annotations.
	 */
	public function testIfRemovePHPDocumentorAnnotations() {
		
		$reflector = new ReflectionMethod(self::TEST_CLASS, 'phpDocumentor');
		try {
			new ReflectionDocComment($reflector);
		} catch (\Exception $e) {
			$this->fail($e->getMessage());
		}
	}
	
	/**
	 * Test if the `getAnnotationList` method return a list of all annotations in a doc comment.
	 */
	public function testGetAnnotationList() {
		
		$reflector = new ReflectionClass(self::TEST_CLASS);
		$comment = new ReflectionDocComment($reflector);
		$list = $comment->getAnnotationList();
		
		$this->assertInstanceOf('com\mohiva\common\lang\AnnotationList', $list);
	}
}
