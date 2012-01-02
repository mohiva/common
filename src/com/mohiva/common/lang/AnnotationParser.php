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

use stdClass;
use Exception;
use com\mohiva\common\parser\exceptions\SyntaxErrorException;
use com\mohiva\common\io\exceptions\ClassNotFoundException;
use com\mohiva\common\lang\exceptions\UndefinedConstantException;
use com\mohiva\common\lang\exceptions\UndefinedParameterException;
use com\mohiva\common\lang\exceptions\UndefinedParameterValueException;
use com\mohiva\common\parser\TokenStream;

/**
 * Parse a string containing DocBlock annotations to annotation objects.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Util
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class AnnotationParser {
	
	/**
	 * @var string
	 */
	const NS_SEPARATOR = '\\';
	
	/**
	 * @var \com\mohiva\common\parser\TokenStream
	 */
	private $stream = null;
	
	/**
	 * @var AnnotationContext
	 */
	private $context = null;
	
	/**
	 * Parse the token stream to annotation objects.
	 *
	 * @param \com\mohiva\common\parser\TokenStream $stream The token stream to parse.
	 * @param AnnotationContext $context The annotation context.
	 * @return \com\mohiva\common\lang\AnnotationList A list containing the found annotation instances.
	 */
	public function parse(TokenStream $stream, AnnotationContext $context) {
		
		$this->stream = $stream;
		$this->context = $context;
		
		return $this->getAnnotations();
	}
	
	/**
	 * Create annotations from the tokenized input string.
	 * 
	 * @return AnnotationList A list of annotation instances or an empty list if no annotation was found.
	 */
	private function getAnnotations() {
		
		$annotations = new AnnotationList(array());
		while (($annotation = $this->getAnnotation()) !== null) {
			$annotations->addAnnotation($annotation);
		}
		
		return $annotations;
	}
	
	/**
	 * Get an instance of an annotation.
	 * 
	 * @return \com\mohiva\common\lang\annotations\Annotation An instance of an annotation.
	 */
	private function getAnnotation() {
		
		$valid = $this->stream->moveTo(AnnotationLexer::T_IDENTIFIER);
		if (!$valid) {
			return null;
		}
		
		$name = $this->getName();
		if ($this->stream->isNext(AnnotationLexer::T_OPEN_PARENTHESIS)) {
			$params = $this->getParams();
		} else {
			$params = array();
		}
		
		return $this->getAnnotationInstance($name, $params);
	}
	
	/**
	 * Create an instance of the given annotation and construct it with the params.
	 * 
	 * @param string $annotationName The name of the annotation.
	 * @param array $annotationParams The params for the annotation.
	 * @return \com\mohiva\common\lang\annotations\Annotation An annotation instance.
	 * @throws \com\mohiva\common\io\exceptions\ClassNotFoundException if the annotation class cannot be found.
	 */
	private function getAnnotationInstance($annotationName, array $annotationParams) {
		
		try {
			$class = new ReflectionClass($annotationName);
		} catch (ClassNotFoundException $e) {
			$message  = "The annotation class `{$annotationName}` cannot be found; ";
			$message .= "called in DocBlock for: {$this->context->getLocation()}; ";
			throw new ClassNotFoundException($message, null, $e);
		}
		
		// Get all method params
		$methodParams = array();
		$methodDefaultValues = array();
		$constructor = $class->getConstructor();
		$constructorParams = $constructor->getParameters();
		foreach ($constructorParams as $constructorParam) {
			/* @var \ReflectionParameter $constructorParam */
			$paramName = $constructorParam->getName();
			if ($constructorParam->isDefaultValueAvailable()) {
				$default = $constructorParam->getDefaultValue();
				$methodDefaultValues[$paramName] = $default;
				$methodParams[$paramName] = $default;
			} else {
				$methodParams[$paramName] = null;
			}
		}
		
		// Instantiate the class
		$methodName = array_key_exists(0, $annotationParams) ? 'Unnamed' : 'Named';
		$instance = $class->newInstanceArgs(
			$this->{"get{$methodName}InstanceParams"}(
				$annotationName,
				$methodParams,
				$methodDefaultValues,
				$annotationParams
			)
		);
		
		return $instance;
	}
	
	/**
	 * Get an array with all named instance parameters. When using named 
	 * parameters, all params excepting params which have default values, 
	 * must be defined in the annotation. It is also not allowed to 
	 * define more params as expected in the constructor definition.
	 * 
	 * @param string $className The name of the annotation class.
	 * @param array $methodParams An array containing all parameters defined in the class constructor.
	 * @param array $methodDefaultValues An array containing all default values defined in the class constructor.
	 * @param array $annotationParams All extracted annotation parameters.
	 * @return array A list with parameters for instantiating the annotation.
	 * @throws UndefinedParameterException if a parameter is not defined.
	 * @throws UndefinedParameterValueException if a parameter value is not given.
	 */
	private function getNamedInstanceParams(
		$className,
		array $methodParams,
		array $methodDefaultValues,
		array $annotationParams) {
		
		$instanceParams = array();
		$params = array_merge($methodParams, $annotationParams);
		foreach ($params as $paramName => $paramValue) {
			$paramExists = array_key_exists($paramName, $methodParams);
			$paramValueExists = array_key_exists($paramName, $annotationParams);
			$defaultParamValueExists = array_key_exists($paramName, $methodDefaultValues);
			if (!$paramExists) {
				throw new UndefinedParameterException(
					"The given param `{$paramName}` is not defined in the class `{$className}`"
				);
			}
			if (!$paramValueExists && !$defaultParamValueExists) {
				throw new UndefinedParameterValueException(
					"Missing value for param `{$paramName}`, defined in class `{$className}`"
				);
			}
			
			$instanceParams[] = $paramValue;
		}
		
		return $instanceParams;
	}
	
	/**
	 * Get an array with all unnamed instance parameters. Unnamed 
	 * parameters uses the same behaviour as PHP functions.
	 * 
	 * @param string $className The name of the annotation class.
	 * @param array $methodParams An array containing all parameters defined in the class constructor.
	 * @param array $methodDefaultValues An array containing all default values defined in the class constructor.
	 * @param array $annotationParams All extracted annotation parameters.
	 * @return array A list with parameters for instantiating the annotation.
	 * @throws UndefinedParameterValueException if a parameter value is not defined.
	 */
	private function getUnnamedInstanceParams(
		$className,
		array $methodParams,
		array $methodDefaultValues,
		array $annotationParams) {
		
		$paramKey = 0;
		$instanceParams = array();
		foreach (array_keys($methodParams) as $paramName) {
			$paramValueExists = array_key_exists($paramKey, $annotationParams);
			$defaultParamValueExists = array_key_exists($paramName, $methodDefaultValues);
			if (!$paramValueExists && !$defaultParamValueExists) {
				throw new UndefinedParameterValueException(
					"Missing value for param `{$paramName}`, defined in class `{$className}`"
				);
			}
			
			$instanceParams[] = $paramValueExists ? $annotationParams[$paramKey] : $methodDefaultValues[$paramName];
			$paramKey++;
		}
		
		$instanceParams = array_merge($instanceParams, $annotationParams);
		
		return $instanceParams;
	}
	
	/**
	 * Get the fully qualified name of an annotation.
	 * 
	 * @return string The fully qualified name of an annotation.
	 */
	private function getName() {
		
		/* @var \com\mohiva\common\lang\AnnotationToken $token */
		if ($this->stream->isNext(AnnotationLexer::T_NS_SEPARATOR)) {
			$token = $this->next(AnnotationLexer::T_NS_SEPARATOR);
			$name = $token->getValue();
		} else {
			$name = '';
		}
		
		$token = $this->next(AnnotationLexer::T_NAME);
		$name .= $token->getValue();
		while ($this->stream->isNext(AnnotationLexer::T_NS_SEPARATOR)) {
			/* @var \com\mohiva\common\lang\AnnotationToken $separator */
			$separator = $this->next(AnnotationLexer::T_NS_SEPARATOR);
			$token = $this->next(AnnotationLexer::T_NAME);
			$name .= $separator->getValue() . $token->getValue();
		}
		
		$name = $this->getFullyQualifiedName($name);
		
		return $name;
	}
	
	/**
	 * Parse all params for an annotation.
	 * 
	 * @return array The params for an annotation.
	 * @throws SyntaxErrorException if named and unnamed parameters are mixed.
	 */
	private function getParams() {
		
		$first = true;
		$params = array();
		
		$this->next(AnnotationLexer::T_OPEN_PARENTHESIS);
		if ($this->stream->isNext(AnnotationLexer::T_CLOSE_PARENTHESIS)) {
			$first = false;
		}
		
		$firstType = null;
		$currentType = null;
		while ($first || $this->stream->isNext(AnnotationLexer::T_COMMA)) {
			if (!$first) $this->next(AnnotationLexer::T_COMMA);			
			
			$first = false;
			$param = $this->getKeyValuePair(AnnotationLexer::T_NAME, AnnotationLexer::T_EQUAL);
			if (array_key_exists('key', $param)) {
				$params[$param['key']] = $param['value'];
			} else {
				$params[] = $param['value'];
			}
			
			// Disallow mixing of named and unnamed parameters
			$currentType = array_key_exists('key', $param) ? 1 : 2;
			if ($firstType === null) $firstType = array_key_exists('key', $param) ? 1 : 2;
			if ($firstType !== $currentType) {
				/* @var \com\mohiva\common\lang\AnnotationToken $token */
				$token = $this->stream->current();
				$message  = "It is not allowed to mix named and unnamed parameters at ";
				$message .= "offset `{$token->getOffset()}`; in {$this->context->getLocation()}";
				throw new SyntaxErrorException($message);
			}
		}
		
		$this->next(AnnotationLexer::T_CLOSE_PARENTHESIS);
		
		return $params;
	}
	
	/**
	 * Parse an annotation array in the form `[...]` to an array.
	 * 
	 * @return array An array.
	 */
	private function getArray() {
		
		$first = true;
		$array = array();
		
		$this->next(AnnotationLexer::T_OPEN_ARRAY);
		if ($this->stream->isNext(AnnotationLexer::T_CLOSE_ARRAY)) {
			$first = false;
		}
		
		while ($first || $this->stream->isNext(AnnotationLexer::T_COMMA)) {
			if (!$first) $this->next(AnnotationLexer::T_COMMA);
			
			$first = false;
			$item = $this->getKeyValuePair(AnnotationLexer::T_VALUE, AnnotationLexer::T_COLON);
			if (array_key_exists('key', $item)) {
				$array[$item['key']] = $item['value'];
			} else {
				$array[] = $item['value'];
			}
		}
		
		$this->next(AnnotationLexer::T_CLOSE_ARRAY);
		
		return $array;
	}
	
	/**
	 * Parse an annotation object in the form `{...}` to an 
	 * object of the type `stdClass`.
	 * 
	 * @return stdClass An object of type `stdClass`.
	 */
	private function getObject() {
		
		$first = true;
		$object = new stdClass();
		
		$this->next(AnnotationLexer::T_OPEN_OBJECT);
		if ($this->stream->isNext(AnnotationLexer::T_CLOSE_OBJECT)) {
			$first = false;
		}
		
		while ($first || $this->stream->isNext(AnnotationLexer::T_COMMA)) {
			if (!$first) $this->next(AnnotationLexer::T_COMMA);
			
			$first = false;
			$item = $this->getKeyValuePair(AnnotationLexer::T_NAME, AnnotationLexer::T_COLON, true);
			$object->{$item['key']} = $item['value'];
		}
		
		$this->next(AnnotationLexer::T_CLOSE_OBJECT);
		
		return $object;
	}
	
	/**
	 * Parse a key => value pair from an annotation.
	 * 
	 * @param int $keyToken The token which matches the key.
	 * @param int $assignmentToken The token which matches the assignment character.
	 * @param boolean $needKey Indicates if a key is needed or not.
	 * @return array If a key exists then an array with a key and value, otherwise an array with only a value.
	 */
	private function getKeyValuePair($keyToken, $assignmentToken, $needKey = false) {
		
		$item = array();
		
		/* @var \com\mohiva\common\lang\AnnotationToken $token */
		if ($needKey == true || $this->stream->isNext($assignmentToken, 2)) {
			if ($keyToken === AnnotationLexer::T_VALUE) {
				$item['key'] = $this->getValue();
			} else {
				$token = $this->next($keyToken);
				$item['key'] = $token->getValue();
			}
			
			$this->next($assignmentToken);
		}
		
		// Get the value token
		if ($this->stream->isNext(AnnotationLexer::T_IDENTIFIER)) {
			$item['value'] = $this->getAnnotation();
		} else if ($this->stream->isNext(AnnotationLexer::T_OPEN_ARRAY)) {
			$item['value'] = $this->getArray();
		} else if ($this->stream->isNext(AnnotationLexer::T_OPEN_OBJECT)) {
			$item['value'] = $this->getObject();
		} else if ($this->stream->isNext(AnnotationLexer::T_VALUE)) {
			$item['value'] = $this->getValue();
		} else if ($this->stream->isNext(AnnotationLexer::T_NAME) || (
			$this->stream->isNext(AnnotationLexer::T_NS_SEPARATOR) &&
			$this->stream->isNext(AnnotationLexer::T_NAME, 2))) {
			
			$item['value'] = $this->getConstant();
		} else {
			/* @var \com\mohiva\common\lang\AnnotationToken $token */
			$token = $this->stream->getLookahead();
			$this->syntaxError(array(
				AnnotationLexer::T_IDENTIFIER,
				AnnotationLexer::T_OPEN_ARRAY,
				AnnotationLexer::T_OPEN_OBJECT,
				AnnotationLexer::T_VALUE,
				AnnotationLexer::T_NAME,
				AnnotationLexer::T_NS_SEPARATOR
			), $token);
		}
		
		return $item;
	}
	
	/**
	 * Get the value from the current token position and cast it to its type.
	 * 
	 * @return mixed The value cast to its type.
	 */
	private function getValue() {
		
		/* @var \com\mohiva\common\lang\AnnotationToken $token */
		$token = $this->next(AnnotationLexer::T_VALUE);
		$value = $token->getValue();
		if (strtolower($value) === 'true') {
			$value = true;
		} else if (strtolower($value) === 'false') {
			$value = false;
		} else if (strtolower($value) === 'null') {
			$value = null;
		} else if (is_numeric($value) && strpos($value, '.') !== false) {
			$value = (float) $value;
		} else if (is_numeric($value)) {
			$value = (int) $value;
		} else if ($value[0] == '"') {
			$value = substr(substr($value, 1), 0, -1);
			$value = preg_replace('/\\\(")/', '$1', $value);
		} else {
			$value = substr(substr($value, 1), 0, -1);
			$value = preg_replace("/\\\\(')/", '$1', $value);
		}
		
		return $value;
	}
	
	/**
	 * Returns the value of a constant.
	 * 
	 * @return mixed The value of the constant.
	 * @throws ClassConstantNotFoundException if the constant is a class constant and if this class cannot be found.
	 * @throws UndefinedConstantExceptions if the constant isn't defined.
	 */
	private function getConstant() {
		
		/* @var \com\mohiva\common\lang\AnnotationToken $token */
		if ($this->stream->isNext(AnnotationLexer::T_NS_SEPARATOR)) {
			$token = $this->next(AnnotationLexer::T_NS_SEPARATOR);
			$constant = $token->getValue();
		} else {
			$constant = '';
		}
		
		$token = $this->next(AnnotationLexer::T_NAME);
		$constant .= $token->getValue();
		while ($this->stream->isNext(AnnotationLexer::T_NS_SEPARATOR)) {
			$token = $this->next(AnnotationLexer::T_NS_SEPARATOR);
			$constant .= $token->getValue();
			$token = $this->next(AnnotationLexer::T_NAME);
			$constant .= $token->getValue();
		}
		
		// Get a class constant
		$colon = $this->stream->isNext(AnnotationLexer::T_DOUBLE_COLON);
		if ($colon && $constant == 'self') {
			$constant = $this->context->getClass();
			$token = $this->next(AnnotationLexer::T_DOUBLE_COLON);
			$constant .= $token->getValue();
			$token = $this->next(AnnotationLexer::T_NAME);
			$constant .= $token->getValue();
		} else if ($colon) {
			$constant = $this->getFullyQualifiedName($constant);
			$token = $this->next(AnnotationLexer::T_DOUBLE_COLON);
			$constant .= $token->getValue();
			$token = $this->next(AnnotationLexer::T_NAME);
			$constant .= $token->getValue();
		}
		
		try {
			return constant($constant);
		} catch (ClassNotFoundException $e) {
			$snippet = preg_replace('/[\r\n\t]*/', '', substr($this->stream->getSource(), $token->getOffset(), 50));
			$message  = "The class for constant `{$constant}` cannot be found; ";
			$message .= "called in DocBlock for: {$this->context->getLocation()}; ";
			$message .= "found at offset `{$token->getOffset()}` near `{$snippet}`";
			throw new ClassNotFoundException($message, null, $e);
		} catch (Exception $e) {
			$snippet = preg_replace('/[\r\n\t]*/', '', substr($this->stream->getSource(), $token->getOffset(), 20));
			$message  = "The constant `{$constant}` is not defined; ";
			$message .= "called in DocBlock for: {$this->context->getLocation()}; ";
			$message .= "found at offset `{$token->getOffset()}` near `{$snippet}`";
			throw new UndefinedConstantException($message, null, $e);
		}
	}
	
	/**
	 * When the given token is the next token then move 
	 * to it, otherwise throw a syntax error exception.
	 * 
	 * @param int $token The token code to check.
	 * @return AnnotationToken The next token.
	 */
	private function next($token) {
		
		/* @var \com\mohiva\common\lang\AnnotationToken $lookahead */
		$lookahead = $this->stream->getLookahead();
		if (($lookahead && $lookahead->getCode() !== $token) || !$lookahead) {
			$this->syntaxError(array($token), $lookahead);
		}
		
		$this->stream->next();
		
		return $this->stream->current();
	}
	
	/**
	 * Use the internal PHP namespace resolution order to 
	 * resolve the FQN for the given name.
	 * 
	 * @param string $name The name to resolve.
	 * @return string The fully qualified name.
	 */
	private function getFullyQualifiedName($name) {
		
		$name = rtrim($name, self::NS_SEPARATOR);
		$pos = strpos($name, self::NS_SEPARATOR);
		$nsAlias = substr($name, 0, $pos);
		$useStatements = $this->context->getUseStatements();
		
		// Try to find the FQN for the annotation
		if ($pos === 0) {
			// Given name is an FQN
			$fqn = $name;
		} else if ($nsAlias && isset($useStatements[$nsAlias])) {
			// It must be an aliased namespace
			$lastPart = substr($name, $pos);
			$fqn = $useStatements[$nsAlias] . $lastPart;
			$fqn = trim($fqn, self::NS_SEPARATOR);
		} else if (!$nsAlias && isset($useStatements[$name])) {
			// It must be an aliased class name
			$fqn = $useStatements[$name];
		} else {
			// Use the namespace of the class
			$namespace = trim($this->context->getNamespace(), self::NS_SEPARATOR);
			$fqn = $namespace . self::NS_SEPARATOR . $name;
		}
		
		return $fqn;
	}
	
	/**
	 * Throw a syntax error exception.
	 * 
	 * @param array $tokens A list with expected tokens.
	 * @param AnnotationToken $lookahead The lookahead token.
	 * @throws SyntaxErrorException if a syntax error occurs.
	 */
	private function syntaxError(array $tokens, AnnotationToken $lookahead = null) {
		
		$refObject = new ReflectionClass(__NAMESPACE__ . '\AnnotationLexer');
		$tokens = array_map(array($refObject, 'getConstantByValue'), $tokens);
		$tokens = implode('`,`', $tokens);
		if (!$lookahead) {
			$message  = "Syntax error in DocBlock for: {$this->context->getLocation()}, ";
			$message .= "expected tokens: `{$tokens}` but end of string reached";
		} else {
			$snippet = preg_replace('/[\r\n\t]*/', '', substr($this->stream->getSource(), $lookahead->getOffset(), 50));
			$message  = "Syntax error in DocBlock for: {$this->context->getLocation()}, ";
			$message .= "expected tokens: `{$tokens}` but found `{$lookahead->getValue()}` ";
			$message .= "at offset `{$lookahead->getOffset()}` near: `{$snippet}`";
		}
		
		throw new SyntaxErrorException($message);
	}
}
