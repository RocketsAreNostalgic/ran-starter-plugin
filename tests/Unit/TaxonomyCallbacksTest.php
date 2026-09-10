<?php
/**
 * Taxonomy callbacks tests.
 *
 * @package RanPlugin
 */

declare(strict_types = 1);

namespace Ran\Tests\Unit;

use Ran\StarterPlugin\Api\Callbacks\TaxonomyCallbacks;
use WP_Mock;

/**
 * Protect taxonomy field rendering behavior.
 */
class TaxonomyCallbacksTest extends WP_Mock\Tools\TestCase {
	/**
	 * Set up WordPress mocks.
	 *
	 * @throws \Exception When the test setup fails.
	 */
	public function setUp(): void {
		WP_Mock::setUp();
	}

	/**
	 * Tear down WordPress mocks.
	 *
	 * @throws \Exception When the test teardown fails.
	 */
	public function tearDown(): void {
		WP_Mock::tearDown();
	}

	/**
	 * The post-type selector must retain its generated checkbox controls.
	 */
	public function test_post_type_checkboxes_are_rendered_as_form_controls(): void {
		WP_Mock::userFunction( 'get_post_types' )
			->once()
			->with( array( 'show_ui' => true ) )
			->andReturn( array( 'post', 'page' ) );
		WP_Mock::userFunction( 'esc_attr' )
			->andReturnUsing( static fn ( mixed $value ): string => htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ) );
		WP_Mock::userFunction( 'esc_html' )
			->andReturnUsing( static fn ( mixed $value ): string => htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ) );

		$callback = new TaxonomyCallbacks();

		ob_start();
		$callback->checkbox_post_types_field(
			array(
				'option_name' => 'ran_plugin_tax',
				'label_for' => 'objects',
				'class' => 'ui-toggle',
			)
		);
		$output = ob_get_clean();

		self::assertIsString( $output );
		self::assertStringContainsString( '<input type="checkbox" id="post" name="ran_plugin_tax[objects][post]"', $output );
		self::assertStringContainsString( '<input type="checkbox" id="page" name="ran_plugin_tax[objects][page]"', $output );
		self::assertStringNotContainsString( '&lt;input', $output );
	}
}
