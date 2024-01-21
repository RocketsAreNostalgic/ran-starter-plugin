<?php
/**
 * Custom Post Types Callbacks.
 *
 * @package  RanPlugin
 */

declare(strict_types = 1);

namespace Ran\MyPlugin\Api\Callbacks;

/**
 * Custom Post Types Callbacks.
 *
 * TODO add nonce verification. See TestimonialController for an example.
 */
class CptCallbacks {


	/**
	 * Create as many Custom Post Types as you want.
	 */
	public function cpt_section_manager(): void {
		echo 'Create as many Custom Post Types as you want.';
	}

	/**
	 * Sanitize the Custom Post Types.
	 *
	 * TODO add nonce verification. See TestimonialController for an example.
	 *
	 * @param  array<mixed> $input - the input array to sanitize.
	 *
	 * @return array<mixed> - The sanitized array.
	 */
	public function cpt_sanitize( array $input ): array {

		$output = get_option( 'ran_plugin_cpt' );

		//phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['remove'] ) ) {
			unset( $output[ $_POST['remove'] ] );
			return $output;
		}
		//phpcs:enable

		if ( count( $output ) == 0 ) {
			$output[ $input['post_type'] ] = $input;

			return $output;
		}

		foreach ( $output as $key => $value ) {
			if ( $input['post_type'] === $key ) {
				$output[ $key ] = $input;
			} else {
				$output[ $input['post_type'] ] = $input;
			}
		}

		return $output;
	}

	/**
	 * Create a new Custom Post Type.
	 *
	 * TODO add nonce verification. See TestimonialController for an example.
	 *
	 * @param mixed $args - Arguments create a CPT text field.
	 */
	public function text_field( mixed $args ): void {
		$name = $args['label_for'];
		$option_name = $args['option_name'];
		$value = '';
		//phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['edit_post'] ) ) {
			$input = get_option( $option_name );
			$value = $input[ $_POST['edit_post'] ][ $name ];
		}
		//phpcs:enable

		echo '<input type="text" class="regular-text" id="' . \esc_attr( $name ) . '" name="' . \esc_attr( $option_name ) . '[' . \esc_attr( $name ) . ']" value="' . \esc_attr( $value ) . '" placeholder="' . \esc_attr( $args['placeholder'] ) . '" required>';
	}

	/**
	 * Create checkbox field.
	 *
	 * TODO Add nonce verification. See TestimonialController for an example.
	 *
	 * @param  array<string|bool> $args - An array of checkbox arguments.
	 */
	public function checkbox_field( array $args ): void {
		$name = $args['label_for'];
		$classes = $args['class'];
		$option_name = $args['option_name'];
		$checked = false;
		//phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['edit_post'] ) ) {
			$checkbox = get_option( $option_name );
			$checked = isset( $checkbox[ $_POST['edit_post'] ][ $name ] ) ?: false; //phpcs:ignore
		}
		// phpcs:enable

		echo '<div class="' . \esc_attr( $classes ) . '"><input type="checkbox" id="' . \esc_attr( $name ) . '" name="' . \esc_attr( $option_name ) . '[' . \esc_attr( $name ) . ']" value="1" class="" ' . ( $checked ? 'checked' : '' ) . '><label for="' . \esc_attr( $name ) . '"><div></div></label></div>';
	}
}
