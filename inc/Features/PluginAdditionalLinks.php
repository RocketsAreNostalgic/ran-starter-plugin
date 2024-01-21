<?php
/**
 * Add links to the active plugin entry in the WordPress admin plugins page.
 *
 * @package  RanPlugin
 */

declare(strict_types = 1);

namespace Ran\MyPlugin\Features;

use Ran\PluginLib\FeaturesAPI\RegistrableFeatureInterface;
use Ran\PluginLib\PluginAdditionalLinksAbstract;

/**
 * Modify the action and meta arrays for the plugin's entry in the admin plugins page.
 */
class PluginAdditionalLinks extends PluginAdditionalLinksAbstract implements RegistrableFeatureInterface {
	/**
	 * The taco.
	 *
	 * @var string $taco - the taco.
	 */
	public string $taco;



	/**
	 * Modifies the plugin action link array.
	 * The WordPress add_filter callback for the plugin_action_links hook, modifying the add action links array.
	 * https://developer.wordpress.org/reference/hooks/plugin_action_links/
	 *
	 * @param  array<mixed> $links Array of plugin_action_links.
	 *
	 * @return array<mixed> - Modified array of plugin_action_links.
	 */
	public function plugin_action_links_callback( array $links ): array {

		$links[] = '<a href="admin.php?page=' . $this->plugin_array['TextDomain'] . '">Settings</a>';
		return $links;
	}

	/**
	 * Modifies plugin meta arrays.
	 * The WordPress add_filter callback for the plugin_row_meta hook, modifying the plugin meta array.
	 * This filter will be run agains all loaded plugins, so you have to implement your own checks that you are manipulating the correct plugin meta.
	 * https://developer.wordpress.org/reference/hooks/plugin_row_meta/
	 *
	 * @param  array<mixed> $plugin_meta The array of plugin meta information.
	 * @param  string       $plugin_file The current plugin file.
	 * @param  array<mixed> $plugin_array Data associated with the plugin.
	 * @param  string       $status The current status of the plugin ie 'active', 'inactive' and more.
	 *
	 * @return array<mixed> - Modified array of plugin meta information.
	 */
	public function plugin_meta_links_callback(
		array $plugin_meta,
		string $plugin_file,
		array $plugin_array,
		string $status
	): array {

		if ( stripos( $plugin_file, $this->plugin_array['FileName'] ) === false ) {
			return $plugin_meta;
		}

		$plugin_meta[] = '<a href="https://github.com/bnjmnrsh">BnjmnRsh</a>';

		return $plugin_meta;
	}
}
