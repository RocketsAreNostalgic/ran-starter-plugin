<?php
/**
 * Class of TestimonialCallbacks.
 *
 * @package  RanPlugin
 */

declare(strict_types = 1);

namespace Ran\StarterPlugin\Api\Callbacks;

use Ran\StarterPlugin\Base\BaseController;

/**
 * TestimonialCallbacks Class
 */
class TestimonialCallbacks extends BaseController {
	/**
	 * Our registration function to add action hooks to WP.
	 */
	public function shortcodePage(): string|bool {
		return require_once "$this->plugin_path/templates/testimonial.php";
	}
}
