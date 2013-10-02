<?php
namespace Ttree\SortableNode\Aspect;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Ttree.SortableNode".         *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Aop\JoinPointInterface;
use TYPO3\TYPO3CR\Domain\Model\NodeData;

/**
 * An aspect to force sorting node in the TYPO3CR
 *
 * @package Ttree\SortableNode\Aspect
 * @Flow\Scope("singleton")
 * @Flow\Aspect
 */
class NodeSortingAspect {

	/**
	 * @Flow\Inject
	 * @var \Ttree\SortableNode\Service\NodeSortingService
	 */
	protected $nodeSortingService;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\TYPO3CR\Domain\Service\ContextFactory
	 */
	protected $contextFactory;

	/**
	 * @param JoinPointInterface $joinPoint
	 * @Flow\After("setting(Ttree.SortableNode.enabled) && method(TYPO3\TYPO3CR\Domain\Repository\NodeDataRepository->(update|add)())")
	 */
	public function sortNodesAfterUpdateOrCreate(JoinPointInterface $joinPoint) {
		/** @var NodeData $nodeData */
		$nodeData = $joinPoint->getMethodArgument('object');
		if (!$nodeData instanceof NodeData) {
			return;
		}
		$context = $this->contextFactory->create(array(
			'workspaceName' => $nodeData->getWorkspace()->getName(),
		));
		$parentNode = $context->getNode($nodeData->getParentPath()) ?: $context->getRootNode();
		$this->nodeSortingService->decideSortingByParentNode($parentNode);
	}
}

?>