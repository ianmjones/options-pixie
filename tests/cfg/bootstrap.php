<?php

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = '/tmp/wordpress-tests-lib';
}

require_once $_tests_dir . '/includes/functions.php';

function _manually_load_plugin() {
	require dirname( __FILE__ ) . '/../../vendor/autoload.php';
}

tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

function _manually_load_options_pixie_plugin() {
	require dirname( __FILE__ ) . '/../../src/options-pixie.php';
}

tests_add_filter( 'muplugins_loaded', '_manually_load_options_pixie_plugin' );

require $_tests_dir . '/includes/bootstrap.php';

define( 'PHPUNIT_RUNNER', true );
define( 'WP_DEFAULT_THEME', 'default' );
