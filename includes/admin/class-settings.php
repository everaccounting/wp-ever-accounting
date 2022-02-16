<?php
/**
 * Admin Settings.
 *
 * @since       1.0.2
 * @subpackage  Admin
 * @package     Ever_Accounting
 */

namespace Ever_Accounting\Admin;

defined( 'ABSPATH' ) || exit();

/**
 * Class Settings
 *
 * @since   1.0.2
 * @package Ever_Accounting\Admin
 */
class Settings {

	/**
	 * Setting pages.
	 *
	 * @var array
	 */
	private static $settings = array();

	/**
	 * Include the settings page classes.
	 */
	public static function get_settings() {
		if ( empty( self::$settings ) ) {
			$settings   = array();
			$settings[] = include __DIR__ . '/settings/class-ea-general-settings.php';
			$settings[] = include __DIR__ . '/settings/class-ea-status-settings.php';

			self::$settings = apply_filters( 'starter_plugin_settings', $settings );
		}

		return self::$settings;
	}


	public static function get_tabs() {
		$tabs     = array();
		$settings = self::get_settings();
		foreach ( $settings as $setting ) {
			if ( empty( $setting->get_sections() ) ) {
				continue;
			}
			$tabs[ $setting->get_id() ] = $setting;
		}

		return $tabs;
	}

	/**
	 * Settings page.
	 *
	 * Handles the display of the main settings page in admin.
	 */
	public static function output() {
		$tabs            = self::get_tabs();
		$default_tab     = current( $tabs )->get_id();
		$current_tab     = isset( $_GET['tab'] ) ? sanitize_title( $_GET['tab'] ) : $default_tab;
		$current_section = empty( $_REQUEST['section'] ) ? '' : sanitize_title( wp_unslash( $_REQUEST['section'] ) );
		$settings_tab    = $tabs[ $current_tab ];
		?>
		<div class="wrap">
		<form method="post" id="mainform" action="" enctype="multipart/form-data">

			<nav class="nav-tab-wrapper">
				<?php
				foreach ( $tabs as $tab ) {
					$active_tab = ( $current_tab === $tab->get_id() ) ? 'nav-tab-active' : '';
					echo '<a href="' . esc_url( add_query_arg( array( 'tab' => $tab->get_id() ), admin_url( 'admin.php?page=eaccounting-settings' ) ) ) . '" class="nav-tab ' . $active_tab . '">' . esc_html( $tab->get_label() ) . '</a>';
				}
				?>
			</nav>
			<h1 class="screen-reader-text"><?php echo esc_html( $tab->get_label() ); ?></h1>
			<?php $settings_tab->output_sections( $current_section ); ?>
			<?php if ( $settings_tab->save( $current_section ) ): ?>
				<div id="message" class="updated notice is-dismissible"><p><strong><?php _e( 'Settings saved.', 'text_domain' ); ?></strong></p></div>
			<?php endif; ?>
			<?php $settings_tab->output( $current_section ); ?>
			<p class="submit">
				<?php if ( ! empty( $GLOBALS['hide_save_button'] ) ) : ?>
					<?php submit_button( __( 'Save Changes', 'text_domain' ), 'primary', 'save', false ); ?>
				<?php endif; ?>
			</p>
		</form>
		<?php
	}
}
