<?php
/**
 * Class to manage the plugin settings page
 *
 * @package    Wp_Cxt
 * @subpackage Wp_Cxt/admin
 * @author     Jared Cobb <jared@alleyinteractive.com>
 */
class Wp_Cxt_Settings_Page {

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
	 * Render the settings page template
	 *
	 * @access   public
	 * @since    1.0.0
	 * @return   void
	 */
	public function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'wp-cxt' ) );
		}
		?>

		<div class="wrap <?php echo esc_attr( $this->plugin_name ); ?>">
			<h2><?php echo esc_html__( 'Connext Settings', 'wp-cxt' ); ?></h2>
			<?php settings_errors(); ?>
			<form action="options.php" method="post">

				<?php settings_fields( $this->plugin_name ); ?>
				<?php do_settings_sections( $this->plugin_name ); ?>

				<p class="submit">
					<input name="submit" class="button-primary" type="submit" value="<?php esc_attr_e( 'Save Changes', 'wp-cxt' ); ?>" />
				</p>
			</form>
		</div>

		<?php
	}

}
