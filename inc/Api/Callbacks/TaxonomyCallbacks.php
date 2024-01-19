<?php
/**
 * Class of taxonomy callbacks.
 *
 * @package  RanPlugin
 */

namespace Ran\MyPlugin\Inc\Api\Callbacks;

/**
 * Class of taxonomy callbacks.
 *
 * TODO: add nonce verification.
 *
 * @package  RanPlugin
 */
class TaxonomyCallbacks {

	/**
	 * Create as many Custom Taxonomies as you want.
	 *
	 * @return void
	 */
	public function tax_section_manager() {
		echo 'Create as many Custom Taxonomies as you want.';
	}

	/**
	 * Sanitize the Custom Taxonomies.
	 *
	 * @param mixed $input - The input to sanitize.
	 * @return mixed
	 */
	public function tax_sanitize( $input ) {
		$output = get_option( 'ran_plugin_tax' );

		if ( isset( $_POST['remove'] ) ) {
			unset( $output[ $_POST['remove'] ] );

			return $output;
		}

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
	 * @return void
	 */
	public function text_field( $args ) {
		$name = $args['label_for'];
		$option_name = $args['option_name'];
		$value = '';

		if ( isset( $_POST['edit_taxonomy'] ) ) {
			$input = get_option( $option_name );
			$value = $input[ $_POST['edit_taxonomy'] ][ $name ];
		}

		echo '<input type="text" class="regular-text" id="' . $name . '" name="' . $option_name . '[' . $name . ']" value="' . $value . '" placeholder="' . $args['placeholder'] . '" required>';
	}

	/**
	 * Checkbox field callback.
	 *
	 * @param array $args - The array of arguments.
	 * @return void
	 */
	public function checkbox_field( $args ) {
		$name = $args['label_for'];
		$classes = $args['class'];
		$option_name = $args['option_name'];
		$checked = false;

		if ( isset( $_POST['edit_taxonomy'] ) ) {
			$checkbox = get_option( $option_name );
			$checked = isset( $checkbox[ $_POST['edit_taxonomy'] ][ $name ] ) ?: false;
		}

		echo '<div class="' . \esc_attr( $classes ) . '"><input type="checkbox" id="' . \esc_attr( $name ) . '" name="' . \esc_attr( $option_name ) . '[' . \esc_attr( $name ) . ']" value="1" class="" ' . ( $checked ? 'checked' : '' ) . '><label for="' . esc_attr( $name ) . '"><div></div></label></div>';
	}

	/**
	 *  Checkbox field callback.
	 *
	 * @param mixed $args - The array of arguments.
	 * @return void
	 */
	public function checkbox_post_types_field( $args ) {
		$output = '';
		$name = $args['label_for'];
		$classes = $args['class'];
		$option_name = $args['option_name'];
		$checked = false;

		if ( isset( $_POST['edit_taxonomy'] ) ) {
			$checkbox = get_option( $option_name );
		}

		$post_types = get_post_types( array( 'show_ui' => true ) );

		foreach ( $post_types as $post ) {

			if ( isset( $_POST['edit_taxonomy'] ) ) {
				$checked = isset( $checkbox[ $_POST['edit_taxonomy'] ][ $name ][ $post ] ) ?: false;
			}

			$output .= '<div class="' . \esc_attr( $classes ) . ' mb-10"><input type="checkbox" id="' . \esc_attr( $post ) . '" name="' . \esc_attr( $option_name ) . '[' . \esc_attr( $name ) . '][' . \esc_attr( $post ) . ']" value="1" class="" ' . ( $checked ? 'checked' : '' ) . '><label for="' . \esc_attr( $post ) . '"><div></div></label> <strong>' . \esc_attr( $post ) . '</strong></div>';
		}

		echo \esc_html( $output );
	}
}
