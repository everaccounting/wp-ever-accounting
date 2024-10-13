<?php
/**
 * Edit invoice view.
 *
 * @since 1.0.0
 * @package EverAccounting
 */

use EverAccounting\Models\Invoice;

defined( 'ABSPATH' ) || exit;

wp_verify_nonce( '_wpnonce' );
$id      = isset( $_GET['id'] ) ? absint( wp_unslash( $_GET['id'] ) ) : 0;
$invoice = EAC()->invoices->get( $id );

?>
<div class="eac-section-header">
	<h1 class="wp-heading-inline">
		<?php esc_html_e( 'View Invoice', 'wp-ever-accounting' ); ?>
		<a href="<?php echo esc_attr( remove_query_arg( array( 'action', 'id' ) ) ); ?>" title="<?php esc_attr_e( 'Go back', 'wp-ever-accounting' ); ?>">
			<span class="dashicons dashicons-undo"></span>
		</a>
	</h1>
	<a href="<?php echo esc_url( $invoice->get_edit_url() ); ?>" class="page-title-action"><?php esc_html_e( 'Edit Invoice', 'wp-ever-accounting' ); ?></a>
</div>


<form id="eac-update-invoice" name="invoice" method="post">

	<div class="eac-poststuff">

		<div class="column-1">
			<?php eac_get_template( 'invoice.php', array( 'invoice' => $invoice ) ); ?>
			<?php
			/**
			 * Fires action to inject custom meta boxes in the main column.
			 *
			 * @param Invoice $invoice Invoice object.
			 *
			 * @since 1.0.0
			 */
			do_action( 'eac_invoice_edit_core_meta_boxes', $invoice );
			?>
		</div>

		<div class="column-2">
			<div class="eac-card">
				<div class="eac-card__header">
					<h2 class="eac-card__title"><?php esc_html_e( 'Actions', 'wp-ever-accounting' ); ?></h2>
				</div>
				<div class="eac-card__body">
					<?php
					eac_form_field(
						array(
							'label'       => __( 'Status', 'wp-ever-accounting' ),
							'type'        => 'select',
							'id'          => 'status',
							'options'     => EAC()->invoices->get_statuses(),
							'value'       => $invoice->status,
							'placeholder' => __( 'Select status', 'wp-ever-accounting' ),
							'required'    => true,
						)
					);

					eac_form_field(
						array(
							'label'       => __( 'Action', 'wp-ever-accounting' ),
							'type'        => 'select',
							'name'        => 'invoice_action',
							'options'     => array(
								'send_invoice' => __( 'Send Invoice', 'wp-ever-accounting' ),
							),
							'placeholder' => __( 'Select action', 'wp-ever-accounting' ),
						)
					);

					/**
					 * Fires to add custom actions.
					 *
					 * @param Invoice $invoice Invoice object.
					 *
					 * @since 2.0.0
					 */
					do_action( 'eac_invoice_view_misc_actions', $invoice );
					?>
				</div>
				<div class="eac-card__footer">
					<a class="del del_confirm" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'action', 'delete', $invoice->get_edit_url() ), 'bulk-invoices' ) ); ?>"><?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?></a>
					<div>
						<?php if ( 'draft' === $invoice->status ) : ?>
							<button class="button button-primary"><?php esc_html_e( 'Send Invoice', 'wp-ever-accounting' ); ?></button>
						<?php elseif ( 'sent' === $invoice->status && ! $invoice->is_paid() ) : ?>
							<button class="button button-primary add-invoice-payment"><?php esc_html_e( 'Add Payment', 'wp-ever-accounting' ); ?></button>
						<?php endif; ?>
						<button class="button button-primary"><?php esc_html_e( 'Submit', 'wp-ever-accounting' ); ?></button>
					</div>
				</div>
			</div>

			<?php
			/**
			 * Fires action to inject custom meta boxes in the side column.
			 *
			 * @param Invoice $invoice Invoice object.
			 *
			 * @since 1.0.0
			 */
			do_action( 'eac_invoice_view_side_meta_boxes', $invoice );
			?>

		</div><!-- .column-2 -->

	</div><!-- .eac-poststuff -->

	<?php wp_nonce_field( 'eac_update_invoice' ); ?>
	<input type="hidden" name="action" value="eac_update_invoice"/>
	<input type="hidden" name="id" value="<?php echo esc_attr( $invoice->id ); ?>"/>
</form>

<script type="text/html" id="tmpl-eac-invoice-payment">
	<div class="eac-modal-header">
		<h3><?php esc_html_e( 'Add Payment', 'wp-ever-accounting' ); ?></h3>
	</div>
	<div class="eac-modal-body">
		<form id="eac-add-invoice-payment" name="add-invoice-payment" method="post">
			<?php
			eac_form_field(
				array(
					'label'            => __( 'Account', 'wp-ever-accounting' ),
					'type'             => 'select',
					'name'             => 'account_id',
					'options'          => array(),
					'value'            => null,
					'class'            => 'eac_select2',
					'tooltip'          => __( 'Select the account.', 'wp-ever-accounting' ),
					'option_value'     => 'id',
					'option_label'     => 'formatted_name',
					'data-placeholder' => __( 'Select an account', 'wp-ever-accounting' ),
					'data-action'      => 'eac_json_search',
					'data-type'        => 'account',
					'required'         => true,
				)
			);
			eac_form_field(
				array(
					'label'       => __( 'Exchange Rate', 'wp-ever-accounting' ),
					'type'        => 'number',
					'name'        => 'exchange_rate',
					'value'       => 1,
					'placeholder' => '1.00',
					'required'    => true,
					'class'       => 'eac_exchange_rate',
					'prefix'      => '1 ' . eac_base_currency() . ' = ',
					'attr-step'   => 'any',
				)
			);

			eac_form_field(
				array(
					'label'         => __( 'Amount', 'wp-ever-accounting' ),
					'name'          => 'amount',
					'placeholder'   => '0.00',
//					'value'         => $payment->amount,
					'required'      => true,
					'tooltip'       => __( 'Enter the amount in the currency of the selected account, use (.) for decimal.', 'wp-ever-accounting' ),
//					'data-currency' => $payment->currency,
					'class'         => 'eac_amount',
				)
			);

			eac_form_field(
				array(
					'label'       => __( 'Date', 'wp-ever-accounting' ),
					'type'        => 'date',
					'name'        => 'date',
					'placeholder' => 'yyyy-mm-dd',
					'value'       => wp_date( 'Y-m-d' ),
					'required'    => true,
					'class'       => 'eac_datepicker',
				)
			);

			eac_form_field(
				array(
					'label'       => __( 'Payment Method', 'wp-ever-accounting' ),
					'type'        => 'select',
					'name'        => 'payment_mode',
					'value'       => '',
					'options'     => eac_get_payment_modes(),
					'placeholder' => __( 'Select &hellip;', 'wp-ever-accounting' ),
				)
			);
			eac_form_field(
				array(
					'label'       => __( 'Reference', 'wp-ever-accounting' ),
					'type'        => 'text',
					'name'        => 'reference',
					'value'       => '',
					'placeholder' => __( 'Enter reference', 'wp-ever-accounting' ),
				)
			);
			?>
		</form>
	</div>
	<div class="eac-modal-footer">
		<button class="button button-primary" form="add-invoice-payment"><?php esc_html_e( 'Submit', 'wp-ever-accounting' ); ?></button>
		<button class="button" data-eacmodal-close="true"><?php esc_html_e( 'Cancel', 'wp-ever-accounting' ); ?></button>
	</div>
</script>
