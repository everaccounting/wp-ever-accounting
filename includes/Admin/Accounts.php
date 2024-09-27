<?php

namespace EverAccounting\Admin;

use EverAccounting\Admin\Banking\Tables;
use EverAccounting\Models\Account;

defined( 'ABSPATH' ) || exit;

/**
 * Class Accounts
 *
 * @since 3.0.0
 * @package EverAccounting\Admin\Banking
 */
class Accounts {
	/**
	 * Accounts constructor.
	 */
	public function __construct() {
		add_filter( 'eac_banking_page_tabs', array( __CLASS__, 'register_tabs' ) );
		add_filter( 'set-screen-option', array( __CLASS__, 'set_screen_option' ), 10, 3 );
		add_action( 'load_eac_banking_page_accounts', array( __CLASS__, 'setup_table' ) );
		add_action( 'eac_banking_page_accounts', array( __CLASS__, 'render_table' ) );
		add_action( 'eac_banking_page_accounts_add', array( __CLASS__, 'render_add' ) );
		add_action( 'eac_banking_page_accounts_edit', array( __CLASS__, 'render_edit' ) );
		add_action( 'admin_post_eac_edit_account', array( __CLASS__, 'handle_edit' ) );
	}

	/**
	 * Register tab.
	 *
	 * @param array $tabs Tabs.
	 *
	 * @since 3.0.0
	 * @return array
	 */
	public static function register_tabs( $tabs ) {
		$tabs['accounts'] = __( 'Accounts', 'wp-ever-accounting' );

		return $tabs;
	}

	/**
	 * Set screen option.
	 *
	 * @param mixed  $status Status.
	 * @param string $option Option.
	 * @param mixed  $value Value.
	 *
	 * @since 3.0.0
	 * @return mixed
	 */
	public static function set_screen_option( $status, $option, $value ) {
		global $list_table;
		if ( "eac_accounts_per_page" === $option ) {
			return $value;
		}

		return $status;
	}

	/**
	 * setup accounts list.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public static function setup_table() {
		global $list_table;
		$screen     = get_current_screen();
		$list_table = new ListTables\Accounts();
		$list_table->prepare_items();
		$screen->add_option( 'per_page', array(
			'label'   => __( 'Number of accounts per page:', 'wp-ever-accounting' ),
			'default' => 20,
			'option'  => "eac_accounts_per_page",
		) );
	}

	/**
	 * Render table.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public static function render_table() {
		global $list_table;
		include __DIR__ . '/views/account-list.php';
	}

	/**
	 * Render add form.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public static function render_add() {
		$account = new Account();
		include __DIR__ . '/views/account-add.php';
	}

	/**
	 * Render edit accounts form.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public static function render_edit() {
		$id      = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );
		$account = Account::find( $id );
		if ( ! $account ) {
			esc_html_e( 'The specified accounts does not exist.', 'wp-ever-accounting' );

			return;
		}
		include __DIR__ . '/views/account-edit.php';
	}

	/**
	 * Handle edit.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	public static function handle_edit() {
		check_admin_referer( 'eac_edit_account' );
		$referer = wp_get_referer();
		$account = EAC()->accounts->insert(
			array(
				'id'              => isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0,
				'name'            => isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '',
				'number'          => isset( $_POST['number'] ) ? sanitize_text_field( wp_unslash( $_POST['number'] ) ) : '',
				'type'            => isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '',
				'currency_code'   => isset( $_POST['currency_code'] ) ? sanitize_text_field( wp_unslash( $_POST['currency_code'] ) ) : '',
				'opening_balance' => isset( $_POST['opening_balance'] ) ? floatval( wp_unslash( $_POST['opening_balance'] ) ) : 0,
				'bank_name'       => isset( $_POST['bank_name'] ) ? sanitize_text_field( wp_unslash( $_POST['bank_name'] ) ) : '',
				'bank_phone'      => isset( $_POST['bank_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['bank_phone'] ) ) : '',
				'bank_address'    => isset( $_POST['bank_address'] ) ? sanitize_text_field( wp_unslash( $_POST['bank_address'] ) ) : '',
				'status'          => isset( $_POST['status'] ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : 'active',
			)
		);

		if ( is_wp_error( $account ) ) {
			EAC()->flash->error( $account->get_error_message() );
		} else {
			EAC()->flash->success( __( 'Account saved successfully.', 'wp-ever-accounting' ) );
			$referer = add_query_arg( ['action' => 'edit', 'id' => $account->id ], $referer );
			$referer = remove_query_arg( array( 'add' ), $referer );
		}

		wp_safe_redirect( $referer );
		exit;
	}
}
