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
	 * @var string - The widget ID.
	 */
	public string $widget_id;

	/**
	 * The widget name.
	 *
	 * @var string - The widget name.
	 */
	public string $widget_name;

	/** The widget options array.
	 *
	 * @var array<mixed>
	 */
	public array $widget_options = array();

	/** The widget options array.
	 *
	 * @var array<mixed>
	 */
	public array $control_options = array();

	/**
	 * Constructor of the class.
	 *
	 * @return void
	 */
	private function __construct() {

		$this->widget_id = 'ran_media_widget';
		$this->widget_name = 'RAN Media Widget';

		$this->widget_options = array(
			'classname' => $this->widget_id,
			'description' => $this->widget_name,
			'customize_selective_refresh' => true,
		);

		$this->control_options = array(
			'width' => 400,
			'height' => 350,
		);
	}

	/**
	 * Our registration function to add action hooks to WP
	 */
	public function register(): void {
		parent::__construct( $this->widget_id, $this->widget_name, $this->widget_options, $this->control_options );

		add_action( 'widgets_init', array( $this, 'widgetsInit' ) );
	}

	/**
	 * Register the widget.
	 */
	public function widgetsInit(): void {
		register_widget( $this );
	}

	/**
	 * The widget output.
	 *
	 * @param  mixed $args   - The arguments of the widget.
	 * @param  mixed $instance - The instance of the widget.
	 */
	public function widget( mixed $args, mixed $instance ): void {
		echo \esc_html( $args['before_widget'] );
		if ( ! empty( $instance['title'] ) ) {
			echo \esc_html( $args['before_title'] ) . \esc_html( apply_filters( 'widget_title', $instance['title'] ) ) . \esc_html( $args['after_title'] );
		}
		if ( ! empty( $instance['image'] ) ) {
			echo '<img src="' . esc_url( $instance['image'] ) . '" alt="">';
		}
		echo \esc_html( $args['after_widget'] );
	}

	/**
	 * Widget form template
	 *
	 * @param  mixed $instance - The widget instance.
	 */
	public function form( mixed $instance ): void {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Custom Text', 'ran-starter-plugin' );
		$image = ! empty( $instance['image'] ) ? $instance['image'] : '';
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
	 * @param  mixed $new_instance - The new instance of the widget.
	 * @param  mixed $old_instance - The old instance of the widget.
	 *
	 * @return mixed - The new instance of the widget.
	 */
	public function update( mixed $new_instance, mixed $old_instance ): mixed {
		$instance = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['image'] = ! empty( $new_instance['image'] ) ? $new_instance['image'] : '';

		return $instance;
	}
}
