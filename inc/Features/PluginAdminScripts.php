<?php

namespace Inc\Services;

use Inc\Base\ControllerInterface;
use Inc\Base\Enqueue;

/**
 *
 * Inherits register method()
 */
class PluginAdminScripts extends Enqueue implements ControllerInterface
{

    public function enqueue()
    {
        $this->styles[]  = ['mypluginstyle', $this->plugin_url . 'assets/mystyle.css'];
        $this->scripts[] = ['media-upload'];
        $this->scripts[] = ['mypluginscript', $this->plugin_url . 'assets/myscript.js'];
        $this->scripts[] = ['test', $this->plugin_url . 'assets/test.js', null, '0.0.1', false];

        // enqueue all our scripts, styles and media
        $this->enqueue_scripts($this->scripts);
        $this->enqueue_styles($this->styles);
        $this->enqueue_media($this->media);
    }
}
