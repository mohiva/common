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

/**
 * Test suite for the Mohiva Common project.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Test
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class AllTests extends \PHPUnit_Framework_TestSuite {
	
	/**
	 * Constructs the test suite handler.
	 */
	public function __construct() {
		
		$this->setName(__CLASS__);
		$this->addTestSuite('\com\mohiva\test\common\lang\ReflectionClassTest');
		$this->addTestSuite('\com\mohiva\test\common\lang\ReflectionClassNamespaceTest');
		$this->addTestSuite('\com\mohiva\test\common\lang\ReflectionDocCommentTest');
		$this->addTestSuite('\com\mohiva\test\common\lang\ReflectionMethodTest');
		$this->addTestSuite('\com\mohiva\test\common\lang\ReflectionObjectTest');
		$this->addTestSuite('\com\mohiva\test\common\lang\ReflectionPropertyTest');
		$this->addTestSuite('\com\mohiva\test\common\lang\AnnotationLexerTest');
		$this->addTestSuite('\com\mohiva\test\common\lang\AnnotationContextTest');
		$this->addTestSuite('\com\mohiva\test\common\lang\AnnotationParserTest');
		$this->addTestSuite('\com\mohiva\test\common\lang\AnnotationListTest');
	}
	
	/**
	 * Creates the suite.
	 * 
	 * @return AllTests The test suite.
	 */
	public static function suite() {
		
		return new self();
	}
}
