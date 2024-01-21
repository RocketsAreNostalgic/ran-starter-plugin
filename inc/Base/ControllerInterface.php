<?php
/**
 * Interface for the Controller class.
 *
 * @package  RanPlugin
 */

declare(strict_types = 1);

namespace Ran\StarterPlugin\Base;

/**
 * Service Controllers must implement a register() method in order to be
 * activated by the RegisterService class.
 */
interface ControllerInterface {
	/**
	 * Register the controller.
	 */
	public function register(): void;
}
