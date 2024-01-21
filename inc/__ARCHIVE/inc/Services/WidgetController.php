<?php

/**
 * @package  RanPlugin
 */

namespace Inc\Services;

use Inc\Api\Widgets\MediaWidget;
use Inc\Base\BaseController;
use Inc\Base\ControllerInterface;

/**
 *
 */
class WidgetController extends BaseController implements ControllerInterface
{
    /**
     * Our registration funtion to add action hooks to WP
     *
     * @return null
     */
    public function register(): void
    {
        if (!$this->activated('media_widget')) return;

        $media_widget = new MediaWidget();
        $media_widget->register();
    }
}
