<?php
/**
 * Class of TestimonialController.
 *
 * @package  RanPlugin
 */

declare(strict_types = 1);

namespace Ran\StarterPlugin\Features;

use Ran\StarterPlugin\Api\Callbacks\TestimonialCallbacks;
use Ran\StarterPlugin\Base\BaseController;
use Ran\StarterPlugin\Base\ControllerInterface;
use Ran\StarterPlugin\Base\SettingsApi;

/**
 * TestimonialController Class.
 */
class TestimonialController extends BaseController implements ControllerInterface {
	/**
	 * Settings API.
	 *
	 * @var SettingsApi
	 */
	public SettingsApi $settings;

	/**
	 * Testimonial callbacks.
	 *
	 * @var TestimonialCallbacks
	 */
	public TestimonialCallbacks $callbacks;

	/**
	 * Register testimonial hooks.
	 */
	public function register(): void {
		if ( ! $this->activated( 'testimonial_manager' ) ) {
			return;
		}

		$this->settings  = new SettingsApi();
		$this->callbacks = new TestimonialCallbacks();

		add_action( 'init', array( $this, 'testimonial_cpt' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_meta_box' ) );
		add_filter( 'manage_testimonial_posts_columns', array( $this, 'set_custom_columns' ) );
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
		if ( ! wp_doing_ajax() || false === check_ajax_referer( 'testimonial-nonce', 'nonce', false ) ) {
			$this->return_json( 'error' );
		}

		$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

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
		$this->return_json( $post_id ? 'success' : 'error' );
	}

	/**
	 * Return a JSON response to the AJAX request.
	 *
	 * @param mixed $status AJAX request status.
	 */
	public function return_json( mixed $status ): never {
		wp_send_json(
			array(
				'status' => $status,
			)
		);
	}

	/**
	 * Render the tracked testimonial form template.
	 *
	 * Derived plugins should enqueue any feature-specific assets explicitly;
	 * the starter does not advertise placeholder asset URLs that do not exist.
	 */
	public function testimonial_form(): ?string {
		ob_start();
		require $this->plugin_path . 'templates/features/contact-form.php';

		$output = ob_get_clean();
		return false === $output ? null : $output;
	}

	/**
	 * Render the tracked testimonial slideshow template.
	 *
	 * Derived plugins should enqueue any feature-specific assets explicitly;
	 * the starter does not advertise placeholder asset URLs that do not exist.
	 */
	public function testimonial_slideshow(): ?string {
		ob_start();
		require $this->plugin_path . 'templates/features/slider.php';

		$output = ob_get_clean();
		return false === $output ? null : $output;
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
		register_post_type(
			'testimonial',
			array(
				'labels' => array(
					'name' => 'Testimonials',
					'singular_name' => 'Testimonial',
				),
				'public' => true,
				'has_archive' => false,
				'menu_icon' => 'dashicons-testimonial',
				'exclude_from_search' => true,
				'publicly_queryable' => false,
				'supports' => array( 'title', 'editor' ),
				'show_in_rest' => true,
			)
		);
	}

	/**
	 * Add the testimonial meta box.
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
	 * @param \WP_Post $post Current post object.
	 */
	public function render_features_box( \WP_Post $post ): void {
		wp_nonce_field( 'ran_testimonial', 'ran_testimonial_nonce' );

		$data     = get_post_meta( $post->ID, '_ran_testimonial_key', true );
		$data     = is_array( $data ) ? $data : array();
		$name     = isset( $data['name'] ) ? (string) $data['name'] : '';
		$email    = isset( $data['email'] ) ? (string) $data['email'] : '';
		$approved = ! empty( $data['approved'] );
		$featured = ! empty( $data['featured'] );
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
					<label for="ran_testimonial_approved"><div></div></label>
				</div>
			</div>
		</div>
		<div class="meta-container">
			<label class="meta-label w-50 text-left" for="ran_testimonial_featured">Featured</label>
			<div class="text-right w-50 inline">
				<div class="ui-toggle inline"><input type="checkbox" id="ran_testimonial_featured" name="ran_testimonial_featured" value="1" <?php echo $featured ? 'checked' : ''; ?>>
					<label for="ran_testimonial_featured"><div></div></label>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Save the meta box.
	 *
	 * @param int $post_id Post ID supplied by the save_post action.
	 */
	public function save_meta_box( int $post_id ): void {
		if ( ! isset( $_POST['ran_testimonial_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ran_testimonial_nonce'] ) ), 'ran_testimonial' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$data = array(
			'name' => isset( $_POST['ran_testimonial_author'] ) ? sanitize_text_field( wp_unslash( $_POST['ran_testimonial_author'] ) ) : '',
			'email' => isset( $_POST['ran_testimonial_email'] ) ? sanitize_email( wp_unslash( $_POST['ran_testimonial_email'] ) ) : '',
			'approved' => isset( $_POST['ran_testimonial_approved'] ) ? 1 : 0,
			'featured' => isset( $_POST['ran_testimonial_featured'] ) ? 1 : 0,
		);
		update_post_meta( $post_id, '_ran_testimonial_key', $data );
	}

	/**
	 * Set the custom columns.
	 *
	 * @param array<mixed> $columns Columns array.
	 * @return array<mixed>
	 */
	public function set_custom_columns( array $columns ): array {
		$title = $columns['title'];
		$date  = $columns['date'];
		unset( $columns['title'], $columns['date'] );

		$columns['name']     = 'Author Name';
		$columns['title']    = $title;
		$columns['approved'] = 'Approved';
		$columns['featured'] = 'Featured';
		$columns['date']     = $date;

		return $columns;
	}

	/**
	 * Output custom-column data.
	 *
	 * @param string $column  Column name.
	 * @param int    $post_id Post ID.
	 */
	public function set_custom_columns_data( string $column, int $post_id ): void {
		$data     = get_post_meta( $post_id, '_ran_testimonial_key', true );
		$data     = is_array( $data ) ? $data : array();
		$name     = isset( $data['name'] ) ? (string) $data['name'] : '';
		$email    = isset( $data['email'] ) ? (string) $data['email'] : '';
		$approved = ! empty( $data['approved'] ) ? '<strong>YES</strong>' : 'NO';
		$featured = ! empty( $data['featured'] ) ? '<strong>YES</strong>' : 'NO';

		switch ( $column ) {
			case 'name':
				echo '<strong>' . \esc_html( $name ) . '</strong><br/><a href="mailto:' . \esc_attr( $email ) . '">' . \esc_html( $email ) . '</a>';
				break;
			case 'approved':
				echo wp_kses_post( $approved );
				break;
			case 'featured':
				echo wp_kses_post( $featured );
				break;
		}
	}

	/**
	 * Set the custom columns sortable data.
	 *
	 * @param array<mixed> $columns Columns array.
	 * @return array<mixed>
	 */
	public function set_custom_columns_sortable( array $columns ): array {
		$columns['name']     = 'name';
		$columns['approved'] = 'approved';
		$columns['featured'] = 'featured';

		return $columns;
	}
}
