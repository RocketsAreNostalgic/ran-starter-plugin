<?php
/**
 * Callback for the plugin Dashboard
 *
 * @package  RanPlugin
 */

declare(strict_types = 1);

namespace Ran\StarterPlugin\Api\Callbacks;

use Ran\PluginLib\Config\ConfigInterface;
use Ran\StarterPlugin\Base\Config;

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
	 * Explicit injection is preferred; the initialized starter Config singleton
	 * preserves compatibility with older optional scaffolding that constructs
	 * this callback without an argument.
	 *
	 * @param ConfigInterface|null $plugin Incoming config instance.
	 */
	public function __construct( ?ConfigInterface $plugin = null ) {
		$config            = $plugin ?? Config::get_instance();
		$this->plugin_data = $config->get_plugin_config();
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
		return require_once $this->plugin_data['PATH'] . 'templates/features/example-feature.php';
	}

	/**
	 * The template for the optional taxonomy manager.
	 */
	public function admin_taxonomy(): mixed {
		return require_once $this->plugin_data['PATH'] . 'templates/features/taxonomy.php';
	}
}
