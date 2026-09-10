<?php
/**
 * Example feature controller tests.
 *
 * @package RanPlugin
 */

declare(strict_types = 1);

namespace Ran\Tests\Unit;

use Ran\PluginLib\Config\ConfigInterface;
use Ran\StarterPlugin\Features\ExampleFeatureController;
use WP_Mock;

/**
 * Protect optional feature initialization behavior.
 */
class ExampleFeatureControllerTest extends WP_Mock\Tools\TestCase {
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
	 * Missing feature state must be added as disabled without replacing other options.
	 */
	public function test_missing_feature_is_disabled_without_replacing_plugin_options(): void {
		$option_name = 'ran_plugin';
		$options     = array(
			'Version' => '1.2.3',
			'other_feature' => true,
		);
		$config      = $this->createMock( ConfigInterface::class );

		$config->method( 'get_config' )->willReturn(
			array(
				'RAN' => array( 'AppOption' => $option_name ),
				'Slug' => $option_name,
			)
		);
		$config->method( 'get_options_key' )->willReturn( $option_name );

		WP_Mock::userFunction( 'get_option' )
			->twice()
			->andReturn( $options );
		WP_Mock::userFunction( 'update_option' )
			->once()
			->with(
				$option_name,
				array(
					'Version' => '1.2.3',
					'other_feature' => true,
					'ExampleFeatureController' => false,
				)
			)
			->andReturn( true );

		$controller = new ExampleFeatureController( $config );

		self::assertFalse( $controller->init() );
	}
}
