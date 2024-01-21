<?php
/**
 * AuthController Class extends BaseController.
 *
 * @package  RanPlugin
 */

 declare(strict_types = 1);

namespace Ran\MyPlugin\Features;

use Ran\MyPlugin\Base\BaseController;
use Ran\MyPlugin\Base\ControllerInterface;

/**
 * AuthController Class
 */
class AuthController extends BaseController implements ControllerInterface {

	/**
	 * Our registration function to add action hooks to WP
	 *
	 * @return null
	 */
	public function register(): void {
		if ( ! $this->activated( 'login_manager' ) ) {
			return;
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
		add_action( 'wp_head', array( $this, 'load_auth_template' ) );
	}

	/**
	 * Enqueue the auth style and script.
	 */
	public function enqueue(): void {
		wp_enqueue_style( 'authstyle', $this->plugin_url . 'assets/auth.css' );
		wp_enqueue_script( 'authscript', $this->plugin_url . 'assets/auth.js' );
	}

	/**
	 * Load the auth template if the user is not logged in.
	 */
	public function load_auth_template(): void {
		if ( is_user_logged_in() ) {
			return;
		}

		$file = $this->plugin_path . 'templates/auth.php';

		if ( file_exists( $file ) ) {
			load_template( $file, true );
		}
	}
}
