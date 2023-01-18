<?php

/**
 * @package  RanStarterPlugin

 */

/*
Plugin Name: RAN Starter Plugin
Plugin URI: http://github.com/RocketsAreNostalgic
Description: A starter plugin with common functionality
Version: 0.0.1
Requires at least: 6.1.0
Requires PHP: 8.0.0
Author: Benjamin Rush "bnjmnrsh" <bnjmnrsh@gmail.com>
Author URI: http://github.com/RocketsAreNostalgic
License: MIT
Text Domain: ran-starter-plugin
Domain Path: /languages
Update URI: http://github.com/RocketsAreNostalgic/{$plugin-name}
*/

// Silence is golden.
defined('ABSPATH') or die('');

// Require Composer Autoload
if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
	require_once dirname(__FILE__) . '/vendor/autoload.php';
}

/**
 * Activation hook
 *
 * @since 0.0.1
 */
function activate_ran_plugin()
{
	Inc\Base\Activate::activate();
}
register_activation_hook(__FILE__, 'activate_ran_plugin');

/**
 * Deactivation hook
 *
 * @since 0.0.1
 */
function deactivate_ran_plugin()
{
	Inc\Base\Deactivate::deactivate();
}
register_deactivation_hook(__FILE__, 'deactivate_ran_plugin');
