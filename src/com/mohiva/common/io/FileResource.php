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
 * @package   Mohiva/Common/IO
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
namespace com\mohiva\common\io;

use SplFileInfo;
use Exception;
use com\mohiva\common\io\exceptions\ResourceNotFoundException;
use com\mohiva\common\io\exceptions\ReadException;
use com\mohiva\common\io\exceptions\LockException;
use com\mohiva\common\io\exceptions\WriteException;
use com\mohiva\common\io\exceptions\RemoveException;
use com\mohiva\common\io\events\ResourceStatisticsChangeEvent;

/**
 * Class which represents a file resource.
 *
 * @category  Mohiva/Common
 * @package   Mohiva/Common/IO
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class FileResource implements Resource {

	/**
	 * The type of this class.
	 *
	 * @var string
	 */
	const TYPE = __CLASS__;

	/**
	 * The resource descriptor.
	 *
	 * @var string
	 */
	const DESCRIPTOR = 'file:';

	/**
	 * The file info object associated with this resource.
	 *
	 * @var SplFileInfo
	 */
	private $fileInfo = null;

	/**
	 * The class constructor.
	 *
	 * @param SplFileInfo $fileInfo The file info object associated with the resource.
	 */
	public function __construct(SplFileInfo $fileInfo) {

		$this->fileInfo = $fileInfo;
	}

	/**
	 * Set the object handle for the resource.
	 *
	 * @param SplFileInfo $fileInfo The object handle of the resource.
	 */
	public function setHandle(SplFileInfo $fileInfo) {

		$this->fileInfo = $fileInfo;
	}

	/**
	 * Return the object handle of the resource or null if the resource isn't open.
	 *
	 * @return SplFileInfo The object handle of the resource or null if the resource isn't open.
	 */
	public function getHandle() {

		return $this->fileInfo;
	}

	/**
	 * Return the `ResourceStatistics` object for the resource.
	 *
	 * This resource supports the modification of the following statistical values:
	 * <ul>
	 *   <li>modification time</li>
	 *   <li>access time</li>
	 * </ul>
	 *
	 * @return ResourceStatistics An object containing statistics information about a resource.
	 */
	public function getStat() {

		clearstatcache();
		$stat = new ResourceStatistics();
		$stat->setAccessTime($this->fileInfo->getATime());
		$stat->setCreationTime($this->fileInfo->getCTime());
		$stat->setModificationTime($this->fileInfo->getMTime());
		$stat->setSize($this->fileInfo->getSize());

		// The event listener used to set the modification and access time
		$fileInfo = $this->fileInfo;
		$listener = function(ResourceStatisticsChangeEvent $event) use ($fileInfo) {
			/* @var ResourceStatistics $target */
			/* @var SplFileInfo $fileInfo */
			$target = $event->getTarget();
			touch($fileInfo->getPathname(), $target->getModificationTime(), $target->getAccessTime());
		};

		// Add event listeners
		$stat->addEventListener(ResourceStatisticsChangeEvent::ACCESS_TIME_CHANGED, $listener);
		$stat->addEventListener(ResourceStatisticsChangeEvent::MODIFICATION_TIME_CHANGED, $listener);

		return $stat;
	}

	/**
	 * Indicates if this resource is a file and if its readable or not.
	 *
	 * @return boolean True if the resource is a file and its readable, false otherwise.
	 */
	public function exists() {

		if ($this->fileInfo->isFile() && $this->fileInfo->isReadable()) {
			return true;
		}

		return false;
	}

	/**
	 * Read the content of the resource and return it.
	 *
	 * @return string The content of the resource.
	 * @throws ResourceNotFoundException if the resource can't be opened.
	 * @throws ReadException if cannot be read from resource.
	 */
	public function read() {

		try {
			$file = $this->fileInfo->openFile('r');
		} catch (Exception $e) {
			throw new ResourceNotFoundException("Cannot open resource: {$this->fileInfo->getPathname()}", 0, $e);
		}

		$content = '';
		try {
			while ($file->valid()) {
				$content .= $file->fgets();
			}
		} catch (Exception $e) {
			throw new ReadException("Cannot read from resource: {$this->fileInfo->getPathname()}", 0, $e);
		}

		return $content;
	}

	/**
	 * Write the given data to the resource.
	 *
	 * Attention! If the resource already exists then this method
	 * will overwrite it with the given data.
	 *
	 * @param string $data The data to write.
	 * @throws ResourceNotFoundException if the resource isn't opened.
	 * @throws WriteException if cannot be written to the resource.
	 */
	public function write($data) {

		// Open the resource in write mode
		try {
			$file = $this->fileInfo->openFile('w');
		} catch (Exception $e) {
			throw new ResourceNotFoundException("Cannot open resource: {$this->fileInfo->getPathname()}", 0, $e);
		}

		// Try to get an exclusive non blocked lock for the resource
		$lock = $file->flock(LOCK_EX | LOCK_NB);
		if (!$lock) {
			throw new LockException("Cannot get the lock for the resource: {$this->fileInfo->getPathname()}");
		}

		// Write the data to the file and release the lock
		$written = $file->fwrite($data);
		$file->flock(LOCK_UN);
		if (!$written) {
			throw new WriteException("Cannot write to the resource: {$this->fileInfo->getPathname()}");
		}
	}

	/**
	 * Remove the resource.
	 *
	 * @param bool $checkExistence True if should be checked if the resource exists before deleting it, false otherwise.
	 * @return bool True if the resource was removed, false if the resource doesn't exists.
	 * @throws RemoveException if the resource cannot be removed.
	 */
	public function remove($checkExistence = true) {

		if ($checkExistence && !$this->exists()) {
			return false;
		}

		try {
			unlink($this->fileInfo->getPathname());
		} catch (Exception $e) {
			throw new RemoveException("Cannot remove the resource: {$this->fileInfo->getPathname()}", 0, $e);
		}

		return true;
	}
}
