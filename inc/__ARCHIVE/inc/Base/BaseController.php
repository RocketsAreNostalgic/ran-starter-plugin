<?php

/**
 * @package  RanPlugin
 */

namespace Ran\MyPlugin\Base;

class BaseController
{
    public string $plugin_path;

    public string $plugin_url;

    public string $plugin_base;

    public string $plugin_name;

    public array $managers = array();

    public function __construct()
    {
        $this->plugin_path = plugin_dir_path(dirname(__FILE__, 2));
        $this->plugin_url = plugin_dir_url(dirname(__FILE__, 2));
        $this->plugin_base = plugin_basename(dirname(__FILE__, 3)) . '/plugin.php';

        $this->managers = array(
            'cpt_manager' => 'Activate CPT Manager',
            'taxonomy_manager' => 'Activate Taxonomy Manager',
            'media_widget' => 'Activate Media Widget',
            'testimonial_manager' => 'Activate Testimonial Manager',
            'templates_manager' => 'Activate Custom Templates',
            'login_manager' => 'Activate Ajax Login/Signup',
            'example_manager' => 'Activate Example Manager'
        );
    }

    /**
     * Returns an arra of manager services with key and discription.
     *
     * @return array mannagers array
     */
    public function get_managers()
    {
        return $this->managers;
    }

    /**
     * Adds a manager service to managers array.
     *
     * @param  array $manager
     *
     * @return null
     */
    public function set_manager(array $manager)
    {
        $this->managers[] = $manager;
    }

    /**
     * Returns the value of an active option, or false.
     *
     * @param  string $key
     *
     * @return string|boolean if value of the option key, or false
     */
    public function activated(string $key)
    {
        $option = get_option('ran_plugin');

        return isset($option[$key]) ? $option[$key] : false;
    }
}
