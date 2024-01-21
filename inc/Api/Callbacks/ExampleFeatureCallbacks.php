<?php
/**
 * An example of a Feature class.
 *
 * @package  RanPlugin
 */

declare(strict_types = 1);

namespace Ran\MyPlugin\Api\Callbacks;

use Ran\PluginLib\Config\ConfigInterface;

/**
 * Example Feature Callback Class
 */
class ExampleFeatureCallbacks {
	/**
	 * Array representation of of Plugin data.
	 *
	 * @var array<mixed>
	 */
	private array $plugin_data = array();

	/**
	 * Constructor for ExampleFeatureCallbacks.
	 *
	 * @param  ConfigInterface $plugin Incoming Plugin instance.
	 */
	public function __construct( ConfigInterface $plugin ) {

		$this->plugin_data = $plugin->get_plugin_config();
	}

	/**
	 * Require the plugin dashboard template.
	 */
	public function admin_dashboard(): bool {

		return require_once $this->plugin_data['PATH'] . '/templates/dashboard.php';
	}

	/**
	 * Require the example template.
	 */
	public function example_feature(): bool {
		return require_once "$this->plugin_data['PATH']" . '/templates/features/example-feature.php';
	}
}
