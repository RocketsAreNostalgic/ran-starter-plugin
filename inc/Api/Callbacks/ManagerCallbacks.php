<?php
/**
 * A class containing all of our callbacks for the admin area.
 *
 * @package  RanPlugin
 */

declare(strict_types = 1);

namespace Ran\MyPlugin\Api\Callbacks;

/**
 * A class containing all of our callbacks for the admin area.
 *
 * @package Ran\MyPlugin\Api\Callbacks
 */
class ManagerCallbacks {

	/**
	 * Array of plugin details.
	 */
	public function admin_section_manager(): void {
		echo 'Manage the Sections and Features of this Plugin by activating the checkboxes from the following list.';
	}

	/**
	 * Checkbox sanitization.
	 *
	 * @param mixed $input - The input.
	 * @return array<bool>
	 */
	public function checkbox_sanitize( mixed $input ): array {
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
	 */
	public function checkbox_field( mixed $args ): void {
		$name = $args['label_for'];
		$classes = $args['class'];
		$option_name = $args['option_name'];
		$checkbox = get_option( $option_name );
		$checked = isset( $checkbox[ $name ] ) ? ( $checkbox[ $name ] ? true : false ) : false;

		echo '<div class="' . \esc_attr( $classes ) . '"><input type="checkbox" id="' . \esc_attr( $name ) . '" name="' . \esc_attr( $option_name ) . '[' . \esc_attr( $name ) . ']" value="1" class="" ' . ( $checked ? 'checked' : '' ) . '><label for="' . \esc_attr( $name ) . '"><div></div></label></div>';
	}
}
