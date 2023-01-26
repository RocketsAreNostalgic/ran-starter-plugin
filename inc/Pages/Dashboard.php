<?php

/**
 * @package  RanPlugin
 */

namespace Ran\MyPlugin\Pages;

use Ran\MyPlugin\Api\Callbacks\AdminCallbacks;
use Ran\MyPlugin\Api\Callbacks\ManagerCallbacks;
use Ran\MyPlugin\Base\SettingsApi;
use Ran\PluginLib\AbstractFeatureController;
use Ran\PluginLib\RegistrableInterface;

class Dashboard extends AbstractFeatureController implements RegistrableInterface {

	public SettingsApi $settings;

	public AdminCallbacks $callbacks;

	public ManagerCallbacks $callbacks_mngr;

	public array $wp_admin_sidebar_menu_item = array();

	/**
	 * Our registration function to add action hooks to WP
	 *
	 * @return void
	 */
	public function register(): void {

		$this->settings = new SettingsApi();

		$this->callbacks = new AdminCallbacks( $this->plugin );

		$this->callbacks_mngr = new ManagerCallbacks( $this->plugin );

		$this->create_wp_admin_sidebar_menu_item();
		$this->set_dashboard_settings();
		$this->set_dashboard_sections();
		$this->set_dashboard_fields();

		$this->settings->add_wp_admin_pages( $this->wp_admin_sidebar_menu_item )->with_subpages( 'Dashboard' )->register();
	}

	public function create_wp_admin_sidebar_menu_item() {
		$this->wp_admin_sidebar_menu_item = array(
			array(
				'page_title' => $this->plugin_data['Name'],
				'menu_title' => $this->plugin_data['Name'],
				'capability' => 'manage_options',
				'menu_slug' => $this->plugin_data['TextDomain'],
				'callback' => array( $this->callbacks, 'admin_dashboard' ),
				'icon_url' => 'dashicons-store',
				'position' => 110,
			),
		);
	}

	public function set_dashboard_settings() {
		$args = array(
			array(
				'option_group' => 'ran_plugin_settings',
				'option_name' => 'ran_plugin',
				'callback' => array( $this->callbacks_mngr, 'checkbox_sanitize' ),
			),
		);

		$this->settings->set_settings( $args );
	}

	public function set_dashboard_sections() {
		$args = array(
			array(
				'id' => 'ran_admin_index',
				'title' => 'Settings Manager',
				'callback' => array( $this->callbacks_mngr, 'admin_section_manager' ),
				'page' => 'ran_plugin',
			),
		);

		$this->settings->set_sections( $args );
	}

	public function set_dashboard_fields() {
		$args = array();

		foreach ( $this->feature_managers as $key => $value ) {
			$args[] = array(
				'id' => $key,
				'title' => $value,
				'callback' => array( $this->callbacks_mngr, 'checkbox_field' ),
				'page' => 'ran_plugin',
				'section' => 'ran_admin_index',
				'args' => array(
					'option_name' => 'ran_plugin',
					'label_for' => $key,
					'class' => 'ui-toggle',
				),
			);
		}

		$this->settings->set_fields( $args );
	}
}
