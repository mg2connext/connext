<?php
/**
 * Class to manage the plugin settings fields
 *
 * @package    Wp_Cxt
 * @subpackage Wp_Cxt/admin
 * @author     Jared Cobb <jared@alleyinteractive.com>
 */
class Wp_Cxt_Settings_Field {

	/**
	 * The key for the field in the settings array
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $field_id
	 */
	private $field_id;

	/**
	 * An array of field settings
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $field
	 */
	private $field;

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
	 * @param   string $field_id Slug of the field.
	 * @param   array  $field Array of field settings.
	 * @return void
	 */
	public function __construct( $field_id, $field ) {

		$this->field_id = $field_id;
		$this->field = $field;
		$this->current_settings = get_option( 'wp_cxt_settings', array() );

	}

	/**
	 * General method that calls correct field render method based on config
	 *
	 * @access   public
	 * @since    1.0.0
	 * @return   void
	 */
	public function render_field() {
		// any new field types should be defined on this class and be assigned to the
		// settings config as a callable class method
		if ( isset( $this->field['render_function'] ) && is_callable( array( $this, $this->field['render_function'] ) ) ) {
			call_user_func( array( $this, $this->field['render_function'] ) );
		}

		if ( ! empty( $this->field['description'] ) ) {
			echo sprintf( '<p class="description">%s</p>', esc_html( $this->field['description'] ) );
		}
	}

	/**
	 * Render a textfield
	 *
	 * @access    private
	 * @since     1.0.0
	 * @return    void
	 */
	private function render_textfield() {
		$value = '';

		if ( isset( $this->current_settings[ $this->field_id ] ) ) {
			$value = $this->current_settings[ $this->field_id ];
		}

		echo sprintf(
			'<input type="text" placeholder="%1$s" name="wp_cxt_settings[%2$s]" value="%3$s" ',
			esc_attr( $this->field['placeholder'] ),
			esc_attr( $this->field_id ),
			esc_attr( $value )
		);

		$this->output_attributes();

		echo '/>';
	}

	/**
	 * Render a chosen select (single value select field)
	 *
	 * @access    private
	 * @since     1.0.0
	 * @return    void
	 */
	private function render_chosen_select() {
		$value = '';

		if ( isset( $this->current_settings[ $this->field_id ] ) ) {
			$value = $this->current_settings[ $this->field_id ];
		} elseif ( ! empty( $this->field['default'] ) ) {
			$value = $this->field['default'];
		}

		echo sprintf(
			'<select name="wp_cxt_settings[%1$s]" ',
			esc_attr( $this->field_id )
		);

		$this->output_attributes();

		echo '>';

		if ( ! empty( $this->field['options'] ) && is_array( $this->field['options'] ) ) {
			foreach ( $this->field['options'] as $key => $label ) {
				echo sprintf(
					'<option value="%1$s" %2$s>%3$s</option>',
					esc_attr( $key ),
					selected( $value, $key, false ),
					esc_html( $label )
				);
			}
		}

		echo '</select>';
	}

	/**
	 * Render a chosen mulitselect (array of values)
	 *
	 * @access    private
	 * @since     1.0.0
	 * @return    void
	 */
	private function render_chosen_multiselect() {
		$value = array();

		if ( ! empty( $this->current_settings[ $this->field_id ] ) && is_array( $this->current_settings[ $this->field_id ] ) ) {
			$value = $this->current_settings[ $this->field_id ];
		}

		echo sprintf(
			'<select name="wp_cxt_settings[%1$s][]" multiple ',
			esc_attr( $this->field_id )
		);

		$this->output_attributes();

		echo '>';

		// multiselect dropdowns should have an empty option on top
		// so that chosen can render some placeholder text
		echo '<option value></option>';

		if ( ! empty( $this->field['options'] ) && is_array( $this->field['options'] ) ) {
			foreach ( $this->field['options'] as $key => $label ) {
				echo sprintf(
					'<option value="%1$s" %2$s>%3$s</option>',
					esc_attr( $key ),
					selected( true, in_array( (string) $key, $value, true ), false ),
					esc_html( $label )
				);
			}
		}

		echo '</select>';
	}

	/**
	 * Common method to echo HTML attributes from the config
	 *
	 * @access   private
	 * @since    1.0.0
	 * @return   void
	 */
	private function output_attributes() {
		$attributes = '';
		if ( ! empty( $this->field['attributes'] ) && is_array( $this->field['attributes'] ) ) {
			foreach ( $this->field['attributes'] as $attr_name => $attr_value ) {
				echo sprintf(
					'%1$s="%2$s" ',
					esc_attr( $attr_name ),
					esc_attr( $attr_value )
				);
			}
		}
	}
}
