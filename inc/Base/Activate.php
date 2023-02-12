<?php
/**
 * Handel plugin activation.
 *
 * @package  RanPlugin
 */

namespace Ran\MyPlugin\Base;

use Ran\PluginLib\ActivationInterface;
use Ran\PluginLib\plugin\PluginInterface;

/**
 * Activation class that establishes the
 *
 * @param  Plugin $plugin the current plugin instance.
 * @param  mixed  ...$args mixed array of arguments.
 *
 * @package  RanPlugin
 */
class Activate implements ActivationInterface {
	/**
	 * Static activation method called by WordPress register_activation_hook when the plugin is activated.
	 * This must be called as a static method, ideally in the plugin root file.
	 *
	 * @param  PluginInterface $plugin An instance of the Plugin class.
	 * @param  mixed           ...$args Any required arguments.
	 *
	 * @return void
	 * @throws \InvalidArgumentException If $plugin is not a Plugin instance.
	 */
	public static function activate( PluginInterface $plugin, mixed ...$args ): void {

		$plugin_data = $plugin->get_plugin();

		$option_data = array( 'Version' => $plugin_data['Version'] );

		flush_rewrite_rules();

		if ( ! get_option( $plugin_data['PluginOption'] ) ) {
			update_option( $plugin_data['PluginOption'], $option_data );
		}
	}
}
