<?php
/**
 * Custom Taxonomy Controller
 *
 * @package  RanPlugin
 */

declare(strict_types = 1);

namespace Ran\StarterPlugin\Features;

use Ran\StarterPlugin\Api\Callbacks\AdminCallbacks;
use Ran\StarterPlugin\Api\Callbacks\TaxonomyCallbacks;
use Ran\StarterPlugin\Base\BaseController;
use Ran\StarterPlugin\Base\ControllerInterface;
use Ran\StarterPlugin\Base\SettingsApi;

/**
 * Custom Taxonomy Controller.
 *
 * @package RanPlugin
 */
class CustomTaxonomyController extends BaseController implements ControllerInterface {
	/**
	 * Settings API.
	 *
	 * @var SettingsApi
	 */
	public SettingsApi $settings;

	/**
	 * Admin callbacks.
	 *
	 * @var AdminCallbacks
	 */
	public AdminCallbacks $callbacks;

	/**
	 * Taxonomy callbacks.
	 *
	 * @var TaxonomyCallbacks
	 */
	public TaxonomyCallbacks $tax_callbacks;

	/**
	 * Subpages array.
	 *
	 * @var array<mixed>
	 */
	public array $subpages = array();

	/**
	 * Taxonomies array.
	 *
	 * @var array<mixed>
	 */
	public array $taxonomies = array();

	/**
	 * Register the optional taxonomy manager.
	 */
	public function register(): void {
		if ( ! $this->activated( 'taxonomy_manager' ) ) {
			return;
		}

		$this->settings      = new SettingsApi();
		$this->callbacks     = new AdminCallbacks( $this->config );
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

	/**
	 * Set Subpages.
	 */
	public function setSubpages(): void {
		$this->subpages = array(
			array(
				'parent_slug' => (string) ( $this->plugin_data['TextDomain'] ?? '' ),
				'page_title' => 'Custom Taxonomies',
				'menu_title' => 'Taxonomy Manager',
				'capability' => 'manage_options',
				'menu_slug' => 'ran_taxonomy',
				'callback' => array( $this->callbacks, 'admin_taxonomy' ),
			),
		);
	}

	/**
	 * Set Settings.
	 */
	public function setSettings(): void {
		$args = array(
			array(
				'option_group' => 'ran_plugin_tax_settings',
				'option_name' => 'ran_plugin_tax',
				'callback' => array( $this->tax_callbacks, 'tax_sanitize' ),
			),
		);

		$this->settings->setSettings( $args );
	}

	/**
	 * Set Sections.
	 */
	public function setSections(): void {
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

	/**
	 * Set Fields.
	 */
	public function setFields(): void {
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

	/**
	 * Store Custom Taxonomies.
	 */
	public function storeCustomTaxonomies(): void {
		$options = get_option( 'ran_plugin_tax', array() );
		if ( ! is_array( $options ) ) {
			$options = array();
		}

		foreach ( $options as $option ) {
			if ( ! is_array( $option ) ) {
				continue;
			}

			$singular = (string) ( $option['singular_name'] ?? '' );
			$taxonomy = (string) ( $option['taxonomy'] ?? '' );
			if ( '' === $taxonomy ) {
				continue;
			}

			$labels = array(
				'name'              => $singular,
				'singular_name'     => $singular,
				'search_items'      => 'Search ' . $singular,
				'all_items'         => 'All ' . $singular,
				'parent_item'       => 'Parent ' . $singular,
				'parent_item_colon' => 'Parent ' . $singular . ':',
				'edit_item'         => 'Edit ' . $singular,
				'update_item'       => 'Update ' . $singular,
				'add_new_item'      => 'Add New ' . $singular,
				'new_item_name'     => 'New ' . $singular . ' Name',
				'menu_name'         => $singular,
			);

			$this->taxonomies[] = array(
				'hierarchical'      => isset( $option['hierarchical'] ),
				'labels'            => $labels,
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'show_in_rest'      => true,
				'rewrite'           => array( 'slug' => $taxonomy ),
				'objects'           => isset( $option['objects'] ) && is_array( $option['objects'] ) ? $option['objects'] : array(),
			);
		}
	}

	/**
	 * Register Custom Taxonomy.
	 */
	public function registerCustomTaxonomy(): void {
		foreach ( $this->taxonomies as $taxonomy ) {
			$objects = isset( $taxonomy['objects'] ) && is_array( $taxonomy['objects'] ) ? array_keys( $taxonomy['objects'] ) : array();
			$rewrite = isset( $taxonomy['rewrite'] ) && is_array( $taxonomy['rewrite'] ) ? $taxonomy['rewrite'] : array();
			$slug    = (string) ( $rewrite['slug'] ?? '' );
			if ( '' === $slug ) {
				continue;
			}
			register_taxonomy( $slug, $objects, $taxonomy );
		}
	}
}
