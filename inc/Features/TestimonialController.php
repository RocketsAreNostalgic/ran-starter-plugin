<?php
/**
 * Class of TestimonialController.
 *
 * @package  RanPlugin
 */

declare(strict_types = 1);

namespace Ran\StarterPlugin\Services;

use Ran\StarterPlugin\Api\Callbacks\TestimonialCallbacks;
use Ran\StarterPlugin\Base\BaseController;
use Ran\StarterPlugin\Base\ControllerInterface;
use Ran\StarterPlugin\Base\SettingsApi;

/**
 * TestimonialController Class
 * TODO add nonce verification. See TestimonialController for an example.
 */
class TestimonialController extends BaseController implements ControllerInterface {

	/**
	 * Public settings variable.
	 *
	 * @var mixed - the settings class.
	 */
	public mixed $settings;

	/**
	 * Public callbacks variable.
	 *
	 * @var mixed - the callbacks class.
	 */
	public mixed $callbacks;

	/**
	 * Our registration funtion to add action hooks to WP
	 *
	 * @return null
	 */
	public function register(): void {
		if ( ! $this->activated( 'testimonial_manager' ) ) {
			return;
		}

		$this->settings = new SettingsApi();

		$this->callbacks = new TestimonialCallbacks();

		add_action( 'init', array( $this, 'testimonial_cpt' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_meta_box' ) );
		add_action( 'manage_testimonial_posts_columns', array( $this, 'set_custom_columns' ) );
		add_action( 'manage_testimonial_posts_custom_column', array( $this, 'set_custom_columns_data' ), 10, 2 );
		add_filter( 'manage_edit-testimonial_sortable_columns', array( $this, 'set_custom_columns_sortable' ) );

		$this->setShortcodePage();

		add_shortcode( 'testimonial-form', array( $this, 'testimonial_form' ) );
		add_shortcode( 'testimonial-slideshow', array( $this, 'testimonial_slideshow' ) );
		add_action( 'wp_ajax_submit_testimonial', array( $this, 'submit_testimonial' ) );
		add_action( 'wp_ajax_nopriv_submit_testimonial', array( $this, 'submit_testimonial' ) );
	}

	/**
	 * Submit a testimonial via an AJAX request.
	 */
	public function submit_testimonial(): void {
		if ( ! DOING_AJAX || ! check_ajax_referer( 'testimonial-nonce', 'nonce' ) ) {
			$this->return_json( 'error' );
		}
		$name = sanitize_text_field( $_POST['name'] );
		$email = sanitize_email( $_POST['email'] );
		$message = sanitize_textarea_field( $_POST['message'] );

		$data = array(
			'name' => $name,
			'email' => $email,
			'approved' => 0,
			'featured' => 0,
		);

		$args = array(
			'post_title' => 'Testimonial from ' . $name,
			'post_content' => $message,
			'post_author' => 1,
			'post_status' => 'publish',
			'post_type' => 'testimonial',
			'meta_input' => array(
				'_ran_testimonial_key' => $data,
			),
		);

		$post_id = wp_insert_post( $args );

		if ( $post_id ) {
			$this->return_json( 'success' );
		}

		$this->return_json( 'error' );
	}

	/**
	 * The return_json function is used to return a JSON response to the AJAX request.
	 *
	 * @param mixed $status - the status of the AJAX request.
	 */
	public function return_json( mixed $status ): void {
		$return = array(
			'status' => $status,
		);
		wp_send_json( $return );
		wp_die();
	}

	/**
	 * Output the testimonial form script and style links.
	 *
	 * TODO: Enqueue scripts and styles correctly.
	 */
	public function testimonial_form(): ?string {
		ob_start();
		// phpcs:disable WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet,WordPress.WP.EnqueuedResources.NonEnqueuedScript
		echo \esc_html( "<link rel=\"stylesheet\" href=\"$this->plugin_url/assets/form.css\" type=\"text/css\" media=\"all\" />" );
		require_once "$this->plugin_path/templates/contact-form.php";
		echo \esc_html( "<script src=\"$this->plugin_url/assets/form.js\"></script>" );
		return ob_get_clean();
		// phpcs:enable
	}

	/**
	 * Output the testimonial slideshow script and style links.
	 */
	public function testimonial_slideshow(): ?string {
		ob_start();
		// phpcs:disable WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet,WordPress.WP.EnqueuedResources.NonEnqueuedScript
		echo \esc_html( "<link rel=\"stylesheet\" href=\"$this->plugin_url/assets/slider.css\" type=\"text/css\" media=\"all\" />" );
		require_once "$this->plugin_path/templates/slider.php";
		echo \esc_html( "<script src=\"$this->plugin_url/assets/slider.js\"></script>" );
		return ob_get_clean();
		// phpcs:enable
	}

	/**
	 * Set the testimonial shortcode admin page.
	 */
	public function setShortcodePage(): void {
		$subpage = array(
			array(
				'parent_slug' => 'edit.php?post_type=testimonial',
				'page_title' => 'Shortcodes',
				'menu_title' => 'Shortcodes',
				'capability' => 'manage_options',
				'menu_slug' => 'ran_testimonial_shortcode',
				'callback' => array( $this->callbacks, 'shortcodePage' ),
			),
		);

		$this->settings->addSubPages( $subpage )->register();
	}

	/**
	 * Register testimonials custom post type.
	 */
	public function testimonial_cpt(): void {
		$labels = array(
			'name' => 'Testimonials',
			'singular_name' => 'Testimonial',
		);

		$args = array(
			'labels' => $labels,
			'public' => true,
			'has_archive' => false,
			'menu_icon' => 'dashicons-testimonial',
			'exclude_from_search' => true,
			'publicly_queryable' => false,
			'supports' => array( 'title', 'editor' ),
			'show_in_rest' => true,
		);

		register_post_type( 'testimonial', $args );
	}

	/**
	 * Add custom columns to the admin testimonials list.
	 */
	public function add_meta_boxes(): void {
		add_meta_box(
			'testimonial_author',
			'Testimonial Options',
			array( $this, 'render_features_box' ),
			'testimonial',
			'side',
			'default'
		);
	}

	/**
	 * Render the meta box.
	 *
	 * TODO : Move markup to template.
	 *
	 * @param \WP_Post $post - the current post object.
	 */
	public function render_features_box( \WP_Post $post ): void {
		wp_nonce_field( 'ran_testimonial', 'ran_testimonial_nonce' );

		$data = get_post_meta( $post->ID, '_ran_testimonial_key', true );
		$name = isset( $data['name'] ) ? $data['name'] : '';
		$email = isset( $data['email'] ) ? $data['email'] : '';
		$approved = isset( $data['approved'] ) ? $data['approved'] : false;
		$featured = isset( $data['featured'] ) ? $data['featured'] : false;
		?>
		<p>
			<label class="meta-label" for="ran_testimonial_author">Author Name</label>
			<input type="text" id="ran_testimonial_author" name="ran_testimonial_author" class="widefat" value="<?php echo esc_attr( $name ); ?>">
		</p>
		<p>
			<label class="meta-label" for="ran_testimonial_email">Author Email</label>
			<input type="email" id="ran_testimonial_email" name="ran_testimonial_email" class="widefat" value="<?php echo esc_attr( $email ); ?>">
		</p>
		<div class="meta-container">
			<label class="meta-label w-50 text-left" for="ran_testimonial_approved">Approved</label>
			<div class="text-right w-50 inline">
				<div class="ui-toggle inline"><input type="checkbox" id="ran_testimonial_approved" name="ran_testimonial_approved" value="1" <?php echo $approved ? 'checked' : ''; ?>>
					<label for="ran_testimonial_approved">
						<div></div>
					</label>
				</div>
			</div>
		</div>
		<div class="meta-container">
			<label class="meta-label w-50 text-left" for="ran_testimonial_featured">Featured</label>
			<div class="text-right w-50 inline">
				<div class="ui-toggle inline"><input type="checkbox" id="ran_testimonial_featured" name="ran_testimonial_featured" value="1" <?php echo $featured ? 'checked' : ''; ?>>
					<label for="ran_testimonial_featured">
						<div></div>
					</label>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Save the meta box.
	 *
	 * @param string|int $post_id - the post ID.
	 */
	public function save_meta_box( string $post_id ): string|int {
		if ( ! isset( $_POST['ran_testimonial_nonce'] ) ) {
			return $post_id;
		}

		if ( ! wp_verify_nonce( $_POST['ran_testimonial_nonce'], 'ran_testimonial' ) ) {
			return $post_id;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		$data = array(
			'name' => sanitize_text_field( $_POST['ran_testimonial_author'] ),
			'email' => sanitize_email( $_POST['ran_testimonial_email'] ),
			'approved' => isset( $_POST['ran_testimonial_approved'] ) ? 1 : 0,
			'featured' => isset( $_POST['ran_testimonial_featured'] ) ? 1 : 0,
		);
		update_post_meta( $post_id, '_ran_testimonial_key', $data );
	}

	/**
	 * Set the custom columns.
	 *
	 * @param array<mixed> $columns - the columns array.
	 *
	 * @return array<mixed>
	 */
	public function set_custom_columns( array $columns ): array {
		$title = $columns['title'];
		$date = $columns['date'];
		unset( $columns['title'], $columns['date'] );

		$columns['name'] = 'Author Name';
		$columns['title'] = $title;
		$columns['approved'] = 'Approved';
		$columns['featured'] = 'Featured';
		$columns['date'] = $date;

		return $columns;
	}

	/**
	 * Set the custom columns data.
	 *
	 * @param  array<mixed> $column - The column array.
	 * @param string|int   $post_id - The post ID.
	 */
	public function set_custom_columns_data( array $column, string|int $post_id ): void {
		$data = get_post_meta( $post_id, '_ran_testimonial_key', true );
		$name = isset( $data['name'] ) ? $data['name'] : '';
		$email = isset( $data['email'] ) ? $data['email'] : '';
		if ( 1 === isset( $data['approved'] ) && $data['approved'] ) {
			$approved = '<strong>YES</strong>';
		} else {
			$approved = 'NO';
		}
		if ( 1 === isset( $data['featured'] ) && $data['featured'] ) {
			$featured = '<strong>YES</strong>';
		} else {
			$featured = 'NO';
		}

		switch ( $column ) {
			case 'name':
				echo '<strong>' . \esc_html( $name ) . '</strong><br/><a href="mailto:' . \esc_attr( $email ) . '">' . \esc_html( $email ) . '</a>';
				break;

			case 'approved':
				echo \esc_html( $approved );

				break;
			case 'featured':
				echo \esc_html( $featured );
				break;
		}
	}


	/**
	 * Set the custom columns sortable data.
	 *
	 * @param  array<mixed> $columns - The columns array.
	 *
	 * @return array<mixed>
	 */
	public function set_custom_columns_sortable( array $columns ): array {
		$columns['name'] = 'name';
		$columns['approved'] = 'approved';
		$columns['featured'] = 'featured';

		return $columns;
	}
}
