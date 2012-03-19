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
 * @package   Mohiva/Common/Parser
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
namespace com\mohiva\common\parser;

use SplDoublyLinkedList;
use Iterator;
use Countable;

/**
 * A list of tokens implemented as double linked list.
 *
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Parser
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class TokenStream implements Iterator, Countable {

	/**
	 * Iterator modes.
	 *
	 * @var int
	 */
	const IT_MODE_LIFO = SplDoublyLinkedList::IT_MODE_LIFO;
	const IT_MODE_FIFO = SplDoublyLinkedList::IT_MODE_FIFO;
	const IT_MODE_DELETE = SplDoublyLinkedList::IT_MODE_DELETE;
	const IT_MODE_KEEP = SplDoublyLinkedList::IT_MODE_KEEP;

	/**
	 * The token list.
	 *
	 * @var \SplDoublyLinkedList
	 */
	private $tokens = null;

	/**
	 * The source code of the stream.
	 *
	 * @var string
	 */
	private $source = null;

	/**
	 * The class constructor.
	 *
	 * @param \SplDoublyLinkedList $list The list implementation to use or null to use the standard PHP implementation.
	 */
	public function __construct(SplDoublyLinkedList $list = null) {

		if ($list instanceof SplDoublyLinkedList) {
			$this->tokens = $list;
		} else {
			$this->tokens = new SplDoublyLinkedList();
		}
	}

	/**
	 * Set the source code of the stream.
	 *
	 * @param string $source The source code of the stream.
	 */
	public function setSource($source) {

		$this->source = $source;
	}

	/**
	 * Returns the source code of the stream.
	 *
	 * @return string The source code of the stream.
	 */
	public function getSource() {

		return $this->source;
	}

	/**
	 * Sets the mode of iteration.
	 *
	 * After changing the direction mode(FIFO or LIFO) on a filled stream, it should be rewinded.
	 *
	 * @param int $mode See the documentation for `SplDoublyLinkedList::setIteratorMode` for more details.
	 * @link http://www.php.net/manual/en/spldoublylinkedlist.setiteratormode.php
	 */
	public function setIteratorMode($mode) {

		$this->tokens->setIteratorMode($mode);
	}

	/**
	 * Returns the mode of iteration.
	 *
	 * @return int Returns the different modes and flags that affect the iteration.
	 * @link http://www.php.net/manual/en/spldoublylinkedlist.getiteratormode.php
	 */
	public function getIteratorMode() {

		return $this->tokens->getIteratorMode();
	}

	/**
	 * Pushes a `Token` at the end of the list.
	 *
	 * @param Token $token The token to push.
	 * @link http://www.php.net/manual/en/spldoublylinkedlist.push.php
	 */
	public function push(Token $token) {

		$this->tokens->push($token);
	}

	/**
	 * Pops a `Token` from the end of the list.
	 *
	 * @return Token The popped token.
	 * @link http://www.php.net/manual/en/spldoublylinkedlist.pop.php
	 */
	public function pop() {

		return $this->tokens->pop();
	}

	/**
	 * Peeks a `Token` from the end of the list.
	 *
	 * @return Token The last token.
	 * @link http://www.php.net/manual/en/spldoublylinkedlist.top.php
	 */
	public function top() {

		return $this->tokens->top();
	}

	/**
	 * Peeks a `Token` from the beginning of the list.
	 *
	 * @return Token The first token.
	 * @link http://www.php.net/manual/en/spldoublylinkedlist.bottom.php
	 */
	public function bottom() {

		return $this->tokens->bottom();
	}

	/**
	 * Shifts a `Token` from the beginning of the list.
	 *
	 * @return Token The shifted token.
	 * @link http://www.php.net/manual/en/spldoublylinkedlist.shift.php
	 */
	public function shift() {

		return $this->tokens->shift();
	}

	/**
	 * Prepends the list with an `Token`.
	 *
	 * @param Token $token The token to unshift.
	 * @link http://www.php.net/manual/en/spldoublylinkedlist.unshift.php
	 */
	public function unshift(Token $token) {

		$this->tokens->unshift($token);
	}

	/**
	 * Flush the stream.
	 */
	public function flush() {

		if ($this->isEmpty()) {
			return;
		}

		$cnt = $this->count();
		for ($i = 0; $i < $cnt; $i++) {
			$this->shift();
		}
	}

	/**
	 * Check if one of the given token codes exists at the current position.
	 *
	 * If none of the token codes is the current token then this method executes the given
	 * closure and passes the current token as argument.
	 *
	 * @param array $tokenCodes A list of token codes to check for.
	 * @param \Closure $errorHandler The function to call if none of the tokens codes is the current token.
	 * @return boolean True if one of the given token codes is the current token, false otherwise.
	 */
	public function expect(array $tokenCodes, \Closure $errorHandler = null) {

		if ($this->valid() && in_array($this->current()->getCode(), $tokenCodes)) {
			return true;
		} else if ($errorHandler) {
			$errorHandler($this->current());
		}

		return false;
	}

	/**
	 * Return current token index.
	 *
	 * @return mixed The current token index.
	 * @link http://www.php.net/manual/en/spldoublylinkedlist.key.php
	 */
	public function key() {

		return $this->tokens->key();
	}

	/**
	 * Return current token.
	 *
	 * @return Token The current token.
	 * @link http://www.php.net/manual/en/spldoublylinkedlist.current.php
	 */
	public function current() {

		return $this->tokens->current();
	}

	/**
	 * Move to next token.
	 *
	 * @link http://www.php.net/manual/en/spldoublylinkedlist.next.php
	 */
	public function next() {

		$this->tokens->next();
	}

	/**
	 * Move to previous token.
	 *
	 * @link http://www.php.net/manual/en/spldoublylinkedlist.prev.php
	 */
	public function prev() {

		$this->tokens->prev();
	}

	/**
	 * Check whether the list contains more tokens.
	 *
	 * @return bool True if the list contains any more tokens, false otherwise.
	 * @link http://www.php.net/manual/en/spldoublylinkedlist.valid.php
	 */
	public function valid() {

		return $this->tokens->valid();
	}

	/**
	 * Rewind iterator back to the start.
	 *
	 * @link http://www.php.net/manual/en/spldoublylinkedlist.rewind.php
	 */
	public function rewind() {

		$this->tokens->rewind();
	}

	/**
	 * Counts the number of tokens in the list.
	 *
	 * @return int Returns the number of tokens in the list.
	 * @link http://www.php.net/manual/en/spldoublylinkedlist.count.php
	 */
	public function count() {

		return $this->tokens->count();
	}

	/**
	 * Checks whether the list is empty or not.
	 *
	 * @return bool True if the list is empty, false otherwise.
	 * @link http://www.php.net/manual/en/spldoublylinkedlist.isempty.php
	 */
	public function isEmpty() {

		return $this->tokens->isEmpty();
	}

	/**
	 * Move the internal array pointer to the next given token.
	 *
	 * @param int $tokenCode The code of the token to find.
	 * @return boolean True if the token can be found, false otherwise.
	 */
	public function moveTo($tokenCode) {

		while ($this->valid()) {
			$current = $this->current();
			if ($current->getCode() === $tokenCode) {
				return true;
			}

			$this->next();
		}

		return false;
	}

	/**
	 * Get the code of the lookahead token.
	 *
	 * @param int $number The number of tokens to look ahead.
	 * @return int The code of the lookahead token or null if it not exists.
	 */
	public function getLookaheadCode($number = 1) {

		$lookahead = $this->getLookahead($number);
		if ($lookahead) {
			return $lookahead->getCode();
		}

		return $lookahead;
	}

	/**
	 * Get the lookahead token.
	 *
	 * @param int $number The number of tokens to look ahead.
	 * @return Token The lookahead token or null if it not exists.
	 */
	public function getLookahead($number = 1) {

		if (!$this->valid()) return null;

		// We must check for IT_MODE_LIFO here because IT_MODE_FIFO has no explicit value, it shares
		// the same value(0) with IT_MODE_KEEP
		$lifo = ($this->getIteratorMode() & self::IT_MODE_LIFO) === self::IT_MODE_LIFO ? true : false;
		if (!$lifo && isset($this->tokens[$this->key() + $number])) {
			// Get the token in forward direction
			$lookahead = $this->tokens[$this->key() + $number];
		} else if ($lifo && isset($this->tokens[$this->count() - 1 - $this->key() + $number])) {
			// Get the token in backward direction
			$lookahead = $this->tokens[$this->count() - 1 - $this->key() + $number];
		} else {
			$lookahead = null;
		}

		return $lookahead;
	}

	/**
	 * Check if the token is the next token in the token stack.
	 *
	 * @param int $tokenCode The token code to check for.
	 * @param int $number The number of tokens to look ahead.
	 * @return boolean True if the token is the next, false otherwise.
	 */
	public function isNext($tokenCode, $number = 1) {

		if ($this->getLookaheadCode($number) === $tokenCode) {
			return true;
		}

		return false;
	}
}
