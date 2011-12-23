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
namespace com\mohiva\test\common\xml\io;

use SplFileInfo;
use com\mohiva\common\xml\io\XMLResource;

/**
 * Unit test case for the `FileResource` class.
 * 
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Test
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2011 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class XMLResourceTest extends \PHPUnit_Framework_TestCase {
	
	/**
	 * The test fixture.
	 * 
	 * @var \SplFileInfo
	 */
	private $fileInfo = null;
	
	/**
	 * Setup the test case.
	 */
	public function setUp() {
		
		$path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'mohiva-XMLResourceTest-' . microtime(true);
		$this->fileInfo = new SplFileInfo($path);
		
		if ($this->fileInfo->isFile()) unlink($this->fileInfo->getPathname());
	}
	
	/**
	 * Tear down the test case.
	 */
	public function tearDown() {
		
		if ($this->fileInfo->isFile()) unlink($this->fileInfo->getPathname());
	}
	
	/**
	 * Test the `setHandle` and `getHandle()` accessors.
	 */
	public function testHandleAccessors() {
		
		$resource = new XMLResource($this->fileInfo);
		$this->assertInstanceOf('com\mohiva\common\xml\XMLDocument', $resource->getHandle());
		
		$stub = $this->getMock('com\mohiva\common\xml\XMLDocument');
		/* @var com\mohiva\common\xml\XMLDocument $stub */
		$resource->setHandle($stub);
		$this->assertSame($stub, $resource->getHandle());
	}
	
	/**
	 * Test the `getStat` method.
	 */
	public function testGetStat() {
		
		$this->fileInfo->openFile('w+');
		$resource = new XMLResource($this->fileInfo);
		$stat = $resource->getStat();
		$this->assertInstanceOf('com\mohiva\common\io\ResourceStatistics', $stat);
	}
	
	/**
	 * Test if can set the modification time of a file resource.
	 */
	public function testSetModificationTime() {
		
		$this->fileInfo->openFile('w+');
		$resource = new XMLResource($this->fileInfo);
		$stat = $resource->getStat();
		$stat->setModificationTime(1);
		clearstatcache();
		$this->assertSame(1, filemtime($this->fileInfo->getPathname()));
	}
	
	/**
	 * Test if can set the access time of a file resource.
	 */
	public function testSetAccessTime() {
		
		$this->fileInfo->openFile('w+');
		$resource = new XMLResource($this->fileInfo);
		$stat = $resource->getStat();
		$stat->setAccessTime(1);
		clearstatcache();
		$this->assertSame(1, fileatime($this->fileInfo->getPathname()));
	}
	
	/**
	 * Test if the `exists` method return false if the resource doesn't exists.
	 */
	public function testExistsReturnFalseOnNonExistingFile() {
		
		$resource = new XMLResource($this->fileInfo);
		
		$this->assertFalse($resource->exists());
	}
	
	/**
	 * Test if the `exists` method return false if the resource is a directory.
	 */
	public function testExistsReturnFalseOnDirectory() {
		
		$resource = new XMLResource(new SplFileInfo(__DIR__));
		
		$this->assertFalse($resource->exists());
	}
	
	/**
	 * Test if the `exists` method return true if the resource does exists.
	 */
	public function testExistsReturnTrueOnExistingFile() {
		
		$this->fileInfo->openFile('w+');
		$resource = new XMLResource($this->fileInfo);
		
		$this->assertTrue($resource->exists());
	}
	
	/**
	 * Test if the `read` method throws an exception if the resource can't be read.
	 * 
	 * @expectedException \com\mohiva\common\io\exceptions\ReadException
	 */
	public function testReadThrowsReadException() {
		
		$resource = new XMLResource($this->fileInfo);
		$resource->read();
	}
	
	/**
	 * Test if the `read` method returns a `XMLDocument` object.
	 */
	public function testReadReturnsXMLElement() {
		
		$string = '<root>A string</root>';
		
		$file = $this->fileInfo->openFile('w+');
		$file->fwrite($string);
		$resource = new XMLResource($this->fileInfo);
		$document = $resource->read();
		
		$this->assertInstanceOf('com\mohiva\common\xml\XMLDocument', $document);
		$this->assertSame($string, $document->saveXML($document->documentElement));
	}
	
	/**
	 * Test if the `write` method throws an exception if the 
	 * process cannot write to the file.
	 * 
	 * @expectedException \com\mohiva\common\io\exceptions\WriteException
	 */
	public function testWriteThrowsWriteException() {
		
		$resource = new XMLResource($this->fileInfo);
		$resource->write('A string');
	}
	
	/**
	 * Test if the write method creates a new file.
	 */
	public function testWriteCreatesFile() {
		
		$string = '<root>A string</root>';
		$resource = new XMLResource($this->fileInfo);
		$resource->write($string);
		
		$resource = new XMLResource($this->fileInfo);
		$document = $resource->read();
		
		$this->assertEquals($string, $document->saveXML($document->documentElement));
	}
	
	/**
	 * Test if the `remove` return false if the resource doesn't exists.
	 */
	public function testRemoveReturnFalseOnNonExistingResource() {
		
		$resource = new XMLResource($this->fileInfo);
		
		$this->assertFalse($resource->remove());
	}
	
	/**
	 * Test if the `remove` return true if the resource was removed.
	 */
	public function testRemoveReturnTrueIfTheResourceWasRemoved() {
		
		$this->fileInfo->openFile('w+');
		$resource = new XMLResource($this->fileInfo);
		
		$this->assertTrue($resource->remove());
	}
	
	/**
	 * Test if the `remove` method throws an exception if
	 * the process cannot remove the resource.
	 * 
	 * @expectedException \com\mohiva\common\io\exceptions\RemoveException
	 */
	public function testRemoveThrowsRemoveException() {
		
		$resource = new XMLResource($this->fileInfo);
		$resource->remove(false);
	}
}
