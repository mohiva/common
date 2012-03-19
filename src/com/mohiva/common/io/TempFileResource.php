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
use SplFileObject;
use InvalidArgumentException;
use com\mohiva\common\util\DefaultEventDispatcher;
use com\mohiva\common\io\exceptions\ReadException;
use com\mohiva\common\io\exceptions\WriteException;
use com\mohiva\common\io\events\ResourceStatisticsChangeEvent;

/**
 * Class which represents a temporary file resource.
 *
 * @category  Mohiva/Common
 * @package   Mohiva/Common/IO
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class TempFileResource implements Resource {

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
	const DESCRIPTOR = 'temp:';

	/**
	 * The file info object associated with this resource.
	 *
	 * @var \SplFileInfo
	 */
	private $fileInfo = null;

	/**
	 * The file object associated with this resource.
	 *
	 * @var \SplFileObject
	 */
	private $fileObject = null;

	/**
	 * The creation time of a resource.
	 *
	 * @var int
	 */
	private $creationTime = null;

	/**
	 * The last access time of a resource.
	 *
	 * @var int
	 */
	private $accessTime = null;

	/**
	 * The modification time of a resource.
	 *
	 * @var int
	 */
	private $modificationTime = null;

	/**
	 * The size of the resource in bytes.
	 *
	 * @var int
	 */
	private $size = null;

	/**
	 * The class constructor.
	 *
	 * @param \SplFileInfo $fileInfo The file info object associated with the resource.
	 */
	public function __construct(SplFileInfo $fileInfo) {

		$this->fileInfo = $fileInfo;
		if (!preg_match('/^php:\/\/[temp|memory]+/', $fileInfo->getPathname())) {
			throw new InvalidArgumentException(
				'The path of a temporary file must start with php://temp or php://memory'
			);
		}
	}

	/**
	 * Set the object handle for the resource.
	 *
	 * @param \SplFileInfo $fileInfo The object handle of the resource.
	 */
	public function setHandle(SplFileInfo $fileInfo) {

		$this->fileInfo = $fileInfo;
	}

	/**
	 * Return the object handle of the resource or null if the resource isn't open.
	 *
	 * @return \SplFileInfo The object handle of the resource or null if the resource isn't open.
	 */
	public function getHandle() {

		return $this->fileInfo;
	}

	/**
	 * Return the `ResourceStatistics` object for the resource.
	 *
	 * This resource supports the modification of the following statistical values:
	 * <ul>
	 *	 <li>creation time</li>
	 *	 <li>modification time</li>
	 *   <li>access time</li>
	 * </ul>
	 *
	 * @return \com\mohiva\common\io\ResourceStatistics An object containing statistics information
	 * about a resource.
	 */
	public function getStat() {

		$stat = new ResourceStatistics();
		$stat->setCreationTime($this->creationTime);
		$stat->setAccessTime($this->accessTime);
		$stat->setModificationTime($this->modificationTime);
		$stat->setSize($this->size);

		$listener = array($this, 'resourceStatisticsHandler');
		$stat->addEventListener(ResourceStatisticsChangeEvent::CREATION_TIME_CHANGED, $listener);
		$stat->addEventListener(ResourceStatisticsChangeEvent::ACCESS_TIME_CHANGED, $listener);
		$stat->addEventListener(ResourceStatisticsChangeEvent::MODIFICATION_TIME_CHANGED, $listener);

		return $stat;
	}

	/**
	 * Handles the `ResourceStatisticsChangeEvent` event.
	 *
	 * @param \com\mohiva\common\io\events\ResourceStatisticsChangeEvent $event The associated event.
	 */
	public function resourceStatisticsHandler(ResourceStatisticsChangeEvent $event) {

		/* @var \com\mohiva\common\io\TempFileResource $self */
		/* @var \com\mohiva\common\io\ResourceStatistics $target */
		$target = $event->getTarget();
		switch ($event->getType()) {
			case ResourceStatisticsChangeEvent::CREATION_TIME_CHANGED:
				$this->creationTime = $target->getCreationTime();
				break;

			case ResourceStatisticsChangeEvent::ACCESS_TIME_CHANGED:
				$this->accessTime = $target->getAccessTime();
				break;

			case ResourceStatisticsChangeEvent::MODIFICATION_TIME_CHANGED:
				$this->modificationTime = $target->getModificationTime();
				break;
		}
	}

	/**
	 * Indicates if the file exists or not.
	 *
	 * A temporary file exists if it was created with the write method.
	 *
	 * @return boolean True if the temporary file exists, false otherwise.
	 */
	public function exists() {

		return $this->fileObject instanceof \SplFileObject;
	}

	/**
	 * Read the content of the resource and return it.
	 *
	 * @return string The content of the resource.
	 * @throws ReadException if cannot be read from resource.
	 */
	public function read() {

		$content = '';
		if (!$this->exists()) {
			return $content;
		}

		try {
			$this->fileObject->rewind();
			while ($this->fileObject->valid()) {
				$content .= $this->fileObject->fgets();
			}
			$this->accessTime = time();
		} catch (\Exception $e) {
			throw new ReadException("Cannot read from resource: {$this->fileInfo->getPathname()}", null, $e);
		}

		return $content;
	}

	/**
	 * Write the given data to the resource.
	 *
	 * Attention! If the resource already exists, then this method
	 * will overwrite it with the given data.
	 *
	 * @param string $data The data to write.
	 * @throws WriteException if cannot be written to the resource.
	 */
	public function write($data) {

		if (!$this->exists()) {
			$this->fileObject = $this->fileInfo->openFile('w+');
			$this->creationTime = time();
			$this->accessTime = time();
		}

		$this->fileObject->ftruncate(0);
		$this->size = $this->fileObject->fwrite($data);
		$this->modificationTime = time();
		if (!$this->size) {
			throw new WriteException("Cannot write to the resource: {$this->fileInfo->getPathname()}");
		}
	}

	/**
	 * Temporary files can't be removed because the PHP process removes these
	 * files automatically if all references are lost or the script ends. This
	 * method removes only the reference to the current opened handle.
	 *
	 * @param bool $checkExistence True if should be checked if the resource exists before deleting it, false otherwise.
	 * @return bool True if the resource was removed, false if the resource doesn't exists.
	 */
	public function remove($checkExistence = true) {

		if ($checkExistence && !$this->exists()) {
			return false;
		}

		$this->fileObject = null;
		$this->creationTime = null;
		$this->accessTime = null;
		$this->modificationTime = null;
		$this->size = null;

		return true;
	}
}
