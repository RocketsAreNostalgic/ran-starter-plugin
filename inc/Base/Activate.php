<?php
/**
 * Handel plugin activation.
 *
 * @package  RanPlugin
 */

namespace Ran\MyPlugin\Base;

use Ran\MyPlugin\Base\Plugin;
use Ran\PluginLib\ActivationInterface;

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
	 * Static activation method called by WP 'register_activation_hook'
	 *
	 * @param  Plugin $plugin The Plugin class instance.
	 * @param  array  ...$args and array of mixed arguments.
	 *
	 * @return void
	 * @throws \InvalidArgumentException If $plugin is not a Plugin instance.
	 */
	public static function activate( $plugin, ...$args ):void {

		if ( ! is_a( $plugin, 'Ran\PluginLib\PluginInterface' ) ) {
			throw new \InvalidArgumentException( '$plugin must be a class that impliments Ran\PluginLib\PluginInterface.' );
		}

		$plugin_data = $plugin->get_plugin();

		$option_data = array( 'Version' => $plugin_data['Version'] );

		flush_rewrite_rules();

		if ( ! get_option( $plugin_data['PluginOption'] ) ) {
			update_option( $plugin_data['PluginOption'], $option_data );
		}
	}
}
