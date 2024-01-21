<?php

/**
 * @package  RanPlugin
 *
 */

namespace Inc\Base;

use Inc\Base\ControllerInterface;

/**
 * Settings API adds custom settings pages and fields to WordPress admin.
 */
class SettingsApi implements ControllerInterface
{
    public $admin_pages = array();

    public $admin_subpages = array();

    public $settings = array();

    public $sections = array();

    public $fields = array();

    /**
     * Our registration funtion to add action hooks to WP
     *
     * @return null
     */
    public function register(): void
    {
        if (!empty($this->admin_pages) || !empty($this->admin_subpages)) {
            add_action('admin_menu', array($this, 'addAdminMenu'));
        }

        if (!empty($this->settings)) {
            add_action('admin_init', array($this, 'registerCustomFields'));
        }
    }

    /**
     * Add pages to the public admin_pages array.
     *
     * @param  array $pages
     *
     * @return class instance
     */
    public function addPages(array $pages)
    {
        $this->admin_pages = $pages;

        return $this;
    }

    /**
     * Adds a subpage to the public subpages array.
     *
     * @param  string|null $title
     *
     * @return class instance
     */
    public function withSubPage(string $title = null)
    {
        if (empty($this->admin_pages)) return $this;

        $admin_page = $this->admin_pages[0];

        $subpage = array(
            array(
                'parent_slug' => $admin_page['menu_slug'],
                'page_title' => $admin_page['page_title'],
                'menu_title' => ($title) ? $title : $admin_page['menu_title'],
                'capability' => $admin_page['capability'],
                'menu_slug' => $admin_page['menu_slug'],
                'callback' => $admin_page['callback']
            )
        );

        $this->admin_subpages = $subpage;

        return $this;
    }

    /**
     * Merges subpages into to the public pages array.
     *
     * @param  array $pages
     *
     * @return class instance
     */
    public function addSubPages(array $pages)
    {
        $this->admin_subpages = array_merge($this->admin_subpages, $pages);

        return $this;
    }

    /**
     * Adds admin pages and subpages to the WordPress admin.
     *
     * @return class instance
     */
    public function addAdminMenu()
    {
        foreach ($this->admin_pages as $page) {
            add_menu_page($page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], $page['callback'], $page['icon_url'], $page['position']);
        }

        foreach ($this->admin_subpages as $page) {
            add_submenu_page($page['parent_slug'], $page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], $page['callback']);
        }
    }


    /**
     * Set incoming array of custom settings to the public property of the class instance.
     *
     * @param  array $settings
     *
     * @return class instance
     */
    public function setSettings(array $settings)
    {
        $this->settings = $settings;

        return $this;
    }

    /**
     * Set incoming array of custom sections to the public property of the class intance.
     *
     * @param  array $sections
     *
     * @return class instance
     */
    public function setSections(array $sections)
    {
        $this->sections = $sections;

        return $this;
    }

    /**
     * Set the incoming array of custom fields to the public property of the class instance.
     *
     * @param  array $fields
     *
     * @return void
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * Registger custom setting groups, sections and fields with WordPress.
     *
     * @return void
     */
    public function registerCustomFields()
    {
        // register setting
        foreach ($this->settings as $setting) {
            register_setting($setting["option_group"], $setting["option_name"], (isset($setting["callback"]) ? $setting["callback"] : ''));
        }

        // add settings section
        foreach ($this->sections as $section) {
            add_settings_section($section["id"], $section["title"], (isset($section["callback"]) ? $section["callback"] : ''), $section["page"]);
        }

        // add settings field
        foreach ($this->fields as $field) {
            add_settings_field($field["id"], $field["title"], (isset($field["callback"]) ? $field["callback"] : ''), $field["page"], $field["section"], (isset($field["args"]) ? $field["args"] : ''));
        }
    }
}
