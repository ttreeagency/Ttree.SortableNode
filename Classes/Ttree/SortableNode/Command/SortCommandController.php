<?php
namespace Ttree\SortableNode\Command;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Ttree.Plugin.MicroEvent".*
 *                                                                        *
 *                                                                        */

use Ttree\SortableNode\Service\NodeSortingService;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Neos\Domain\Model\Site;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;

/**
 * Sorting CLI utilities
 */
class SortCommandController extends \TYPO3\Flow\Cli\CommandController {

	/**
	 * @Flow\Inject
	 * @var NodeSortingService
	 */
	protected $nodeSortingService;

	/**
	 * @param NodeInterface $path
	 */
	public function pathCommand(NodeInterface $path) {
		$this->nodeSortingService->decideSortingByParentNode($path);
	}
}

?>