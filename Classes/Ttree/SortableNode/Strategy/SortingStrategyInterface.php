<?php
namespace Ttree\SortableNode\Strategy;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Ttree.SortableNode".    *
 *                                                                        *
 *                                                                        */

use Ttree\SortableNode\Domain\Model\NodeSortingDefinition;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;

/**
 * Node sorting strategy interface
 *
 * @package Ttree\Plugin\MicroEvent\Strategy
 */
interface SortingStrategyInterface {

	/**
	 * @param NodeInterface $parentNode
	 * @param NodeSortingDefinition $nodeSortingDefinition
	 * @return mixed
	 */
	public function sortByParentNodeAndNodeSortingDefinition(NodeInterface $parentNode, NodeSortingDefinition $nodeSortingDefinition);

}

?>