<?php
/**
 * Class to manage the plugin settings
 *
 * @package    Wp_Cxt
 * @subpackage Wp_Cxt/admin
 * @author     Jared Cobb <jared@alleyinteractive.com>
 */
class Wp_Cxt_Settings {

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
	 * @param    string    $plugin_name       The name of this plugin.
	 */
	public function __construct( $plugin_name ) {
		$this->plugin_name = $plugin_name;
		$this->current_settings = get_option( 'wp_cxt_settings', array() );
	}

	/**
	 * Callback for admin_menu hook to create
	 * a submenu page for the WP Connext settings
	 *
	 * @return void
	 */
	public function initialize_settings_page() {

		$settings_page = new Wp_Cxt_Settings_Page( $this->plugin_name );
		$is_first_submenu = $this->is_first_submenu();

		add_submenu_page(
			'mg2-top-menu',
			__( 'Connext Settings', 'wp-cxt' ),
			__( 'Connext Settings', 'wp-cxt' ),
			'manage_options',
			$is_first_submenu ? 'mg2-top-menu' : $this->plugin_name,
			array( $settings_page, 'render_page' )
		);
	}

	/**
	 * Callback for admin_init hook to configure
	 * the WP Connext plugin settings
	 *
	 * @return void
	 */
	public function initialize_settings_fields() {

		register_setting( $this->plugin_name, 'wp_cxt_settings', array( $this, 'sanitize_settings' ) );

		// The settings config is made up of an array of sections with each
		// section having it's own array of fields
		foreach ( $this->get_settings_config() as $section_id => $section ) {

			$settings_section = new Wp_Cxt_Settings_Section( $section_id, $section );
			add_settings_section(
				$section_id,
				$section['section_title'],
				array(
					$settings_section,
					'render_section',
				),
				$this->plugin_name
			);

			foreach ( $section['fields'] as $field_id => $field ) {

				$settings_field = new Wp_Cxt_Settings_Field( $field_id, $field );
				add_settings_field(
					$field_id,
					$field['title'],
					array(
						$settings_field,
						'render_field',
					),
					$this->plugin_name,
					$section_id
				);

			}

		}

	}

	/**
	 * Sanitize and validate the settings fields
	 *
	 * @access   public
	 * @since    1.0.0
	 * @param    array $user_input An array of fields to sanitize.
	 * @return   array $sanitized_input
	 */
	public function sanitize_settings( $user_input ) {

		$settings_config = $this->get_settings_config();
		$sanitized_input = array();

		// Go through each section in the config and use the
		// config data as the baseline for valid field data
		foreach ( $settings_config as $section ) {

			// Evaulate each field that the user submitted from the settings page
			foreach ( $user_input as $field_name => $field_value ) {

				// If the user's submitted field has a valid matching validation_type
				// setting from the settings config, validate its data type and sanitize it
				if ( ! empty( $section['fields'][ $field_name ]['validation_type'] ) ) {

					// Add additional validation types here (integers, domains, booleans, etc)
					// and be sure to sanitize the new validation type as well.
					switch ( $section['fields'][ $field_name ]['validation_type'] ) {

						case 'alphanumeric':

							// Allow the saving of empty fields or valid alphanumeric data
							// and then sanitize it
							if ( empty( $field_value ) || ctype_alnum( $field_value ) ) {
								$sanitized_input[ $field_name ] = sanitize_text_field( $field_value );
							} else {
								add_settings_error(
									$field_name,
									$field_name,
									sprintf(
										esc_html__(
											'%s can only contain letters and numbers',
											'wp-cxt'
										),
										esc_html( $section['fields'][ $field_name ]['title'] )
									)
								);
							}
							break;

						case 'alphanumeric_array':

							// Ensure fields that are multiselect (arrays) only contain
							// valid alphanumeric values
							if ( ! empty( $field_value ) && is_array( $field_value ) ) {
								$sanitized_array = array();
								foreach ( $field_value as $single_value ) {
									if ( ctype_alnum( $single_value ) ) {
										$sanitized_array[] = sanitize_text_field( $single_value );
									} else {
										add_settings_error(
											$field_name,
											$field_name,
											sprintf(
												esc_html__(
													'%s can only contain letters and numbers',
													'wp-cxt'
												),
												esc_html( $section['fields'][ $field_name ]['title'] )
											)
										);
									}
								}
								$sanitized_input[ $field_name ] = $sanitized_array;
							}
							break;
						case 'alphanumeric_comma':

							// Allow the saving of empty fields or valid alphanumeric data
							// and then sanitize it
							if ( preg_match( '/^[a-zA-Z0-9,]+$/', $field_value ) ) {
								$sanitized_input[ $field_name ] = sanitize_text_field( $field_value );
							} else {
								add_settings_error(
									$field_name,
									$field_name,
									sprintf(
										esc_html__(
											'%s can only contain letters, numbers or commas',
											'wp-cxt'
										),
										esc_html( $section['fields'][ $field_name ]['title'] )
									)
								);
							}
							break;
						// As a precaution, if the config has defined a validation_type but it
						// hasn't been defined here, ensure we at least sanitize it
						default:
							$sanitized_input[ $field_name ] = sanitize_text_field( $field_value );
							break;

					}

				}

			}

		}

		// Any data submitted that hasn't defined a validation_type will be discarded
		return $sanitized_input;

	}

