<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://marketingg2.com
 * @since      1.0.0
 *
 * @package    Wp_Cxt
 * @subpackage Wp_Cxt/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    Wp_Cxt
 * @subpackage Wp_Cxt/admin
 * @author     Marketing G2 <jscanlon@marketingg2.com>
 */
class Wp_Cxt_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 */
	public function __construct( $plugin_name ) {
		$this->plugin_name = $plugin_name;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		$screen = get_current_screen();
		// only enqueue the chosen css on the specific WP Connext settings page
		if ( ! empty( $screen->id ) && ( 'toplevel_page_mg2-top-menu' === $screen->id || 'mg2_page_wp-cxt' === $screen->id ) ) {
			wp_enqueue_style( 'chosen', WP_CXT_URL . 'admin/css/chosen/chosen.min.css', array(), '1.7.0', 'all' );
		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		$screen = get_current_screen();
		// only enqueue the chosen js and custom functionality on the specific WP Connext settings page
		if ( ! empty( $screen->id ) && ( 'toplevel_page_mg2-top-menu' === $screen->id || 'mg2_page_wp-cxt' === $screen->id ) ) {
			wp_enqueue_script( 'chosen', WP_CXT_URL . 'admin/js/chosen/chosen.jquery.min.js', array( 'jquery' ), '1.7.0', true );
			wp_enqueue_script( 'wp-cxt-admin', WP_CXT_URL . 'admin/js/wp-cxt-admin.js', array( 'chosen' ), WP_CXT_VERSION, true );
		}

	}
}
