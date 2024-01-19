<?php

/**
 * Custom Taxonomy Controller
 *
 * @package  RanPlugin
 */

namespace Ran\MyPlugin\Inc\Services;

use Ran\MyPlugin\Inc\Api\Callbacks\AdminCallbacks;
use Ran\MyPlugin\Inc\Api\Callbacks\TaxonomyCallbacks;
use Ran\MyPlugin\Inc\Base\BaseController;
use Ran\MyPlugin\Inc\Base\ControllerInterface;
use Ran\MyPlugin\Inc\Base\SettingsApi;

/**
 * Custom Taxonomy Controller
 *
 * @package RanPlugin
 */
class CustomTaxonomyController extends BaseController implements ControllerInterface {

	public $settings;

	public $callbacks;

	public $tax_callbacks;

	public $subpages = array();

	public $taxonomies = array();

	/**
	 * Our registration funtion to add action hooks to WP
	 *
	 * @return null
	 */
	public function register(): void {
		if ( ! $this->activated( 'taxonomy_manager' ) ) {
			return;
		}

		$this->settings = new SettingsApi();

		$this->callbacks = new AdminCallbacks();

		$this->tax_callbacks = new TaxonomyCallbacks();

		$this->setSubpages();

		$this->setSettings();

		$this->setSections();

		$this->setFields();

		$this->settings->addSubPages( $this->subpages )->register();

		$this->storeCustomTaxonomies();

		if ( ! empty( $this->taxonomies ) ) {
			add_action( 'init', array( $this, 'registerCustomTaxonomy' ) );
		}
	}

	public function setSubpages() {
		$this->subpages = array(
			array(
				'parent_slug' => 'ran_plugin',
				'page_title' => 'Custom Taxonomies',
				'menu_title' => 'Taxonomy Manager',
				'capability' => 'manage_options',
				'menu_slug' => 'ran_taxonomy',
				'callback' => array( $this->callbacks, 'adminTaxonomy' ),
			),
		);
	}

	public function setSettings() {
		$args = array(
			array(
				'option_group' => 'ran_plugin_tax_settings',
				'option_name' => 'ran_plugin_tax',
				'callback' => array( $this->tax_callbacks, 'tax_sanitize' ),
			),
		);

		$this->settings->setSettings( $args );
	}

	public function setSections() {
		$args = array(
			array(
				'id' => 'ran_tax_index',
				'title' => 'Custom Taxonomy Manager',
				'callback' => array( $this->tax_callbacks, 'tax_section_manager' ),
				'page' => 'ran_taxonomy',
			),
		);

		$this->settings->setSections( $args );
	}

	public function setFields() {
		$args = array(
			array(
				'id' => 'taxonomy',
				'title' => 'Custom Taxonomy ID',
				'callback' => array( $this->tax_callbacks, 'text_field' ),
				'page' => 'ran_taxonomy',
				'section' => 'ran_tax_index',
				'args' => array(
					'option_name' => 'ran_plugin_tax',
					'label_for' => 'taxonomy',
					'placeholder' => 'eg. genre',
					'array' => 'taxonomy',
				),
			),
			array(
				'id' => 'singular_name',
				'title' => 'Singular Name',
				'callback' => array( $this->tax_callbacks, 'text_field' ),
				'page' => 'ran_taxonomy',
				'section' => 'ran_tax_index',
				'args' => array(
					'option_name' => 'ran_plugin_tax',
					'label_for' => 'singular_name',
					'placeholder' => 'eg. Genre',
					'array' => 'taxonomy',
				),
			),
			array(
				'id' => 'hierarchical',
				'title' => 'Hierarchical',
				'callback' => array( $this->tax_callbacks, 'checkbox_field' ),
				'page' => 'ran_taxonomy',
				'section' => 'ran_tax_index',
				'args' => array(
					'option_name' => 'ran_plugin_tax',
					'label_for' => 'hierarchical',
					'class' => 'ui-toggle',
					'array' => 'taxonomy',
				),
			),
			array(
				'id' => 'objects',
				'title' => 'Post Types',
				'callback' => array( $this->tax_callbacks, 'checkbox_post_types_field' ),
				'page' => 'ran_taxonomy',
				'section' => 'ran_tax_index',
				'args' => array(
					'option_name' => 'ran_plugin_tax',
					'label_for' => 'objects',
					'class' => 'ui-toggle',
					'array' => 'taxonomy',
				),
			),
		);

		$this->settings->setFields( $args );
	}

	public function storeCustomTaxonomies() {
		$options = get_option( 'ran_plugin_tax' ) ?: array();

		foreach ( $options as $option ) {
			$labels = array(
				'name'              => $option['singular_name'],
				'singular_name'     => $option['singular_name'],
				'search_items'      => 'Search ' . $option['singular_name'],
				'all_items'         => 'All ' . $option['singular_name'],
				'parent_item'       => 'Parent ' . $option['singular_name'],
				'parent_item_colon' => 'Parent ' . $option['singular_name'] . ':',
				'edit_item'         => 'Edit ' . $option['singular_name'],
				'update_item'       => 'Update ' . $option['singular_name'],
				'add_new_item'      => 'Add New ' . $option['singular_name'],
				'new_item_name'     => 'New ' . $option['singular_name'] . ' Name',
				'menu_name'         => $option['singular_name'],
			);

			$this->taxonomies[] = array(
				'hierarchical'      => isset( $option['hierarchical'] ) ? true : false,
				'labels'            => $labels,
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'show_in_rest'        => true,
				'rewrite'           => array( 'slug' => $option['taxonomy'] ),
				'objects'           => isset( $option['objects'] ) ? $option['objects'] : null,
			);
		}
	}

	public function registerCustomTaxonomy() {
		foreach ( $this->taxonomies as $taxonomy ) {
			$objects = isset( $taxonomy['objects'] ) ? array_keys( $taxonomy['objects'] ) : null;
			register_taxonomy( $taxonomy['rewrite']['slug'], $objects, $taxonomy );
		}
	}
}
