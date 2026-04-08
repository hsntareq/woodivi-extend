<?php
/**
 * Plugin Name: WooDivi Extend
 * Plugin URI: https://example.com/woodivi-extend
 * Description: Extended features for WooCommerce with Divi integration
 * Version: 1.0.0
 * Author: Hasan Tareq
 * Author Email: hsntareq@gmail.com
 * Author URI: https://github.com/hsntareq
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: woodivi-extend
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.4
 *
 * @package WooDiviExtended
 */

// Prevent direct access to this file
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct access forbidden.' );
}

// Define plugin constants
define( 'WOO_DIVI_EXTENDED_VERSION', '1.0.0' );
define( 'WOO_DIVI_EXTENDED_PATH', plugin_dir_path( __FILE__ ) );
define( 'WOO_DIVI_EXTENDED_URL', plugin_dir_url( __FILE__ ) );
define( 'WOO_DIVI_EXTENDED_BASENAME', plugin_basename( __FILE__ ) );

// Autoloader
require_once WOO_DIVI_EXTENDED_PATH . 'vendor/autoload.php';

// Initialize the plugin
add_action( 'plugins_loaded', function() {
	\WooDiviExtended\Plugin::get_instance();
} );

// Activation hook
register_activation_hook( __FILE__, function() {
	\WooDiviExtended\Plugin::activate();
} );

// Deactivation hook
register_deactivation_hook( __FILE__, function() {
	\WooDiviExtended\Plugin::deactivate();
} );
