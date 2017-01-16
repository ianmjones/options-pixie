<?php

/**
 * @link              https://www.bytepixie.com/options-pixie/
 * @since             1.0
 * @package           Options_Pixie
 *
 * @wordpress-plugin
 * Plugin Name:       Options Pixie
 * Plugin URI:        https://www.bytepixie.com/options-pixie/
 * Description:       List, filter, sort and view options records, even serialized and base64 encoded values.
 * Version:           1.1.2
 * Author:            Byte Pixie
 * Author URI:        https://www.bytepixie.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       options-pixie
 * Domain Path:       /languages
 * Network:           True
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-options-pixie-activator.php
 *
 * @since 1.0
 */
function activate_options_pixie() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-options-pixie-activator.php';
	Options_Pixie_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-options-pixie-deactivator.php
 *
 * @since 1.0
 */
function deactivate_options_pixie() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-options-pixie-deactivator.php';
	Options_Pixie_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_options_pixie' );
register_deactivation_hook( __FILE__, 'deactivate_options_pixie' );

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 *
 * @since 1.0
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-options-pixie.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since 1.0
 */
function run_options_pixie() {

	$plugin = new Options_Pixie();
	$plugin->run();
}

run_options_pixie();
