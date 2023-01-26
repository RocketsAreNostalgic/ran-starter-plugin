<?php

namespace Ran\MyPlugin\Base;

use Ran\MyPlugin\Base\Plugin;
use Ran\MyPlugin\Features;
use Ran\MyPlugin\Pages;
use Ran\PluginLib\BootstrapInterface;
use Ran\PluginLib\RegisterFeatures;

/**
 * Plugin bootstrap class
 */
class Bootstrap implements BootstrapInterface {

	/**
	 * Internal reference to the admin pages array.
	 *
	 * @var array $admin_pages
	 */
	private array $admin_pages = array(
		'cpt_manager' => 'Activate CPT Manager',
		'taxonomy_manager' => 'Activate Taxonomy Manager',
		'media_widget' => 'Activate Media Widget',
		'testimonial_manager' => 'Activate Testimonial Manager',
		'templates_manager' => 'Activate Custom Templates',
		'login_manager' => 'Activate Ajax Login/Signup',
		'example_manager' => 'Activate Example Manager',
	);

	/**
	 * An array of plugin feature classes to register.
	 *
	 * @var array $plugin_features
	 */
	public static array $features = array(
		Pages\Dashboard::class => array(),
		Features\ExampleFeatureController::class => array(),
	);

	/**
	 * An array of feature controllers to register.
	 *
	 * @var array
	 */
	private array $feature_controllers = array();

	/**
	 * Bootstrap init method.
	 *
	 * @param string $plugin_file Is the plugin root file or __FILE__.
	 *
	 * @return Plugin
	 */
	public static function init( string $plugin_file = '' ):Plugin {
		$plugin = new Plugin( $plugin_file );

		// Register feature controllers.
		$features = new RegisterFeatures();

		$features->instantiate( $plugin, self::$features );

		// Register admin pages.

		return $plugin;
	}
}
