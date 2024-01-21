<?php
/**
 * RAN Starter Plugin: Deactivate
 *
 * @package  RanPlugin
 */

declare(strict_types = 1);

namespace Ran\MyPlugin\Base;

use Ran\PluginLib\ActivationInterface;
use Ran\PluginLib\Config\ConfigInterface;
/**
 * Activation class that establishes the
 *
 * @param  ConfigInterface $plugin the current config instance.
 * @param  mixed ...$args mixed array of arguments.
 *
 * @package  RanPlugin
 */
class Activate implements ActivationInterface {
	/**
	 * Static activation method called by WordPress register_activation_hook when the plugin is activated.
	 * This must be called as a static method, ideally in the plugin root file.
	 *
	 * @param  ConfigInterface $config An instance of the Config class.
	 * @param  mixed           ...$args Any required arguments.
	 *
	 * @throws \InvalidArgumentException If $config is not a Config instance.
	 */
	public static function activate( ConfigInterface $config, mixed ...$args ): void {

		$plugin_data = $config->get_plugin_config();

		$option_data = array( 'Version' => $plugin_data['Version'] );

		flush_rewrite_rules();

		if ( ! get_option( $plugin_data['PluginOption'] ) ) {
			update_option( $plugin_data['PluginOption'], $option_data );
		}
	}
}
