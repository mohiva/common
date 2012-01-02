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
 * @package   Mohiva/Common/Lang
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
namespace com\mohiva\common\lang;

use com\mohiva\common\parser\TokenStream;

/**
 * Tokenize an annotation string.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Util
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class AnnotationLexer {
	
	/**
	 * Annotation tokens.
	 * 
	 * @var int
	 */
	const T_NONE              = 0;
	const T_IDENTIFIER        = 1;  // @
	const T_OPEN_PARENTHESIS  = 2;  // (
	const T_CLOSE_PARENTHESIS = 3;  // )
	const T_OPEN_ARRAY        = 4;  // [
	const T_CLOSE_ARRAY       = 5;  // ]
	const T_OPEN_OBJECT       = 6;  // {
	const T_CLOSE_OBJECT      = 7;  // }
	const T_EQUAL             = 8;  // =
	const T_COMMA             = 9;  // ,
	const T_COLON             = 10; // :
	const T_DOUBLE_COLON      = 11; // ::
	const T_NS_SEPARATOR      = 12; // \
	const T_VALUE             = 13; // "",'',-1,0.1,true,false,null
	const T_NAME              = 14; // [a-zA-Z0-9_]
	
	/**
	 * The lexemes to find the tokens.
	 * 
	 * @var array
	 */
	private $lexemes = array(
		"('(?:[^'\\\\]|\\\\['\"]|\\\\)*')",
		'("(?:[^"\\\]|\\\["\']|\\\)*")',
		'(-?[0-9]+\.?[0-9]*)',
		'([A-Za-z0-9_]+)',
		'(\:\:)',
		'(.)'
	);
	
	/**
	 * Map the constant values with its token type.
	 * 
	 * @var array
	 */
	private $constMap = array(
		'@'  => self::T_IDENTIFIER,
		'('  => self::T_OPEN_PARENTHESIS,
		')'  => self::T_CLOSE_PARENTHESIS,
		'['  => self::T_OPEN_ARRAY,
		']'  => self::T_CLOSE_ARRAY,
		'{'  => self::T_OPEN_OBJECT,
		'}'  => self::T_CLOSE_OBJECT,
		'='  => self::T_EQUAL,
		','  => self::T_COMMA,
		':'  => self::T_COLON,
		'::' => self::T_DOUBLE_COLON,
		'\\' => self::T_NS_SEPARATOR
	);
	
	/**
	 * The token stream.
	 * 
	 * @var \com\mohiva\common\parser\TokenStream
	 */
	private $stream = null;
	
	/**
	 * The class constructor.
	 *
	 * @param \com\mohiva\common\parser\TokenStream $stream The token stream to use for this lexer.
	 */
	public function __construct(TokenStream $stream) {
		
		$this->stream = $stream;
	}
	
	/**
	 * Return the token stream instance.
	 * 
	 * @return \com\mohiva\common\parser\TokenStream The token stream to use for this lexer.
	 */
	public function getStream() {
		
		return $this->stream;
	}
	
	/**
	 * Tokenize the given input string and return the resulting token stream.
	 * 
	 * @param string $input The string input to scan.
	 * @return \com\mohiva\common\parser\TokenStream The resulting token stream.
	 */
	public function scan($input) {
		
		$this->stream->flush();
		$this->stream->setSource($input);
		$this->tokenize($input);
		$this->stream->rewind();
		
		return $this->stream;
	}
	
	/**
	 * Transform the input string into a token stream.
	 * 
	 * @param string $input The string input to tokenize.
	 */
	private function tokenize($input) {
		
		$pattern = '/' . implode('|', $this->lexemes) . '/';
		$flags = PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_OFFSET_CAPTURE;
		$matches = preg_split($pattern, $input, -1, $flags);
		foreach ($matches as $match) {
			
			$value = strtolower($match[0]);
			if ($value[0] == "'" ||
				$value[0] == '"' ||
				$value == 'true' ||
				$value == 'false' ||
				$value == 'null' ||
				is_numeric($value)) {
				
				$code = self::T_VALUE;
			} else if (isset($this->constMap[$value])) {
				$code = $this->constMap[$value];
			} else if (preg_match('/[a-z0-9_]+/', $value)) {
				$code = self::T_NAME;
			} else if (ctype_space($value)) {
				continue;
			} else {
				$code = self::T_NONE;
			}
			
			$this->stream->push(new AnnotationToken(
				$code,
				$match[0],
				$match[1]
			));
		}
	}
}
