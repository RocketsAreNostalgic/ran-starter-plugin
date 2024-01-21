<?php
/**
 * An API for adding plugin admin settings pages and subpages.
 *
 * @package  RanPlugin
 */

declare(strict_types = 1);

namespace Ran\StarterPlugin\Base;

use Ran\PluginLib\FeaturesAPI\RegistrableFeatureInterface;

/**
 * Settings API adds custom settings pages and fields to WordPress admin.
 */
class SettingsApi implements RegistrableFeatureInterface {

	/**
	 * Array of top level pages.
	 *
	 * @var array<mixed>
	 */
	public array $wp_admin_pages = array();

	/**
	 * Array of subpages.
	 *
	 * @var array<mixed>
	 */
	public array $wp_admin_subpages = array();

	/**
	 * Settings array.
	 *
	 * @var array<mixed>
	 */
	public array $settings = array();

	/**
	 * Array of sections.
	 *
	 * @var array<mixed>
	 */
	public array $sections = array();

	/**
	 * Array of fields.
	 *
	 * @var array<mixed>
	 */
	public array $fields = array();

	/**
	 * Our registration function to add action hooks to WP
	 */
	public function init(): SettingsApi {
		if ( ! empty( $this->wp_admin_pages ) || ! empty( $this->wp_admin_subpages ) ) {
			add_action( 'admin_menu', array( $this, 'add_admin_menu_pages' ) );
		}

		if ( ! empty( $this->settings ) ) {
			add_action( 'admin_init', array( $this, 'register_custom_fields' ) );
		}
		return $this;
	}

	/**
	 * Internal reference of to the WordPress Pages array.
	 *
	 * @param array<mixed> $pages An array of top level pages.
	 */
	public function add_wp_admin_pages( array $pages ): SettingsApi {
		$this->wp_admin_pages = $pages;

		return $this;
	}

	/**
	 * Adds a subpage to the public subpages array.
	 *
	 * @param  string|null $title the tile of the page being added.
	 */
	public function with_subpages( ?string $title = null ): SettingsApi {
		if ( empty( $this->wp_admin_pages ) ) {
			return $this;
		}

		$admin_page = $this->wp_admin_pages[0];

		$subpage = array(
			array(
				'parent_slug' => $admin_page['menu_slug'],
				'page_title' => $admin_page['page_title'],
				'menu_title' => ( $title ) ? $title : $admin_page['menu_title'],
				'capability' => $admin_page['capability'],
				'menu_slug' => $admin_page['menu_slug'],
				'callback' => $admin_page['callback'],
			),
		);

		$this->wp_admin_subpages = $subpage;

		return $this;
	}

	/**
	 * Merges subpages into to the public pages array.
	 *
	 * @param  array<mixed> $subpages The pages array provided by WordPress.
	 */
	public function add_subpages( array $subpages ): SettingsApi {
		$this->wp_admin_subpages = array_merge( $this->wp_admin_subpages, $subpages );

		return $this;
	}

	/**
	 * Adds admin pages and subpages to the WordPress admin.
	 */
	public function add_admin_menu_pages(): void {
		foreach ( $this->wp_admin_pages as $page ) {
			add_menu_page( $page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], $page['callback'], $page['icon_url'], $page['position'] );
		}

		foreach ( $this->wp_admin_subpages as $page ) {
			add_submenu_page( $page['parent_slug'], $page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], $page['callback'] );
		}
	}

	/**
	 * Set incoming array of custom settings to the public property of the class instance.
	 *
	 * @param  array<mixed> $settings An array of settings.
	 */
	public function set_settings( array $settings ): SettingsApi {
		$this->settings = $settings;

		return $this;
	}

	/**
	 * Set incoming array of custom sections to the public property of the class instance.
	 *
	 * @param  array<mixed> $sections An array of sections.
	 */
	public function set_sections( array $sections ): SettingsApi {
		$this->sections = $sections;

		return $this;
	}

	/**
	 * Set the incoming array of custom fields to the public property of the class instance.
	 *
	 * @param  array<mixed> $fields An array of custom fields.
	 */
	public function set_fields( array $fields ): SettingsApi {
		$this->fields = $fields;

		return $this;
	}

	/**
	 * Register custom setting groups, sections and fields with WordPress.
	 */
	public function register_custom_fields(): void {
		// Register each setting.
		foreach ( $this->settings as $setting ) {
			register_setting( $setting['option_group'], $setting['option_name'], ( isset( $setting['callback'] ) ? $setting['callback'] : '' ) );
		}

		// Add settings to each section.
		foreach ( $this->sections as $section ) {
			add_settings_section( $section['id'], $section['title'], ( isset( $section['callback'] ) ? $section['callback'] : '' ), $section['page'] );
		}

		// Add settings field to each setting.
		foreach ( $this->fields as $field ) {
			add_settings_field( $field['id'], $field['title'], ( isset( $field['callback'] ) ? $field['callback'] : '' ), $field['page'], $field['section'], ( isset( $field['args'] ) ? $field['args'] : '' ) );
		}
	}
}
