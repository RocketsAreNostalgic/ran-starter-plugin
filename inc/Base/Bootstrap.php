<?php
/**
 * RAN Starter Plugin
 *
 * @package  RanStarterPlugin
 */

namespace Ran\MyPlugin\Base;

use Ran\MyPlugin\Features;
use Ran\PluginLib\BootstrapInterface;
use Ran\PluginLib\Config\ConfigInterface;
use Ran\PluginLib\EnqueueAccessory\EnqueueAdmin;
use Ran\PluginLib\EnqueueAccessory\EnqueuePublic;
use Ran\PluginLib\FeaturesAPI\FeaturesManager;

/**
 * Plugin bootstrap class
 */
class Bootstrap implements BootstrapInterface {

	/**
	 * The Plugin class object.
	 *
	 * @var Config
	 */
	private Config $config;

	/**
	 * Plugin data array
	 *
	 * @var array
	 */
	private array $plugin_data = array();

	/**
	 * Bootstrap constructor, loads our config from the docblock in the plugin's entrance file.
	 *
	 * @param  string $plugin_file file path or __FILE__.
	 */
	public function __construct( string $plugin_file ) {
		$this->config = new Config( $plugin_file );
		$this->plugin_data = $this->config->get_plugin();
	}

	/**
	 * Bootstrap init method.
	 *
	 * @return Plugin
	 */
	public function init(): ConfigInterface {
		// TODO: This could be more ergonomic if we created a 'feature'
		// and contract which triggers the loading of EnqueueAdmin() for us.
		// TODO: We are register and enqueue at the same time (not leveraging wp_register_script)
		// TODO: At the moment are not passing

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
			array(),
		);

		// Add additional links to plugin entry.
		$manager->register_feature(
			'plugin-meta-links',
			Features\PluginAdditionalLinks::class,
			array(
				array( 'taco' => 'a tasty treat' ),
			)
		);

		// Example feature controller.
		$manager->register_feature(
			'example-feature-manager',
			Features\ExampleFeatureController::class,
		);

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
		return $this->config;
	}

	/**
	 * Construct an array of admin css styles.
	 *
	 * @return array of admin styles.
	 */
	private function admin_styles(): array {
		$admin_styles[] = array( 'dashboard', $this->plugin_data['URL'] . 'assets/dist/admin/styles/dashboard.css' );
		return $admin_styles;
	}

	/**
	 * Construct an array of admin scripts.
	 *
	 * @return array
	 */
	private function admin_scripts(): array {
		$admin_scripts[] = array( 'admin', $this->plugin_data['URL'] . 'assets/dist/admin/js/admin.js' );
		return $admin_scripts;
	}

	/**
	 * Construct an array of public css styles.
	 *
	 * @return array of public styles.
	 */
	private function public_styles(): array {
		$public_styles[] = array( 'dashboard', $this->plugin_data['URL'] . 'assets/dist/public/styles/plugin.css' );
		return $public_styles;
	}

	/**
	 * Construct an array of public scripts.
	 *
	 * @return array
	 */
	private function public_scripts(): array {
		$public_scripts[] = array( 'public', $this->plugin_data['URL'] . 'assets/dist/public/js/public.js' );
		return $public_scripts;
	}
}
