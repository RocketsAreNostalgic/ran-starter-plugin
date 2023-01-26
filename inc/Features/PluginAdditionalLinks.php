<?php

/**
 * @package  RanPlugin
 */

namespace Ran\MyPlugin\Features;

use Ran\PluginLib\AbstractPluginAdditionalLinks;
use Ran\PluginLib\RegistrableInterface;

class PluginAdditionalLinks extends AbstractPluginAdditionalLinks implements RegistrableInterface {

	public function plugin_action_links_callback( array $links ):array {

		$links[] = '<a href="admin.php?page=' . $this->plugin_data['TextDomain'] . '">Settings</a>';
		return $links;
	}

	public function plugin_meta_links_callback( array $plugin_meta, string $plugin_file, array $plugin_data, string $status ):array {

		if ( stripos( $plugin_file, $this->plugin_data['FileName'] ) === false ) {
			return $plugin_meta;
		}

		$plugin_meta[] = '<a href="https://github.com/bnjmnrsh">BnjmnRsh</a>';

		return $plugin_meta;
	}
}
