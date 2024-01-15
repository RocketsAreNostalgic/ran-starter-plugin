<?php
/**
 * @package  RanPlugin
 */

namespace Ran\MyPlugin\Api\Callbacks;

use Ran\PluginLib\Config\ConfigInterface;

class ExampleFeatureCallbacks {
	/**
	 * Array representation of of Plugin data.
	 *
	 * @var array
	 */
	private array $plugin_data = array();


	public function __construct( ConfigInterface $plugin ) {

		$this->plugin_data = $plugin->get_plugin();
	}

	public function admin_dashboard() {

		return require_once $this->plugin_data['PATH'] . '/templates/dashboard.php';
	}

	public function example_feature() {
		return require_once "$this->plugin_data['PATH']" . '/templates/features/example-feature.php';
	}
}
