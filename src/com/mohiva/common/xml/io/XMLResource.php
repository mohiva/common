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
 * @package   Mohiva/Common/XML/IO
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
namespace com\mohiva\common\xml\io;

use Exception;
use SplFileInfo;
use com\mohiva\common\io\Resource;
use com\mohiva\common\io\ResourceStatistics;
use com\mohiva\common\io\exceptions\ReadException;
use com\mohiva\common\io\exceptions\WriteException;
use com\mohiva\common\io\exceptions\RemoveException;
use com\mohiva\common\io\events\ResourceStatisticsChangeEvent;
use com\mohiva\common\xml\XMLDocument;

/**
 * Class which represents a XML document resource.
 *
 * @category  Mohiva/Common
 * @package   Mohiva/Common/XML/IO
 * @author    Christian Kaps <christian.kaps@mohiva.com>
 * @copyright Copyright (c) 2007-2012 Christian Kaps (http://www.mohiva.com)
 * @license   https://github.com/mohiva/common/blob/master/LICENSE.textile New BSD License
 * @link      https://github.com/mohiva/common
 */
class XMLResource implements Resource {

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
	const DESCRIPTOR = 'xml:';

	/**
	 * The file info object associated with this resource.
	 *
	 * @var SplFileInfo
	 */
	private $fileInfo = null;

	/**
	 * The XMLDocument object associated with this resource.
	 *
	 * @var XMLDocument
	 */
	private $document = null;

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
	 * @param XMLDocument $document The object handle of the resource.
	 */
	public function setHandle(XMLDocument $document) {

		$this->document = $document;
	}

	/**
	 * Return the object handle of the resource.
	 *
	 * @return XMLDocument The object handle of the resource.
	 */
	public function getHandle() {

		if ($this->document === null) {
			$this->document = new XMLDocument();
		}

		return $this->document;
	}

	/**
	 * Return the `ResourceStatistics` object for the resource.
	 *
	 * This resource supports the modification of the following statistical values:
	 * <ul>
	 *	 <li>modification time</li>
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
	 * @return XMLDocument The root node of the XML document.
	 * @throws ReadException if cannot be read from resource.
	 */
	public function read() {

		$document = $this->getHandle();
		try {
			$document->load($this->fileInfo->getPathname());
		} catch(Exception $e) {
			throw new ReadException("Cannot read from resource: {$this->fileInfo->getPathname()}", 0, $e);
		}

		return $document;
	}

	/**
	 * Write the given data to the resource.
	 *
	 * Attention! If the resource already exists then this method
	 * will overwrite it with the given data.
	 *
	 * @param string $data The data to write.
	 * @throws WriteException if cannot be written to the resource.
	 */
	public function write($data) {

		$document = $this->getHandle();
		try {
			$document->loadXML($data);
			$document->save($this->fileInfo->getPathname());
		} catch (Exception $e) {
			throw new WriteException("Cannot write to the resource: {$this->fileInfo->getPathname()}", 0, $e);
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
