<?php

class AdminTest extends \WP_UnitTestCase {
	/**
	 * Check that our_screen function exists (as a static function).
	 */
	public function test_our_screen_exists() {
		$this->assertTrue( method_exists( 'Options_Pixie_Admin', 'our_screen' ) );
	}

	/**
	 * Check that our_screen function returns true when expected.
	 *
	 * @depends test_our_screen_exists
	 */
	public function test_our_screen_true() {
		set_current_screen( 'wibble' );
		$screen = get_current_screen();
		$this->assertTrue( Options_Pixie_Admin::our_screen( $screen, 'wibble' ) );
	}

	/**
	 * Check that our_screen function returns true when expected for multisite network admin.
	 *
	 * @depends test_our_screen_exists
	 */
	public function test_our_screen_true_network() {
		set_current_screen( 'wibble-network' );
		$screen = get_current_screen();
		$this->assertTrue( Options_Pixie_Admin::our_screen( $screen, 'wibble' ) );
	}

	/**
	 * Check that our_screen function returns false when expected.
	 *
	 * @depends test_our_screen_exists
	 */
	public function test_our_screen_false() {
		set_current_screen( 'wibble' );
		$screen = get_current_screen();
		$this->assertFalse( Options_Pixie_Admin::our_screen( $screen, 'wobble' ) );
	}
}
