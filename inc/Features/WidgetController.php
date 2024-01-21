<?php
/**
 * Class of WidgetController.
 *
 * @package  RanPlugin
 */

declare(strict_types = 1);

namespace Ran\MyPlugin\Features;

use Ran\MyPlugin\Api\Widgets\MediaWidget;
use Ran\MyPlugin\Base\BaseController;
use Ran\MyPlugin\Base\ControllerInterface;

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
