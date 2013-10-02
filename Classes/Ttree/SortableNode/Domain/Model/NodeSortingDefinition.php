<?php
namespace Ttree\SortableNode\Domain\Model;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Ttree.SortableNode".         *
 *                                                                        *
 *                                                                        */

use Ttree\SortableNode\Factory\SortingStrategyFactory;
use Ttree\SortableNode\Service\SettingsService;
use Ttree\SortableNode\Strategy\SortingStrategyInterface;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Exception;
use TYPO3\Flow\Utility\Arrays;
use TYPO3\TYPO3CR\Domain\Model\Node;
use TYPO3\TYPO3CR\Domain\Service\NodeTypeManager;

/**
 * Node Sorting Definition DTO
 */
class NodeSortingDefinition {

	/**
	 * @Flow\Inject
	 * @var SortingStrategyFactory
	 */
	protected $sortingNodeStrategyFactory;

	/**
	 * @Flow\Inject
	 * @var NodeTypeManager
	 */
	protected $nodeTypeManager;

	/**
	 * @Flow\Inject
	 * @var SettingsService
	 */
	protected $settingsService;

	/**
	 * @var \TYPO3\TYPO3CR\Domain\Model\NodeType
	 */
	protected $nodeType;

	/**
	 * @var string
	 */
	protected $label;

	/**
	 * @var string
	 */
	protected $identifier;

	/**
	 * @var SortingStrategyInterface
	 */
	protected $strategy;

	/**
	 * @var boolean
	 */
	protected $enabled;

	/**
	 * @var array
	 */
	protected $properties = array();

	/**
	 * @param string $nodeTypeName
	 * @param array $configuration
	 * @throws Exception
	 */
	function __construct($nodeTypeName, array $configuration) {
		$nodeTypeName = trim($nodeTypeName);
		if ($nodeTypeName === '') {
			throw new Exception('Empty NodeType name is not allowed', 1380721651);
		}
		$identifier = trim(Arrays::getValueByPath($configuration, 'identifier'));
		if ($identifier === '') {
			throw new Exception('Empty identifier is not allowed', 1380718239);
		}
		$properties = Arrays::getValueByPath($configuration, 'properties');
		if (!is_array($properties) || $properties === array()) {
			throw new Exception('Properties must be an array and contain at least one property', 1380718300);
		}

		$this->nodeType     = $nodeTypeName;
		$this->label        = Arrays::getValueByPath($configuration, 'label');
		$this->identifier   = $identifier;
		$this->enabled      = Arrays::getValueByPath($configuration, 'enabled') === FALSE ? FALSE : TRUE;
		$this->strategy     = Arrays::getValueByPath($configuration, 'strategy');
		$this->properties   = $properties;
	}

	/**
	 * Initialize object
	 */
	public function initializeObject() {
		$this->strategy   = $this->sortingNodeStrategyFactory->create($this->strategy ?: $this->settingsService->getByPath('defaultSortingStrategy'));
		$this->nodeType   = $this->nodeTypeManager->getNodeType($this->nodeType);
		$properties = $this->nodeType->getProperties();

		foreach ($this->properties as $propertyName => $direction) {
			if (!isset($properties[$propertyName]) || !is_array($properties)) {
				throw new Exception(sprintf('Invalid property (%s) for NodeType (%s)', $propertyName, $this->getNodeType()->getName()), 1380718239);
			}
			$this->properties[$propertyName] = new PropertySortingDefinition($propertyName, $direction, $properties[$propertyName]['type']);
		}
	}

	/**
	 * @param string $propertyName
	 * @return PropertySortingDefinition
	 */
	public function getPropertySortingConfiguration($propertyName) {
		return isset($this->properties[$propertyName]) ? $this->properties[$propertyName] : NULL;
	}

	/**
	 * @return \TYPO3\TYPO3CR\Domain\Model\NodeType
	 */
	public function getNodeType() {
		return $this->nodeType;
	}

	/**
	 * @return string
	 */
	public function getHash() {
		return md5($this->getNodeType()->getName() . $this->identifier);
	}

	/**
	 * @return string
	 */
	public function getLabel() {
		return $this->label;
	}

	/**
	 * @return boolean
	 */
	public function getEnabled() {
		return $this->enabled;
	}

	/**
	 * @return array
	 */
	public function getProperties() {
		return $this->properties;
	}

	/**
	 * @return \Ttree\SortableNode\Strategy\SortingStrategyInterface
	 */
	public function getStrategy() {
		return $this->strategy;
	}

	/**
	 * @return boolean
	 */
	public function isEnabled() {
		return $this->enabled;
	}

	/**
	 * @param Node $node
	 * @return bool
	 */
	public function matchesNode(Node $node) {
		$matches = FALSE;

		if ($this->identifier === $node->getNodeData()->getIdentifier() || $this->identifier === $node->getNodeData()->getPath()) {
			$matches = TRUE;
		}

		return $matches;
	}
}

?>