<?php
/**
 * Display notices in admin
 *
 * @package EverAccounting\Admin
 * @version 1.0.2
 */

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit;

class Admin_Notices {
	/**
	 * All notices.
	 *
	 * @since 1.0.2
	 * @var array
	 */
	private static $notices = array();

	/**
	 * Array of notices - name => callback.
	 *
	 * @since 1.0.2
	 * @var array
	 */
	private static $core_notices = array(
		'install'             => 'install_notice',
		'update'              => 'update_notice',
		'default_currency'    => 'default_currency_notice',
		'base_tables_missing' => 'base_tables_missing_notice',
	);

	/**
	 * Constructor.
	 */
	public static function init() {
		self::$notices = get_option( 'ea_admin_notices', array() );
		add_action( 'wp_loaded', array( __CLASS__, 'hide_notices' ) );
		if ( current_user_can( 'manage_eaccounting' ) ) {
			add_action( 'admin_print_styles', array( __CLASS__, 'add_notices' ) );
		}
	}

	/**
	 * Save notices to DB
	 */
	public static function save_notices() {
		update_option( 'ea_admin_notices', self::get_notices() );
	}

	/**
	 * Get notices
	 *
	 * @return array
	 */
	public static function get_notices() {
		return self::$notices;
	}

	/**
	 * Remove all notices.
	 */
	public static function remove_all_notices() {
		self::$notices = array();
	}

	/**
	 * Show a notice.
	 *
	 * @param string $name       Notice name.
	 * @param bool   $force_save Force saving inside this method instead of at the 'shutdown'.
	 */
	public static function add_notice( $name, $force_save = false ) {
		self::$notices = array_unique( array_merge( self::get_notices(), array( $name ) ) );

		if ( $force_save ) {
			// Adding early save to prevent more race conditions with notices.
			self::save_notices();
		}
	}

	/**
	 * Remove a notice from being displayed.
	 *
	 * @param string $name       Notice name.
	 * @param bool   $force_save Force saving inside this method instead of at the 'shutdown'.
	 */
	public static function remove_notice( $name, $force_save = false ) {
		self::$notices = array_diff( self::get_notices(), array( $name ) );
		delete_option( 'ea_admin_notice_' . $name );

		if ( $force_save ) {
			// Adding early save to prevent more race conditions with notices.
			self::save_notices();
		}
	}

	/**
	 * See if a notice is being shown.
	 *
	 * @param string $name Notice name.
	 *
	 * @return boolean
	 */
	public static function has_notice( $name ) {
		return in_array( $name, self::get_notices(), true );
	}

	/**
	 * Hide a notice if the GET variable is set.
	 */
	public static function hide_notices() {
		if ( isset( $_GET['ea-hide-notice'] ) && isset( $_GET['_ea_notice_nonce'] ) ) {
			if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_ea_notice_nonce'] ) ), 'eaccounting_hide_notices_nonce' ) ) {
				wp_die( esc_html__( 'Action failed. Please refresh the page and retry.', 'wp-ever-accounting' ) );
			}

			if ( ! current_user_can( 'manage_eaccounting' ) ) {
				wp_die( esc_html__( 'You don&#8217;t have permission to do this.', 'wp-ever-accounting' ) );
			}

			$hide_notice = sanitize_text_field( wp_unslash( $_GET['ea-hide-notice'] ) ); // WPCS: input var ok, CSRF ok.

			self::remove_notice( $hide_notice );

			update_user_meta( get_current_user_id(), 'dismissed_' . $hide_notice . '_notice', true );

			do_action( 'eaccounting_hide_' . $hide_notice . '_notice' );
		}
	}

	/**
	 * Add notices + styles if needed.
	 */
	public static function add_notices() {
		$notices = self::get_notices();

		if ( empty( $notices ) ) {
			return;
		}

		$screen          = get_current_screen();
		$screen_id       = $screen ? $screen->id : '';
		$show_on_screens = array(
			'dashboard',
			'plugins',
		);

		// Notices should only show on EverAccounting screens, the main dashboard, and on the plugins screen.
		if ( ! in_array( $screen_id, eaccounting_get_screen_ids(), true ) && ! in_array( $screen_id, $show_on_screens, true ) ) {
			return;
		}

		foreach ( $notices as $notice ) {
			if ( ! empty( self::$core_notices[ $notice ] ) && apply_filters( 'eaccounting_show_admin_notice', true, $notice ) ) {
				add_action( 'admin_notices', array( __CLASS__, self::$core_notices[ $notice ] ) );
			} else {
				add_action( 'admin_notices', array( __CLASS__, 'output_custom_notices' ) );
			}
		}
	}

	/**
	 * Output any stored custom notices.
	 */
	public static function output_custom_notices() {
		$notices = self::get_notices();

		if ( ! empty( $notices ) ) {
			foreach ( $notices as $notice ) {
				if ( empty( self::$core_notices[ $notice ] ) ) {
					$notice_html = get_option( 'ea_admin_notice_' . $notice );

					if ( $notice_html ) {
						?>
						<div id="message" class="updated eaccounting-message">
							<a class="eaccounting-message-close notice-dismiss" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'ea-hide-notice', $notice ), 'eaccounting_hide_notices_nonce', '_ea_notice_nonce' ) ); ?>"><?php _e( 'Dismiss', 'wp-ever-accounting' ); ?></a>
							<?php echo wp_kses_post( wpautop( $notice_html ) ); ?>
						</div>
						<?php
					}
				}
			}
		}
	}


	/**
	 * If we have just installed, show a message with the install pages button.
	 */
	public static function install_notice() {
		?>
		<div id="message" class="updated eaccounting-message">
			<p><?php _e( '<strong>Welcome to Ever Accounting</strong> &#8211; You&lsquo;re almost ready to start selling :)', 'wp-ever-accounting' ); ?></p>
			<p class="submit"><a href="<?php echo esc_url( admin_url( 'admin.php?page=ea-setup' ) ); ?>" class="button-primary"><?php _e( 'Run the Setup Wizard', 'wp-ever-accounting' ); ?></a> <a class="button-secondary skip" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'ea-hide-notice', 'install' ), 'eaccounting_hide_notices_nonce', '_ea_notice_nonce' ) ); ?>"><?php _e( 'Skip setup', 'wp-ever-accounting' ); ?></a></p>
		</div>
		<?php
	}


	/**
	 * Notice about base tables missing.
	 */
	public static function base_tables_missing_notice() {
		$missing_tables = get_option( 'eaccounting_schema_missing_tables' );
		?>
		<div id="message" class="error eaccounting-message">
			<p>
				<strong><?php esc_html_e( 'Database tables missing', 'wp-ever-accounting' ); ?></strong>
			</p>
			<p>
				<?php
				echo wp_kses_post(
					sprintf(
						__( 'One or more tables required for Ever Accounting to function are missing, some features may not work as expected. Missing tables: %1$s.', 'wp-ever-accounting' ),
						esc_html( implode( ', ', $missing_tables ) )
					)
				);
				?>
			</p>
		</div>
		<?php
	}

}

Admin_Notices::init();
