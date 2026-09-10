<?php
/**
 * Class of taxonomy callbacks.
 *
 * @package  RanPlugin
 */

declare(strict_types = 1);

namespace Ran\StarterPlugin\Api\Callbacks;

/**
 * Class of taxonomy callbacks.
 *
 * TODO add nonce verification. See TestimonialController for an example.
 *
 * @package  RanPlugin
 */
class TaxonomyCallbacks {
	/**
	 * Create as many Custom Taxonomies as you want.
	 */
	public function tax_section_manager(): void {
		echo 'Create as many Custom Taxonomies as you want.';
	}

	/**
	 * Sanitize the Custom Taxonomies.
	 *
	 * TODO add nonce verification. See TestimonialController for an example.
	 *
	 * @param mixed $input The input to sanitize.
	 * @return array<mixed>
	 */
	public function tax_sanitize( mixed $input ): array {
		$output = get_option( 'ran_plugin_tax', array() );
		$output = is_array( $output ) ? $output : array();
		$input  = is_array( $input ) ? $input : array();

		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['remove'] ) ) {
			$remove_key = sanitize_text_field( wp_unslash( $_POST['remove'] ) );
			unset( $output[ $remove_key ] );

			return $output;
		}
		// phpcs:enable

		$taxonomy = isset( $input['taxonomy'] ) ? (string) $input['taxonomy'] : '';
		if ( '' === $taxonomy ) {
			return $output;
		}

		$output[ $taxonomy ] = $input;

		return $output;
	}

	/**
	 * Text field callback.
	 *
	 * @param mixed $args The array of arguments.
	 */
	public function text_field( mixed $args ): void {
		$name        = $args['label_for'];
		$option_name = $args['option_name'];
		$value       = '';

		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['edit_taxonomy'] ) ) {
			$input         = get_option( $option_name, array() );
			$input         = is_array( $input ) ? $input : array();
			$edit_taxonomy = sanitize_text_field( wp_unslash( $_POST['edit_taxonomy'] ) );
			$value         = $input[ $edit_taxonomy ][ $name ] ?? '';
		}
		// phpcs:enable

		echo '<input type="text" class="regular-text" id="' . \esc_attr( $name ) . '" name="' . \esc_attr( $option_name ) . '[' . \esc_attr( $name ) . ']" value="' . \esc_attr( $value ) . '" placeholder="' . \esc_attr( $args['placeholder'] ) . '" required>';
	}

	/**
	 * Checkbox field callback.
	 * TODO: Add nonce verification. See TestimonialController for an example.
	 *
	 * @param array<mixed> $args The array of arguments.
	 */
	public function checkbox_field( array $args ): void {
		$name        = $args['label_for'];
		$classes     = $args['class'];
		$option_name = $args['option_name'];
		$checked     = false;

		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['edit_taxonomy'] ) ) {
			$checkbox      = get_option( $option_name, array() );
			$checkbox      = is_array( $checkbox ) ? $checkbox : array();
			$edit_taxonomy = sanitize_text_field( wp_unslash( $_POST['edit_taxonomy'] ) );
			$checked       = isset( $checkbox[ $edit_taxonomy ][ $name ] );
		}
		// phpcs:enable

		echo '<div class="' . \esc_attr( $classes ) . '"><input type="checkbox" id="' . \esc_attr( $name ) . '" name="' . \esc_attr( $option_name ) . '[' . \esc_attr( $name ) . ']" value="1" class="" ' . ( $checked ? 'checked' : '' ) . '><label for="' . esc_attr( $name ) . '"><div></div></label></div>';
	}

	/**
	 * Checkbox post-types field callback.
	 *
	 * TODO Add nonce verification. See TestimonialController for an example.
	 *
	 * @param mixed $args The array of arguments.
	 */
	public function checkbox_post_types_field( mixed $args ): void {
		$name          = $args['label_for'];
		$classes       = $args['class'];
		$option_name   = $args['option_name'];
		$checkbox      = array();
		$edit_taxonomy = '';

		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['edit_taxonomy'] ) ) {
			$stored        = get_option( $option_name, array() );
			$checkbox      = is_array( $stored ) ? $stored : array();
			$edit_taxonomy = sanitize_text_field( wp_unslash( $_POST['edit_taxonomy'] ) );
		}
		// phpcs:enable

		$post_types = get_post_types( array( 'show_ui' => true ) );

		foreach ( $post_types as $post ) {
			$checked = '' !== $edit_taxonomy && isset( $checkbox[ $edit_taxonomy ][ $name ][ $post ] );
			echo '<div class="' . \esc_attr( $classes ) . ' mb-10"><input type="checkbox" id="' . \esc_attr( $post ) . '" name="' . \esc_attr( $option_name ) . '[' . \esc_attr( $name ) . '][' . \esc_attr( $post ) . ']" value="1" class="" ' . ( $checked ? 'checked' : '' ) . '><label for="' . \esc_attr( $post ) . '"><div></div></label> <strong>' . \esc_html( $post ) . '</strong></div>';
		}
	}
}
