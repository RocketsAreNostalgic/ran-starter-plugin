<?php
/**
 * Callback for the plugin Dashboard
 *
 * @package  RanPlugin
 */

declare(strict_types = 1);

namespace Ran\MyPlugin\Api\Callbacks;

use Ran\PluginLib\Config\ConfigInterface;

/**
 * A class containing all of our callbacks for the admin area.
 */
class AdminCallbacks {

	/**
	 * Array of plugin details.
	 *
	 * @var array<string, mixed>
	 */
	private array $plugin_data = array();

	/**
	 * Constructor of our AdminCallbacks class.
	 *
	 * @param  ConfigInterface $plugin Incoming Plugin instance.
	 */
	public function __construct( ConfigInterface $plugin ) {
		$this->plugin_data = $plugin->get_plugin_config();
	}

	/**
	 * Template for our plugin Dashboard.
	 */
	public function admin_dashboard(): mixed {
		return require_once $this->plugin_data['PATH'] . 'templates/dashboard.php';
	}


	/**
	 * The template for the example feature.
	 */
	public function example_feature(): mixed {
		return require_once $this->plugin_data['PATH'] . '/templates/features/example-feature.php';
	}
}
