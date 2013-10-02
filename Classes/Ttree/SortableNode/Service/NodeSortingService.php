<?php
namespace Ttree\SortableNode\Service;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Ttree.SortableNode".         *
 *                                                                        *
 *                                                                        */

use Ttree\SortableNode\Domain\Model\NodeSortingDefinition;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Exception;
use TYPO3\Flow\Utility\Arrays;
use TYPO3\Flow\Utility\PositionalArraySorter;
use TYPO3\TYPO3CR\Domain\Model\Node;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;
use TYPO3\TYPO3CR\Domain\Repository\NodeDataRepository;

/**
 * Node Sorting Service
 *
 * @package Ttree\Plugin\MicroEvent\Service
 * @Flow\Scope("singleton")
 */
class NodeSortingService {

	/**
	 * @Flow\Inject
	 * @var NodeDataRepository
	 */
	protected $nodeDataRepository;

	/**
	 * @Flow\Inject
	 * @var SettingsService
	 */
	protected $settingsService;

	/**
	 * @param NodeInterface $parentNode
	 * @throws \TYPO3\Flow\Exception
	 */
	public function decideSortingByParentNode(NodeInterface $parentNode) {
		$nodeSortingDefinitions = $this->getNodeSortingDefinitionsByParentNode($parentNode);

		foreach ($nodeSortingDefinitions as $nodeSortingDefinition) {
			/** @var NodeSortingDefinition $nodeSortingDefinition */
			$strategy = $nodeSortingDefinition->getStrategy();
			$strategy->sortByParentNodeAndNodeSortingDefinition($parentNode, $nodeSortingDefinition);
		}
	}

	/**
	 * @param Node $parentNode
	 * @return array
	 */
	public function getNodeSortingDefinitionsByParentNode(Node $parentNode) {
		$nodeSortingDefinitions = array();
		foreach ($this->settingsService->getByPath('configuration') as $nodeTypeName => $configuration) {
			$configuration = new PositionalArraySorter($configuration);
			foreach ($configuration->toArray() as $pathConfiguration) {
				$nodeSortingDefinition = new NodeSortingDefinition($nodeTypeName, $pathConfiguration);
				if ($nodeSortingDefinition->matchesNode($parentNode) && $nodeSortingDefinition->isEnabled()) {
					$nodeSortingDefinitions[$nodeSortingDefinition->getNodeType()->getName()] = $nodeSortingDefinition;
				}
			}
		}
		return $nodeSortingDefinitions;
	}

}

?>