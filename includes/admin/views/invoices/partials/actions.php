<?php
/**
 * Render invoice action box
 * Used in view invoice page.
 *
 * @package EverAccounting\Admin
 * @var Invoice $invoice The item being displayed
 */

use EverAccounting\Models\Invoice;

defined( 'ABSPATH' ) || exit;

$invoice_actions = apply_filters(
	'eaccounting_invoice_actions',
	array(
		'send_customer_invoice' => __( 'Email invoice to customer', 'wp-ever-accounting' ),
		'status_pending'          => __( 'Status to "Pending', 'wp-ever-accounting' ),
		'status_paid'             => __( 'Status to "paid"', 'wp-ever-accounting' ),
		'status_overdue'          => __( 'Status to "Overdue"', 'wp-ever-accounting' ),
		'status_cancelled'        => __( 'Status to "Cancelled"', 'wp-ever-accounting' ),
		'status_refunded'         => __( 'Status to "Refunded"', 'wp-ever-accounting' ),
	)
);
$del_url         = wp_nonce_url( admin_url( 'admin.php?page=ea-sales&tab=invoices&action=delete&invoice_id=' . $invoice->get_id() ), 'invoice-nonce', '_wpnonce' );
?>
<form action="<?php echo admin_url( 'admin-post.php' ); ?>" method="post">
	<div class="ea-card">
		<div class="ea-card__header">
			<h3 class="ea-card__title">
				<?php esc_html_e( 'Invoice Actions', 'wp-ever-accounting' ); ?>
			</h3>
		</div>

		<div class="ea-card__inside">
			<select name="invoice_action" id="invoice_action" style="width: 100%;" required>
				<option value=""><?php esc_html_e( 'Choose an action...', 'wp-ever-accounting' ); ?></option>
				<?php foreach ( $invoice_actions as $action => $title ) { ?>
					<option value="<?php echo esc_attr( $action ); ?>"><?php echo esc_html( $title ); ?></option>
				<?php } ?>
				<input type="hidden" name="action" value="eaccounting_invoice_action">
				<input type="hidden" name="invoice_id" value="<?php echo esc_attr( $invoice->get_id() ); ?>">
				<?php wp_nonce_field( 'ea_invoice_action' ); ?>
			</select>
		</div>

		<div class="ea-card__footer">
			<a href="<?php echo esc_url( $del_url ); ?>"><?php esc_html_e( 'Remove', 'wp-ever-accounting' ); ?></a>
			<button class="button-primary"><span><?php esc_html_e( 'Apply', 'wp-ever-accounting' ); ?></span></button>
		</div>
	</div><!--.ea-card-->
</form>
