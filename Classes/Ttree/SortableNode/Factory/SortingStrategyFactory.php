<?php
namespace Ttree\SortableNode\Factory;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Ttree.SortableNode".         *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Exception;
use TYPO3\Flow\Object\ObjectManager;

/**
 * Sorting Strategy Factory
 *
 * @Flow\Scope("singleton")
 */
class SortingStrategyFactory {

	/**
	 * @Flow\Inject
	 * @var ObjectManager
	 */
	protected $objectManager;

	/**
	 * @param string $type
	 * @return \Ttree\SortableNode\Strategy\SortingStrategyInterface
	 * @throws \TYPO3\Flow\Exception
	 */
	public function create($type) {
		$type = trim($type);
		if ($type === '') {
			throw new Exception('Type of Sorting Strategy can not be empty', 1380716315);
		}
		if (strpos(trim($type), '\\') !== FALSE) {
			$objectName = $type;
		} else {
			$objectName = sprintf('\Ttree\SortableNode\Strategy\%s', trim($type));
		}
		if (!class_exists($objectName)) {
			throw new Exception(sprintf('Sorting strategy class (%s) not found', $objectName), 1380716380);
		}

		return $this->objectManager->get($objectName);
	}

}

?>