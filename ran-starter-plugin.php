<?php
/**
 * Plugin Name: RAN Starter Plugin
 * Plugin URI: https://github.com/RocketsAreNostalgic/ran-starter-plugin
 * Description: A modern WordPress plugin starter with optional feature scaffolding.
 * x-release-please-start-version
 * Version: 0.0.4
 * x-release-please-end
 * Requires at least: 7.0
 * Requires PHP: 8.4
 * Author: Rockets Are Nostalgic
 * Author URI: https://github.com/RocketsAreNostalgic
 * License: MIT
 * Text Domain: ran-starter-plugin
 * Domain Path: /languages
 * Update URI: https://github.com/RocketsAreNostalgic/ran-starter-plugin
 *
 * @package  RanStarterPlugin
 */

declare(strict_types = 1);

namespace Ran\StarterPlugin;

// Silence is golden.
defined( 'ABSPATH' ) || die( '' );

// A deployable archive must include Composer dependencies. A source checkout
// must run `composer install` before it can be activated safely.
if ( ! file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	add_action(
		'admin_notices',
		static function (): void {
			printf(
				'<div class="notice notice-error"><p>%s</p></div>',
				esc_html__( 'RAN Starter Plugin needs its Composer dependencies. Run composer install before activating this source checkout.', 'ran-starter-plugin' )
			);
		}
	);

	return;
}

require_once __DIR__ . '/vendor/autoload.php';

use Ran\StarterPlugin\Base\Config;
use Ran\StarterPlugin\Base\Activate;
use Ran\StarterPlugin\Base\Bootstrap;
use Ran\StarterPlugin\Base\Deactivate;

// Initialize Config.
$ran_config = Config::init( __FILE__ );

/**
 * Bootstrap our plugin after WP and plugins but before theme, this can be changed as required.
 *
 * Other hooks include:
 * - 'init' - for loading text domains
 * - 'wp_enqueue_scripts' - for enqueuing assets
 * - 'admin_enqueue_scripts' - for enqueuing admin assets
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
