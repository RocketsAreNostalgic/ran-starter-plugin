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
use Ran\PluginLib\EnqueueAccessory\EnqueueAdmin;
use Ran\PluginLib\EnqueueAccessory\EnqueuePublic;
use Ran\PluginLib\FeaturesAPI\FeaturesManager;
use Ran\StarterPlugin\Features;

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

		// TODO: This could be more ergonomic if we created a 'feature' to trigger the loading of EnqueueAdmin() for us.
		// TODO: We are register and enqueue at the same time (not leveraging wp_register_script).

		// Enqueue admin assets for the plugin in general.
		$admin_assets = new EnqueueAdmin();
		$admin_assets->add_styles( $this->admin_styles() )
		->add_scripts( $this->admin_scripts() )
		->load();

		$public_assets = new EnqueuePublic();
		$public_assets->add_styles( $this->public_styles() )
		->add_scripts( $this->public_scripts() )
		->load();

		// Registering feature classes with the plugin.
		$manager = new FeaturesManager( $this->config );
		// Admin Dashboard.
		$manager->register_feature(
			$this->plugin_data['TextDomain'],
			Features\Pages\Dashboard::class,
		);

		// TODO IN PROGRESS
		// Add additional links to plugin entry.
		$manager->register_feature(
			'plugin-meta-links',
			Features\PluginAdditionalLinks::class
		);

		//phpcs:disable
		// Example feature controller.
		// $manager->register_feature(
		// 'example-feature-manager',
		// Features\ExampleFeatureController::class,
		// );

		// Load all of our registered features.
		$manager->load_all();

		// echo '<pre>';
		// // \print_r( $manager->get_registry() );
		// echo '<br>';
		// echo '<h2> Before registry</h2>';
		// echo '<br>';
		// foreach ( $manager->get_registry() as $container ) {
		// echo '<br>';
		// echo '<h2>New Instance</h2>';
		// echo '<br>';
		// print_r( $container->get_instance() );
		// }
		// echo '<br>';
		// echo '</pre>';
		// die();
		//phpcs:enable

		return $this->config;
	}

	/**
	 * Construct an array of admin css styles.
	 *
	 * @return array<mixed>
	 */
	private function admin_styles(): array {
		$admin_styles[] = array( 'dashboard', $this->plugin_data['URL'] . 'assets/dist/admin/styles/dashboard.css' );
		return $admin_styles;
	}

	/**
	 * Construct an array of admin scripts.
	 *
	 * @return array<mixed>
	 */
	private function admin_scripts(): array {
		$admin_scripts[] = array( 'admin', $this->plugin_data['URL'] . 'assets/dist/admin/js/admin.min.js' );
		return $admin_scripts;
	}

	/**
	 * Construct an array of public css styles.
	 *
	 * @return array<mixed> of public styles.
	 */
	private function public_styles(): array {
		$public_styles[] = array( 'dashboard', $this->plugin_data['URL'] . 'assets/dist/public/styles/plugin.css' );
		return $public_styles;
	}

	/**
	 * Construct an array of public scripts.
	 *
	 * @return array<mixed>
	 */
	private function public_scripts(): array {
		$public_scripts[] = array( 'public', $this->plugin_data['URL'] . 'assets/dist/public/js/public.min.js' );
		return $public_scripts;
	}
}
