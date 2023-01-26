<?php
/**
 * RAN Starter Plugin
 *
 * @package  RanStarterPlugin
 */

/*
Plugin Name: RAN Starter Plugin
Plugin URI: http://github.com/RocketsAreNostalgic/ran-starter-plugin
Description: A starter plugin with scaffold for common functionality using the RAN Plugin Lib.
Version: 0.0.1
Requires at least: 6.1.0
Requires PHP: 8.0.0
Author: Rocket Are Nostalgic
Author URI: http://github.com/RocketsAreNostalgic
License: MIT
Text Domain: ran-starter-plugin
Domain Path: /languages
Update URI: http://github.com/RocketsAreNostalgic/ran-starter-plugin
*/

// Silence is golden.
defined( 'ABSPATH' ) || die( '' );

// Require Composer Autoload.
if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
	require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}

use Ran\MyPlugin\Base\Plugin;

/**
 * Activation hook
 *
 * @since 0.0.1
 */
function ran_activate_plugin() {
	$plugin = new Plugin( __FILE__ );
	require_once plugin_dir_path( __FILE__ ) . 'inc/Base/Activate.php';
	Ran\MyPlugin\Base\Activate::activate( $plugin );
}
register_activation_hook( __FILE__, 'ran_activate_plugin' );

/**
 * Deactivation hook
 *
 * @since 0.0.1
 */
function ran_deactivate_plugin() {
	$plugin = new Plugin( __FILE__ );
	require_once plugin_dir_path( __FILE__ ) . 'inc/Base/Deactivate.php';
	Ran\MyPlugin\Base\Deactivate::deactivate( $plugin );
}
register_deactivation_hook( __FILE__, 'ran_deactivate_plugin' );

// Kick off the plugin.
require_once plugin_dir_path( __FILE__ ) . 'inc/Base/Bootstrap.php';
Ran\MyPlugin\Base\Bootstrap::init( __FILE__ );
