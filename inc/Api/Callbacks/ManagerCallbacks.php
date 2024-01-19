<?php
/**
 * A class containing all of our callbacks for the admin area.
 *
 * @package  RanPlugin
 */

namespace Ran\MyPlugin\Api\Callbacks;

/**
 * A class containing all of our callbacks for the admin area.
 *
 * @package Ran\MyPlugin\Api\Callbacks
 */
class ManagerCallbacks {

	/**
	 * Array of plugin details.
	 *
	 * @return void
	 */
	public function admin_section_manager() {
		echo 'Manage the Sections and Features of this Plugin by activating the checkboxes from the following list.';
	}

	/**
	 * Checkbox sanitization.
	 *
	 * @param mixed $input - The input.
	 * @return bool[]
	 */
	public function checkbox_sanitize( $input ) {
		$output = array();

		// Loop through our checkbox array and create an array of sanitized values.
		foreach ( $this as $key => $value ) {
			$output[ $key ] = isset( $input[ $key ] ) ? true : false;
		}

		return $output;
	}

	/**
	 * Checkbox field.
	 *
	 * @param mixed $args - Array of arguments for checkboxes.
	 * @return void
	 */
	public function checkbox_field( $args ) {
		$name = $args['label_for'];
		$classes = $args['class'];
		$option_name = $args['option_name'];
		$checkbox = get_option( $option_name );
		$checked = isset( $checkbox[ $name ] ) ? ( $checkbox[ $name ] ? true : false ) : false;

		echo '<div class="' . \esc_attr( $classes ) . '"><input type="checkbox" id="' . \esc_attr( $name ) . '" name="' . \esc_attr( $option_name ) . '[' . \esc_attr( $name ) . ']" value="1" class="" ' . ( $checked ? 'checked' : '' ) . '><label for="' . \esc_attr( $name ) . '"><div></div></label></div>';
	}
}
