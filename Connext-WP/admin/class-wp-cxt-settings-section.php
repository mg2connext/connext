<?php
/**
 * Class to manage the plugin settings sections
 *
 * @package    Wp_Cxt
 * @subpackage Wp_Cxt/admin
 * @author     Jared Cobb <jared@alleyinteractive.com>
 */
class Wp_Cxt_Settings_Section {

	/**
	 * The key for the section in the settings array
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $section_id
	 */
	private $section_id;

	/**
	 * An array of field sections and their settings
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $section
	 */
	private $section;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param   string $section_id The slug of the section.
	 * @param   array  $section An array of section settings.
	 * @return void
	 */
	public function __construct( $section_id, $section ) {
		$this->section_id = $section_id;
		$this->section = $section;
	}

	/**
	 * Render the section
	 *
	 * @access    public
	 * @since     1.0.0
	 * @return    void
	 */
	public function render_section() {
		if ( ! empty( $this->section['section_description'] ) ) {
			echo sprintf( '<p>%s</p>', esc_html( $this->section['section_description'] ) );
		}
	}

}
