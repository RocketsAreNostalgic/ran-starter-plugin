<?php

/**
 * @package  RanPlugin
 */

namespace Ran\MyPlugin\Api\Callbacks;

class AdminCallbacks {

	private object $plugin;
	private array $plugin_data = array();

	public function __construct( object $plugin ) {

		$this->plugin = $plugin;
		$this->plugin_data = $plugin->get_plugin();
	}

	public function admin_dashboard() {

		return require_once( $this->plugin_data['PATH'] . '/templates/features/admin.php' );
	}

	public function example_feature() {
		return require_once( $this->plugin_data['PATH'] . '/templates/features/example-feature.php' );
	}
}
