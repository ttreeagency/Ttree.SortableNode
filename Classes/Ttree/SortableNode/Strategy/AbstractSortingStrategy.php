<?php
namespace Ttree\SortableNode\Strategy;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Ttree.SortableNode".    *
 *                                                                        *
 *                                                                        */

use Ttree\SortableNode\Domain\Model\PropertySortingDefinition;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Persistence\Generic\PersistenceManager;
use TYPO3\Flow\Property\PropertyMapper;
use TYPO3\Flow\Property\PropertyMappingConfigurationBuilder;
use TYPO3\TYPO3CR\Domain\Repository\NodeDataRepository;

/**
 * Node sorting strategy interface
 *
 * @package Ttree\Plugin\MicroEvent\Strategy
 */
abstract class AbstractSortingStrategy {

	/**
	 * @Flow\Inject
	 * @var PropertyMapper
	 */
	protected $propertyMapper;

	/**
	 * @Flow\Inject
	 * @var PropertyMappingConfigurationBuilder
	 */
	protected $propertyMappingConfigurationBuilder;

	/**
	 * @Flow\Inject
	 * @var NodeDataRepository
	 */
	protected $nodeDataRepository;

	/**
	 * @param mixed $source
	 * @param PropertySortingDefinition $propertySortingDefinition
	 * @return mixed
	 */
	protected function convertPropertyType($source, PropertySortingDefinition $propertySortingDefinition) {
		$propertyMappingConfiguration = $this->propertyMappingConfigurationBuilder->build();

		$targetType = NULL;
		switch ($propertySortingDefinition->getType()) {
			case 'date':
				$targetType = 'DateTime';
				$propertyMappingConfiguration->setTypeConverterOption(
					'TYPO3\Flow\Property\TypeConverter\DateTimeConverter',
					\TYPO3\Flow\Property\TypeConverter\DateTimeConverter::CONFIGURATION_DATE_FORMAT,
					'Y-m-d'
				);
				break;
		}

		return $targetType ? $this->propertyMapper->convert($source, $targetType, $propertyMappingConfiguration) : $source;
	}

}

?>