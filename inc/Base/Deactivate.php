<?php
/**
 * RAN Starter Plugin
 *
 * @package  RanStarterPlugin
 */

namespace Ran\MyPlugin\Base;

use Ran\PluginLib\DeactivationInterface;

/**
 * Deactivation class
 *
 * @package  RanPlugin
 */
class Deactivate implements DeactivationInterface {

	/**
	 * Plugin deactivation callback
	 *
	 * @param  Plugin $plugin the current plugin instance.
	 * @param  mixed  ...$args mixed array of arguments.
	 *
	 * @return void
	 */
	public static function deactivate( $plugin, ...$args ):void {
		flush_rewrite_rules();
	}
}
