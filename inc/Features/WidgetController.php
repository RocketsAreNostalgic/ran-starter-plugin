<?php
/**
 * Class of WidgetController.
 *
 * @package  RanPlugin
 */

declare(strict_types = 1);

namespace Ran\StarterPlugin\Features;

use Ran\StarterPlugin\Api\Widgets\MediaWidget;
use Ran\StarterPlugin\Base\BaseController;
use Ran\StarterPlugin\Base\ControllerInterface;

/**
 * WidgetController implements the ControllerInterface.
 */
class WidgetController extends BaseController implements ControllerInterface {

	/**
	 * Our registration function to add action hooks to WP.
	 *
	 * @return null
	 */
	public function register(): void {
		if ( ! $this->activated( 'media_widget' ) ) {
			return;
		}

		$media_widget = new MediaWidget();
		$media_widget->register();
	}
}
