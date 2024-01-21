<?php
/**
 * The plugin base class.
 *
 * @package  RanPlugin
 */

declare(strict_types = 1);

namespace Ran\StarterPlugin\Base;

use Ran\PluginLib\Config\ConfigAbstract;
use Ran\PluginLib\Config\ConfigInterface;

/**
 * Base config class collates basic information about the plugin using the WordPresses, plugin docblock.
 * It assumes that the plugin root file is 'plugin.php' unless it is passed a $file parameter during construction.
 * As this involves a file system read, it is more expensive to run, so best treated as a singleton.
 *
 * @package  RanPlugin
 */
final class Config extends ConfigAbstract implements ConfigInterface {
	// we're just going to keep the stock implementation for now.
}
