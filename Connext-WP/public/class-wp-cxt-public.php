<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://marketingg2.com
 * @since      1.0.0
 *
 * @package    Wp_Cxt
 * @subpackage Wp_Cxt/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * @package    Wp_Cxt
 * @subpackage Wp_Cxt/public
 * @author     Marketing G2 <jscanlon@marketingg2.com>
 */
class Wp_Cxt_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The existing settings currently stored
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $current_settings
	 */
	private $current_settings;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 */
	public function __construct( $plugin_name ) {
		$this->plugin_name = $plugin_name;
		$this->current_settings = get_option( 'wp_cxt_settings', array() );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * Whether or not the WP Connext script should be enqueued
		 * on this specific public page. Fired during `wp_enqueue_scripts`
		 *
		 * @param    bool $connext_enabled
		 */
		$connext_enabled = apply_filters( 'wp_cxt_is_connext_enabled', $this->connext_enabled() );
		if ( $connext_enabled ) {
			wp_enqueue_style( 'wp-cxt-public', WP_CXT_URL . 'public/css/Connext.min.css', array(), WP_CXT_VERSION, 'all' );
		}
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * Whether or not the WP Connext script should be enqueued
		 * on this specific public page. Fired during `wp_enqueue_scripts`
		 *
		 * @param    bool $connext_enabled
		 */
		$connext_enabled = apply_filters( 'wp_cxt_connext_enabled', $this->connext_enabled() );
		if ( $connext_enabled ) {
			if ( 'test' === $this->current_settings['environment'] ) {
				wp_register_script( 'connext', WP_CXT_URL . 'public/js/Connext.js', array( 'jquery' ), WP_CXT_VERSION, true );
			} else {
				wp_register_script( 'connext', WP_CXT_URL . 'public/js/Connext.min.js', array( 'jquery' ), WP_CXT_VERSION, true );
			}

			wp_register_script( 'wp-connext-public', WP_CXT_URL . 'public/js/wp-cxt-public.js', array( 'jquery' ), WP_CXT_VERSION, true );

			$connext_script_settings = $this->get_connext_script_settings();
			wp_localize_script( 'wp-connext-public', 'WP_CXT', $connext_script_settings );

			wp_enqueue_script( 'connext' );
			wp_enqueue_script( 'wp-connext-public' );
		}

	}

	/**
	 * Determine whether or not the WP Connext scripts should
	 * be rendered on this specific public page.
	 *
	 * Uses the WP Connext Settings to set a priority level. General
	 * Conditional Tags are evaluated first (i.e. home). Next, taxonomy
	 * archives are evaluated. Finally singular pages are evaluated.
	 * Singular pages that have defined "yes" or "no" on their specific
	 * posts/pages take the highest priority. As a fallback, a post/page
	 * is evaluated for any matching terms based on the taxonomy
	 * settings on the settings page.
	 *
	 * @return bool
	 */
	private function connext_enabled() {
		// First determine if the home page or front page are enabled
		if ( is_home() && ! empty( $this->current_settings['display_home'] ) && 'yes' === $this->current_settings['display_home'] ) {
			return true;
		} elseif ( is_front_page() && ! empty( $this->current_settings['display_front'] ) && 'yes' === $this->current_settings['display_front'] ) {
			return true;
		} elseif ( is_category() || is_tag() || is_tax() ) {
			$term = get_queried_object();
			// If the settings have defined a general setting for this taxonomy
			if ( ! empty( $this->current_settings[ 'display_' . $term->taxonomy ] ) ) {
				if ( 'no' === $this->current_settings[ 'display_' . $term->taxonomy ] ) {
					return false;
				} elseif ( 'all' === $this->current_settings[ 'display_' . $term->taxonomy ] ) {
					return true;
				} elseif (
					// If this archive is a term included in the "some" terms multiselect box
					'some' === $this->current_settings[ 'display_' . $term->taxonomy ]
					&& ! empty( $this->current_settings[ 'display_' . $term->taxonomy . '_terms' ] )
					&& in_array( (string) $term->term_id, $this->current_settings[ 'display_' . $term->taxonomy . '_terms' ], true )
				) {
					return true;
				}
			}
		} elseif ( is_singular() ) {
			$post = get_queried_object();
			// First check the post meta as the post's specific settings should take priority
			$wp_cxt_display_script = get_post_meta( $post->ID, 'wp_cxt_display_script', true );
			if ( ! empty( $wp_cxt_display_script ) ) {
				if ( 'no' === $wp_cxt_display_script ) {
					return false;
				} elseif ( 'yes' === $wp_cxt_display_script ) {
					return true;
				}
			} else {
				// Check this post's taxonomies for any matching taxonomies that were
				// set in the WP Connext Settings page
				$post_taxonomies = get_object_taxonomies( $post->post_type, 'objects' );
				if ( ! empty( $post_taxonomies ) ) {
					foreach ( $post_taxonomies as $taxonomy ) {
						// If this taxonomy does have a general setting configured
						if ( ! empty( $this->current_settings[ 'display_' . $taxonomy->name ] ) ) {
							// If the setting is "no" continue to evaluate any other taxonomies
							if ( 'no' === $this->current_settings[ 'display_' . $taxonomy->name ] ) {
								continue;
							} elseif ( 'all' === $this->current_settings[ 'display_' . $taxonomy->name ] && has_term( '', $taxonomy->name ) ) {
								return true;
							} elseif ( 'some' === $this->current_settings[ 'display_' . $taxonomy->name ] ) {
								// Determine if this post has any of the terms that are whitelisted in the settings page
								if ( ! empty( $this->current_settings[ 'display_' . $taxonomy->name . '_terms' ] ) ) {
									$term_ids = array_map( 'absint', $this->current_settings[ 'display_' . $taxonomy->name . '_terms' ] );
									if ( has_term( $term_ids, $taxonomy->name ) ) {
										return true;
									}
								}
							}
						}
					}
				}
			}
		}

		// If no business rules were matched, do not enqueue the Connext code
		return false;
	}

	/**
	 * Get an array of general settings for the Connext code
	 * which will be localized. Also escapes values for js.
	 *
	 * @return array
	 */
	private function get_connext_script_settings() {

		return  array(
			'siteCode' => isset( $this->current_settings['site_code'] ) ? esc_js( $this->current_settings['site_code'] ) : '',
			'configCode' => isset( $this->current_settings['config_code'] ) ? esc_js( $this->current_settings['config_code'] ) : '',
			'attr' => isset( $this->current_settings['attributes'] ) ? esc_js( $this->current_settings['attributes'] ) : '',
			'settingsKey' => isset( $this->current_settings['settings_key'] ) ? esc_js( $this->current_settings['settings_key'] ) : '',
			'debug' => isset( $this->current_settings['debug'] ) ? esc_js( $this->current_settings['debug'] ) : '',
			'environment' => isset( $this->current_settings['environment'] ) ? esc_js( $this->current_settings['environment'] ) : '',
			'silentMode' => isset( $this->current_settings['silent_mode'] ) ? esc_js( $this->current_settings['silent_mode'] ) : '',
		);

	}

}
