<?php
/**
 * Enqueue class.
 *
 * @package  RanPlugin
 */

declare(strict_types = 1);

namespace Ran\StarterPlugin\Base;

/**
 * This class is meant to be extended and instantiated via the RegisterServices Class.
 */
class Enqueue extends BaseController implements ControllerInterface {

	/**
	 * Styles to enqueue.
	 *
	 * @var array<string>
	 */
	public array $styles = array();

	/**
	 * Scripts to enqueue.
	 *
	 * @var array<string>
	 */
	public array $scripts = array();


	/**
	 * Media to enqueue.
	 *
	 * @var array<string>
	 */
	public array $media = array();

	/**
	 * Our registration function to add action hooks to WP
	 */
	public function register(): void {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	/**
	 * A chain-able call to enqueue all our scripts, styles and media.
	 *
	 * @param  array<mixed> $scripts - The array of scripts.
	 */
	public function enqueue_scripts( array $scripts ): Enqueue {
		foreach ( $scripts as $script ) {
			wp_enqueue_script( ...$script );
		}
		return $this;
	}

	/**
	 * A chain-able call to enqueue all our styles.
	 *
	 * @param  array<mixed> $styles - The array of styles.
	 */
	public function enqueue_styles( array $styles ): Enqueue {
		foreach ( $styles as $style ) {
			wp_enqueue_style( ...$style );
		}

		return $this;
	}

	/**
	 * A chain-able call to enqueue all our media.
	 *
	 * @param  array<mixed> $media - An array of media.
	 */
	public function enqueue_media( array $media ): Enqueue {
		foreach ( $media as $args ) {
			wp_enqueue_media( $args );
		}

		return $this;
	}
	/**
	 * Enqueue all our scripts, styles and media.
	 */
	public function enqueue(): void {
		$this->enqueue_scripts( $this->scripts );
		$this->enqueue_styles( $this->styles );
		$this->enqueue_media( $this->media );
	}
}
