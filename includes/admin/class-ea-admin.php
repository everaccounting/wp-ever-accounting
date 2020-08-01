<?php
/**
 * EverAccounting Admin
 *
 * @class    EAccounting_Admin
 * @package  EverAccounting/Admin
 * @version  1.0.2
 */


defined( 'ABSPATH' ) || exit();

/**
 * Class EAccounting_Admin
 * @since 1.0.2
 */
class EAccounting_Admin {
	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'includes' ) );
		add_action( 'admin_init', array( $this, 'buffer' ), 1 );
		add_action( 'admin_footer', 'eaccounting_print_js', 25 );
		add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), 1 );
	}

	/**
	 * Output buffering allows admin screens to make redirects later on.
	 *
	 * @return void
	 * @since 1.0.2
	 */
	public function buffer() {
		ob_start();
	}


	/**
	 * Include any classes we need within admin.
	 *
	 * @return void
	 * @since 1.0.2
	 */
	public function includes() {
		include_once __DIR__ . '/ea-admin-functions.php';
		include_once __DIR__ . '/class-ea-admin-menus.php';
		include_once __DIR__ . '/banking/banking.php';
		include_once __DIR__ . '/accounts/accounts.php';
		//		include_once EACCOUNTING_ABSPATH . '/includes/abstracts/abstract-ea-admin-list-table.php';
	}

	/**
	 * Change the admin footer text on EverAccounting admin pages.
	 *
	 * @param string $footer_text text to be rendered in the footer.
	 *
	 * @return string
	 * @since  1.0.2
	 */
	public function admin_footer_text( $footer_text ) {
		if ( ! current_user_can( 'manage_options' ) || ! function_exists( 'eaccounting_get_screen_ids' ) ) {
			return $footer_text;
		}
		$current_screen = get_current_screen();
		$ea_pages       = eaccounting_get_screen_ids();

		// Set only EA pages.
		$ea_pages = array_diff( $ea_pages, array( 'profile', 'user-edit' ) );

		// Check to make sure we're on a WooCommerce admin page.
		if ( isset( $current_screen->id ) && apply_filters( 'eaccounting_display_admin_footer_text', in_array( $current_screen->id, $ea_pages, true ) ) ) {
			// Change the footer text.
			if ( ! get_option( 'eaccounting_admin_footer_text_rated' ) ) {
				$footer_text = sprintf(
					__( 'If you like %1$s please leave us a %2$s rating. A huge thanks in advance!', 'wp-ever-accounting' ),
					sprintf( '<strong>%s</strong>', esc_html__( 'Ever Accounting', 'wp-ever-accounting' ) ),
					'<a href="https://wordpress.org/support/plugin/wp-ever-accounting/reviews?rate=5#new-post" target="_blank" class="ea-rating-link" aria-label="' . esc_attr__( 'five star', 'wp-ever-accounting' ) . '" data-rated="' . esc_attr__( 'Thanks :)', 'wp-ever-accounting' ) . '">&#9733;&#9733;&#9733;&#9733;&#9733;</a>'
				);
				eaccounting_enqueue_js(
					"jQuery( 'a.ea-rating-link' ).click( function() {
						jQuery.post( '" . eaccounting()->ajax_url() . "', { action: 'eaccounting_rated' } );
						jQuery( this ).parent().text( jQuery( this ).data( 'rated' ) );
					});"
				);
			} else {
				$footer_text = __( 'Thank you for using with Ever Accounting.', 'wp-ever-accounting' );
			}
		}

		return $footer_text;
	}

}

return new EAccounting_Admin();
