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
namespace com\mohiva\test\common\io;

use SplFileInfo;
use com\mohiva\common\io\FileResource;

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
class FileResourceTest extends \PHPUnit_Framework_TestCase {
	
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
		
		$path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'mohiva-FileResourceTest-' . microtime(true);
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
	 * Test the `setHandle` and `getHandle` accessors.
	 */
	public function testHandleAccessors() {
		
		$resource = new FileResource($this->fileInfo);
		$this->assertSame($this->fileInfo, $resource->getHandle());
		
		$stub = $this->getMock('SplFileInfo', array(), array('dummy')); /* @var \SplFileInfo $stub */
		$resource->setHandle($stub);
		$this->assertSame($stub, $resource->getHandle());
	}
	
	/**
	 * Test the `getStat` method.
	 */
	public function testGetStat() {
		
		$this->fileInfo->openFile('w+');
		$resource = new FileResource($this->fileInfo);
		$stat = $resource->getStat();
		$this->assertInstanceOf('com\mohiva\common\io\ResourceStatistics', $stat);
	}
	
	/**
	 * Test if can set the modification time of a file resource.
	 */
	public function testSetModificationTime() {
		
		$this->fileInfo->openFile('w+');
		$resource = new FileResource($this->fileInfo);
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
		$resource = new FileResource($this->fileInfo);
		$stat = $resource->getStat();
		$stat->setAccessTime(1);
		clearstatcache();
		$this->assertSame(1, fileatime($this->fileInfo->getPathname()));
	}
	
	/**
	 * Test if the `exists` method return false if the resource doesn't exists.
	 */
	public function testExistsReturnFalseOnNonExistingFile() {
		
		$resource = new FileResource($this->fileInfo);
		
		$this->assertFalse($resource->exists());
	}
	
	/**
	 * Test if the `exists` method return false if the resource is a directory.
	 */
	public function testExistsReturnFalseOnDirectory() {
		
		$resource = new FileResource(new SplFileInfo(__DIR__));
		
		$this->assertFalse($resource->exists());
	}
	
	/**
	 * Test if the `exists` method return true if the resource does exists.
	 */
	public function testExistsReturnTrueOnExistingFile() {
		
		$this->fileInfo->openFile('w+');
		$resource = new FileResource($this->fileInfo);
		
		$this->assertTrue($resource->exists());
	}
	
	/**
	 * Test if the `read` method throws an exception if the resource doesn't exists.
	 * 
	 * @expectedException \com\mohiva\common\io\exceptions\ResourceNotFoundException
	 */
	public function testReadThrowsResourceNotFoundException() {
		
		$stub = $this->getMock('SplFileInfo', array(), array('dummy')); /* @var \SplFileInfo $stub */
		$stub->expects($this->any())
             ->method('openFile')
             ->will($this->throwException(new \Exception));
		
		$resource = new FileResource($stub);
		$resource->read();
	}
	
	/**
	 * Test if the `read` method throws an exception if the 
	 * process cannot read from file.
	 * 
	 * @expectedException \com\mohiva\common\io\exceptions\ReadException
	 */
	public function testReadThrowsReadException() {
		
		$this->fileInfo->openFile('w+');
		$this->fileInfo->setFileClass('com\mohiva\test\resources\common\io\SplFileObjectReadMock');
		$resource = new FileResource($this->fileInfo);
		$resource->read();
	}
	
	/**
	 * Test if the `read` method returns the file content.
	 */
	public function testReadReturnsFileContent() {
		
		$content = file_get_contents(__FILE__);				
		$temp = $this->fileInfo->openFile('w+');
		$temp->fwrite($content);
		
		$resource = new FileResource($this->fileInfo);
		
		$this->assertSame(sha1($content), sha1($resource->read()));
	}
	
	/**
	 * Test if the `write` method throws an exception if the resource doesn't exists.
	 * 
	 * @expectedException \com\mohiva\common\io\exceptions\ResourceNotFoundException
	 */
	public function testWriteThrowsResourceNotFoundException() {
		
		$stub = $this->getMock('SplFileInfo', array(), array('dummy')); /* @var \SplFileInfo $stub */
		$stub->expects($this->any())
             ->method('openFile')
             ->will($this->throwException(new \Exception));
		
		$resource = new FileResource($stub);
		$resource->write('A string');
	}
	
	/**
	 * Test if the `write` method throws an exception if the 
	 * process cannot get the lock for the file.
	 * 
	 * @expectedException \com\mohiva\common\io\exceptions\LockException
	 */
	public function testWriteThrowsLockException() {
		
		$temp = $this->fileInfo->openFile('w+');
		$resource = new FileResource($this->fileInfo);
		$temp->flock(LOCK_EX);
		$resource->write('A string');
	}
	
	/**
	 * Test if the `write` method throws an exception if the 
	 * process cannot write to the file.
	 * 
	 * @expectedException \com\mohiva\common\io\exceptions\WriteException
	 */
	public function testWriteThrowsWriteException() {
		
		$this->fileInfo->openFile('w+');
		$this->fileInfo->setFileClass('com\mohiva\test\resources\common\io\SplFileObjectWriteMock');
		$resource = new FileResource($this->fileInfo);
		$resource->write('A string');
	}
	
	/**
	 * Test if the write method creates a new file.
	 */
	public function testWriteCreatesFile() {
		
		$this->assertFalse(file_exists($this->fileInfo->getPathname()));
		
		$resource = new FileResource($this->fileInfo);
		$resource->write('A string');
		
		$this->assertSame('A string', file_get_contents($this->fileInfo->getPathname()));
	}
	
	/**
	 * Test if the `remove` return false if the resource doesn't exists.
	 */
	public function testRemoveReturnFalseOnNonExistingResource() {
		
		$resource = new FileResource($this->fileInfo);
		
		$this->assertFalse($resource->remove());
	}
	
	/**
	 * Test if the `remove` return true if the resource was removed.
	 */
	public function testRemoveReturnTrueIfTheResourceWasRemoved() {
		
		$this->fileInfo->openFile('w+');
		$resource = new FileResource($this->fileInfo);
		
		$this->assertTrue($resource->remove());
	}
	
	/**
	 * Test if the `remove` method throws an exception if
	 * the process cannot remove the resource.
	 * 
	 * @expectedException \com\mohiva\common\io\exceptions\RemoveException
	 */
	public function testRemoveThrowsRemoveException() {
		
		$resource = new FileResource($this->fileInfo);
		$resource->remove(false);
	}
}
