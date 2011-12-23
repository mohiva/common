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
 * @copyright Copyright (c) 2007-2011 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
namespace com\mohiva\common\lang;

use com\mohiva\common\parser\TokenStream;
use com\mohiva\common\cache\containers\AnnotationContainer;

/**
 * The `ReflectionDocComment` class reports information about a doc comment 
 * containing DocBlock annotations.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Util
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2011 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class ReflectionDocComment {
	
	/**
	 * @var \com\mohiva\common\cache\containers\AnnotationContainer
	 */
	private static $cacheContainer = null;
	
	/** 
	 * @var \com\mohiva\common\lang\AnnotationReflector
	 */
	private $reflector = null;
	
	/**
	 * @var \com\mohiva\common\lang\AnnotationList
	 */
	private $annotations = null;
	
	/**
	 * @var array
	 */
	private $docTags = array(
		"@abstract\\s",
		"@access\\s",
		"@author\\s",
		"@category\\s",
		"@copyright\\s",
		"@deprecated\\s",
		"@example\\s",
		"@final\\s",
		"@filesource\\s",
		"@global\\s",
		"@ignore\\s",
		"@internal\\s",
		"@license\\s",
		"@link\\s",
		"@method\\s",
		"@name\\s",
		"@package\\s",
		"@param\\s",
		"@property\\s",
		"@return\\s",
		"@see\\s",
		"@since\\s",
		"@static\\s",
		"@staticvar\\s",
		"@subpackage\\s",
		"@todo\\s",
		"@tutorial\\s",
		"@uses\\s",
		"@var\\s",
		"@version\\s"
	);
	
	/**
	 * @var array
	 */
	private $docInlineTags = array(
		'{@example}',
		'{@id}',
		'{@internal}',
		'{@inheritdoc}',
		'{@link}',
		'{@source}',
		'{@toc}',
		'{@tutorial}'
	);
	
	/**
	 * Set the cache container used to cache annotations.
	 * 
	 * @param \com\mohiva\common\cache\containers\AnnotationContainer $container
	 */
	public static function setCacheContainer(AnnotationContainer $container) {
		
		self::$cacheContainer = $container;
	}
	
	/**
	 * The class constructor.
	 * 
	 * @param AnnotationReflector $reflector
	 */
	public function __construct(AnnotationReflector $reflector) {
		
		$this->reflector = $reflector;
	}
	
	/**
	 * Gets the list with all found annotations.
	 * 
	 * @return AnnotationList
	 */
	public function getAnnotationList() {
		
		if ($this->annotations === null) {
			$this->annotations = $this->parseAnnotations();
		}
		
		return $this->annotations;
	}
	
	/**  
	 * @return AnnotationList
	 */
	private function parseAnnotations() {
		
		$docComment = $this->reflector->getDocComment();
		if (!$docComment) {
			return new AnnotationList();
		}
		
		$annotations = $this->getCachedAnnotations($docComment);
		if ($annotations !== null) {
			return $annotations;
		}
		
		$docComment = $this->cleanDocComment($docComment);
		$stream = $this->createTokenStream($docComment);
		$context = $this->getAnnotationContext();
		$parser = new AnnotationParser();
		$annotations = $parser->parse($stream, $context);
		
		$this->cacheAnnotations($docComment, $annotations);
		
		return $annotations;
	}
	
	/**
	 * Remove registered annotations from comment.
	 * 
	 * @param string $rawComment
	 * @return string
	 */
	private function cleanDocComment($rawComment) {

		$exceptions = array_merge($this->docTags, $this->docInlineTags);
		$cleanedComment = preg_replace('/' . implode('|', $exceptions) . '/', '', $rawComment);
		$cleanedComment = substr($cleanedComment, strpos($cleanedComment, '@'));
		$cleanedComment = preg_replace('/^[\*\s]+/m', '', $cleanedComment);
		$cleanedComment = trim($cleanedComment, '*/ ');
		
		return $cleanedComment;
	}
	
	/**
	 * @param string $docComment 
	 * @return \com\mohiva\common\parser\TokenStream
	 */
	private function createTokenStream($docComment) {
		
		$lexer = new AnnotationLexer(new TokenStream());
		$stream = $lexer->scan($docComment);
		
		return $stream;
	}
	
	/**
	 * @return AnnotationContext
	 */
	private function getAnnotationContext() {
		
		$namespace = $this->reflector->getNamespace();
		$context = new AnnotationContext(
			$namespace->getName(),
			$namespace->getUseStatements(),
			$this->reflector->getClassContext(),
			$this->reflector->getParseContext()
		);
		
		return $context;
	}
	
	/** 
	 * @param string $docComment
	 * @return AnnotationList
	 */
	private function getCachedAnnotations($docComment) {
		
		if (self::$cacheContainer === null) {
			return null;
		}
		
		$annotations = self::$cacheContainer->fetch($docComment);
		
		return $annotations;
	}
	
	/**
	 * @param string $docComment
	 * @param AnnotationList $annotations
	 */
	private function cacheAnnotations($docComment, AnnotationList $annotations) {
		
		if (self::$cacheContainer === null) {
			return;
		}
		
		self::$cacheContainer->store($docComment, $annotations);
	}
}