	/**
	 * Get the sections and fields for the settings.
	 *
	 * The config array is a collection of section arrays
	 * that contain fields for its section. The fields are
	 * arrays of field settings including a render function,
	 * validation type, etc.
	 *
	 * @access    private
	 * @since     1.0.0
	 * @return    array
	 */
	private function get_settings_config() {
		$config = array(
			'general_settings' => array(
				'section_title' => esc_html__( 'General Settings', 'wp-cxt' ),
				'section_description' => __( 'Setup the general configuration of the Connext plugin', 'wp-cxt' ),
				'fields' => array(
					'site_code' => array(
						'title' => esc_html__( 'Site Code', 'wp-cxt' ),
						'render_function' => 'render_textfield',
						'placeholder' => __( 'Site Code', 'wp-cxt' ),
						'validation_type' => 'alphanumeric',
						'description' => __( 'This is your site code given by your PM.', 'wp-cxt' ),
						'attributes' => array(
							'class' => 'regular-text',
						),
					),
					'config_code' => array(
						'title' => esc_html__( 'Config Code', 'wp-cxt' ),
						'render_function' => 'render_textfield',
						'placeholder' => __( 'Config Code', 'wp-cxt' ),
						'validation_type' => 'alphanumeric',
						'description' => __( 'This is the configuration code you want to use. You can get this from the Connext Admin.', 'wp-cxt' ),
						'attributes' => array(
							'class' => 'regular-text',
						),
					),
					'attributes' => array(
						'title' => esc_html__( 'Attr', 'wp-cxt' ),
						'render_function' => 'render_textfield',
						'placeholder' => __( 'Attr', 'wp-cxt' ),
						'validation_type' => 'alphanumeric',
						'description' => __( 'This is the attributes.', 'wp-cxt' ),
						'attributes' => array(
							'class' => 'regular-text',
						),
					),
					'settings_key' => array(
						'title' => esc_html__( 'Settings Key', 'wp-cxt' ),
						'render_function' => 'render_textfield',
						'placeholder' => __( 'Settings Key', 'wp-cxt' ),
						'validation_type' => 'alphanumeric_comma',
						'description' => __( 'Settings key for multi paper.', 'wp-cxt' ),
						'attributes' => array(
							'class' => 'regular-text',
						),
					),
					'debug' => array(
						'title' => esc_html__( 'Debug', 'wp-cxt' ),
						'render_function' => 'render_chosen_select',
						'options' => array(
							'false' => __( 'No', 'wp-cxt' ),
							'true' => __( 'Yes', 'wp-cxt' ),
						),
						'default' => 'true',
						'validation_type' => 'alphanumeric',
						'description' => __( 'Controls how much is written to windows console.', 'wp-cxt' ),
						'attributes' => array(
							'class' => 'chosen-select',
						),
					),
					'environment' => array(
						'title' => esc_html__( 'Environment', 'wp-cxt' ),
						'render_function' => 'render_chosen_select',
						'options' => array(
							'test' => __( 'Test', 'wp-cxt' ),
							'stage' => __( 'Stage', 'wp-cxt' ),
							'prod' => __( 'Production', 'wp-cxt' ),
						),
						'default' => 'test',
						'validation_type' => 'alphanumeric',
						'attributes' => array(
							'class' => 'chosen-select',
						),
					),
					'silent_mode' => array(
						'title' => esc_html__( 'Silent Mode', 'wp-cxt' ),
						'render_function' => 'render_chosen_select',
						'options' => array(
							'true' => __( 'True', 'wp-cxt' ),
							'false' => __( 'False', 'wp-cxt' ),
						),
						'default' => 'false',
						'validation_type' => 'alphanumeric',
						'attributes' => array(
							'class' => 'chosen-select',
						),
					),
				),
			),
			'display_settings' => array(
				'section_title' => esc_html__( 'Display Settings', 'wp-cxt' ),
				'section_description' => __( 'Choose on which pages the Connext code should render', 'wp-cxt' ),
				'fields' => array(
					'display_home' => array(
						'title' => esc_html__( 'Display on Home Page', 'wp-cxt' ),
						'render_function' => 'render_chosen_select',
						'options' => array(
							'no' => __( 'No', 'wp-cxt' ),
							'yes' => __( 'Yes', 'wp-cxt' ),
						),
						'default' => 'no',
						'validation_type' => 'alphanumeric',
						'description' => __( 'Determined via `is_home()`', 'wp-cxt' ),
						'attributes' => array(
							'class' => 'chosen-select',
						),
					),
					'display_front' => array(
						'title' => esc_html__( 'Display on Front Page', 'wp-cxt' ),
						'render_function' => 'render_chosen_select',
						'options' => array(
							'no' => __( 'No', 'wp-cxt' ),
							'yes' => __( 'Yes', 'wp-cxt' ),
						),
						'default' => 'no',
						'validation_type' => 'alphanumeric',
						'description' => __( 'Determined via `is_front_page()`', 'wp-cxt' ),
						'attributes' => array(
							'class' => 'chosen-select',
						),
					),
				),
			),
		);

		// The taxonomy config is generated dynamically and merged into
		// the main config. By default it is a collection of public taxonomies.
		$config = $this->set_taxonomy_config( $config );

		return $config;

	}

