<?php

/**
 * @package  RanPlugin
 */

namespace Ran\MyPlugin\Features;

use Ran\MyPlugin\Api\Callbacks\AdminCallbacks;
use Ran\MyPlugin\Api\Callbacks\ManagerCallbacks;
use Ran\MyPlugin\Base\SettingsApi;
use Ran\PluginLib\AbstractFeatureController;
use Ran\PluginLib\RegistrableInterface;

/**
 * An example Service Controller
 */
class ExampleFeatureController extends AbstractFeatureController implements RegistrableInterface {


	public AdminCallbacks $callbacks;

	public SettingsApi $settings;

	public array $feature_subpage = array( 'ExampleFeatureController' => 'An Example Feature Controller Subpage.' );
	public $subpages = array();

	/**
	 * Our registration function to add action hooks to WP
	 *
	 * @return null
	 */
	public function register(): void {

		if ( ! $this->activated( key( $this->feature_subpage ) ) ) {

			update_option( $this->plugin_data['PluginOption'], $this->feature_subpage );
			return;
		}

		$this->settings = new SettingsApi();
		$this->callbacks = new AdminCallbacks( $this->plugin );

		$this->set_subpages();

		$this->settings->add_subpages( $this->subpages )->register();
	}

	public function set_subpages() {
		$this->subpages = array(
			array(
				'parent_slug' => $this->plugin_data['TextDomain'],
				'page_title' => 'Example Manager',
				'menu_title' => 'Example Manager',
				'capability' => 'manage_options',
				'menu_slug' => 'ran_example',
				'callback' => array( $this->callbacks, 'example_feature' ),
			),
		);
	}
}
