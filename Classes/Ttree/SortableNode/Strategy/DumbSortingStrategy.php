<?php
namespace Ttree\SortableNode\Strategy;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Ttree.SortableNode".    *
 *                                                                        *
 *                                                                        */

use Ttree\SortableNode\Domain\Model\NodeSortingDefinition;
use Ttree\SortableNode\Domain\Model\PropertySortingDefinition;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Exception;
use TYPO3\Flow\Utility\Arrays;
use TYPO3\TYPO3CR\Domain\Model\NodeData;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;
use TYPO3\TYPO3CR\Domain\Repository\NodeDataRepository;

/**
 * Simple Insertion Sorting Strategy
 *
 * @package Ttree\Plugin\MicroEvent\Strategy
 */
class DumbSortingStrategy extends AbstractSortingStrategy {

	/**
	 * @param NodeInterface $parentNode
	 * @param NodeSortingDefinition $nodeSortingDefinition
	 * @throws \TYPO3\Flow\Exception
	 */
	public function sortByParentNodeAndNodeSortingDefinition(NodeInterface $parentNode, NodeSortingDefinition $nodeSortingDefinition) {
		if (count($nodeSortingDefinition->getProperties()) > 1) {
			throw new Exception(sprintf('DumbSortingStrategy support only sorting with one property, %d given in the current configuration', count($nodeSortingDefinition->getProperties())), 1380724384);
		}
		$nodes        = array();
		$hasChildNode = FALSE;
		$i            = 0;
		foreach ($parentNode->getChildNodes($nodeSortingDefinition->getNodeType()->getName()) as $node) {
			/** @var NodeInterface $node */
			$nodes[$i] = array(
				'data'       => $node->getNodeData(),
				'properties' => array(),
			);
			foreach ($nodeSortingDefinition->getProperties() as $sortingPropertyDefinition) {
				/** @var PropertySortingDefinition $sortingPropertyDefinition */
				$propertyName                           = $sortingPropertyDefinition->getName();
				$nodes[$i]['properties'][$propertyName] = $this->convertPropertyType($node->getProperty($propertyName), $sortingPropertyDefinition);
			}
			$hasChildNode = TRUE;
			$i++;
		}
		if ($hasChildNode === FALSE) {
			return;
		}
		usort($nodes, function ($a, $b) {
			$a = reset($a['properties']);
			$b = reset($b['properties']);

			if (is_string($a) && is_string($b)) {
				return mb_strtolower(trim($a)) > mb_strtolower(trim($b));
			} else {
				return $a > $b;
			}
		});

		$key = 0;
		$previousNodeData = NULL;
		foreach ($nodes as $node) {
			if ($key > 0) {
				/** @var NodeData $nodeData */
				$nodeData = $node['data'];
				$this->nodeDataRepository->setNewIndex($nodeData, NodeDataRepository::POSITION_AFTER, $previousNodeData);
			}
			/** @var NodeData $previousNodeData */
			$previousNodeData = $node['data'];

			$key++;
		}
	}
}

?>