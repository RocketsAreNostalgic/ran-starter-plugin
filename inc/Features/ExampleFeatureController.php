<?php
/**
 * An example FeatureController using the current ran/plugin-lib Features API.
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

/**
 * An example feature controller.
 */
class ExampleFeatureController extends FeatureControllerAbstract implements RegistrableFeatureInterface {
	/**
	 * Admin callbacks.
	 *
	 * @var AdminCallbacks
	 */
	public AdminCallbacks $callbacks;

	/**
	 * Settings API.
	 *
	 * @var SettingsApi
	 */
	public SettingsApi $settings;

	/**
	 * The settings page key and description.
	 *
	 * @var array<string, array{0: string, 1: array<int, string>}>
	 */
	public array $feature_settings_page = array(
		'ExampleFeatureController' => array(
			'An Example Feature Controller Subpage.',
			array( 'example_feature' ),
		),
	);

	/**
	 * Subpages array.
	 *
	 * @var array<mixed>
	 */
	public array $subpages = array();

	/**
	 * Initialize the optional example feature.
	 */
	public function init(): ExampleFeatureController|false {
		$feature_key = array_key_first( $this->feature_settings_page );
		if ( null === $feature_key ) {
			return false;
		}

		if ( ! $this->is_activated( $feature_key ) ) {
			$option_name = $this->config->get_options_key();
			$options     = get_option( $option_name, array() );

			if ( is_array( $options ) && ! array_key_exists( $feature_key, $options ) ) {
				$options[ $feature_key ] = false;
				update_option( $option_name, $options );
			}

			return false;
		}

		$this->settings  = new SettingsApi();
		$this->callbacks = new AdminCallbacks( $this->config );

		$this->set_subpages();
		$this->settings->add_subpages( $this->subpages )->init();

		return $this;
	}

	/**
	 * Configure the feature's admin subpage.
	 */
	public function set_subpages(): void {
		$feature_key = array_key_first( $this->feature_settings_page );
		if ( null === $feature_key ) {
			$this->subpages = array();
			return;
		}

		$this->subpages = array(
			array(
				'parent_slug' => (string) ( $this->config_array['TextDomain'] ?? '' ),
				'page_title' => $feature_key,
				'menu_title' => $feature_key,
				'capability' => 'manage_options',
				'menu_slug' => $feature_key,
				'callback' => array( $this->callbacks, 'example_feature' ),
			),
		);
	}
}
