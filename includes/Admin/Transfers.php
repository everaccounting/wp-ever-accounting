<?php

namespace EverAccounting\Admin;

use EverAccounting\Models\Transfer;

defined( 'ABSPATH' ) || exit;

/**
 * Class Transfers
 *
 * @since 2.0.0
 * @package EverAccounting
 */
class Transfers {

	/**
	 * Transfers constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_filter( 'eac_banking_page_tabs', array( __CLASS__, 'register_tabs' ) );
		add_action( 'eac_banking_page_transfers_loaded', array( __CLASS__, 'handle_actions' ) );
		add_action( 'eac_banking_page_transfers_loaded', array( __CLASS__, 'page_loaded' ) );
		add_action( 'eac_banking_page_transfers_content', array( __CLASS__, 'page_content' ) );
	}

	/**
	 * Register tab.
	 *
	 * @param array $tabs Tabs.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public static function register_tabs( $tabs ) {
		if ( current_user_can( 'eac_manage_transfer' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown -- Custom capability.
			$tabs['transfers'] = __( 'Transfers', 'wp-ever-accounting' );
		}

		return $tabs;
	}

	/**
	 * Handle actions.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function handle_actions() {
		if ( isset( $_POST['action'] ) && 'eac_edit_transfer' === $_POST['action'] && check_admin_referer( 'eac_edit_transfer' ) && current_user_can( 'eac_manage_transfer' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown -- Custom capability.

		}
	}

	/**
	 * Handle page loaded.
	 *
	 * @param string $action Current action.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public static function page_loaded( $action ) {
		global $list_table;
		switch ( $action ) {
			case 'add':
				// Nothing to do here.
				break;

			case 'edit':
				$id = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );
				if ( ! EAC()->transfers->get( $id ) ) {
					wp_die( esc_html__( 'You attempted to retrieve a transfer that does not exist. Perhaps it was deleted?', 'wp-ever-accounting' ) );
				}
				break;

			default:
				$screen     = get_current_screen();
				$list_table = new ListTables\Transfers();
				$list_table->prepare_items();
				$screen->add_option(
					'per_page',
					array(
						'label'   => __( 'Number of items per page:', 'wp-ever-accounting' ),
						'default' => 20,
						'option'  => 'eac_transfers_per_page',
					)
				);
				break;
		}
	}

	/**
	 * Handle page content.
	 *
	 * @param string $action Current action.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public static function page_content( $action ) {
		switch ( $action ) {
			case 'add':
			case 'edit':
				include __DIR__ . '/views/transfer-edit.php';
				break;
			default:
				include __DIR__ . '/views/transfer-list.php';
				break;
		}
	}
}
