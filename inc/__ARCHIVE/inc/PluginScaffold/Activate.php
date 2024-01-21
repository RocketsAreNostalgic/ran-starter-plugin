<?php

/**
 * @package  RanPlugin
 *
 * Handels the registering plugin options.
 *
 * TODO: make registration of options dynamic.
 */

namespace Inc\PluginScaffold;

use Inc\Base;

/**
 * Activation class that establishes the
 *
 * @package  RanPlugin
 */
class Activate extends
{
    /**
     * Static activation method called by WP 'register_activation_hook'
     *
     * @return void
     */
    public static function activate()
    {
        flush_rewrite_rules();

        $default = array();

        if (!get_option('ran_plugin')) {
            update_option('ran_plugin', $default);
        }

        if (!get_option('ran_plugin_cpt')) {
            update_option('ran_plugin_cpt', $default);
        }

        if (!get_option('ran_plugin_tax')) {
            update_option('ran_plugin_tax', $default);
        }
    }
}
