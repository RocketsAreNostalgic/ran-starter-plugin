<?php
/**
 * The plugin base class.
 *
 * @package  RanPlugin
 */

declare(strict_types = 1);

namespace Ran\StarterPlugin\Base;

use LogicException;
use Ran\PluginLib\Config\Config as PluginLibConfig;

/**
 * Base config class collates basic information about the plugin using the WordPresses, plugin docblock.
 * It assumes that the plugin root file is 'plugin.php' unless it is passed a $file parameter during construction.
 * As this involves a file system read, it is more expensive to run, so best treated as a singleton.
 *
 * @package  RanPlugin
 */
final class Config extends PluginLibConfig {
	/**
	 * The plugin-wide configuration instance.
	 *
	 * @var self|null
	 */
	private static ?self $instance = null;

	/**
	 * Initialize the configuration from the plugin's main file.
	 *
	 * @param string $plugin_file Absolute path to the plugin's main file.
	 * @return self
	 */
	public static function init( string $plugin_file ): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
			self::$instance->_hydrateFromPlugin( $plugin_file );
		}

		return self::$instance;
	}

	/**
	 * Return the initialized plugin-wide configuration.
	 *
	 * @return self
	 * @throws LogicException When the configuration has not been initialized.
	 */
	public static function get_instance(): self {
		if ( null === self::$instance ) {
			throw new LogicException( 'RAN Starter Plugin configuration has not been initialized.' );
		}

		return self::$instance;
	}

	/**
	 * Return the legacy plugin data shape used by the starter scaffolding.
	 *
	 * @return array<string, mixed>
	 */
	public function get_plugin_config(): array {
		$config                 = $this->get_config();
		$config['PluginOption'] = $this->get_options_key();

		return $config;
	}
}
