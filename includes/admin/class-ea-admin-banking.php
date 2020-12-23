<?php
/**
 * Admin Banking Page
 *
 * Functions used for displaying banking related pages.
 *
 * @author      EverAccounting
 * @category    Admin
 * @package     EverAccounting\Admin\Banking
 * @version     1.1.10
 */

defined( 'ABSPATH' ) || exit();

class EAccounting_Admin_Banking {
	/**
	 * EAccounting_Admin_Banking constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_page' ), 20 );
	}

	/**
	 * Registers the reports page.
	 *
	 */
	public function register_page() {
		add_submenu_page(
			'eaccounting',
			__( 'Banking', 'wp-ever-accounting' ),
			__( 'Banking', 'wp-ever-accounting' ),
			'manage_eaccounting',
			'ea-banking',
			array( $this, 'render_page' )
		);
	}

	/**
	 * Get banking page tabs.
	 *
	 * @since 1.1.0
	 * @return array
	 */
	public function get_tabs() {
		$tabs = array();
		if ( current_user_can( 'ea_manage_payment' ) && current_user_can( 'ea_manage_revenue' ) ) {
			$tabs['transactions'] = __( 'Transactions', 'wp-ever-accounting' );
		}
		if ( current_user_can( 'ea_manage_account' ) ) {
			$tabs['accounts'] = __( 'Accounts', 'wp-ever-accounting' );
		}
		if ( current_user_can( 'ea_manage_transfer' ) ) {
			$tabs['transfers'] = __( 'Transfers', 'wp-ever-accounting' );
		}

		return apply_filters( 'eaccounting_banking_tabs', $tabs );
	}

	/**
	 * Render page.
	 *
	 * @since 1.1.0
	 */
	public function render_page() {
		$tabs        = $this->get_tabs();
		$first_tab   = current( array_keys( $tabs ) );
		$current_tab = ! empty( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $tabs ) ? sanitize_title( $_GET['tab'] ) : $first_tab;
		?>
		<div class="wrap eaccounting ea-banking">
			<nav class="nav-tab-wrapper ea-nav-tab-wrapper">
				<?php
				foreach ( $tabs as $name => $label ) {
					echo '<a href="' . admin_url( 'admin.php?page=ea-banking&tab=' . $name ) . '" class="nav-tab ';
					if ( $current_tab === $name ) {
						echo 'nav-tab-active';
					}
					echo '">' . esc_html( $label ) . '</a>';
				}
				?>
			</nav>
			<h1 class="screen-reader-text"><?php echo esc_html( $tabs[ $current_tab ] ); ?></h1>
			<div class="ea-admin-page">
				<?php
				switch ( $current_tab ) {
					case 'accounts':
						self::accounts();
						break;
					case 'transactions':
						self::transactions();
						break;
					case 'transfers':
						self::transfers();
						break;
					default:
						if ( array_key_exists( $current_tab, $tabs ) && has_action( 'eaccounting_banking_tab_' . $current_tab ) ) {
							do_action( 'eaccounting_banking_tab_' . $current_tab );
						}
						break;
				}
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render accounts tab.
	 * @since 1.1.0
	 */
	public static function accounts() {
		if ( ! current_user_can( 'ea_manage_account' ) ) {
			wp_die( __( 'Sorry you are not allowed to access this page.', 'wp-ever-accounting' ) );
		}

		$action = isset( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ) : null;
		if ( in_array( $action, array( 'edit', 'add' ), true ) ) {
			include dirname( __FILE__ ) . '/views/accounts/edit-account.php';
		} else {
			require_once dirname( __FILE__ ) . '/list-tables/class-ea-account-list-table.php';
			$list_table = new EAccounting_Account_List_Table();
			$list_table->prepare_items();
			include dirname( __FILE__ ) . '/views/accounts/list-account.php';
		}
	}

	/**
	 * Render transactions tab.
	 * @since 1.1.0
	 */
	public static function transactions() {
		if ( ! current_user_can( 'ea_manage_payment' ) || ! current_user_can( 'ea_manage_revenue' ) ) {
			wp_die( __( 'Sorry you are not allowed to access this page.', 'wp-ever-accounting' ) );
		}

		$action = isset( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ) : null;

	}

	/**
	 * Render transfers tab.
	 *
	 * @since 1.1.0
	 */
	public static function transfers() {
		if ( ! current_user_can( 'ea_manage_account' ) ) {
			wp_die( __( 'Sorry you are not allowed to access this page.', 'wp-ever-accounting' ) );
		}

		$action = isset( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ) : null;
		var_dump( $action );
	}
}

new EAccounting_Admin_Banking();
