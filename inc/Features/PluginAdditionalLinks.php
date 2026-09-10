<?php
/**
 * Add links to the active plugin entry in the WordPress admin plugins page.
 *
 * @package  RanPlugin
 */

declare(strict_types = 1);

namespace Ran\StarterPlugin\Features;

use Ran\PluginLib\FeaturesAPI\RegistrableFeatureInterface;
use Ran\PluginLib\PluginAdditionalLinksAbstract;

/**
 * Modify the action and meta arrays for the plugin's entry in the admin plugins page.
 */
class PluginAdditionalLinks extends PluginAdditionalLinksAbstract implements RegistrableFeatureInterface {
	/**
	 * Register plugin-row filters using the current config contract.
	 */
	public function init(): PluginAdditionalLinks {
		$basename = (string) ( $this->config_array['Basename'] ?? '' );
		if ( '' !== $basename ) {
			add_filter( 'plugin_action_links_' . $basename, array( $this, 'plugin_action_links_callback' ) );
		}
		add_filter( 'plugin_row_meta', array( $this, 'plugin_meta_links_callback' ), 10, 4 );

		return $this;
	}

	/**
	 * Modifies the plugin action link array.
	 *
	 * @param array<mixed> $links Array of plugin action links.
	 * @return array<mixed> Modified array of plugin action links.
	 */
	public function plugin_action_links_callback( array $links ): array {
		$text_domain = (string) ( $this->config_array['TextDomain'] ?? '' );
		$links[]     = '<a href="admin.php?page=' . esc_attr( $text_domain ) . '">Settings</a>';

		return $links;
	}

	/**
	 * Modifies plugin meta arrays.
	 *
	 * @param array<mixed> $plugin_meta Plugin meta information.
	 * @param string       $plugin_file Current plugin file.
	 * @param array<mixed> $plugin_data Data associated with the plugin.
	 * @param string       $status      Current plugin status.
	 * @return array<mixed> Modified plugin meta information.
	 */
	public function plugin_meta_links_callback(
		array $plugin_meta,
		string $plugin_file,
		array $plugin_data,
		string $status
	): array {
		$basename = (string) ( $this->config_array['Basename'] ?? '' );
		if ( '' === $basename || false === stripos( $plugin_file, $basename ) ) {
			return $plugin_meta;
		}

		$plugin_meta[] = '<a href="https://github.com/bnjmnrsh">BnjmnRsh</a>';

		return $plugin_meta;
	}
}
