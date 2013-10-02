<?php
namespace Ttree\SortableNode\Service;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Ttree.SortableNode".         *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Exception;
use TYPO3\Flow\Utility\Arrays;
use TYPO3\Flow\Property\PropertyMappingConfigurationInterface;
use TYPO3\TYPO3CR\Domain\Model\NodeInterface;

/**
 * Settings Service
 *
 * @Flow\Scope("singleton")
 */
class SettingsService {

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
	 * @param string $path
	 * @return mixed
	 */
	public function getByPath($path) {
		return Arrays::getValueByPath($this->settings, $path);
	}
}

?>