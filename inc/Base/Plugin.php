<?php
/**
 * The plugin base class.
 *
 * @package  RanPlugin
 */

namespace Ran\MyPlugin\Base;

use Ran\PluginLib\Plugin\PluginAbstract;

/**
 * Base plugin class collates basic information about the plugin using the plugin docblock.
 * It assumes that the plugin root file is 'plugin.php' unless it is passed a $file parameter during construction.
 * As this involves a file system read, it is more expensive to run, so best treated as a singleton.
 *
 * @package  RanPlugin
 */
final class Plugin extends PluginAbstract {
	// we're just going to keep the stock implementation for now.
}