	/**
	 * Determine which taxonomies are allowed to be part
	 * of the WP Connext configuration settings
	 *
	 * @param     array $config An array of the settings config
	 * @return    array
	 */
	private function set_taxonomy_config( $config ) {

		$taxonomies = get_taxonomies( array(
			'public' => true,
		), 'objects' );

		/**
		 * An array of taxonomy objects that the WP Connext plugin
		 * is aware of and capable of exposing in the settings page.
		 * By default, all public taxonomies are available.
		 *
		 * @param     array $config An array of the settings config
		 * @return    array
		 */
		$taxonomies = apply_filters( 'wp_cxt_settings_taxonomies', $taxonomies );

		if ( ! empty( $taxonomies ) && is_array( $taxonomies ) ) {
			foreach ( $taxonomies as $tax_object ) {
				if ( $tax_object instanceof WP_Taxonomy ) {
					$terms = get_terms( array(
						'taxonomy' => $tax_object->name,
						'hide_empty' => false,
					) );

					// Render a select field that will allow you to enable Connext
					// on "no", "all", or "some" terms of this taxonomy
					if ( ! empty( $terms ) ) {
						$config['display_settings']['fields'][ 'display_' . $tax_object->name ] = array(
							'title' => sprintf( __( 'Display on %s', 'wp-cxt' ), $tax_object->label ),
							'render_function' => 'render_chosen_select',
							'options' => array(
								'no' => sprintf( __( 'No %s', 'wp-cxt' ), $tax_object->label ),
								'all' => sprintf( __( 'All %s', 'wp-cxt' ), $tax_object->label ),
								'some' => sprintf( __( 'Some %s', 'wp-cxt' ), $tax_object->label ),
							),
							'default' => 'no',
							'validation_type' => 'alphanumeric',
							'description' => sprintf( __( 'Should the Connext code render on No, All, or Some %s?', 'wp-cxt' ), $tax_object->label ),
							'attributes' => array(
								'class' => 'chosen-select display-terms-parent',
								'data-term-selector' => 'display-' . $tax_object->name . '-terms',
							),
						);

						// If the user selects "some" terms they will see this additional multiselect
						// box that will allow them to choose which terms are whitelisted for WP Connext.
						//
						// NOTE: This chosen multiselect field has an autocomplete feature useful for tags.
						// The performance has been tested with up to 10,000 tags and the user experience
						// is fine, however it's recommended to disable taxonomies with more than 10,000 terms
						// via the `wp_cxt_settings_taxonomies` filter if performance becomes an issue.
						$config['display_settings']['fields'][ 'display_' . $tax_object->name . '_terms' ] = array(
							'title' => sprintf( __( 'Display on some %s', 'wp-cxt' ), $tax_object->label ),
							'render_function' => 'render_chosen_multiselect',
							'options' => $this->build_terms_options( $terms ),
							'validation_type' => 'alphanumeric_array',
							'description' => __( 'Choose one or more terms.', 'wp-cxt' ),
							'attributes' => array(
								'class' => 'chosen-select display-' . $tax_object->name . '-terms',
								'data-placeholder' => __( 'Choose one or more terms.', 'wp-cxt' ),
							),
						);
					}
				}
			}
		}

		return $config;
	}

	/**
	 * Helper method to create a formatted array
	 * of terms suitable for a select box
	 *
	 * @param     array $terms Array of term objects
	 * @return    array
	 */
	private function build_terms_options( $terms ) {
		$term_options = array();
		foreach ( $terms as $term ) {
			$term_options[ $term->term_id ] = $term->name;
		}
		return $term_options;
	}

	/**
	 * Determines whether or not this is the first MG2
	 * submenu to be registered under the parent MG2 page.
	 *
	 * @return bool
	 */
	private function is_first_submenu() {
		global $admin_page_hooks;
		if ( empty( $admin_page_hooks['mg2-top-menu'] ) ) {
			$this->create_top_menu();
			return true;
		}
		return false;
	}

	/**
	 * Creates an initial top level menu for any
	 * MG2 plugins that may be installed.
	 *
	 * @return void
	 */
	private function create_top_menu() {
		add_menu_page(
			__( 'MG2', 'wp-cxt' ),
			__( 'MG2', 'wp-cxt' ),
			'manage_options',
			'mg2-top-menu',
			'__return_null',
			'dashicons-admin-generic'
		);
	}

}
