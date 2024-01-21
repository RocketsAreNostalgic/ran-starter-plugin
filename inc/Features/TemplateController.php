<?php
/**
 * Template Controller
 *
 * @package  RanPlugin
 */

 declare(strict_types = 1);

namespace Ran\MyPlugin\Features;

use Ran\MyPlugin\Base\BaseController;
use Ran\MyPlugin\Base\ControllerInterface;

/**
 * Template Controller
 */
class TemplateController extends BaseController implements ControllerInterface {
	/**
	 * The public templates variable.
	 *
	 * @var array<mixed> - An array of templates.
	 */
	public array $templates;

	/**
	 * Our registration function to add action hooks to WP
	 *
	 * @return null
	 */
	public function register(): void {
		if ( ! $this->activated( 'templates_manager' ) ) {
			return;
		}

		$this->templates = array(
			'page-templates/two-columns-tpl.php' => 'Two Columns Layout',
		);

		add_filter( 'theme_page_templates', array( $this, 'custom_template' ) );
		add_filter( 'template_include', array( $this, 'load_template' ) );
	}

	/**
	 * Add our custom template to the list of available templates.
	 *
	 * @param mixed $templates - The list of templates.
	 */
	public function custom_template( mixed $templates ): mixed {
		$templates = array_merge( $templates, $this->templates );

		return $templates;
	}

	/**
	 * Load the template if the user is not logged in.
	 *
	 * @param mixed $template - The template to load.
	 */
	public function load_template( mixed $template ): mixed {
		global $post;

		if ( ! $post ) {
			return $template;
		}
		// If it is the front page, load a custom template.
		if ( is_front_page() ) {
			$file = $this->plugin_path . 'page-templates/front-page.php';

			if ( file_exists( $file ) ) {
				return $file;
			}
		}

		$template_name = get_post_meta( $post->ID, '_wp_page_template', true );

		if ( ! isset( $this->templates[ $template_name ] ) ) {
			return $template;
		}

		$file = $this->plugin_path . $template_name;

		if ( file_exists( $file ) ) {
			return $file;
		}

		return $template;
	}
}
