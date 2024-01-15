<?php
/**
 * RAN Starter Plugin
 *
 * @package  RanStarterPlugin
 */

namespace Ran\MyPlugin;

/*
Plugin Name: RAN Starter Plugin
Plugin URI: http://github.com/RocketsAreNostalgic/ran-starter-plugin
Description: A starter plugin with scaffold for common functionality using the RAN Plugin Lib.
Version: 0.0.1
Requires at least: 6.1.0
Requires PHP: 8.0.0
Author: Rockets Are Nostalgic
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

use Ran\MyPlugin\Base\Activate;
use Ran\MyPlugin\Base\Bootstrap;
use Ran\MyPlugin\Base\Config;
use Ran\MyPlugin\Base\Deactivate;

/**
 * Plugin Activation hook
 * Passing as skeleton instance of Plugin, will not have full record of all plugin classes etc.
 *
 * @since 0.0.1
 */
function activate_plugin() {
	Activate::activate( new Config( __FILE__ ) );
}
register_activation_hook( __FILE__, __NAMESPACE__ . '\activate_plugin' );

/**
 * Plugin Deactivation hook
 * Passing as skeleton instance of Plugin, will not have full record of all plugin classes etc.
 *
 * @since 0.0.1
 */
function deactivate_plugin() {
	Deactivate::deactivate( new Config( __FILE__ ) );
}
register_deactivation_hook( __FILE__, __NAMESPACE__ . '\deactivate_plugin' );

/**
 * Bootstrap our plugin after WP and plugins but before theme, this can be changed as required.
 */
add_action(
	'plugins_loaded',
	function() {
		$bootstrap = new Bootstrap( __FILE__ );
		$bootstrap->init();
	},
	20
);
