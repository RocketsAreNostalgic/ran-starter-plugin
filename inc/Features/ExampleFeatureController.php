<?php
/**
 * An example FeatureController using several Accessories provided by the @ran\plugin-lib.
 *
 * @author bnjmnrsh <bnjmnrsh@gmail.com>
 * @package  RanPlugin
 */

declare(strict_types = 1);

namespace Ran\StarterPlugin\Features;

use Ran\StarterPlugin\Api\Callbacks\AdminCallbacks;
use Ran\StarterPlugin\Base\SettingsApi;
use Ran\PluginLib\FeaturesAPI\FeatureControllerAbstract;
use Ran\PluginLib\FeaturesAPI\RegistrableFeatureInterface;
use Ran\PluginLib\TestAccessory\TestAccessory;

/**
 * An example Service Controller
 */
class ExampleFeatureController extends FeatureControllerAbstract implements RegistrableFeatureInterface, TestAccessory {

	/**
	 *
	 * AdminCallbacks.
	 *
	 * @var mixed
	 */
	public AdminCallbacks $callbacks;

	/**
	 * SettingsApi settings.
	 *
	 * @var mixed
	 */
	public SettingsApi $settings;

	/**
	 * The settings page key and description.
	 * The key is use for slugs and database entries, the value is used in
	 *
	 * @var array<mixed>
	 */
	public array $feature_settings_page = array(
		array(
			'ExampleFeatureController' => array(
				'An Example Feature Controller Subpage.',
				array(
					'example_feature',
				),
			),
		),
	);

	/**
	 * Subpages array
	 *
	 * @var array<mixed>
	 */
	public array $subpages = array();

	/**
	 * Our registration function to add action hooks to WP
	 *
	 * @return null
	 */
	public function init(): ExampleFeatureController|false {

		if ( ! $this->is_activated( key( $this->feature_settings_page ) ) ) {
			update_option( $this->plugin_array['PluginOption'], $this->feature_settings_page );
			return false;
		}

		$this->settings = new SettingsApi();
		$this->callbacks = new AdminCallbacks( $this->plugin );

		$this->set_subpages();

		$this->settings->add_subpages( $this->subpages )->init();

		return $this;
	}

	/**
	 * Undocumented function
	 */
	public function set_subpages(): void {
		$this->subpages = array(
			array(
				'parent_slug' => $this->plugin_data['TextDomain'],
				'page_title' => array_key_last( $this->feature_settings_page ),
				'menu_title' => array_key_last( $this->feature_settings_page ),
				'capability' => 'manage_options',
				'menu_slug' => array_key_first( $this->feature_settings_page ),
				'callback' => array( $this->callbacks, 'example_feature' ),
			),
		);
	}

	/**
	 * Test so far...
	 *
	 * @param string $string - A string to test.
	 */
	public function test( string $string = 'test' ): string {
		return $string;
	}
}
