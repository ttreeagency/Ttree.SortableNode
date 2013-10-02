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

/**
 * Property Sorting Definition DTO
 */
class PropertySortingDefinition {

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $direction;

	/**
	 * @var string
	 */
	protected $type;

	/**
	 * @param string $name
	 * @param string $direction
	 * @param string $type
	 */
	function __construct($name, $direction, $type) {
		$this->name      = $name;
		$this->direction = $direction;
		$this->type      = $type;
	}

	/**
	 * @return string
	 */
	public function getDirection() {
		return $this->direction;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}

}

?>