<?php
/**
 * Plugin Name:       Advanced WooCommerce User Panel
 * Description:       Provides a customizable WooCommerce customer dashboard with modular widgets.
 * Version:           0.1.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Your Name
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       woo-advanced-user-panel
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! defined( 'WAUP_PLUGIN_FILE' ) ) {
    define( 'WAUP_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'WAUP_PLUGIN_PATH' ) ) {
    define( 'WAUP_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'WAUP_PLUGIN_URL' ) ) {
    define( 'WAUP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'WAUP_PLUGIN_VERSION' ) ) {
    define( 'WAUP_PLUGIN_VERSION', '0.1.0' );
}

require_once WAUP_PLUGIN_PATH . 'includes/class-waup-loader.php';

/**
 * Begins execution of the plugin.
 */
function waup_run_plugin() {
    $loader = new Waup_Loader();
    $loader->run();
}
waup_run_plugin();

register_activation_hook( __FILE__, [ 'Waup_Loader', 'activate' ] );
register_deactivation_hook( __FILE__, [ 'Waup_Loader', 'deactivate' ] );
