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

use ReflectionClass;
use com\mohiva\test\common\Bootstrap;
use com\mohiva\common\lang\ReflectionClassNamespace;
use com\mohiva\common\io\DefaultClassLoader;
use com\mohiva\common\io\IncludePath;

/**
 * Unit test case for the `ReflectionClassNamespace` class.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Test
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2011 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class ReflectionClassNamespaceTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * The fixture namespace.
	 */
	const FIXTURE_NS = 'com\mohiva\test\resources\common\lang\reflection';
	
	/**
	 * Setup the test case.
	 */
	public function setUp() {
		
		IncludePath::addPath(Bootstrap::$testDir . '/com/mohiva/test/resources/common/lang/reflection');
	}
	
	/**
	 * Tear down the test case.
	 */
	public function tearDown() {
		
		IncludePath::removePath(Bootstrap::$testDir . '/com/mohiva/test/resources/common/lang/reflection');
	}
	
	/**
	 * Test if returns the correct namespace name.
	 */
	public function testGetName() {
		
		$class = new ReflectionClass(__CLASS__);
		$ns = new ReflectionClassNamespace($class);
		
		$this->assertEquals(__NAMESPACE__, $ns->getName());
	}
	
	/**
	 * Test if an internal class does return an empty array.
	 */
	public function testInternalClass() {
		
		$class = new ReflectionClass('\ReflectionClass');
		$ns = new ReflectionClassNamespace($class);
		
		$this->assertEquals(array(), $ns->getUseStatements());
	}
	
	/**
	 * Test if can get use statements from a class without namespaces.
	 */
	public function testClassWithoutNamespace() {
		
		$classLoader = new DefaultClassLoader();
		$classLoader->load('ClassWithoutNamespace');
		
		$expected = array('ReflectionClass' => 'com\mohiva\common\lang\ReflectionClass');
		$class = new ReflectionClass('ClassWithoutNamespace');
		$ns = new ReflectionClassNamespace($class);
		
		$this->assertEquals($expected, $ns->getUseStatements());
	}
	
	/**
	 * Test if can get multiple use statements declared for a class.
	 */
	public function testClassWithMultipleUseStatements() {
		
		$expected = array(
			'ReflectionClass' => 'com\mohiva\common\lang\ReflectionClass',
			'ReflectionProperty' => 'com\mohiva\common\lang\ReflectionProperty',
			'ReflectionMethod' => 'com\mohiva\common\lang\ReflectionMethod'
		);
		$class = new ReflectionClass(self::FIXTURE_NS . '\ClassWithMultipleUseStatements');
		$ns = new ReflectionClassNamespace($class);
		
		$this->assertEquals($expected, $ns->getUseStatements());
	}
	
	/**
	 * Test if can get aliased use statements declared for a class.
	 */
	public function testClassWithAliasedUseStatements() {
		
		$expected = array(
			'lang'   => 'com\mohiva\common\lang',
			'Class1' => 'lang\ReflectionClass',
			'Class2' => 'lang\ReflectionProperty',
			'Class3' => 'lang\ReflectionMethod'
		);
		$class = new ReflectionClass(self::FIXTURE_NS . '\ClassWithAliasedUseStatements');
		$ns = new ReflectionClassNamespace($class);
		
		$this->assertEquals($expected, $ns->getUseStatements());
	}
	
	/**
	 * Test if can get fully qualified use statements declared for a class.
	 */
	public function testClassWithFullyQualifiedUseStatements() {
		
		$expected = array(
			'Class1' => '\com\mohiva\common\lang\ReflectionClass',
			'Class2' => '\com\mohiva\common\lang\ReflectionProperty',
			'Class3' => '\com\mohiva\common\lang\ReflectionMethod'
		);
		$class = new ReflectionClass(self::FIXTURE_NS . '\ClassWithFullyQualifiedUseStatements');
		$ns = new ReflectionClassNamespace($class);
		
		$this->assertEquals($expected, $ns->getUseStatements());
	}
	
	/**
	 * Test if skip namespaces or classes which are commented out.
	 */
	public function testNamespaceAndClassCommentedOut() {
		
		$expected = array(
			'Class4' => 'com\mohiva\common\lang\ReflectionClass',
			'Class5' => 'com\mohiva\common\lang\ReflectionProperty',
			'Class6' => 'com\mohiva\common\lang\ReflectionMethod'
		);
		$className = self::FIXTURE_NS . '\NamespaceAndClassCommentedOut';
		$class = new ReflectionClass($className);
		$ns = new ReflectionClassNamespace($class);
		
		$this->assertEquals($expected, $ns->getUseStatements());
	}
	
	/**
	 * Test if recognize only the use statements for the first class in a file with 
	 * multiple(equal) namespaces.
	 */
	public function testEqualNamespacesPerFileWithClassAsFirst() {
		
		$expected = array(
			'Class1' => 'com\mohiva\common\lang\ReflectionClass',
			'Class2' => 'com\mohiva\common\lang\ReflectionProperty',
			'Class3' => 'com\mohiva\common\lang\ReflectionMethod'
		);
		$className = self::FIXTURE_NS . '\EqualNamespacesPerFileWithClassAsFirst';
		$class = new ReflectionClass($className);
		$ns = new ReflectionClassNamespace($class);
		
		$this->assertEquals($expected, $ns->getUseStatements());
	}
	
	/**
	 * Test if recognize only the use statements for the last class in a file with 
	 * multiple(equal) namespaces.
	 */
	public function testEqualNamespacesPerFileWithClassAsLast() {
		
		$expected = array(
			'Class4' => 'com\mohiva\common\lang\ReflectionClass',
			'Class5' => 'com\mohiva\common\lang\ReflectionProperty',
			'Class6' => 'com\mohiva\common\lang\ReflectionMethod'
		);
		$className = self::FIXTURE_NS . '\EqualNamespacesPerFileWithClassAsLast';
		$class = new ReflectionClass($className);
		$ns = new ReflectionClassNamespace($class);
		
		$this->assertEquals($expected, $ns->getUseStatements());
	}
	
	/**
	 * Test if recognize only the use statements for the first class in a file with 
	 * multiple(different) namespaces.
	 */
	public function testDifferentNamespacesPerFileWithClassAsFirst() {
		
		$expected = array(
			'Class1' => 'com\mohiva\common\lang\ReflectionClass',
			'Class2' => 'com\mohiva\common\lang\ReflectionProperty',
			'Class3' => 'com\mohiva\common\lang\ReflectionMethod'
		);
		$className = self::FIXTURE_NS . '\DifferentNamespacesPerFileWithClassAsFirst';
		$class = new ReflectionClass($className);
		$ns = new ReflectionClassNamespace($class);
		
		$this->assertEquals($expected, $ns->getUseStatements());
	}
	
	/**
	 * Test if recognize only the use statements for the last class in a file with 
	 * multiple(different) namespaces.
	 */
	public function testDifferentNamespacesPerFileWithClassAsLast() {
		
		$expected = array(
			'Class7' => 'com\mohiva\common\lang\ReflectionClass',
			'Class8' => 'com\mohiva\common\lang\ReflectionProperty',
			'Class9' => 'com\mohiva\common\lang\ReflectionMethod'
		);
		$className = self::FIXTURE_NS . '\DifferentNamespacesPerFileWithClassAsLast';
		$class = new ReflectionClass($className);
		$ns = new ReflectionClassNamespace($class);
		
		$this->assertEquals($expected, $ns->getUseStatements());
	}
	
	/**
	 * Test if recognize only the use statements for the first class in a file with 
	 * multiple(global) namespaces.
	 */
	public function testGlobalNamespacesPerFileWithClassAsFirst() {
		
		$className = 'GlobalNamespacesPerFileWithClassAsFirst';
		$classLoader = new DefaultClassLoader();
		$classLoader->load($className);
		
		$expected = array(
			'Class1' => 'com\mohiva\common\lang\ReflectionClass',
			'Class2' => 'com\mohiva\common\lang\ReflectionProperty',
			'Class3' => 'com\mohiva\common\lang\ReflectionMethod'
		);
		$class = new ReflectionClass($className);
		$ns = new ReflectionClassNamespace($class);
		
		$this->assertEquals($expected, $ns->getUseStatements());
	}
	
	/**
	 * Test if recognize only the use statements for the last class in a file with 
	 * multiple(global) namespaces.
	 */
	public function testGlobalNamespacesPerFileWithClassAsLast() {
		
		$className = 'GlobalNamespacesPerFileWithClassAsLast';
		$classLoader = new DefaultClassLoader();
		$classLoader->load($className);
		$expected = array(
			'Class4' => 'com\mohiva\common\lang\ReflectionClass',
			'Class5' => 'com\mohiva\common\lang\ReflectionProperty',
			'Class6' => 'com\mohiva\common\lang\ReflectionMethod'
		);
		
		$class = new ReflectionClass($className);
		$ns = new ReflectionClassNamespace($class);
		
		$this->assertEquals($expected, $ns->getUseStatements());
	}
	
	/**
	 * Test if the use statements from the closures are dropped.
	 */
	public function testNamespaceWithClosureDeclaration() {
		
		$expected = array(
			'ReflectionClass' => 'com\mohiva\common\lang\ReflectionClass',
			'ReflectionProperty' => 'com\mohiva\common\lang\ReflectionProperty',
			'ReflectionMethod' => 'com\mohiva\common\lang\ReflectionMethod'
		);
		$class = new ReflectionClass(self::FIXTURE_NS . '\NamespaceWithClosureDeclaration');
		$ns = new ReflectionClassNamespace($class);
		
		$this->assertEquals($expected, $ns->getUseStatements());
	}
}
