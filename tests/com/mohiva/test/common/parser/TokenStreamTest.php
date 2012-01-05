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
namespace com\mohiva\test\common\parser;

use SplDoublyLinkedList;
use com\mohiva\common\parser\Token;
use com\mohiva\common\parser\TokenStream;
use com\mohiva\test\resources\common\parser\TestToken;

/**
 * Unit test case for the `TokenStream` class.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Test
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class TokenStreamTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * Test tokens.
	 * 
	 * @var int
	 */
	const T_NONE              = 0;
	const T_OPEN_PARENTHESIS  = 1;
	const T_CLOSE_PARENTHESIS = 2;
	const T_OPEN_ARRAY        = 3;
	const T_CLOSE_ARRAY       = 4;
	const T_POINT             = 5;
	const T_COMMA             = 6;
	const T_QUESTION_MARK     = 7;
	const T_COLON             = 8;
	const T_DOUBLE_COLON      = 9;
	const T_NS_SEPARATOR      = 10;
	const T_VALUE             = 11;
	const T_OPERATOR          = 12;
	const T_NAME              = 13;
	
	/**
	 * Test the `setSource` and `getSource` accessors.
	 */
	public function testSourceAccessors() {
		
		$source = 'a string';
		
		$stream = new TokenStream();
		$stream->setSource($source);
		
		$this->assertSame($source, $stream->getSource());
	}
	
	/**
	 * Test the `setIteratorMode` and `getIteratorMode` accessors.
	 */
	public function testIteratorModeAccessors() {
		
		$mode = SplDoublyLinkedList::IT_MODE_FIFO | SplDoublyLinkedList::IT_MODE_DELETE;
		
		$stream = new TokenStream();
		$stream->setIteratorMode($mode);
		
		$this->assertSame($mode, $stream->getIteratorMode());
	}
	
	/**
	 * Test the `push` method.
	 */
	public function testPush() {
		
		/* @var \com\mohiva\common\parser\Token $token */
		$token = $this->getMock('\com\mohiva\common\parser\Token');
		
		$list = new SplDoublyLinkedList();
		$stream = new TokenStream($list);
		$stream->push($token);
		
		$this->assertSame($token, $list->pop());
	}
	
	/**
	 * Test the `pop` method.
	 */
	public function testPop() {
		
		/* @var \com\mohiva\common\parser\Token $token1 */
		/* @var \com\mohiva\common\parser\Token $token2 */
		$token1 = $this->getMock('\com\mohiva\common\parser\Token');
		$token2 = $this->getMock('\com\mohiva\common\parser\Token');
		
		$list = new SplDoublyLinkedList();
		$list->push($token1);
		$list->push($token2);
		
		$stream = new TokenStream($list);
		
		$this->assertSame($token2, $stream->pop());
	}
	
	/**
	 * Test the `top` method.
	 */
	public function testTop() {
		
		/* @var \com\mohiva\common\parser\Token $token1 */
		/* @var \com\mohiva\common\parser\Token $token2 */
		$token1 = $this->getMock('\com\mohiva\common\parser\Token');
		$token2 = $this->getMock('\com\mohiva\common\parser\Token');
		
		$list = new SplDoublyLinkedList();
		$list->push($token1);
		$list->push($token2);
		
		$stream = new TokenStream($list);
		
		$this->assertSame($token2, $stream->top());
	}
	
	/**
	 * Test the `bottom` method.
	 */
	public function testBottom() {
		
		/* @var \com\mohiva\common\parser\Token $token1 */
		/* @var \com\mohiva\common\parser\Token $token2 */
		$token1 = $this->getMock('\com\mohiva\common\parser\Token');
		$token2 = $this->getMock('\com\mohiva\common\parser\Token');
		
		$list = new SplDoublyLinkedList();
		$list->push($token1);
		$list->push($token2);
		
		$stream = new TokenStream($list);
		
		$this->assertSame($token1, $stream->bottom());
	}
	
	/**
	 * Test the `shift` method.
	 */
	public function testShift() {
		
		/* @var \com\mohiva\common\parser\Token $token1 */
		/* @var \com\mohiva\common\parser\Token $token2 */
		$token1 = $this->getMock('\com\mohiva\common\parser\Token');
		$token2 = $this->getMock('\com\mohiva\common\parser\Token');
		
		$list = new SplDoublyLinkedList();
		$list->push($token1);
		$list->push($token2);
		
		$stream = new TokenStream($list);
		
		$this->assertSame($token1, $stream->shift());
	}
	
	/**
	 * Test the `unshift` method.
	 */
	public function testUnshift() {
		
		/* @var \com\mohiva\common\parser\Token $token1 */
		/* @var \com\mohiva\common\parser\Token $token2 */
		$token1 = $this->getMock('\com\mohiva\common\parser\Token');
		$token2 = $this->getMock('\com\mohiva\common\parser\Token');
		
		$list = new SplDoublyLinkedList();
		$list->push($token1);
		
		$stream = new TokenStream($list);
		$stream->unshift($token2);
		
		$this->assertSame($token2, $list->bottom());
	}
	
	/**
	 * Test the `flush` method.
	 */
	public function testFlush() {
		
		$list = new SplDoublyLinkedList();
		$list->push($this->getMock('\com\mohiva\common\parser\Token'));
		$list->push($this->getMock('\com\mohiva\common\parser\Token'));
		
		$stream = new TokenStream($list);
		$stream->flush();
		
		$this->assertTrue($list->isEmpty());
	}
	
	/**
	 * Test if the `expect` method returns true if the given token is the current token.
	 * 
	 * @param \com\mohiva\common\parser\TokenStream $stream The token stream to use for this test.
	 * @dataProvider tokenStreamProvider
	 */
	public function testExpectReturnsTrue(TokenStream $stream) {
		
		$this->assertTrue($stream->expect(array(self::T_NAME)));
	}
	
	/**
	 * Test if the `expect` method returns false if the given token isn't the current token.
	 * 
	 * @param \com\mohiva\common\parser\TokenStream $stream The token stream to use for this test.
	 * @dataProvider tokenStreamProvider
	 */
	public function testExpectReturnsFalse(TokenStream $stream) {
		
		$this->assertFalse($stream->expect(array(self::T_COLON)));
	}
	
	/**
	 * Test if the `expect` method returns false if stream has reached the end.
	 */
	public function testExpectReturnsFalseAtStreamEnd() {
		
		$stream = new TokenStream();
		$stream->push(new TestToken(self::T_NAME));
		
		$this->assertFalse($stream->expect(array(self::T_NAME)));
	}
	
	/**
	 * Test if the `expect` method executes the given closure if the given token isn't the current token.
	 * 
	 * @param \com\mohiva\common\parser\TokenStream $stream The token stream to use for this test.
	 * @dataProvider tokenStreamProvider
	 */
	public function testExpectExecutesClosure(TokenStream $stream) {
		
		$expected = array(self::T_COLON);
		$stream->expect($expected, function(Token $current) use ($expected) {
			$this->assertSame(self::T_NAME, $current->getCode());
		});
	}
	
	/**
	 * Test the `key` method.
	 */
	public function testKey() {
		
		$list = new SplDoublyLinkedList();
		$list->push($this->getMock('\com\mohiva\common\parser\Token'));
		$list->push($this->getMock('\com\mohiva\common\parser\Token'));
		$list->rewind();
		
		$stream = new TokenStream($list);
		
		$this->assertSame(0, $stream->key());
	}
	
	/**
	 * Test the `current` method.
	 */
	public function testCurrent() {
		
		/* @var \com\mohiva\common\parser\Token $token1 */
		/* @var \com\mohiva\common\parser\Token $token2 */
		$token1 = $this->getMock('\com\mohiva\common\parser\Token');
		$token2 = $this->getMock('\com\mohiva\common\parser\Token');
		
		$list = new SplDoublyLinkedList();
		$list->push($token1);
		$list->push($token2);
		$list->rewind();
		
		$stream = new TokenStream($list);
		
		$this->assertSame($token1, $stream->current());
	}
	
	/**
	 * Test the `next` method.
	 */
	public function testNext() {
		
		/* @var \com\mohiva\common\parser\Token $token1 */
		/* @var \com\mohiva\common\parser\Token $token2 */
		$token1 = $this->getMock('\com\mohiva\common\parser\Token');
		$token2 = $this->getMock('\com\mohiva\common\parser\Token');
		
		$list = new SplDoublyLinkedList();
		$list->push($token1);
		$list->push($token2);
		$list->rewind();
		
		$stream = new TokenStream($list);
		$stream->next();
		
		$this->assertSame($token2, $list->current());
	}
	
	/**
	 * Test the `prev` method.
	 */
	public function testPrev() {
		
		/* @var \com\mohiva\common\parser\Token $token1 */
		/* @var \com\mohiva\common\parser\Token $token2 */
		$token1 = $this->getMock('\com\mohiva\common\parser\Token');
		$token2 = $this->getMock('\com\mohiva\common\parser\Token');
		
		$list = new SplDoublyLinkedList();
		$list->push($token1);
		$list->push($token2);
		$list->rewind();
		
		$stream = new TokenStream($list);
		$stream->next();
		$stream->prev();
		
		$this->assertSame($token1, $list->current());
	}
	
	/**
	 * Test if the `valid` method returns true.
	 */
	public function testValidReturnsTrue() {
		
		$list = new SplDoublyLinkedList();
		$list->push($this->getMock('\com\mohiva\common\parser\Token'));
		$list->rewind();
		
		$stream = new TokenStream($list);
		
		$this->assertTrue($stream->valid());
	}
	
	/**
	 * Test if the `valid` method returns false.
	 */
	public function testValidReturnsFalse() {
		
		$list = new SplDoublyLinkedList();
		$list->push($this->getMock('\com\mohiva\common\parser\Token'));
		$list->rewind();
		
		$stream = new TokenStream($list);
		$stream->next();
		
		$this->assertFalse($stream->valid());
	}
	
	/**
	 * Test the `rewind` method.
	 */
	public function testRewind() {
		
		/* @var \com\mohiva\common\parser\Token $token1 */
		/* @var \com\mohiva\common\parser\Token $token2 */
		$token1 = $this->getMock('\com\mohiva\common\parser\Token');
		$token2 = $this->getMock('\com\mohiva\common\parser\Token');
		
		$list = new SplDoublyLinkedList();
		$list->push($token1);
		$list->push($token2);
		
		$stream = new TokenStream($list);
		$stream->rewind();
		
		$this->assertSame($token1, $list->current());
	}
	
	/**
	 * Test the `count` method.
	 */
	public function testCount() {
		
		$list = new SplDoublyLinkedList();
		$list->push($this->getMock('\com\mohiva\common\parser\Token'));
		$list->push($this->getMock('\com\mohiva\common\parser\Token'));
		
		$stream = new TokenStream($list);
		
		$this->assertSame(2, $stream->count());
	}
	
	/**
	 * Test the `isEmpty` method returns true.
	 */
	public function testIsEmptyReturnTrue() {
				
		$stream = new TokenStream();
		
		$this->assertTrue($stream->isEmpty());
	}
	
	/**
	 * Test the `isEmpty` method returns false.
	 */
	public function testIsEmptyReturnFalse() {
		
		$list = new SplDoublyLinkedList();
		$list->push($this->getMock('\com\mohiva\common\parser\Token'));
		
		$stream = new TokenStream($list);
		
		$this->assertFalse($stream->isEmpty());
	}
	
	/**
	 * Test if can move to a specified token.
	 * 
	 * @param \com\mohiva\common\parser\TokenStream $stream The token stream to use for this test.
	 * @dataProvider tokenStreamProvider
	 */
	public function testMoveTo(TokenStream $stream) {
		
		// user.name.split(" ").join("-")
		$moved = $stream->moveTo(self::T_POINT);
		$current = $stream->current();
		$this->assertTrue($moved);
		$this->assertSame($current->getCode(), self::T_POINT);
		
		$moved = $stream->moveTo(self::T_POINT);
		$current = $stream->current();
		$this->assertTrue($moved);
		$this->assertSame($current->getCode(), self::T_POINT);
		
		$moved = $stream->moveTo(self::T_VALUE);
		$current = $stream->current();
		$this->assertTrue($moved);
		$this->assertSame($current->getCode(), self::T_VALUE);
		
		$moved = $stream->moveTo(self::T_OPEN_PARENTHESIS);
		$current = $stream->current();
		$this->assertTrue($moved);
		$this->assertSame($current->getCode(), self::T_OPEN_PARENTHESIS);
		
		$moved = $stream->moveTo(self::T_CLOSE_PARENTHESIS);
		$current = $stream->current();
		$this->assertTrue($moved);
		$this->assertSame($current->getCode(), self::T_CLOSE_PARENTHESIS);
		
		$moved = $stream->moveTo(self::T_NAME);
		$this->assertFalse($moved);
	}
	
	/**
	 * Test if can get the code for the lookahead token.
	 * 
	 * @param \com\mohiva\common\parser\TokenStream $stream The token stream to use for this test.
	 * @dataProvider tokenStreamProvider
	 */
	public function testGetLookaheadCode(TokenStream $stream) {
		
		// user.name.split(" ").join("-")
		$this->assertSame($stream->getLookaheadCode(), self::T_POINT);
		$this->assertSame($stream->getLookaheadCode(1), self::T_POINT);
		$stream->next();
		$this->assertSame($stream->getLookaheadCode(1), self::T_NAME);
		$stream->next();
		$this->assertSame($stream->getLookaheadCode(1), self::T_POINT);
		$stream->next();
		$this->assertSame($stream->getLookaheadCode(1), self::T_NAME);
		$this->assertSame($stream->getLookaheadCode(2), self::T_OPEN_PARENTHESIS);
		$this->assertSame($stream->getLookaheadCode(3), self::T_VALUE);
		$this->assertSame($stream->getLookaheadCode(4), self::T_CLOSE_PARENTHESIS);
		$this->assertSame($stream->getLookaheadCode(5), self::T_POINT);
		$this->assertSame($stream->getLookaheadCode(6), self::T_NAME);
		$this->assertSame($stream->getLookaheadCode(7), self::T_OPEN_PARENTHESIS);
		$this->assertSame($stream->getLookaheadCode(8), self::T_VALUE);
		$this->assertSame($stream->getLookaheadCode(9), self::T_CLOSE_PARENTHESIS);
		$this->assertNull($stream->getLookaheadCode(10));
	}
	
	/**
	 * Test if can get the lookahead token if iterator mode is set to FIFO.
	 * 
	 * @param \com\mohiva\common\parser\TokenStream $stream The token stream to use for this test.
	 * @dataProvider tokenStreamProvider
	 */
	public function testGetLookaheadInFifoMode(TokenStream $stream) {
		
		// user.name.split(" ").join("-")
		$stream->setIteratorMode(TokenStream::IT_MODE_FIFO);
		$stream->rewind();
		
		$this->assertSame($stream->getLookahead()->getCode(), self::T_POINT);
		$this->assertSame($stream->getLookahead(1)->getCode(), self::T_POINT);
		$stream->next();
		$this->assertSame($stream->getLookahead(1)->getCode(), self::T_NAME);
		$stream->next();
		$this->assertSame($stream->getLookahead(1)->getCode(), self::T_POINT);
		$stream->next();
		$this->assertSame($stream->getLookahead(1)->getCode(), self::T_NAME);
		$this->assertSame($stream->getLookahead(2)->getCode(), self::T_OPEN_PARENTHESIS);
		$this->assertSame($stream->getLookahead(3)->getCode(), self::T_VALUE);
		$this->assertSame($stream->getLookahead(4)->getCode(), self::T_CLOSE_PARENTHESIS);
		$this->assertSame($stream->getLookahead(5)->getCode(), self::T_POINT);
		$this->assertSame($stream->getLookahead(6)->getCode(), self::T_NAME);
		$this->assertSame($stream->getLookahead(7)->getCode(), self::T_OPEN_PARENTHESIS);
		$this->assertSame($stream->getLookahead(8)->getCode(), self::T_VALUE);
		$this->assertSame($stream->getLookahead(9)->getCode(), self::T_CLOSE_PARENTHESIS);
		$this->assertNull($stream->getLookahead(10));
	}
	
	/**
	 * Test if can get the lookahead token if iterator mode is set to LIFO.
	 * 
	 * @param \com\mohiva\common\parser\TokenStream $stream The token stream to use for this test.
	 * @dataProvider tokenStreamProvider
	 */
	public function testGetLookaheadInLifoMode(TokenStream $stream) {
		
		// user.name.split(" ").join("-")
		$stream->setIteratorMode(TokenStream::IT_MODE_LIFO);
		$stream->rewind();
		
		$this->assertSame($stream->getLookahead()->getCode(), self::T_VALUE);
		$this->assertSame($stream->getLookahead(1)->getCode(), self::T_VALUE);
		$stream->next();
		$this->assertSame($stream->getLookahead(1)->getCode(), self::T_OPEN_PARENTHESIS);
		$stream->next();
		$this->assertSame($stream->getLookahead(1)->getCode(), self::T_NAME);
		$stream->next();
		$this->assertSame($stream->getLookahead(1)->getCode(), self::T_POINT);
		$this->assertSame($stream->getLookahead(2)->getCode(), self::T_CLOSE_PARENTHESIS);
		$this->assertSame($stream->getLookahead(3)->getCode(), self::T_VALUE);
		$this->assertSame($stream->getLookahead(4)->getCode(), self::T_OPEN_PARENTHESIS);
		$this->assertSame($stream->getLookahead(5)->getCode(), self::T_NAME);
		$this->assertSame($stream->getLookahead(6)->getCode(), self::T_POINT);
		$this->assertSame($stream->getLookahead(7)->getCode(), self::T_NAME);
		$this->assertSame($stream->getLookahead(8)->getCode(), self::T_POINT);
		$this->assertSame($stream->getLookahead(9)->getCode(), self::T_NAME);
		$this->assertNull($stream->getLookahead(10));
	}
	
	/**
	 * Test if a specified token is the next token.
	 * 
	 * @param \com\mohiva\common\parser\TokenStream $stream The token stream to use for this test.
	 * @dataProvider tokenStreamProvider
	 */
	public function testIsNext(TokenStream $stream) {
		
		// user.name.split(" ").join("-")
		$this->assertTrue($stream->isNext(self::T_POINT));
		$stream->next();
		$this->assertTrue($stream->isNext(self::T_NAME));
		$stream->next();
		$this->assertTrue($stream->isNext(self::T_POINT));
		$stream->next();
		$this->assertTrue($stream->isNext(self::T_NAME));
		$stream->next();
		$this->assertTrue($stream->isNext(self::T_OPEN_PARENTHESIS));
		$stream->next();
		$this->assertTrue($stream->isNext(self::T_VALUE));
		$this->assertTrue($stream->isNext(self::T_CLOSE_PARENTHESIS, 2));
		$this->assertTrue($stream->isNext(self::T_POINT, 3));
		$this->assertTrue($stream->isNext(self::T_NAME, 4));
		$this->assertTrue($stream->isNext(self::T_OPEN_PARENTHESIS, 5));
		$this->assertTrue($stream->isNext(self::T_VALUE, 6));
		$this->assertTrue($stream->isNext(self::T_CLOSE_PARENTHESIS, 7));
		$this->assertFalse($stream->isNext(self::T_NAME, 8));
	}
	
	/**
	 * Data provider which returns a TokenStream instance.
	 * 
	 * @return array An array containing a TokenStream instance.
	 */
	public function tokenStreamProvider() {
		
		// user.name.split(" ").join("-")
		$stream = new TokenStream();
		$stream->push(new TestToken(self::T_NAME));
		$stream->push(new TestToken(self::T_POINT));
		$stream->push(new TestToken(self::T_NAME));
		$stream->push(new TestToken(self::T_POINT));
		$stream->push(new TestToken(self::T_NAME));
		$stream->push(new TestToken(self::T_OPEN_PARENTHESIS));
		$stream->push(new TestToken(self::T_VALUE));
		$stream->push(new TestToken(self::T_CLOSE_PARENTHESIS));
		$stream->push(new TestToken(self::T_POINT));
		$stream->push(new TestToken(self::T_NAME));
		$stream->push(new TestToken(self::T_OPEN_PARENTHESIS));
		$stream->push(new TestToken(self::T_VALUE));
		$stream->push(new TestToken(self::T_CLOSE_PARENTHESIS));
		$stream->rewind();
		
		return array(array($stream));
	}
}
