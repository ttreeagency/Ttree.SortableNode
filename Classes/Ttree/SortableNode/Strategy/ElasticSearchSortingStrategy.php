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
 * Simple Insertion Sorting Strategy
 *
 * @package Ttree\Plugin\MicroEvent\Strategy
 */
class ElasticSearchSortingStrategy extends AbstractSortingStrategy {

	/**
	 * @param NodeInterface $parentNode
	 * @param NodeSortingDefinition $nodeSortingDefinition
	 * @return mixed
	 */
	public function sortByParentNodeAndNodeSortingDefinition(NodeInterface $parentNode, NodeSortingDefinition $nodeSortingDefinition) {
		// TODO: Implement sortByParentNodeAndNodeType() method.
	}

}

?>