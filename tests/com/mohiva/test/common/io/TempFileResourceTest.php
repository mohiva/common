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
namespace com\mohiva\test\common\io;

use SplFileInfo;
use com\mohiva\common\io\TempFileResource;

/**
 * Unit test case for the `TempFileResource` class.
 *
 * @category  Mohiva/Common
 * @package   Mohiva/Common/Test
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class TempFileResourceTest extends \PHPUnit_Framework_TestCase {

	/**
	 * The test fixture.
	 *
	 * @var \SplFileInfo
	 */
	private $fileInfo = null;

	/**
	 * The test fixture.
	 *
	 * @var \com\mohiva\common\io\TempFileResource
	 */
	private $resource = null;

	/**
	 * Setup the test case.
	 */
	public function setUp() {

		$this->fileInfo = new SplFileInfo('php://temp');
		$this->resource = new TempFileResource($this->fileInfo);
	}

	/**
	 * Test if the `__construct` method throws an exception if the path doesn't start
	 * with php://temp or php://memory.
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testThrowsExceptionOnWrongPath() {

		$fileInfo = new SplFileInfo('/path/to/file');
		new TempFileResource($fileInfo);
	}

	/**
	 * Test the `setHandle` and `getHandle()` accessors.
	 */
	public function testHandleAccessors() {

		$this->assertSame($this->fileInfo, $this->resource->getHandle());

		$stub = $this->getMock('SplFileInfo', array(), array('php://temp')); /* @var \SplFileInfo $stub */
		$this->resource->setHandle($stub);

		$this->assertSame($stub, $this->resource->getHandle());
	}

	/**
	 * Test if the `getStat` method returns an empty `ResourceStatistics`
	 * object if the file doesn't exists.
	 */
	public function testGetStatReturnEmptyStatisticsOnNonExistingResource() {

		$stat = $this->resource->getStat();

		$this->assertInstanceOf('com\mohiva\common\io\ResourceStatistics', $stat);
		$this->assertNull($stat->getAccessTime());
		$this->assertNull($stat->getCreationTime());
		$this->assertNull($stat->getModificationTime());
		$this->assertNull($stat->getSize());
	}

	/**
	 * Test if the `getStat` method returns a non empty `ResourceStatistics`
	 * object if the file exists.
	 */
	public function testGetStatReturnStatisticsOnExistingResource() {

		$this->resource->write('A string');
		$stat = $this->resource->getStat();

		$this->assertInstanceOf('com\mohiva\common\io\ResourceStatistics', $stat);
		$this->assertInternalType('int', $stat->getAccessTime());
		$this->assertInternalType('int', $stat->getCreationTime());
		$this->assertInternalType('int', $stat->getModificationTime());
		$this->assertInternalType('int', $stat->getSize());
	}

	/**
	 * Test if can set the creation time of a file resource.
	 */
	public function testSetCreationTime() {

		$this->resource->write('A string');
		$stat = $this->resource->getStat();

		$this->assertNotSame(1, $stat->getCreationTime());

		$stat->setCreationTime(1);

		$this->assertSame(1, $this->resource->getStat()->getCreationTime());
	}

	/**
	 * Test if can set the modification time of a file resource.
	 */
	public function testSetModificationTime() {

		$this->resource->write('A string');
		$stat = $this->resource->getStat();

		$this->assertNotSame(1, $stat->getModificationTime());

		$stat->setModificationTime(1);

		$this->assertSame(1, $this->resource->getStat()->getModificationTime());
	}

	/**
	 * Test if can set the access time of a file resource.
	 */
	public function testSetAccessTime() {

		$this->resource->write('A string');
		$stat = $this->resource->getStat();

		$this->assertNotSame(1, $stat->getAccessTime());

		$stat->setAccessTime(1);

		$this->assertSame(1, $this->resource->getStat()->getAccessTime());
	}

	/**
	 * Test if the `exists` method return false if the resource doesn't exists.
	 */
	public function testExistsReturnFalseOnNonExistingFile() {

		$this->assertFalse($this->resource->exists());
	}

	/**
	 * Test if the `exists` method return true if the resource does exists.
	 */
	public function testExistsReturnTrueOnExistingFile() {

		$this->resource->write('A string');

		$this->assertTrue($this->resource->exists());
	}

	/**
	 * Test if the `read` method returns an empty string if the resource
	 * doesn't exists.
	 */
	public function testReadReturnEmptyStringOnNonExistingResource() {

		$this->assertSame('', $this->resource->read());
	}

	/**
	 * Test if the `read` method throws an exception if the
	 * process cannot read from file.
	 *
	 * @expectedException \com\mohiva\common\io\exceptions\ReadException
	 */
	public function testReadThrowsReadException() {

		$this->fileInfo->setFileClass('com\mohiva\test\resources\common\io\SplFileObjectReadMock');
		$this->resource->write('A string');
		$this->resource->read();
	}

	/**
	 * Test if the `read` method returns the file content.
	 */
	public function testReadReturnsFileContent() {

		$content = file_get_contents(__FILE__);
		$this->resource->write($content);

		$this->assertSame(sha1($content), sha1($this->resource->read()));
	}

	/**
	 * Test if the `write` method throws a exception if the
	 * process cannot write to the file.
	 *
	 * @expectedException \com\mohiva\common\io\exceptions\WriteException
	 */
	public function testWriteThrowsWriteException() {

		$this->fileInfo->setFileClass('com\mohiva\test\resources\common\io\SplFileObjectWriteMock');
		$this->resource->write('A string');
	}

	/**
	 * Test if the write method creates a new file.
	 */
	public function testWriteCreatesFile() {

		$this->assertFalse($this->resource->exists());

		$this->resource->write('A string');

		$this->assertSame('A string', $this->resource->read());

		// Check if a second call overwrites the content
		$this->resource->write('A other string');

		$this->assertSame('A other string', $this->resource->read());
	}

	/**
	 * Test if the `remove` return false if the resource doesn't exists.
	 */
	public function testRemoveReturnFalseOnNonExistingResource() {

		$this->assertFalse($this->resource->remove());
	}

	/**
	 * Test if the `remove` return true if the resource was removed.
	 */
	public function testRemoveReturnTrueIfTheResourceWasRemoved() {

		$this->resource->write('A string');

		$this->assertTrue($this->resource->remove());
	}

	/**
	 * Test if the remove object unset the global `SplFileObject` object.
	 */
	public function testRemoveUnsetGlobalFileObject() {

		$this->resource->write('A string');
		$this->resource->remove();

		$this->assertFalse($this->resource->exists());
	}
}
