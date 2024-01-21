<?php

/**
 * @package  RanPlugin
 */

namespace Ran\MyPlugin\Base;

/**
 * This class is meant to be extended and instantiated via the RegisterServices Class.
 */
class Enqueue extends BaseController implements ControllerInterface
{
    public  array $styles = [];
    public  array $scripts = [];
    public  array $media = [];

    /**
     * Our registration funtion to add action hooks to WP
     *
     * @return null
     */
    public function register(): void
    {
        add_action('admin_enqueue_scripts', array($this, 'enqueue'));
    }

    function enqueue_scripts(array $scripts)
    {
        foreach ($scripts as $script) {
            wp_enqueue_script(...$script);
        }
        // die();
        return $this;
    }

    function enqueue_styles(array $styles)
    {
        foreach ($styles as $style) {
            wp_enqueue_style(...$style);
        }
        return $this;
    }

    function enqueue_media(array $media)
    {
        foreach ($media as $args) {
            wp_enqueue_media($args);
        }

        return $this;
    }
    function enqueue()
    {
        // enqueue all our scripts, styles and media
        $this->enqueue_scripts($this->scripts);
        $this->enqueue_styles($this->styles);
        $this->enqueue_media($this->media);
    }
}
