<?php
/**
 * Focused starter contract regressions.
 *
 * @package RanPlugin
 */

declare(strict_types = 1);
namespace Ran\Tests\Unit;

use Ran\PluginLib\Config\ConfigInterface;
use Ran\StarterPlugin\Api\Callbacks\AdminCallbacks;
use Ran\StarterPlugin\Api\Callbacks\CptCallbacks;
use Ran\StarterPlugin\Api\Callbacks\ExampleFeatureCallbacks;
use Ran\StarterPlugin\Api\Callbacks\ManagerCallbacks;
use Ran\StarterPlugin\Api\Callbacks\TestimonialCallbacks;
use Ran\StarterPlugin\Base\SettingsApi;
use Ran\StarterPlugin\Base\Activate;
use Ran\StarterPlugin\Base\Bootstrap;
use Ran\StarterPlugin\Base\Config;
use Ran\StarterPlugin\Features\CustomTaxonomyController;
use Ran\StarterPlugin\Features\TestimonialController;
use WP_Mock;
/** Protect the actual producer and callback boundaries. */
class StarterContractsTest extends WP_Mock\Tools\TestCase {
	/** Set up synthetic WordPress functions. */
	public function setUp(): void {
		WP_Mock::setUp(); }
	/** Tear down synthetic WordPress functions. */
	public function tearDown(): void {
		WP_Mock::tearDown(); }
	/** Activation must support the promised neutral interface. */
	public function test_activation_uses_interface_options_key_and_preserves_version(): void {
		$config = $this->createMock( ConfigInterface::class );
		$config->expects( self::once() )->method( 'get_config' )->willReturn( array( 'Version' => '1.2.3' ) );
		$config->expects( self::once() )->method( 'get_options_key' )->willReturn( 'fixture_option' );
		WP_Mock::userFunction( 'flush_rewrite_rules' )->once();
		WP_Mock::userFunction( 'get_option' )->once()->with( 'fixture_option' )->andReturn( false );
		WP_Mock::userFunction( 'update_option' )->once()->with( 'fixture_option', array( 'Version' => '1.2.3' ) )->andReturn( true );
		Activate::activate( $config );
	}
	/** Bootstrap retains the injected interface instance. */
	public function test_bootstrap_supports_a_config_interface_without_legacy_methods(): void {
		$config = $this->createMock( ConfigInterface::class );
		$config->expects( self::once() )->method( 'get_config' )->willReturn( array() );
		WP_Mock::userFunction( 'is_admin' )->once()->andReturn( false );
		$bootstrap = new Bootstrap( $config );
		WP_Mock::expectActionAdded( 'wp_enqueue_scripts', array( $bootstrap, 'enqueue_public_assets' ) );
		self::assertSame( $config, $bootstrap->init() );
	}
	/** Missing and malformed stored values are normalized without changing valid arrays. */
	public function test_cpt_sanitizer_preserves_rows_and_normalizes_nonarray_options(): void {
		$input = array(
			'post_type'     => 'book',
			'singular_name' => 'Book',
		);
		foreach ( array( false, 'malformed', array( 'old' => array( 'post_type' => 'old' ) ) ) as $stored ) {
			WP_Mock::userFunction( 'get_option' )->once()->with( 'ran_plugin_cpt' )->andReturn( $stored );
			$expected         = is_array( $stored ) ? $stored : array();
			$expected['book'] = $input;
			self::assertSame( $expected, ( new CptCallbacks() )->cpt_sanitize( $input ) );
			WP_Mock::tearDown();
			WP_Mock::setUp();
		}
	}
	/** Plain visible properties and Traversable subclasses retain their original iteration. */
	public function test_manager_preserves_plain_and_traversable_iteration(): void {
		$plain    = new class() extends ManagerCallbacks { /**
															* Visible checkbox state.
															*
															* @var bool
															*/
			public bool $feature = false;
		};
		$iterator = new class() extends ManagerCallbacks implements \IteratorAggregate {
			/**
			 * Expose iterator checkbox state.
			 *
			 * @return \Traversable<string, bool> Iterator values.
			 */
			public function getIterator(): \Traversable {
				yield 'iterated' => false; }
		};
		self::assertSame( array(), ( new ManagerCallbacks() )->checkbox_sanitize( array() ) );
		self::assertSame( array( 'feature' => true ), $plain->checkbox_sanitize( array( 'feature' => '1' ) ) );
		self::assertSame( array( 'iterated' => true ), $iterator->checkbox_sanitize( array( 'iterated' => '1' ) ) );
	}
	/** Enabled taxonomy scaffolding must use the actual Settings API. */
	public function test_enabled_taxonomy_uses_real_settings_registration(): void {
		WP_Mock::userFunction( 'sanitize_key' )->andReturn( 'fixture_option' );
		WP_Mock::userFunction( 'trailingslashit' )->andReturnUsing( static fn ( string $value ): string => $value . '/' );
		WP_Mock::userFunction( 'get_option' )->andReturnUsing( static fn ( string $name ): array => 'fixture_option' === $name ? array( 'taxonomy_manager' => true ) : array() );
		$controller = new CustomTaxonomyController( new Config() );
		$controller->register();
		self::assertSame( 'ran_taxonomy', $controller->settings->wp_admin_subpages[0]['menu_slug'] );
		WP_Mock::userFunction( 'register_setting' )->once()->with( 'ran_plugin_tax_settings', 'ran_plugin_tax', array( $controller->tax_callbacks, 'tax_sanitize' ) );
		WP_Mock::userFunction( 'add_settings_section' )->once();
		WP_Mock::userFunction( 'add_settings_field' )->times( count( $controller->settings->fields ) );
		$controller->settings->register_custom_fields();
	}
	/**
	 * Enabled CPT scaffolding uses the real Settings API and stored-option observations.
	 *
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function test_enabled_cpt_uses_real_settings_registration(): void {
		WP_Mock::userFunction( 'get_plugin_data' )->andReturn(
			array(
				'Name'       => 'Fixture',
				'Version'    => '1.2.3',
				'TextDomain' => 'fixture',
			)
		);
		WP_Mock::userFunction( 'plugin_dir_path' )->andReturn( dirname( __DIR__, 2 ) . '/' );
		WP_Mock::userFunction( 'plugin_dir_url' )->andReturn( 'https://example.com/plugin/' );
		WP_Mock::userFunction( 'plugin_basename' )->andReturn( 'ran-starter-plugin.php' );
		WP_Mock::userFunction( 'sanitize_key' )->andReturn( 'fixture' );
		WP_Mock::userFunction( 'trailingslashit' )->andReturnUsing( static fn ( string $value ): string => rtrim( $value, '/' ) . '/' );
		Config::init( dirname( __DIR__, 2 ) . '/ran-starter-plugin.php' );
		WP_Mock::userFunction( 'get_option' )->with( 'fixture', array() )->andReturn( array( 'cpt_manager' => true ) );
		WP_Mock::userFunction( 'get_option' )->with( 'ran_plugin_cpt' )->once()->andReturn( false );
		$controller = new \Ran\StarterPlugin\Features\CustomPostTypeController( new Config() );
		$controller->register();
		self::assertInstanceOf( \Ran\StarterPlugin\Base\SettingsApi::class, $controller->settings );
		self::assertSame( 'ran_cpt', $controller->settings->wp_admin_subpages[0]['menu_slug'] );
		foreach ( array_merge( $controller->settings->settings, $controller->settings->sections, $controller->settings->fields ) as $entry ) {
			self::assertTrue( is_callable( $entry['callback'] ) );
		}

		WP_Mock::userFunction( 'register_setting' )->once()->with( 'ran_plugin_cpt_settings', 'ran_plugin_cpt', array( $controller->cpt_callbacks, 'cpt_sanitize' ) );
		WP_Mock::userFunction( 'add_settings_section' )->once();
		WP_Mock::userFunction( 'add_settings_field' )->times( count( $controller->settings->fields ) );
		$controller->settings->register_custom_fields();
	}
	/**
	 * The registered WordPress adapters preserve nullable overrides and normal output.
	 *
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function test_registered_shortcodes_normalize_nullable_subclass_results(): void {
		WP_Mock::userFunction( 'get_plugin_data' )->andReturn(
			array(
				'Name'       => 'Fixture',
				'Version'    => '1.2.3',
				'TextDomain' => 'fixture',
			)
		);
		WP_Mock::userFunction( 'plugin_dir_path' )->andReturn( dirname( __DIR__, 2 ) . '/' );
		WP_Mock::userFunction( 'plugin_dir_url' )->andReturn( 'https://example.com/plugin/' );
		WP_Mock::userFunction( 'plugin_basename' )->andReturn( 'ran-starter-plugin.php' );
		WP_Mock::userFunction( 'sanitize_key' )->andReturn( 'fixture' );
		WP_Mock::userFunction( 'trailingslashit' )->andReturnUsing( static fn ( string $value ): string => rtrim( $value, '/' ) . '/' );
		Config::init( dirname( __DIR__, 2 ) . '/ran-starter-plugin.php' );
		$real            = new TestimonialController( Config::get_instance() );
		$real->callbacks = new TestimonialCallbacks( Config::get_instance() );
		$real->settings  = new SettingsApi();
		$real->setShortcodePage();
		self::assertSame( 'ran_testimonial_shortcode', $real->settings->wp_admin_subpages[0]['menu_slug'] );
		WP_Mock::userFunction( 'admin_url' )->andReturn( '/admin/' );
		WP_Mock::userFunction( 'wp_create_nonce' )->andReturn( 'nonce' );
		WP_Mock::userFunction( 'esc_attr' )->andReturnUsing( static fn ( mixed $value ): string => (string) $value );
		WP_Mock::userFunction( 'esc_url' )->andReturnUsing( static fn ( mixed $value ): string => (string) $value );
		$form = $real->testimonial_form();
		self::assertIsString( $form );
		self::assertStringContainsString( 'ran-testimonial-form', $form );

		foreach ( array( null, 'rendered output' ) as $content ) {
			$captured = array();
			WP_Mock::userFunction( 'add_shortcode' )->twice()->andReturnUsing(
				static function ( string $tag, callable $callback ) use ( &$captured ): void {
					$captured[ $tag ] = $callback; }
			);
			$controller = new class( $content ) extends TestimonialController {
				/**
				 * Nullable feature output.
				 *
				 * @var string|null
				 */
				private ?string $content;
				/**
				 * Observed WordPress arguments.
				 *
				 * @var array<string, list<mixed>>
				 */
				public array $received = array();
				/**
				 * Supply a deliberate nullable override without requiring plugin initialization.
				 *
				 * @param string|null $content Callback result.
				 */
				public function __construct( ?string $content ) {
					$this->content = $content; }
				/**
				 * Enable this synthetic feature.
				 *
				 * @param string $key Feature key.
				 * @param string $option_name Options key.
				 */
				protected function activated( string $key, string $option_name = '' ): bool {
					return true; }
				/** Leave unrelated settings setup to its separate real-instance test. */
				public function setShortcodePage(): void {}
				/**
				 * Return deliberate nullable form content.
				 *
				 * @param array<string> $atts WordPress shortcode attributes.
				 * @param string|null   $content WordPress shortcode content.
				 * @param string        $tag WordPress shortcode tag.
				 */
				public function testimonial_form( array $atts = array(), ?string $content = null, string $tag = '' ): ?string {
					$this->received['testimonial-form'] = func_get_args();
					return $this->content; }
				/**
				 * Return deliberate nullable slideshow content.
				 *
				 * @param array<string> $atts WordPress shortcode attributes.
				 * @param string|null   $content WordPress shortcode content.
				 * @param string        $tag WordPress shortcode tag.
				 */
				public function testimonial_slideshow( array $atts = array(), ?string $content = null, string $tag = '' ): ?string {
					$this->received['testimonial-slideshow'] = func_get_args();
					return $this->content; }
			};
			$controller->register();
			self::assertSame( $content, $controller->testimonial_form() );
			self::assertSame( $content, $controller->testimonial_slideshow() );
			self::assertSame( array( 'testimonial-form', 'testimonial-slideshow' ), array_keys( $captured ) );
			foreach ( $captured as $tag => $callback ) {
				self::assertSame( $content ?? '', $callback( array(), null, $tag ) );
				self::assertSame( array( array(), null, $tag ), $controller->received[ $tag ] ); }
		}
	}
	/**
	 * Invalid post contexts omit metadata reads while valid IDs retain the author.
	 *
	 * @param int|false $post_id Current post identifier.
	 * @param string    $author Expected author text.
	 * @dataProvider slider_contexts
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function test_slider_preserves_valid_and_invalid_post_contexts( int|false $post_id, string $author ): void {
		$query = \Mockery::mock( 'overload:WP_Query' );
		$posts = $query->shouldReceive( 'have_posts' );
		self::assertInstanceOf( \Mockery\CompositeExpectation::class, $posts );
		$posts->__call( 'times', array( 3 ) )->andReturn( true, true, false );
		$advance = $query->shouldReceive( 'the_post' );
		self::assertInstanceOf( \Mockery\CompositeExpectation::class, $advance );
		$advance->__call( 'once', array() );
		WP_Mock::userFunction( 'get_the_ID' )->once()->andReturn( $post_id );
		if ( false === $post_id ) {
			WP_Mock::userFunction( 'get_post_meta' )->never(); } else {
			WP_Mock::userFunction( 'get_post_meta' )->once()->with( $post_id, '_ran_testimonial_key', true )->andReturn( array( 'name' => $author ) ); }
			WP_Mock::userFunction( 'get_the_content' )->once()->andReturn( 'quote' );
			WP_Mock::userFunction( 'esc_html' )->andReturnUsing( static fn ( mixed $value ): string => (string) $value );
			WP_Mock::userFunction( 'wp_reset_postdata' )->once();
			ob_start();
			require dirname( __DIR__, 2 ) . '/templates/features/slider.php';
			$output = ob_get_clean();
			self::assertIsString( $output );
			self::assertStringContainsString( '~ ' . $author . ' ~', $output );
			self::assertStringContainsString( 'quote', $output );
	}
	/**
	 * Supply genuine and absent post contexts.
	 *
	 * @return list<array{int|false,string}> Contexts and authors.
	 */
	public function slider_contexts(): array {
		return array( array( 7, 'Author' ), array( false, '' ) ); }
	/**
	 * Real templates retain output, boolean returns and require-once semantics.
	 *
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function test_example_callbacks_load_real_templates_once_with_interface_config(): void {
		$config = $this->createMock( ConfigInterface::class );
		$config->method( 'get_config' )->willReturn( array( 'PATH' => dirname( __DIR__, 2 ) . '/' ) );
		foreach ( array( 'settings_errors', 'settings_fields', 'do_settings_sections', 'submit_button' ) as $function ) {
			WP_Mock::userFunction( $function ); }
		WP_Mock::userFunction( 'get_option' )->andReturn( array() );
		$callbacks = new ExampleFeatureCallbacks( $config );
		foreach ( array(
			'admin_dashboard' => 'RAN Plugin',
			'example_feature' => 'Example Feature Manager',
		) as $method => $heading ) {
			ob_start();
			self::assertTrue( $callbacks->$method() );
			$output = ob_get_clean();
			self::assertIsString( $output );
			self::assertStringContainsString( $heading, $output );
			ob_start();
			self::assertTrue( $callbacks->$method() );
			self::assertSame( '', ob_get_clean() );
		}
		self::assertTrue( ( new AdminCallbacks( $config ) )->admin_dashboard() );
	}
}
