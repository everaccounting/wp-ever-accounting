<?php

namespace EverAccounting\Admin;

use EverAccounting\Models\Bill;

defined( 'ABSPATH' ) || exit;

/**
 * Class Bills
 *
 * @package EverAccounting\Admin\Sales
 */
class Bills {

	/**
	 * Bills constructor.
	 */
	public function __construct() {
		add_filter( 'eac_purchases_page_tabs', array( __CLASS__, 'register_tabs' ) );
		add_action( 'eac_purchases_page_bills_loaded', array( __CLASS__, 'handle_actions' ) );
		add_action( 'eac_purchases_page_bills_loaded', array( __CLASS__, 'page_loaded' ) );
		add_action( 'eac_purchases_page_bills_content', array( __CLASS__, 'page_content' ) );
		add_action( 'eac_bill_edit_side_meta_boxes', array( __CLASS__, 'bill_attachment' ) );
		add_action( 'eac_bill_view_side_meta_boxes', array( __CLASS__, 'bill_attachment' ) );
		add_action( 'eac_bill_edit_side_meta_boxes', array( __CLASS__, 'bill_notes' ) );
		add_action( 'eac_bill_view_side_meta_boxes', array( __CLASS__, 'bill_notes' ) );
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
		if ( current_user_can( 'eac_manage_bill' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown -- Custom capability.
			$tabs['bills'] = __( 'Bills', 'wp-ever-accounting' );
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
		if ( isset( $_POST['action'] ) && 'eac_edit_bill' === $_POST['action'] && check_admin_referer( 'eac_edit_bill' ) && current_user_can( 'eac_manage_bill' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown -- Custom capability.
			$bill    = EAC()->bills->insert( $_POST );
			$referer = wp_get_referer();
			if ( is_wp_error( $bill ) ) {
				EAC()->flash->error( $bill->get_error_message() );
			} else {
				EAC()->flash->success( __( 'Bill saved successfully.', 'wp-ever-accounting' ) );
				$referer = add_query_arg( 'id', $bill->id, $referer );
				$referer = add_query_arg( 'action', 'view', $referer );
				$referer = remove_query_arg( array( 'add' ), $referer );
			}

			wp_safe_redirect( $referer );
			exit;
		}
		if ( isset( $_POST['action'] ) && 'eac_update_bill' === $_POST['action'] && check_admin_referer( 'eac_update_bill' ) && current_user_can( 'eac_manage_bill' ) ) {// phpcs:ignore WordPress.WP.Capabilities.Unknown -- Custom capability.
			$id            = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0;
			$status        = isset( $_POST['status'] ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : '';
			$attachment_id = isset( $_POST['attachment_id'] ) ? absint( wp_unslash( $_POST['attachment_id'] ) ) : 0;
			$bill_action   = isset( $_POST['bill_action'] ) ? sanitize_text_field( wp_unslash( $_POST['bill_action'] ) ) : '';

			$bill = EAC()->bills->get( $id );

			// If bill not found, bail.
			if ( ! $bill ) {
				wp_die( esc_html__( 'You attempted to update an bill that does not exist. Perhaps it was deleted?', 'wp-ever-accounting' ) );
			}

			// Update bill status.
			if ( ! empty( $status ) && $status !== $bill->status ) {
				$bill->status = $status;
			}

			// Update bill attachment.
			if ( $attachment_id !== $bill->attachment_id ) {
				$bill->attachment_id = $attachment_id;
			}

			if ( $bill->is_dirty() && $bill->save() ) {
				$ret = $bill->save();
				if ( is_wp_error( $ret ) ) {
					EAC()->flash->error( $ret->get_error_message() );
				} else {
					EAC()->flash->success( __( 'Bill updated successfully.', 'wp-ever-accounting' ) );
				}
			}

			// todo handle payment action.
			if ( ! empty( $bill_action ) ) {
				switch ( $bill_action ) {
					case 'send_receipt':
						// Send bill.
						break;
					default:
						/**
						 * Fires action to handle custom bill actions.
						 *
						 * @param Bill $bill Payment object.
						 *
						 * @since 1.0.0
						 */
						do_action( 'eac_bill_action_' . $bill_action, $bill );
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
				if ( ! EAC()->bills->get( $id ) ) {
					wp_die( esc_html__( 'You attempted to retrieve an bill that does not exist. Perhaps it was deleted?', 'wp-ever-accounting' ) );
				}
				break;

			default:
				$screen     = get_current_screen();
				$list_table = new ListTables\Bills();
				$list_table->prepare_items();
				$screen->add_option(
					'per_page',
					array(
						'label'   => __( 'Number of items per page:', 'wp-ever-accounting' ),
						'default' => 20,
						'option'  => 'eac_bills_per_page',
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
				include __DIR__ . '/views/bill-edit.php';
				break;
			case 'view':
				include __DIR__ . '/views/bill-view.php';
				break;
			default:
				include __DIR__ . '/views/bill-list.php';
				break;
		}
	}

	/**
	 * Bill attachment.
	 *
	 * @param Bill $bill Bill object.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function bill_attachment( $bill ) {
		?>

		<div class="eac-card">
			<div class="eac-card__header">
				<h3 class="eac-card__title"><?php esc_html_e( 'Attachment', 'wp-ever-accounting' ); ?></h3>
			</div>
			<div class="eac-card__body">
				<?php eac_file_uploader( array( 'value' => $bill->attachment_id ) ); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Bill notes.
	 *
	 * @param Bill $bill Bill object.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function bill_notes( $bill ) {
		// bail if bill not exist.
		if ( ! $bill->exists() ) {
			return;
		}

		$notes = EAC()->notes->query(
			array(
				'parent_id'   => $bill->id,
				'parent_type' => 'bill',
				'orderby'     => 'date_created',
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
				<button id="eac-add-note" type="button" class="button tw-mb-[20px]" data-parent_id="<?php echo esc_attr( $bill->id ); ?>" data-parent_type="bill" data-nonce="<?php echo esc_attr( wp_create_nonce( 'eac_add_note' ) ); ?>">
					<?php esc_html_e( 'Add Note', 'wp-ever-accounting' ); ?>
				</button>

				<?php include __DIR__ . '/views/note-list.php'; ?>
			</div>
		</div>
		<?php
	}
}
