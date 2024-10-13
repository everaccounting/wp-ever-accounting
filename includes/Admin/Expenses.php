<?php

namespace EverAccounting\Admin;

use EverAccounting\Models\Expense;

defined( 'ABSPATH' ) || exit;

/**
 * Class Expenses
 *
 * @package EverAccounting\Admin\Sales
 */
class Expenses {
	/**
	 * Expenses constructor.
	 */
	public function __construct() {
		add_filter( 'eac_purchases_page_tabs', array( __CLASS__, 'register_tabs' ) );
		add_action( 'eac_purchases_page_expenses_loaded', array( __CLASS__, 'handle_actions' ) );
		add_action( 'eac_purchases_page_expenses_loaded', array( __CLASS__, 'page_loaded' ) );
		add_action( 'eac_purchases_page_expenses_content', array( __CLASS__, 'page_content' ) );
		add_action( 'eac_expense_edit_side_meta_boxes', array( __CLASS__, 'expense_attachment' ) );
		add_action( 'eac_expense_view_side_meta_boxes', array( __CLASS__, 'expense_attachment' ) );
		add_action( 'eac_expense_edit_side_meta_boxes', array( __CLASS__, 'expense_notes' ) );
		add_action( 'eac_expense_view_side_meta_boxes', array( __CLASS__, 'expense_notes' ) );
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
		if ( current_user_can( 'eac_manage_expense' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown -- Custom capability.
			$tabs['expenses'] = __( 'Expenses', 'wp-ever-accounting' );
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
		if ( isset( $_POST['action'] ) && 'eac_edit_expense' === $_POST['action'] && check_admin_referer( 'eac_edit_expense' ) && current_user_can( 'eac_manage_expense' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown -- Custom capability.
			$referer = wp_get_referer();
			$data    = array(
				'id'            => isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0,
				'date'          => isset( $_POST['date'] ) ? sanitize_text_field( wp_unslash( $_POST['date'] ) ) : '',
				'account_id'    => isset( $_POST['account_id'] ) ? absint( wp_unslash( $_POST['account_id'] ) ) : 0,
				'amount'        => isset( $_POST['amount'] ) ? floatval( wp_unslash( $_POST['amount'] ) ) : 0,
				'exchange_rate' => isset( $_POST['exchange_rate'] ) ? floatval( wp_unslash( $_POST['exchange_rate'] ) ) : 1,
				'category_id'   => isset( $_POST['category_id'] ) ? absint( wp_unslash( $_POST['category_id'] ) ) : 0,
				'contact_id'    => isset( $_POST['contact_id'] ) ? absint( wp_unslash( $_POST['contact_id'] ) ) : 0,
				'attachment_id' => isset( $_POST['attachment_id'] ) ? absint( wp_unslash( $_POST['attachment_id'] ) ) : 0,
				'payment_mode'  => isset( $_POST['payment_mode'] ) ? sanitize_text_field( wp_unslash( $_POST['payment_mode'] ) ) : '',
				'bill_id'       => isset( $_POST['bill_id'] ) ? absint( wp_unslash( $_POST['bill_id'] ) ) : 0,
				'reference'     => isset( $_POST['reference'] ) ? sanitize_text_field( wp_unslash( $_POST['reference'] ) ) : '',
				'note'          => isset( $_POST['note'] ) ? sanitize_textarea_field( wp_unslash( $_POST['note'] ) ) : '',
				'status'        => isset( $_POST['status'] ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : 'active',
			);

			$expense = EAC()->expenses->insert( $data );
			if ( is_wp_error( $expense ) ) {
				EAC()->flash->error( $expense->get_error_message() );
			} else {
				EAC()->flash->success( __( 'Expense saved successfully.', 'wp-ever-accounting' ) );
				$referer = add_query_arg( 'id', $expense->id, $referer );
				$referer = add_query_arg( 'action', 'view', $referer );
				$referer = remove_query_arg( array( 'add' ), $referer );
			}

			wp_safe_redirect( $referer );
			exit;
		}

		if ( isset( $_POST['action'] ) && 'eac_update_expense' === $_POST['action'] && check_admin_referer( 'eac_update_expense' ) && current_user_can( 'eac_manage_expense' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown -- Custom capability.}
			$id             = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0;
			$status         = isset( $_POST['status'] ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : '';
			$attachment_id  = isset( $_POST['attachment_id'] ) ? absint( wp_unslash( $_POST['attachment_id'] ) ) : 0;
			$expense_action = isset( $_POST['expense_action'] ) ? sanitize_text_field( wp_unslash( $_POST['expense_action'] ) ) : '';
			$expense        = EAC()->expenses->get( $id );

			// bail if expense is not found.
			if ( ! $expense ) {
				EAC()->flash->error( __( 'Expense not found.', 'wp-ever-accounting' ) );

				return;
			}

			// Update expense status.
			if ( ! empty( $status ) && $status !== $expense->status ) {
				$expense->status = $status;
			}

			// Update expense attachment.
			if ( $attachment_id !== $expense->attachment_id ) {
				$expense->attachment_id = $attachment_id;
			}

			if ( $expense->is_dirty() && $expense->save() ) {
				$ret = $expense->save();
				if ( is_wp_error( $ret ) ) {
					EAC()->flash->error( $ret->get_error_message() );
				} else {
					EAC()->flash->success( __( 'Expense updated successfully.', 'wp-ever-accounting' ) );
				}
			}

			// todo handle expense action.
			if ( ! empty( $expense_action ) ) {
				switch ( $expense_action ) {
					case 'send_receipt':
						// Send expense.
						break;
					default:
						/**
						 * Fires action to handle custom expense actions.
						 *
						 * @param Expense $expense Expense object.
						 *
						 * @since 1.0.0
						 */
						do_action( 'eac_expense_action_' . $expense_action, $expense );
						break;
				}
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

			case 'view':
			case 'edit':
				$id = filter_input( INPUT_GET, 'id', FILTER_VALIDATE_INT );
				if ( ! EAC()->expenses->get( $id ) ) {
					wp_die( esc_html__( 'You attempted to retrieve a expense that does not exist. Perhaps it was deleted?', 'wp-ever-accounting' ) );
				}
				break;

			default:
				$screen     = get_current_screen();
				$list_table = new ListTables\Expenses();
				$list_table->prepare_items();
				$screen->add_option(
					'per_page',
					array(
						'label'   => __( 'Number of items per page:', 'wp-ever-accounting' ),
						'default' => 20,
						'option'  => 'eac_expenses_per_page',
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
				include __DIR__ . '/views/expense-edit.php';
				break;
			case 'view':
				include __DIR__ . '/views/expense-view.php';
				break;
			default:
				include __DIR__ . '/views/expense-list.php';
				break;
		}
	}

	/**
	 * Expense attachment.
	 *
	 * @param Expense $expense Expense object.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function expense_attachment( $expense ) {
		?>

		<div class="eac-card">
			<div class="eac-card__header">
				<h3 class="eac-card__title"><?php esc_html_e( 'Attachment', 'wp-ever-accounting' ); ?></h3>
			</div>
			<div class="eac-card__body">
				<?php eac_file_uploader( array( 'value' => $expense->attachment_id ) ); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Expense notes.
	 *
	 * @param Expense $expense Expense object.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function expense_notes( $expense ) {
		// bail if expense is not found.
		if ( ! $expense ) {
			return;
		}

		$notes = EAC()->notes->query(
			array(
				'parent_id'   => $expense->id,
				'parent_type' => 'expense',
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
				<button id="eac-add-note" type="button" class="button tw-mb-[20px]" data-parent_id="<?php echo esc_attr( $expense->id ); ?>" data-parent_type="expense" data-nonce="<?php echo esc_attr( wp_create_nonce( 'eac_add_note' ) ); ?>">
					<?php esc_html_e( 'Add Note', 'wp-ever-accounting' ); ?>
				</button>

				<?php include __DIR__ . '/views/note-list.php'; ?>
			</div>
		</div>
		<?php
	}
}
