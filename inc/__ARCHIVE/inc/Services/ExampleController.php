<?php

/**
 * @package  RanPlugin
 */

namespace Inc\Services;

use Inc\Api\Callbacks\AdminCallbacks;
use Inc\Base\BaseController;
use Inc\Base\ControllerInterface;
use Inc\Base\SettingsApi;

/**
 * An example Service Controller
 */
class ExampleController extends BaseController implements ControllerInterface
{
    public $callbacks;

    public $settings;

    public $subpages = array();

    /**
     * Our registration funtion to add action hooks to WP
     *
     * @return null
     */
    public function register(): void
    {
        if (!$this->activated('chat_manager')) return;

        $this->settings = new SettingsApi();

        $this->callbacks = new AdminCallbacks();

        $this->setSubpages();

        $this->settings->addSubPages($this->subpages)->register();
    }

    public function setSubpages()
    {
        $this->subpages = array(
            array(
                'parent_slug' => 'ran_plugin',
                'page_title' => 'Example Manager',
                'menu_title' => 'Example Manager',
                'capability' => 'manage_options',
                'menu_slug' => 'ran_example',
                'callback' => array($this->callbacks, 'exampleController')
            )
        );
    }
}
