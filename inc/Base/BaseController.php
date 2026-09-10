<?php
/**
 * RAN Starter Plugin: Base Controller
 *
 * @package  RanStarterPlugin
 */

declare(strict_types = 1);

namespace Ran\StarterPlugin\Base;

/**
 * Base Controller Class.
 *
 * The older optional feature scaffolding still extends this controller. Keep
 * the compatibility surface explicit and typed while deriving all plugin
 * identity from the current Config object rather than dynamic properties.
 */
class BaseController implements ControllerInterface {
	/**
	 * Plugin configuration object.
	 *
	 * @var Config
	 */
	protected Config $config;

	/**
	 * Normalized plugin configuration used by legacy starter scaffolding.
	 *
	 * @var array<string, mixed>
	 */
	protected array $plugin_data = array();

	/**
	 * Backwards-compatible alias for older feature scaffolding.
	 *
	 * @var array<string, mixed>
	 */
	protected array $plugin_array = array();

	/**
	 * Absolute plugin root path with trailing slash.
	 *
	 * @var string
	 */
	protected string $plugin_path;

	/**
	 * Plugin root URL with trailing slash.
	 *
	 * @var string
	 */
	protected string $plugin_url;

	/**
	 * Build the controller from the initialized plugin configuration.
	 *
	 * @param Config|null $config Explicit config for tests/features; otherwise
	 *                            use the plugin-wide initialized singleton.
	 */
	public function __construct( ?Config $config = null ) {
		$this->config       = $config ?? Config::get_instance();
		$this->plugin_data  = $this->config->get_plugin_config();
		$this->plugin_array = $this->plugin_data;
		$this->plugin_path  = trailingslashit( (string) ( $this->plugin_data['PATH'] ?? '' ) );
		$this->plugin_url   = trailingslashit( (string) ( $this->plugin_data['URL'] ?? '' ) );
	}

	/**
	 * Base Controller register method.
	 */
	public function register(): void {
		// Optional feature controllers override this method.
	}

	/**
	 * Return whether an option-backed feature is enabled.
	 *
	 * @param string $key         Feature option key.
	 * @param string $option_name Optional explicit WordPress option name.
	 */
	protected function activated( string $key, string $option_name = '' ): bool {
		if ( '' === $option_name ) {
			$option_name = (string) ( $this->plugin_data['PluginOption'] ?? $this->config->get_options_key() );
		}

		$option = get_option( $option_name, array() );

		return is_array( $option ) && ! empty( $option[ $key ] );
	}
}
