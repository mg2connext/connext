<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://marketingg2.com
 * @since             1.0.0
 * @package           Wp_Cxt
 *
 * @wordpress-plugin
 * Plugin Name:       Connext
 * Plugin URI:        http://marketingg2.com
 * Description:       To enable Connext pleasce contact your project manager for site configuration data.
 * Version:           1.14.2
 * Author:            Marketing G2
 * Author URI:        http://marketingg2.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-cxt
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'WP_CXT_PATH', plugin_dir_path( __FILE__ ) );
define( 'WP_CXT_URL', plugin_dir_url( __FILE__ ) );
define( 'WP_CXT_BASENAME', plugin_basename( __FILE__ ) );
define( 'WP_CXT_VERSION', '1.14.2' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require WP_CXT_PATH . 'includes/class-wp-cxt.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_cxt() {

	$plugin = new Wp_Cxt();
	$plugin->run();

}
run_wp_cxt();
