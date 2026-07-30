<?php
/**
 * RAN Starter Plugin: Bootstrap Class
 *
 * @package  RanStarterPlugin
 */

declare(strict_types = 1);

namespace Ran\StarterPlugin\Base;

use Ran\PluginLib\BootstrapInterface;
use Ran\PluginLib\Config\ConfigInterface;

/**
 * Plugin bootstrap class registers our plugin with WordPress and sets up our features.
 */
class Bootstrap implements BootstrapInterface {

	/**
	 * The Config class object.
	 *
	 * @var Config $config - the config object.
	 */
	private Config $config;

	/**
	 * Plugin data array
	 *
	 * @var array<mixed>
	 */
	private array $plugin_data = array();

	/**
	 * Bootstrap constructor, loads our config details from the docblock in the plugin's entrance file.
	 *
	 * @param  ConfigInterface $config the config object.
	 */
	public function __construct( ConfigInterface $config ) {
		$this->config = $config;
		$this->plugin_data = $this->config->get_plugin_config();
	}

	/**
	 * Bootstrap our plugin and
	 *
	 * @return ConfigInterface the config object.
	 */
	public function init(): ConfigInterface {

		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		} else {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public_assets' ) );
		}

		return $this->config;
	}

	/**
	 * Enqueue the starter's admin assets with the stable core WordPress APIs.
	 */
	public function enqueue_admin_assets(): void {
		foreach ( $this->admin_styles() as $style ) {
			wp_enqueue_style( $style['handle'], $style['src'], $style['deps'], $style['version'] );
		}
		foreach ( $this->admin_scripts() as $script ) {
			wp_enqueue_script( $script['handle'], $script['src'], $script['deps'], $script['version'], $script['in_footer'] );
		}
	}

	/**
	 * Enqueue the starter's public assets with the stable core WordPress APIs.
	 */
	public function enqueue_public_assets(): void {
		foreach ( $this->public_styles() as $style ) {
			wp_enqueue_style( $style['handle'], $style['src'], $style['deps'], $style['version'] );
		}
		foreach ( $this->public_scripts() as $script ) {
			wp_enqueue_script( $script['handle'], $script['src'], $script['deps'], $script['version'], $script['in_footer'] );
		}
	}

	/**
	 * Construct an array of admin css styles.
	 *
	 * @return array<mixed>
	 */
	private function admin_styles(): array {
		$asset = 'assets/dist/admin/styles/admin.min.css';

		return array(
			array(
				'handle'  => 'ran-starter-plugin-admin',
				'src'     => $this->asset_url( $asset ),
				'deps'    => array(),
				'version' => $this->asset_version( $asset ),
			),
		);
	}

	/**
	 * Construct an array of admin scripts.
	 *
	 * @return array<mixed>
	 */
	private function admin_scripts(): array {
		$asset = 'assets/dist/admin/js/admin.min.js';

		return array(
			array(
				'handle'    => 'ran-starter-plugin-admin',
				'src'       => $this->asset_url( $asset ),
				'deps'      => array(),
				'version'   => $this->asset_version( $asset ),
				'in_footer' => true,
			),
		);
	}

	/**
	 * Construct an array of public css styles.
	 *
	 * @return array<mixed> of public styles.
	 */
	private function public_styles(): array {
		$asset = 'assets/dist/public/styles/public.min.css';

		return array(
			array(
				'handle'  => 'ran-starter-plugin-public',
				'src'     => $this->asset_url( $asset ),
				'deps'    => array(),
				'version' => $this->asset_version( $asset ),
			),
		);
	}

	/**
	 * Construct an array of public scripts.
	 *
	 * @return array<mixed>
	 */
	private function public_scripts(): array {
		$asset = 'assets/dist/public/js/public.min.js';

		return array(
			array(
				'handle'    => 'ran-starter-plugin-public',
				'src'       => $this->asset_url( $asset ),
				'deps'      => array(),
				'version'   => $this->asset_version( $asset ),
				'in_footer' => true,
			),
		);
	}

	/**
	 * Returns a plugin asset URL from a repository-relative path.
	 *
	 * @param string $asset Relative asset path.
	 */
	private function asset_url( string $asset ): string {
		return trailingslashit( (string) $this->plugin_data['URL'] ) . ltrim( $asset, '/' );
	}

	/**
	 * Uses the built asset modification time as a cache-busting version.
	 *
	 * @param string $asset Relative asset path.
	 */
	private function asset_version( string $asset ): string|false {
		$path = trailingslashit( (string) $this->plugin_data['PATH'] ) . ltrim( $asset, '/' );
		if ( ! file_exists( $path ) ) {
			return false;
		}

		$modified = filemtime( $path );

		return false === $modified ? false : (string) $modified;
	}
}
