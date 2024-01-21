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
	 * @param mixed $input - The input to sanitize.
	 */
	public function tax_sanitize( mixed $input ): mixed {
		$output = get_option( 'ran_plugin_tax' );

		//phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['remove'] ) ) {
			unset( $output[ $_POST['remove'] ] );

			return $output;
		}
		//phpcs:enable

		if ( count( $output ) == 0 ) {
			$output[ $input['taxonomy'] ] = $input;

			return $output;
		}

		foreach ( $output as $key => $value ) {
			if ( $input['taxonomy'] === $key ) {
				$output[ $key ] = $input;
			} else {
				$output[ $input['taxonomy'] ] = $input;
			}
		}

		return $output;
	}

	/**
	 * Text field callback.
	 *
	 * @param mixed $args - The array of arguments.
	 */
	public function text_field( mixed $args ): void {
		$name = $args['label_for'];
		$option_name = $args['option_name'];
		$value = '';

		//phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['edit_taxonomy'] ) ) {
			$input = get_option( $option_name );
			$value = $input[ $_POST['edit_taxonomy'] ][ $name ];
		}
		//phpcs:enable

		echo '<input type="text" class="regular-text" id="' . \esc_attr( $name ) . '" name="' . \esc_attr( $option_name ) . '[' . \esc_attr( $name ) . ']" value="' . \esc_attr( $value ) . '" placeholder="' . \esc_attr( $args['placeholder'] ) . '" required>';
	}

	/**
	 * Checkbox field callback.
	 * TODO: Add nonce verification. See TestimonialController for an example.
	 *
	 * @param array<mixed> $args - The array of arguments.
	 */
	public function checkbox_field( array $args ): void {
		$name = $args['label_for'];
		$classes = $args['class'];
		$option_name = $args['option_name'];
		$checked = false;

		//phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['edit_taxonomy'] ) ) {
			$checkbox = get_option( $option_name );
			$checked = isset( $checkbox[ $_POST['edit_taxonomy'] ][ $name ] ) ?: false; //phpcs:ignore
		}
		// phpcs:enable

		echo '<div class="' . \esc_attr( $classes ) . '"><input type="checkbox" id="' . \esc_attr( $name ) . '" name="' . \esc_attr( $option_name ) . '[' . \esc_attr( $name ) . ']" value="1" class="" ' . ( $checked ? 'checked' : '' ) . '><label for="' . esc_attr( $name ) . '"><div></div></label></div>';
	}

	/**
	 *  Checkbox field callback.
	 *
	 * TODO Add nonce verification. See TestimonialController for an example.
	 *
	 * @param mixed $args - The array of arguments.
	 */
	public function checkbox_post_types_field( mixed $args ): void {
		$output = '';
		$name = $args['label_for'];
		$classes = $args['class'];
		$option_name = $args['option_name'];
		$checked = false;

		//phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['edit_taxonomy'] ) ) {
			$checkbox = get_option( $option_name );
		}
		// phpcs:enable

		$post_types = get_post_types( array( 'show_ui' => true ) );

		foreach ( $post_types as $post ) {
			//phpcs:disable WordPress.Security.NonceVerification.Missing
			if ( isset( $_POST['edit_taxonomy'] ) ) {
				$checked = isset( $checkbox[ $_POST['edit_taxonomy'] ][ $name ][ $post ] ) ?: false; //phpcs:ignore
			}
			// phpcs:enable

			$output .= '<div class="' . \esc_attr( $classes ) . ' mb-10"><input type="checkbox" id="' . \esc_attr( $post ) . '" name="' . \esc_attr( $option_name ) . '[' . \esc_attr( $name ) . '][' . \esc_attr( $post ) . ']" value="1" class="" ' . ( $checked ? 'checked' : '' ) . '><label for="' . \esc_attr( $post ) . '"><div></div></label> <strong>' . \esc_attr( $post ) . '</strong></div>';
		}

		echo \esc_html( $output );
	}
}
