<?php

/**
 * @package  RanPlugin
 */

namespace Inc\Services;

use Inc\Base\BaseController;
use Inc\Base\ControllerInterface;

/**
 *
 */
class AuthController extends BaseController implements ControllerInterface
{
    /**
     * Our registration funtion to add action hooks to WP
     *
     * @return null
     */
    public function register(): void
    {
        if (!$this->activated('login_manager')) return;

        add_action('wp_enqueue_scripts', array($this, 'enqueue'));
        add_action('wp_head', array($this, 'add_auth_template'));
    }

    public function enqueue()
    {
        wp_enqueue_style('authstyle', $this->plugin_url . 'assets/auth.css');
        wp_enqueue_script('authscript', $this->plugin_url . 'assets/auth.js');
    }

    public function add_auth_template()
    {
        if (is_user_logged_in()) return;

        $file = $this->plugin_path . 'templates/auth.php';

        if (file_exists($file)) {
            load_template($file, true);
        }
    }
}
