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
 * Class OrderingService
 * @package Ttree\Plugin\MicroEvent\Service
 * @Flow\Scope("singleton")
 */
class SortingService {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Property\PropertyMapper
	 */
	protected $propertyMapper;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Property\PropertyMappingConfigurationBuilder
	 */
	protected $propertyMappingConfigurationBuilder;

	/**
	 * @param NodeInterface $parentNode
	 * @param $nodeType string
	 * @param array $configuration
	 * @throws \TYPO3\Flow\Exception
	 */
	public function orderByNodePathAndType(NodeInterface $parentNode, $nodeType, array $configuration) {
		if (!isset($configuration['sortingType'])) {
			$configuration['sortingType'] = 'simple';
		}

		switch ($configuration['sortingType']) {
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
		$sortingProperty = $configuration['sortingProperty'];
		$sortingSource   = array();
		foreach ($parentNode->getChildNodes($nodeType) as $node) {
			/** @var NodeInterface $node */
			$property        = $this->convertPropertyType($node->getProperty($sortingProperty), $configuration['sortingPropertyTargetType']);
			$sortingSource[] = array(
				'path'            => $node->getPath(),
				'sortingProperty' => $property,
				'currentIndex'    => $node->getIndex(),
			);
		}
		$currentIndexes = $this->extractCurrentIndexes($sortingSource);
		usort($sortingSource, function ($a, $b) {
			return $a['sortingProperty'] < $b['sortingProperty'];
		});
		$this->applySorting($sortingSource, $currentIndexes, $parentNode);
		foreach (Arrays::arrayMergeRecursiveOverrule($sortingSource, $currentIndexes) as $node) {
			$parentNode->getNode($node['path'])->setIndex($node['currentIndex']);
		}
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