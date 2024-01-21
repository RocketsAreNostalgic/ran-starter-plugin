<?php

/**
 * @package  RanPlugin
 */

namespace Inc\Services;

use Inc\Base\ControllerInterface;
use Inc\Base\SettingsLinks;

class PluginActiveLinks extends SettingsLinks implements ControllerInterface
{
    public function plugin_links(array $links)
    {
        $links[] = '<a href="admin.php?page=ran_plugin">Settings</a>';
        // $links[] = '<a href="admin.php?page=ran_plugin">SomeOtherLink</a>';
        return $links;
    }
}
