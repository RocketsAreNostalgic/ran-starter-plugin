<?php

namespace Ran\Tests\Unit;

use WP_Mock;

/**
 * Test class.
 */
class SampleWithWPMockTest extends WP_Mock\Tools\TestCase {
	public function setUp():void {
		WP_Mock::setUp();
	}

	public function tearDown(): void {
		WP_Mock::tearDown();
	}
}
