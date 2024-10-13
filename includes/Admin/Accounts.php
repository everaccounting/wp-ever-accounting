<?php

namespace EverAccounting\Admin;

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
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_filter( 'eac_banking_page_tabs', array( __CLASS__, 'register_tabs' ) );
		add_action( 'eac_banking_page_accounts_loaded', array( __CLASS__, 'handle_actions' ) );
		add_action( 'eac_banking_page_accounts_loaded', array( __CLASS__, 'page_loaded' ) );
		add_action( 'eac_banking_page_accounts_content', array( __CLASS__, 'page_content' ) );
		add_action( 'eac_account_edit_side_meta_boxes', array( __CLASS__, 'account_notes' ) );
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
		if ( current_user_can( 'eac_manage_account' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown -- Custom capability.
			$tabs['accounts'] = __( 'Account', 'wp-ever-accounting' );
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
		if ( isset( $_POST['action'] ) && 'eac_edit_account' === $_POST['action'] && check_admin_referer( 'eac_edit_account' ) && current_user_can( 'eac_manage_account' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown -- Custom capability.
			$data = array(
				'id'           => isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0,
				'name'         => isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '',
				'number'       => isset( $_POST['number'] ) ? sanitize_text_field( wp_unslash( $_POST['number'] ) ) : '',
				'type'         => isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '',
				'currency'     => isset( $_POST['currency'] ) ? sanitize_text_field( wp_unslash( $_POST['currency'] ) ) : '',
				'bank_name'    => isset( $_POST['bank_name'] ) ? sanitize_text_field( wp_unslash( $_POST['bank_name'] ) ) : '',
				'bank_phone'   => isset( $_POST['bank_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['bank_phone'] ) ) : '',
				'bank_address' => isset( $_POST['bank_address'] ) ? sanitize_text_field( wp_unslash( $_POST['bank_address'] ) ) : '',
			);

			$account = EAC()->accounts->insert( $data );
			if ( is_wp_error( $account ) ) {
				EAC()->flash->error( $account->get_error_message() );
			} else {
				EAC()->flash->success( __( 'Account saved successfully.', 'wp-ever-accounting' ) );
			}
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
				$id = filter_input( INPUT_GET, 'id', FILTER_VALIDATE_INT );
				if ( ! EAC()->accounts->get( $id ) ) {
					wp_die( esc_html__( 'You attempted to retrieve an account that does not exist. Perhaps it was deleted?', 'wp-ever-accounting' ) );
				}
				break;

			default:
				$screen     = get_current_screen();
				$list_table = new ListTables\Accounts();
				$list_table->prepare_items();
				$screen->add_option(
					'per_page',
					array(
						'label'   => __( 'Number of accounts per page:', 'wp-ever-accounting' ),
						'default' => 20,
						'option'  => 'eac_accounts_per_page',
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
				include __DIR__ . '/views/account-edit.php';
				break;
			default:
				include __DIR__ . '/views/account-list.php';
				break;
		}
	}

	/**
	 * Account notes.
	 *
	 * @param Account $account Account object.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function account_notes( $account ) {
		// bail if account does not exist.
		if ( ! $account->exists() ) {
			return;
		}

		$notes = EAC()->notes->query(
			array(
				'parent_id'   => $account->id,
				'parent_type' => 'account',
				'orderby'     => 'created_at',
				'order'       => 'DESC',
				'limit'       => 20,
			)
		);
		?>
		<div class="eac-card">
			<div class="eac-card__header">
				<h3 class="eac-card__title"><?php esc_html_e( 'Notes', 'wp-ever-accounting' ); ?></h3>
			</div>
			<div class="eac-card__body">
				<div class="eac-form-field">
					<label for="eac-note"><?php esc_html_e( 'Add Note', 'wp-ever-accounting' ); ?></label>
					<textarea id="eac-note" cols="30" rows="2" placeholder="<?php esc_attr_e( 'Enter Note', 'wp-ever-accounting' ); ?>"></textarea>
				</div>
				<button id="eac-add-note" type="button" class="button tw-mb-[20px]" data-parent_id="<?php echo esc_attr( $account->id ); ?>" data-parent_type="account" data-nonce="<?php echo esc_attr( wp_create_nonce( 'eac_add_note' ) ); ?>">
					<?php esc_html_e( 'Add Note', 'wp-ever-accounting' ); ?>
				</button>

				<?php include __DIR__ . '/views/note-list.php'; ?>
			</div>
		</div>
		<?php
	}
}
