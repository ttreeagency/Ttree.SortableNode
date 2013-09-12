<?php
namespace Ttree\Starter\Service;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Ttree.Starter".         *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Exception;
use TYPO3\Flow\Utility\Arrays;
use TYPO3\Flow\Property\PropertyMappingConfigurationInterface;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;

/**
 * A service to sort node stored in the content repository
 *
 * @package Ttree\Plugin\MicroEvent\Service
 * @Flow\Scope("singleton")
 */
class NodeSortingService {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Property\PropertyMapper
	 */
	protected $propertyMapper;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\TYPO3CR\Domain\Repository\NodeDataRepository
	 */
	protected $nodeDataRepository;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Property\PropertyMappingConfigurationBuilder
	 */
	protected $propertyMappingConfigurationBuilder;

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * @param array $settings
	 */
	public function injectSettings(array $settings) {
		$this->settings = $settings;
	}

	/**
	 * @param NodeInterface $parentNode
	 * @throws \TYPO3\Flow\Exception
	 */
	public function decideSortingByParentNode(NodeInterface $parentNode) {
		$configurationPath = 'nodeSorting.configuration.' . $parentNode->getPath();
		if (NULL === $configuration = Arrays::getValueByPath($this->settings, $configurationPath)) {
			return;
		}

		foreach ($configuration as $subConfiguration) {
			if (!isset($subConfiguration['type']) || trim($subConfiguration['type']) === '') {
				throw new Exception('Missing sortable node type setting', 1378377366);
			}

			$this->orderByNodePathAndType($parentNode, $subConfiguration['type'], $subConfiguration['configuration']);
		}
	}

	/**
	 * @param NodeInterface $parentNode
	 * @param $nodeType string
	 * @param array $configuration
	 * @throws \TYPO3\Flow\Exception
	 */
	public function orderByNodePathAndType(NodeInterface $parentNode, $nodeType, array $configuration) {
		if (!is_string($nodeType) || trim($nodeType) === '') {
			throw new Exception('Empty or invalid node type', 1378378768);
		}
		if (!isset($configuration['type'])) {
			$configuration['type'] = 'simple';
		}

		switch ($configuration['type']) {
			case 'simple':
				$this->applySimpleSorting($parentNode, $nodeType, $configuration);
				break;
			default:
				throw new Exception('Unsupported sorting type', 1378367450);
		}
	}

	/**
	 * @param NodeInterface $parentNode
	 * @param string $nodeType
	 * @param array $configuration
	 */
	protected function applySimpleSorting(NodeInterface $parentNode, $nodeType, array $configuration) {
		$property      = $configuration['property'];
		$sortingSource = array();
		$hasChildNode  = FALSE;
		foreach ($parentNode->getChildNodes($nodeType) as $node) {
			/** @var NodeInterface $node */
			$sortingSource[] = array(
				'path'         => $node->getPath(),
				'property'     => $this->convertPropertyType($node->getProperty($property), $configuration['propertyTargetType']),
				'currentIndex' => $node->getIndex(),
			);
			$hasChildNode = TRUE;
		}
		if ($hasChildNode === FALSE) {
			return;
		}
		$currentIndexes = $this->extractCurrentIndexes($sortingSource);
		usort($sortingSource, function ($a, $b) {
			return $a['property'] < $b['property'];
		});
		$this->applySorting($sortingSource, $currentIndexes, $parentNode);
	}

	/**
	 * @param mixed $source
	 * @param string $targetType
	 * @param PropertyMappingConfigurationInterface $propertyMappingConfiguration
	 * @return mixed
	 */
	protected function convertPropertyType($source, $targetType, PropertyMappingConfigurationInterface $propertyMappingConfiguration = NULL) {
		if ($propertyMappingConfiguration !== NULL) {
			return $this->propertyMapper->convert($source, $targetType, $propertyMappingConfiguration);
		}

		$propertyMappingConfiguration = $this->propertyMappingConfigurationBuilder->build();
		switch ($targetType) {
			case 'DateTime':
				$propertyMappingConfiguration->setTypeConverterOption(
					'TYPO3\Flow\Property\TypeConverter\DateTimeConverter',
					\TYPO3\Flow\Property\TypeConverter\DateTimeConverter::CONFIGURATION_DATE_FORMAT,
					'Y-m-d'
				);
				break;
		}

		return $this->propertyMapper->convert($source, $targetType, $propertyMappingConfiguration);
	}

	/**
	 * @param array $sortingSource
	 * @return array
	 */
	protected function extractCurrentIndexes(array $sortingSource) {
		$currentIndexes = array();
		array_walk($sortingSource, function ($key) use (&$currentIndexes) {
			$currentIndexes[] = array(
				'currentIndex' => $key['currentIndex']
			);
		});

		return $currentIndexes;
	}

	/**
	 * @param array $sortingSource
	 * @param array $currentIndexes
	 * @param NodeInterface $parentNode
	 */
	protected function applySorting(array $sortingSource, array $currentIndexes, NodeInterface $parentNode) {
		foreach (Arrays::arrayMergeRecursiveOverrule($sortingSource, $currentIndexes) as $node) {
			$parentNode->getNode($node['path'])->setIndex($node['currentIndex']);
		}
	}
}

?>