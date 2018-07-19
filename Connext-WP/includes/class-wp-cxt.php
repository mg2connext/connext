<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://marketingg2.com
 * @since      1.0.0
 *
 * @package    Wp_Cxt
 * @subpackage Wp_Cxt/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin.
 *
 * @since      1.0.0
 * @package    Wp_Cxt
 * @subpackage Wp_Cxt/includes
 * @author     Marketing G2 <jscanlon@marketingg2.com>
 */
class Wp_Cxt {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      Wp_Cxt_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	private $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	private $plugin_name;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'wp-cxt';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Wp_Cxt_Loader. Orchestrates the hooks of the plugin.
	 * - Wp_Cxt_i18n. Defines internationalization functionality.
	 * - Wp_Cxt_Admin. Defines all hooks for the admin area.
	 * - Wp_Cxt_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once WP_CXT_PATH . 'includes/class-wp-cxt-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once WP_CXT_PATH . 'includes/class-wp-cxt-i18n.php';

		/**
		 * The classes responsible for managing the plugin settings
		 */
		require_once WP_CXT_PATH . 'admin/class-wp-cxt-settings.php';
		require_once WP_CXT_PATH . 'admin/class-wp-cxt-settings-page.php';
		require_once WP_CXT_PATH . 'admin/class-wp-cxt-settings-section.php';
		require_once WP_CXT_PATH . 'admin/class-wp-cxt-settings-field.php';
		require_once WP_CXT_PATH . 'admin/class-wp-cxt-meta-box.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once WP_CXT_PATH . 'admin/class-wp-cxt-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once WP_CXT_PATH . 'public/class-wp-cxt-public.php';

		$this->loader = new Wp_Cxt_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Wp_Cxt_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Wp_Cxt_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Wp_Cxt_Admin( $this->get_plugin_name() );
		$plugin_settings = new Wp_Cxt_Settings( $this->get_plugin_name() );
		$plugin_meta_box = new Wp_Cxt_Meta_Box( $this->get_plugin_name() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_settings, 'initialize_settings_page' );
		$this->loader->add_action( 'admin_init', $plugin_settings, 'initialize_settings_fields' );
		$this->loader->add_action( 'add_meta_boxes', $plugin_meta_box, 'initialize_meta_box' );
		$this->loader->add_action( 'save_post', $plugin_meta_box, 'save_meta_box' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Wp_Cxt_Public( $this->get_plugin_name() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Wp_Cxt_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

}
