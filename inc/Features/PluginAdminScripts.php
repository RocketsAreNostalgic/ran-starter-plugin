<?php
/**
 * Plugin Admin Scripts
 *
 * @package RanPlugin
 */

declare(strict_types = 1);

namespace Ran\StarterPlugin\Features;

use Ran\StarterPlugin\Base\ControllerInterface;
use Ran\StarterPlugin\Base\Enqueue;

/**
 * Plugin Admin Scripts
 */
class PluginAdminScripts extends Enqueue implements ControllerInterface {

	/**
	 * Register the Admin Scripts.
	 */
	public function register(): void {}

	/**
	 * Enqueue the Admin Scripts.
	 */
	public function enqueue(): void {
		$this->styles[]  = array( 'mypluginstyle', $this->plugin_url . 'assets/mystyle.css' );
		$this->scripts[] = array( 'media-upload' );
		$this->scripts[] = array( 'mypluginscript', $this->plugin_url . 'assets/myscript.js' );
		$this->scripts[] = array( 'test', $this->plugin_url . 'assets/test.js', null, '0.0.1', false );

		// Enqueue all our scripts, styles and media.
		$this->enqueue_scripts( $this->scripts );
		$this->enqueue_styles( $this->styles );
		$this->enqueue_media( $this->media );
	}
}
