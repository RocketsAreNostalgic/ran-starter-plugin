<?php
/**
 * Media Widgets
 *
 * @package  RanPlugin
 */

declare(strict_types = 1);

namespace Ran\StarterPlugin\Api\Widgets;

use WP_Widget;

/**
 * Class of MediaWidget.
 */
class MediaWidget extends WP_Widget {
	/**
	 * The widget ID.
	 *
	 * @var string
	 */
	private string $ran_widget_id = 'ran_media_widget';

	/**
	 * The widget name.
	 *
	 * @var string
	 */
	private string $ran_widget_name = 'RAN Media Widget';

	/**
	 * Constructor of the class.
	 */
	public function __construct() {
		parent::__construct(
			$this->ran_widget_id,
			$this->ran_widget_name,
			array(
				'classname' => $this->ran_widget_id,
				'description' => $this->ran_widget_name,
				'customize_selective_refresh' => true,
			),
			array(
				'width' => 400,
				'height' => 350,
			)
		);
	}

	/**
	 * Our registration function to add action hooks to WP.
	 */
	public function register(): void {
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );
	}

	/**
	 * Register the widget.
	 */
	public function widgets_init(): void {
		register_widget( $this );
	}

	/**
	 * The widget output.
	 *
	 * @param mixed $args     The arguments of the widget.
	 * @param mixed $instance The instance of the widget.
	 */
	public function widget( mixed $args, mixed $instance ): void {
		$args     = is_array( $args ) ? $args : array();
		$instance = is_array( $instance ) ? $instance : array();

		// Theme-provided widget wrappers are trusted presentation markup, matching WordPress core widgets.
		echo isset( $args['before_widget'] ) ? (string) $args['before_widget'] : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		if ( ! empty( $instance['title'] ) ) {
			$title = apply_filters( 'widget_title', (string) $instance['title'], $instance, $this->id_base );
			echo isset( $args['before_title'] ) ? (string) $args['before_title'] : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo esc_html( $title );
			echo isset( $args['after_title'] ) ? (string) $args['after_title'] : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		if ( ! empty( $instance['image'] ) ) {
			echo '<img src="' . esc_url( (string) $instance['image'] ) . '" alt="">';
		}

		echo isset( $args['after_widget'] ) ? (string) $args['after_widget'] : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Widget form template.
	 *
	 * @param mixed $instance The widget instance.
	 */
	public function form( mixed $instance ): void {
		$instance = is_array( $instance ) ? $instance : array();
		$title    = ! empty( $instance['title'] ) ? (string) $instance['title'] : esc_html__( 'Custom Text', 'ran-starter-plugin' );
		$image    = ! empty( $instance['image'] ) ? (string) $instance['image'] : '';
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'ran-starter-plugin' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'image' ) ); ?>"><?php esc_attr_e( 'Image:', 'ran-starter-plugin' ); ?></label>
			<input class="widefat image-upload" id="<?php echo esc_attr( $this->get_field_id( 'image' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'image' ) ); ?>" type="text" value="<?php echo esc_url( $image ); ?>">
			<button type="button" class="button button-primary js-image-upload">Select Image</button>
		</p>
		<?php
	}

	/**
	 * Update the widget instance.
	 *
	 * @param mixed $new_instance The new instance of the widget.
	 * @param mixed $old_instance The old instance of the widget.
	 *
	 * @return mixed The new instance of the widget.
	 */
	public function update( mixed $new_instance, mixed $old_instance ): mixed {
		$new_instance = is_array( $new_instance ) ? $new_instance : array();
		$instance     = is_array( $old_instance ) ? $old_instance : array();

		$instance['title'] = isset( $new_instance['title'] ) ? sanitize_text_field( (string) $new_instance['title'] ) : '';
		$instance['image'] = isset( $new_instance['image'] ) ? esc_url_raw( (string) $new_instance['image'] ) : '';

		return $instance;
	}
}
