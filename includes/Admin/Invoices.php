<?php

namespace EverAccounting\Admin;

use EverAccounting\Models\Invoice;

defined( 'ABSPATH' ) || exit;

/**
 * Class Invoices
 *
 * @package EverAccounting\Admin\Sales
 */
class Invoices {

	/**
	 * Invoices constructor.
	 */
	public function __construct() {
		add_filter( 'eac_sales_page_tabs', array( __CLASS__, 'register_tabs' ) );
		add_action( 'eac_sales_page_invoices_loaded', array( __CLASS__, 'handle_actions' ) );
		add_action( 'eac_sales_page_invoices_loaded', array( __CLASS__, 'page_loaded' ) );
		add_action( 'eac_sales_page_invoices_content', array( __CLASS__, 'page_content' ) );
		add_action( 'eac_invoice_edit_side_meta_boxes', array( __CLASS__, 'invoice_attachment' ) );
		add_action( 'eac_invoice_view_side_meta_boxes', array( __CLASS__, 'invoice_attachment' ) );
		add_action( 'eac_invoice_edit_side_meta_boxes', array( __CLASS__, 'invoice_notes' ) );
		add_action( 'eac_invoice_view_side_meta_boxes', array( __CLASS__, 'invoice_notes' ) );
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
		if ( current_user_can( 'eac_manage_invoice' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown -- Custom capability.
			$tabs['invoices'] = __( 'Invoices', 'wp-ever-accounting' );
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
		if ( isset( $_POST['action'] ) && 'eac_edit_invoice' === $_POST['action'] && check_admin_referer( 'eac_edit_invoice' ) && current_user_can( 'eac_manage_invoice' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown -- Custom capability.
			$invoice = EAC()->invoices->insert( $_POST );
			$referer = wp_get_referer();
			if ( is_wp_error( $invoice ) ) {
				EAC()->flash->error( $invoice->get_error_message() );
			} else {
				EAC()->flash->success( __( 'Invoice saved successfully.', 'wp-ever-accounting' ) );
				$referer = add_query_arg( 'id', $invoice->id, $referer );
				$referer = add_query_arg( 'action', 'view', $referer );
				$referer = remove_query_arg( array( 'add' ), $referer );
			}

			wp_safe_redirect( $referer );
			exit;
		}
		if ( isset( $_POST['action'] ) && 'eac_update_invoice' === $_POST['action'] && check_admin_referer( 'eac_update_invoice' ) && current_user_can( 'eac_manage_invoice' ) ) {// phpcs:ignore WordPress.WP.Capabilities.Unknown -- Custom capability.
			$id             = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0;
			$status         = isset( $_POST['status'] ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : '';
			$attachment_id  = isset( $_POST['attachment_id'] ) ? absint( wp_unslash( $_POST['attachment_id'] ) ) : 0;
			$invoice_action = isset( $_POST['invoice_action'] ) ? sanitize_text_field( wp_unslash( $_POST['invoice_action'] ) ) : '';

			$invoice = EAC()->invoices->get( $id );

			// If invoice not found, bail.
			if ( ! $invoice ) {
				wp_die( esc_html__( 'You attempted to update an invoice that does not exist. Perhaps it was deleted?', 'wp-ever-accounting' ) );
			}

			// Update invoice status.
			if ( ! empty( $status ) && $status !== $invoice->status ) {
				$invoice->status = $status;
			}

			// Update invoice attachment.
			if ( $attachment_id !== $invoice->attachment_id ) {
				$invoice->attachment_id = $attachment_id;
			}

			if ( $invoice->is_dirty() && $invoice->save() ) {
				$ret = $invoice->save();
				if ( is_wp_error( $ret ) ) {
					EAC()->flash->error( $ret->get_error_message() );
				} else {
					EAC()->flash->success( __( 'Invoice updated successfully.', 'wp-ever-accounting' ) );
				}
			}

			// todo handle payment action.
			if ( ! empty( $invoice_action ) ) {
				switch ( $invoice_action ) {
					case 'send_receipt':
						// Send invoice.
						break;
					default:
						/**
						 * Fires action to handle custom invoice actions.
						 *
						 * @param Invoice $invoice Payment object.
						 *
						 * @since 1.0.0
						 */
						do_action( 'eac_invoice_action_' . $invoice_action, $invoice );
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
				if ( ! EAC()->invoices->get( $id ) ) {
					wp_die( esc_html__( 'You attempted to retrieve an invoice that does not exist. Perhaps it was deleted?', 'wp-ever-accounting' ) );
				}
				break;

			default:
				$screen     = get_current_screen();
				$list_table = new ListTables\Invoices();
				$list_table->prepare_items();
				$screen->add_option(
					'per_page',
					array(
						'label'   => __( 'Number of items per page:', 'wp-ever-accounting' ),
						'default' => 20,
						'option'  => 'eac_invoices_per_page',
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
				include __DIR__ . '/views/invoice-edit.php';
				break;
			case 'view':
				include __DIR__ . '/views/invoice-view.php';
				break;
			default:
				include __DIR__ . '/views/invoice-list.php';
				break;
		}
	}

	/**
	 * Invoice attachment.
	 *
	 * @param Invoice $invoice Invoice object.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function invoice_attachment( $invoice ) {
		?>

		<div class="eac-card">
			<div class="eac-card__header">
				<h3 class="eac-card__title"><?php esc_html_e( 'Attachment', 'wp-ever-accounting' ); ?></h3>
			</div>
			<div class="eac-card__body">
				<?php eac_file_uploader( array( 'value' => $invoice->attachment_id ) ); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Invoice notes.
	 *
	 * @param Invoice $invoice Invoice object.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function invoice_notes( $invoice ) {
		// bail if invoice is does not exist.
		if ( ! $invoice->exists() ) {
			return;
		}

		$notes = EAC()->notes->query(
			array(
				'parent_id'   => $invoice->id,
				'parent_type' => 'invoice',
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
				<button id="eac-add-note" type="button" class="button tw-mb-[20px]" data-parent_id="<?php echo esc_attr( $invoice->id ); ?>" data-parent_type="invoice" data-nonce="<?php echo esc_attr( wp_create_nonce( 'eac_add_note' ) ); ?>">
					<?php esc_html_e( 'Add Note', 'wp-ever-accounting' ); ?>
				</button>

				<?php include __DIR__ . '/views/note-list.php'; ?>
			</div>
		</div>
		<?php
	}
}
