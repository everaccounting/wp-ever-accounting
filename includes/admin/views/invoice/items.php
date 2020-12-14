<?php
/**
 * Invoice Items
 *
 * This template can be overridden by copying it to yourtheme/eaccounting/invoice/items.php.
 *
 * HOWEVER, on occasion WP Ever Accounting will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @package eaccounting\Templates
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="ea-invoice-items-wrapper">
	<table cellpadding="0" cellspacing="0" class="ea-invoice-items">
		<thead>
		<tr>
			<?php if ( $invoice->is_editable() ) : ?>
				<th class="line-actions">&nbsp;</th>
			<?php endif; ?>
			<th class="line-name" colspan="2"><?php esc_html_e( 'Item', 'wp-ever-accounting' ); ?></th>
			<th class="line-price"><?php esc_html_e( 'Price', 'wp-ever-accounting' ); ?></th>
			<th class="line-quantity"><?php esc_html_e( 'Quantity', 'wp-ever-accounting' ); ?></th>
			<th class="line-tax"><?php esc_html_e( 'Tax(%)', 'wp-ever-accounting' ); ?></th>
			<th class="line-vat"><?php esc_html_e( 'Vat(%)', 'wp-ever-accounting' ); ?></th>
			<th class="line-subtotal"><?php esc_html_e( 'Subtotal', 'wp-ever-accounting' ); ?></th>
		</tr>
		</thead>
		<tbody class="ea-invoice-line-items">
		<?php eaccounting_get_admin_template( 'invoice/line-items', array( 'invoice' => $invoice ) ); ?>
		</tbody>
	</table>
	<div class="ea-invoice-data-row ea-invoice-total-items">
		<?php eaccounting_get_admin_template( 'invoice/totals', array( 'invoice' => $invoice ) ); ?>
	</div>

	<?php if ( $invoice->is_editable() ) : ?>
		<div class="ea-invoice-data-row ea-invoice-tools">
			<div class="tools-left">
				<button type="button" class="button ea-add-line-item btn-secondary" data-modal-title="<?php esc_html_e( 'Add Item', 'wp-ever-accounting' ); ?>"><span class="dashicons dashicons-plus">&nbsp;</span><?php esc_html_e( 'Item', 'wp-ever-accounting' ); ?></button>
			</div>
			<div class="tools-right">
				<button type="button" class="button button-primary recalculate-totals"><?php esc_html_e( 'Recalculate Totals', 'wp-ever-accounting' ); ?></button>
			</div>
		</div>
	<?php endif; ?>

</div>
