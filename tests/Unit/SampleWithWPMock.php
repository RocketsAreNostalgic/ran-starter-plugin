<?php
/**
 * Class SampleWithWPMockTest
 *
 * @package RanPlugin
 */

declare(strict_types = 1);

namespace Ran\Tests\Unit;

use WP_Mock;

/**
 * Test class.
 */
class SampleWithWPMockTest extends WP_Mock\Tools\TestCase {

	/**
	 * Setup the test.
	 *
	 * @throws \Exception - Throw an exception if the test fails.
	 */
	public function setUp(): void {
		WP_Mock::setUp();
	}

	/**
	 * Tear down the test.
	 *
	 * @throws \Exception  - Throw an exception if the test fails.
	 */
	public function tearDown(): void {
		WP_Mock::tearDown();
	}

	/**
	 * Ensures the WP_Mock harness itself remains usable by the starter.
	 */
	public function test_harness_is_available(): void {
		self::assertTrue( true );
	}
}
