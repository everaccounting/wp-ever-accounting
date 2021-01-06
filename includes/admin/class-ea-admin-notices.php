<?php
/**
 * Display notices in admin
 *
 * @package EverAccounting\Admin
 * @version 1.0.2
 */

defined( 'ABSPATH' ) || exit;

class EverAccounting_Admin_Notices {
	/**
	 * The single instance of the class.
	 *
	 * @since 1.0.0
	 * @var EverAccounting_Admin_Notices
	 */
	protected static $instance = null;

	/**
	 * All notices.
	 *
	 * @since 1.0.2
	 * @var array
	 */
	private $notices = array();

	/**
	 * Array of notices - name => callback.
	 *
	 * @since 1.0.2
	 * @var array
	 */
	private $core_notices = array(
		'install'             => 'install_notice',
		'update'              => 'update_notice',
		'default_currency'    => 'default_currency_notice',
		'base_tables_missing' => 'base_tables_missing_notice',
	);

	/**
	 * Constructor.
	 */
	public static function init() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof \EverAccounting_Admin_Notices ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * EverAccounting_Admin_Notices constructor.
	 */
	public function __construct() {
		$this->notices = get_option( 'ea_admin_notices', array() );
		add_action( 'wp_loaded', array( $this, 'hide_notices' ) );
		if ( current_user_can( 'manage_eaccounting' ) ) {
			add_action( 'admin_print_styles', array( $this, 'maybe_show_notices' ) );
		}
	}


	/**
	 * Add notices + styles if needed.
	 */
	public  function maybe_show_notices() {
		$notices = $this->notices;

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
			if ( ! empty( $this->$core_notices[ $notice ] ) && apply_filters( 'eaccounting_show_admin_notice', true, $notice ) ) {
				add_action( 'admin_notices', array( $this, $this->core_notices[ $notice ] ) );
			} else {
				add_action( 'admin_notices', array( $this, 'output_custom_notices' ) );
			}
		}
	}

	/**
	 * Output any stored custom notices.
	 */
	public function output_custom_notices() {

		if ( ! empty( $this->notices ) ) {
			foreach ( $this->notices as $notice ) {
				if ( empty( $this->core_notices[ $notice ] ) ) {
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
	public function install_notice() {
		?>
		<div id="message" class="updated eaccounting-message">
			<p><?php _e( '<strong>Welcome to Ever Accounting</strong> &#8211; You&lsquo;re almost done :)', 'wp-ever-accounting' ); ?></p>
			<p class="submit"><a href="<?php echo esc_url( admin_url( 'admin.php?page=ea-setup' ) ); ?>" class="button-primary"><?php _e( 'Run the Setup Wizard', 'wp-ever-accounting' ); ?></a> <a class="button-secondary skip" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'ea-hide-notice', 'install' ), 'eaccounting_hide_notices_nonce', '_ea_notice_nonce' ) ); ?>"><?php _e( 'Skip setup', 'wp-ever-accounting' ); ?></a></p>
		</div>
		<?php
	}


	/**
	 * Notice about base tables missing.
	 */
	public function base_tables_missing_notice() {
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
							/* translators: %1$s table names */
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

//EverAccounting_Admin_Notices::init();
