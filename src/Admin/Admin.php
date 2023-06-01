<?php

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Class Admin
 *
 * @since 1.1.6
 * @package EverAccounting
 */
class Admin extends \EverAccounting\Singleton {

	/**
	 * Admin constructor.
	 *
	 * @since 1.1.6
	 */
	protected function __construct() {
		add_action( 'init', array( $this, 'includes' ), 0 );
		add_action( 'init', array( $this, 'init' ), 0 );
		add_action( 'admin_init', array( $this, 'buffer' ), 1 );
		add_action( 'wp_loaded', array( $this, 'save_settings' ) );
		add_filter( 'admin_body_class', array( $this, 'admin_body_class' ) );
		add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), 1 );
	}

	/**
	 * Include required files.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	public function includes() {
		require_once __DIR__ . '/Functions.php';
		Actions::instantiate();
		Extensions::instantiate();
		Products::instantiate();
		Banking::instantiate();
		Misc::instantiate();
		Overview::instantiate();
		Purchase::instantiate();
		Reports::instantiate();
		Sales::instantiate();
		Tools::instantiate();
	}

	/**
	 * Init admin.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	public function init() {
		Menus::instantiate();
	}

	/**
	 * Output buffering allows admin screens to make redirects later on.
	 *
	 * @since 1.0.2
	 * @return void
	 */
	public function buffer() {
		ob_start();
	}

	/**
	 * Save settings.
	 *
	 * @since 1.1.6
	 */
	public function save_settings() {
		global $current_tab, $current_section;
		$page = eac_get_input_var( 'page' );

		// We should only save on the settings page.
		if ( ! is_admin() || empty( $page ) || 'eac-settings' !== $page ) {
			return;
		}

		// Include settings pages.
		Settings::get_tabs();

		// Get current tab/section.
		$current_tab     = eac_get_input_var( 'tab', 'general' );
		$current_section = eac_get_input_var( 'section', '' );
		$is_save         = ! empty( eac_get_input_var( 'save', '', 'POST' ) );

		// Save settings if data has been posted.
		if ( '' !== $current_section && apply_filters( "ever_accounting_save_settings_{$current_tab}_{$current_section}", $is_save ) ) {
			Settings::save();
		} elseif ( '' === $current_section && apply_filters( "ever_accounting_save_settings_{$current_tab}", $is_save ) ) {
			Settings::save();
		}
	}

	/**
	 * Add custom class in admin body
	 *
	 * @since 1.0.2
	 *
	 * @param string $classes Admin body classes.
	 *
	 * @return string
	 */
	public function admin_body_class( $classes ) {
		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';
		if ( in_array( $screen_id, eac_get_screen_ids(), true ) ) {
			$classes .= ' ever-accounting ';
		}

		return $classes;
	}

	/**
	 * Change the admin footer text on EverAccounting admin pages.
	 *
	 * @since  1.0.2
	 *
	 * @param string $footer_text text to be rendered in the footer.
	 *
	 * @return string
	 */
	public function admin_footer_text( $footer_text ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return $footer_text;
		}
		$current_screen = get_current_screen();
		$screen_id      = $current_screen ? $current_screen->id : '';
		// Check to make sure we're on a EverAccounting admin page.
		if ( in_array( $screen_id, eac_get_screen_ids(), true ) ) {
			$footer_text = sprintf(
			/* translators: %s page */
				__( 'If you like %1$s please leave us a %2$s rating. A huge thanks in advance!', 'wp-ever-accounting' ),
				sprintf( '<strong>%s</strong>', esc_html__( 'Ever Accounting', 'wp-ever-accounting' ) ),
				'<a href="https://wordpress.org/support/plugin/wp-ever-accounting/reviews?rate=5#new-post" target="_blank" class="ea-rating-link" aria-label="' . esc_attr__( 'five star', 'wp-ever-accounting' ) . '" data-rated="' . esc_attr__( 'Thanks :)', 'wp-ever-accounting' ) . '">&#9733;&#9733;&#9733;&#9733;&#9733;</a>'
			);
		}

		return $footer_text;
	}
}
