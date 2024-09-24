<?php

namespace EverAccounting\Admin\Banking;

use EverAccounting\Models\Transfer;

defined( 'ABSPATH' ) || exit;

/**
 * Class Transfers
 *
 * @since 3.0.0
 * @package EverAccounting\Admin\Banking
 */
class Transfers {
	/**
	 * Transfers constructor.
	 */
	public function __construct() {
		add_filter( 'eac_banking_page_tabs', array( __CLASS__, 'register_tabs' ) );
		add_filter( 'set-screen-option', array( __CLASS__, 'set_screen_option' ), 10, 3 );
		add_action( 'load_eac_banking_page_transfers', array( __CLASS__, 'setup_table' ) );
		add_action( 'eac_banking_page_transfers', array( __CLASS__, 'render_table' ) );
		add_action( 'eac_banking_page_transfers_add', array( __CLASS__, 'render_add' ) );
		add_action( 'eac_banking_page_transfers_edit', array( __CLASS__, 'render_edit' ) );
//		add_action( 'admin_post_eac_edit_account', array( __CLASS__, 'handle_edit' ) );
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
		$tabs['transfers'] = __( 'Transfers', 'wp-ever-accounting' );

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
		if ( "eac_transfers_per_page" === $option ) {
			return $value;
		}

		return $status;
	}

	/**
	 * setup transfers list.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public static function setup_table() {
		global $list_table;
		$screen     = get_current_screen();
		$list_table = new Tables\TransfersTable();
		$list_table->prepare_items();
		$screen->add_option( 'per_page', array(
			'label'   => __( 'Number of transfers per page:', 'wp-ever-accounting' ),
			'default' => 20,
			'option'  => "eac_transfers_per_page",
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
		include __DIR__ . '/views/transfer-list.php';
	}

	/**
	 * Render add form.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public static function render_add() {
		$transfer = new Transfer();
		include __DIR__ . '/views/transfer-add.php';
	}

	/**
	 * Render edit transfers form.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public static function render_edit() {
		$id       = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );
		$transfer = Transfer::find( $id );
		if ( ! $transfer ) {
			esc_html_e( 'The specified transfers does not exist.', 'wp-ever-accounting' );

			return;
		}
		include __DIR__ . '/views/transfer-edit.php';
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
		// TODO: edit transfer will handle here.
		wp_safe_redirect( $referer );
		exit;
	}
}
