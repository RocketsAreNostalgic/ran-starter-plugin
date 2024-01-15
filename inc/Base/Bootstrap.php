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
	 * Bootstrap constructor.
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
	public function init():ConfigInterface {
		// TODO: Implement this as a feature with contract which triggers the loading of EnqueueAdmin().
		// Enqueue admin assets for the plugin in general.
		// $admin_assets = new EnqueueAdmin();
		// $admin_assets->add_styles( $this->admin_styles() )
		// ->add_scripts( $this->admin_scripts() )
		// ->load();

		// Registering feature classes with the plugin.
		$manager = new FeaturesManager( $this->config );
		// Admin Dashboard.
		// $manager->register_feature(
			// $this->plugin_data['TextDomain'],
			// Features\Pages\Dashboard::class,
			// array(),
		// );

		// Add additional links to plugin entry.
		$manager->register_feature(
			'plugin-meta-links',
			Features\PluginAdditionalLinks::class,
			array(
				array( 'taco' => 'a tasty treat' ),
			)
		);

		// Example feature controller.
		// $manager->register_feature(
		// 'example-feature-manager',
		// Features\ExampleFeatureController::class,
		// );

		$manager->load_all();

		// echo '<pre>';
		// // \print_r( $manager->get_registery() );
		// echo '<br>';
		// echo '<h2	> before registery</h2>';
		// echo '<br>';
		// foreach ( $manager->get_registery() as $container ) {
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
	private function admin_styles():array {
		$admin_styles[] = array( 'dashboard', $this->plugin_data['URL'] . 'assets/dist/admin/styles/dashboard.css' );
		return $admin_styles;
	}

	/**
	 * Construct an array of admin scripts.
	 *
	 * @return array
	 */
	private function admin_scripts():array {
		$admin_scripts[] = array( 'admin', $this->plugin_data['URL'] . 'assets/dist/admin/js/admin.js' );
		return $admin_scripts;
	}
}
