<?php
/**
 * RAN Starter Plugin
 *
 * @package  RanStarterPlugin
 */

declare(strict_types = 1);

namespace Ran\StarterPlugin;

/*
	Plugin Name: RAN Starter Plugin
	Plugin URI: http://github.com/RocketsAreNostalgic/ran-starter-plugin
	Description: A starter plugin with scaffold for common functionality using the RAN Plugin Lib.
	Version: 0.0.1
	Requires at least: 6.1.0
	Requires PHP: 8.1.0
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
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

use Ran\StarterPlugin\Base\Activate;
use Ran\StarterPlugin\Base\Bootstrap;
use Ran\StarterPlugin\Base\Config;
use Ran\StarterPlugin\Base\Deactivate;


$ran_config = Config::init( __FILE__ );

/**
 * Bootstrap our plugin after WP and plugins but before theme, this can be changed as required.
 */
add_action(
	'plugins_loaded',
	function (): void {
		$bootstrap = new Bootstrap( Config::get_instance() );
		$bootstrap->init();
	},
	20
);

/**
 * Plugin Activation hook
 *
 * @since 0.0.1
 */
function activate_plugin(): void {
	Activate::activate( Config::get_instance() );
}
register_activation_hook( __FILE__, __NAMESPACE__ . '\activate_plugin' );

/**
 * Plugin Deactivation hook
 *
 * @since 0.0.1
 */
function deactivate_plugin(): void {
	Deactivate::deactivate( Config::get_instance() );
}
register_deactivation_hook( __FILE__, __NAMESPACE__ . '\deactivate_plugin' );
