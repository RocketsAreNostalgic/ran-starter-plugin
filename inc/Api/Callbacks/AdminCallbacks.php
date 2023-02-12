<?php
/**
 * Callback for the plugin Dashboard
 *
 * @package  RanPlugin
 */

namespace Ran\MyPlugin\Api\Callbacks;

use Ran\PluginLib\Plugin\PluginInterface;

/**
 * A class containing all of our callbacks for the admin area.
 */
class AdminCallbacks {

	/**
	 * Array of plugin details.
	 *
	 * @var array
	 */
	private array $plugin_data = array();

	/**
	 * Constructor of our AdminCallbacks class.
	 *
	 * @param  PluginInterface $plugin Incoming Plugin instance.
	 */
	public function __construct( PluginInterface $plugin ) {
		$this->plugin_data = $plugin->get_plugin();
	}

	/**
	 * Template for our plugin Dashboard.
	 *
	 * @return string
	 */
	public function admin_dashboard():string {
		return require_once $this->plugin_data['PATH'] . 'templates/dashboard.php';
	}


	/**
	 * The template for the example feature.
	 *
	 * @return string
	 */
	public function example_feature(): string {
		return require_once( $this->plugin_data['PATH'] . '/templates/features/example-feature.php' );
	}
}
