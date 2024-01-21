<?php

/**
 * @package  RanPlugin
 */

namespace Inc\Base;

use Inc\Base\BaseController;
use Inc\Base\ControllerInterface;


class SettingsLinks extends BaseController implements ControllerInterface
{
    /**
     * Our registration funtion to add action hooks to WP
     *
     * @return null
     */
    public function register(): void
    {
        add_filter("plugin_action_links_$this->plugin_base", array($this, 'plugin_links'));
    }

    public function plugin_links(array $links)
    {
        return $links;
    }
}
