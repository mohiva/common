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

use ArrayIterator;
use com\mohiva\common\lang\AnnotationLexer;
use com\mohiva\common\parser\TokenStream;

/**
 * Unit test case for the `AnnotationLexer` class.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Test
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class AnnotationLexerTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * Test if a string will be correct tokenized.
	 */
	public function testIfTokenizeCorrect() {
		
		$lexer = new AnnotationLexer(new TokenStream());
		$lexer->scan('
			@Annotation(
				param = ["key":"va\"l\"ue"],
				{key:\'value\'},
				1,
				-1,
				1.1,
				-1.1,
				true,
				false,
				null,
				namespace\Class::CONSTANT
			)'
		);
		$actual = $this->buildActualTokens($lexer->getStream());
		$expected = array(
			array(AnnotationLexer::T_IDENTIFIER => '@'),
			array(AnnotationLexer::T_NAME => 'Annotation'),
			array(AnnotationLexer::T_OPEN_PARENTHESIS => '('),
			array(AnnotationLexer::T_NAME => 'param'),
			array(AnnotationLexer::T_EQUAL => '='),
			array(AnnotationLexer::T_OPEN_ARRAY => '['),
			array(AnnotationLexer::T_VALUE => '"key"'),
			array(AnnotationLexer::T_COLON => ':'),
			array(AnnotationLexer::T_VALUE => '"va\"l\"ue"'),
			array(AnnotationLexer::T_CLOSE_ARRAY => ']'),
			array(AnnotationLexer::T_COMMA => ','),
			array(AnnotationLexer::T_OPEN_OBJECT => '{'),
			array(AnnotationLexer::T_NAME => 'key'),
			array(AnnotationLexer::T_COLON => ':'),
			array(AnnotationLexer::T_VALUE => '\'value\''),
			array(AnnotationLexer::T_CLOSE_OBJECT => '}'),
			array(AnnotationLexer::T_COMMA => ','),
			array(AnnotationLexer::T_VALUE => '1'),
			array(AnnotationLexer::T_COMMA => ','),
			array(AnnotationLexer::T_VALUE => '-1'),
			array(AnnotationLexer::T_COMMA => ','),
			array(AnnotationLexer::T_VALUE => '1.1'),
			array(AnnotationLexer::T_COMMA => ','),
			array(AnnotationLexer::T_VALUE => '-1.1'),
			array(AnnotationLexer::T_COMMA => ','),
			array(AnnotationLexer::T_VALUE => 'true'),
			array(AnnotationLexer::T_COMMA => ','),
			array(AnnotationLexer::T_VALUE => 'false'),
			array(AnnotationLexer::T_COMMA => ','),
			array(AnnotationLexer::T_VALUE => 'null'),
			array(AnnotationLexer::T_COMMA => ','),
			array(AnnotationLexer::T_NAME => 'namespace'),
			array(AnnotationLexer::T_NS_SEPARATOR => '\\'),
			array(AnnotationLexer::T_NAME => 'Class'),
			array(AnnotationLexer::T_DOUBLE_COLON => '::'),
			array(AnnotationLexer::T_NAME => 'CONSTANT'),
			array(AnnotationLexer::T_CLOSE_PARENTHESIS => ')')
		);
		$this->assertSame($expected, $actual);
	}
	
	/**
	 * Test the syntax for single quoted strings.
	 */
	public function testSingleQuotedStringSyntax() {
		
		$lexer = new AnnotationLexer(new TokenStream());
		$lexer->scan(" 'key\\\\':va\\'l\\'ue\\'' ");
		
		$actual = $this->buildActualTokens($lexer->getStream());
		$expected = array(
			array(AnnotationLexer::T_VALUE => "'key\\\\':va\\'l\\'ue\\''")
		);
		$this->assertSame($expected, $actual);
	}
	
	/**
	 * Test the syntax for double quoted strings.
	 */
	public function testDoubleQuotedStringSyntax() {
		
		$lexer = new AnnotationLexer(new TokenStream());
		$lexer->scan(' "key\":va\"l\"ue\"" ');
		
		$actual = $this->buildActualTokens($lexer->getStream());
		$expected = array(
			array(AnnotationLexer::T_VALUE => '"key\":va\"l\"ue\""')
		);
		$this->assertSame($expected, $actual);
	}
	
	/**
	 * Test the none token.
	 */
	public function testNoneToken() {
		
		$lexer = new AnnotationLexer(new TokenStream());
		$lexer->scan(' # ');
		
		$actual = $this->buildActualTokens($lexer->getStream());
		$expected = array(
			array(AnnotationLexer::T_NONE => '#'),
		);
		$this->assertSame($expected, $actual);
	}
	
	/**
	 * Create an array from the token stream which contains the tokens and the values as token => value pair.
	 * 
	 * @param \com\mohiva\common\parser\TokenStream $stream The stream containing the lexer tokens.
	 * @return array The actual list with tokens and values.
	 */
	private function buildActualTokens(TokenStream $stream) {
		
		$actual = array();
		while ($stream->valid()) {
			/* @var \com\mohiva\common\lang\AnnotationToken $current */
			$current = $stream->current();
			$stream->next();
			$actual[] = array($current->getCode() => $current->getValue());
		}
		
		return $actual;
	}
}
