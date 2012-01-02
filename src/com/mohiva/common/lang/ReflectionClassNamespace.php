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

use ReflectionClass as InternalReflectionClass;
use SplFileObject;

/**
 * The `ReflectionClassNamespace` class reports information about the namespace of a class.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Util
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class ReflectionClassNamespace {
	
	/**
	 * The `ReflectionClass` object for which the namespace should be reflected.
	 * 
	 * @var \ReflectionClass
	 */
	private $class = null;
	
	/**
	 * A list with use statements within the class namespace.
	 * 
	 * The entries are stored in the form (Alias => NS|Class).
	 * 
	 * @var array
	 */
	private $useStatements = null;
	
	/**
	 * The token list.
	 * 
	 * @var array
	 */
	private $tokens = array();
	
	/**
	 * The number of tokens.
	 * 
	 * @var int
	 */
	private $numTokens = 0;
	
	/**
	 * The current array pointer.
	 * 
	 * @var int
	 */
	private $pointer = 0;
	
	/**
	 * The class constructor.
	 *
	 * @param \ReflectionClass $class The `ReflectionClass` object for which the namespace should
	 * be reflected.
	 */
	public function __construct(InternalReflectionClass $class) {
		
		$this->class = $class;
	}
	
	/**
	 * Gets the namespace name.
	 * 
	 * @return string The namespace name.
	 */
	public function getName() {
		
		return $this->class->getNamespaceName();
	}
	
	/**
	 * Gets the list with use statements within the namespace.
	 * 
	 * @return array An array containing use statements in the form (Alias => NS|Class).
	 */
	public function getUseStatements() {
		
		if ($this->useStatements === null) {
			$this->useStatements = $this->parse($this->class->getFileName());
		}
		
		return $this->useStatements;
	}
	
	/**
	 * Parse the class file and extract the use statements.
	 * 
	 * @param string $fileName The name of the file to parse.
	 * @return array A list with use statements or an empty array if no use statements exists.
	 */
	private function parse($fileName) {
		
		if (!$fileName) {
			return array();
		}
		
		$content = $this->getFileContent($fileName, $this->class->getStartLine());
		$namespace = str_replace('\\', '\\\\', $this->class->getNamespaceName());
		$content = preg_replace('/^.*?(\bnamespace\s+' . $namespace . '\s*[;|{].*)$/s', '\\1', $content);
		$this->tokens = token_get_all('<?php ' . $content);
		$this->numTokens = count($this->tokens);
		
		$statements = $this->parseUseStatements();
		
		return $statements;
	}
	
	/**
	 * Get the content of the file right up to the given line number.
	 * 
	 * @param string $fileName The name of the file to load.
	 * @param int $lineNumber The number of lines to read from file.
	 * @return string The content of the file.
	 */
	private function getFileContent($fileName, $lineNumber) {
		
		$content = '';
		$lineCnt = 0;
		$file = new SplFileObject($fileName);
		while(!$file->eof()) {
			if ($lineCnt++ == $lineNumber) {
				break;
			}
			
			$content .= $file->fgets();
		}
		
		return $content;
	}
	
	/**
	 * Gets the next non whitespace and non comment token.
	 * 
	 * @return array The token if exists, null otherwise.
	 */
	private function next() {
		
		for ($i = $this->pointer; $i < $this->numTokens; $i++) {
			$this->pointer++;
			if ($this->tokens[$i][0] === T_WHITESPACE ||
				$this->tokens[$i][0] === T_COMMENT ||
				$this->tokens[$i][0] === T_DOC_COMMENT) {
				
				continue;
			}
			
			return $this->tokens[$i];
		}
		
		return null;
	}
	
	/**
	 * Get all use statements.
	 * 
	 * @return array A list with all found use statements.
	 */
	private function parseUseStatements() {
		
		$statements = array();
		while (($token = $this->next())) {
			if ($token[0] === T_USE) {
				$statements = array_merge($statements, $this->parseUseStatement());
				continue;
			} else if ($token[0] !== T_NAMESPACE || $this->parseNamespace() != $this->class->getNamespaceName()) {
				continue;
			}
			
			// Get fresh array for new namespace. This is to prevent the parser to collect the use statements
			// for a previous namespace with the same name. This is the case if a namespace is defined twice
			// or if a namespace with the same name is commented out.
			$statements = array();
		}
		
		return $statements;
	}
	
	/**
	 * Get the namespace name.
	 *
	 * @return string The found namespace name.
	 */
	private function parseNamespace() {
		
		$namespace = '';
		while (($token = $this->next())){
			if ($token[0] === T_STRING || $token[0] === T_NS_SEPARATOR) {
				$namespace .= $token[1];
			} else {
				break;
			}
		}
		
		return $namespace;
	}
	
	/**
	 * Parse a single use statement.
	 * 
	 * @return array A list with all found class names for a use statement.
	 */
	private function parseUseStatement() {
		
		$class = '';
		$alias = '';
		$statements = array();
		$explicitAlias = false;
		while (($token = $this->next())) {
			$isNameToken = $token[0] === T_STRING || $token[0] === T_NS_SEPARATOR;
			if (!$explicitAlias && $isNameToken) {
				$class .= $token[1];
				$alias = $token[1];
			} else if ($explicitAlias && $isNameToken) {
				$alias .= $token[1];
			} else if ($token[0] === T_AS) {
				$explicitAlias = true;
				$alias = '';
			} else if ($token === ',') {
				$statements[$alias] = $class;
				$class = '';
				$alias = '';
				$explicitAlias = false;
			} else if ($token === ';') {
				$statements[$alias] = $class;
				break;
			} else {
				break;
			}
		}
		
		return $statements;
	}
}
