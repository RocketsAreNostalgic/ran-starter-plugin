<?php
/**
 * RAN Starter Plugin: Deactivate
 *
 * @package  RanStarterPlugin
 */

declare(strict_types = 1);

namespace Ran\StarterPlugin\Base;

use Ran\PluginLib\Config\ConfigInterface;
use Ran\PluginLib\DeactivationInterface;

/**
 * A Deactivate class which implements the DeactivationInterface
 */
class Deactivate implements DeactivationInterface {

	/**
	 * Deactivation function called by WordPress register_deactivation_hook when the plugin is deactivated.
	 * This must be called as a static method, ideally in the plugin root file or Bootstrap.php
	 *
	 * @param  ConfigInterface $config the current config instance.
	 * @param  mixed           ...$args mixed array of arguments.
	 */
	public static function deactivate( ConfigInterface $config, mixed ...$args ): void {
		flush_rewrite_rules();
	}
}
