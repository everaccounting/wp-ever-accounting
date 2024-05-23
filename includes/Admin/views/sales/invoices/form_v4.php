<?php
/**
 * Admin Invoices Form.
 * Page: Sales
 * Tab: Invoices
 *
 * @package EverAccounting
 * @version 1.0.0
 * @var $document \EverAccounting\Models\Invoice Invoice object.
 */

defined( 'ABSPATH' ) || exit;

$columns = apply_filters(
	'eac_invoice_form_columns',
	array(
		'item'     => __( 'Item', 'wp-ever-accounting' ),
		'price'    => __( 'Price', 'wp-ever-accounting' ),
		'quantity' => __( 'Quantity', 'wp-ever-accounting' ),
		'tax'      => __( 'Tax', 'wp-ever-accounting' ),
		'subtotal' => __( 'Subtotal', 'wp-ever-accounting' ),
		'actions'  => '&nbsp;',
	)
);

if ( ( ! eac_tax_enabled() || $document->vat_exempt ) && isset( $columns['tax'] ) ) {
	unset( $columns['tax'] );
}

?>
<form id="eac-invoice-form" method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>" class="eac-document-form">
	<div class="bkit-row">
		<div class="eac-document-form__main bkit-col-9">
			<?php require __DIR__ . '/form-main.php'; ?>
		</div><!-- .bkit-col-9 -->
		<div class="eac-document-form__sidebar bkit-col-3">
			<?php require __DIR__ . '/form-sidebar.php'; ?>
		</div><!-- .eac-document-form__aside -->
	</div><!-- .bkit-row -->

	<?php wp_nonce_field( 'eac_edit_invoice' ); ?>
	<input type="hidden" name="billing_name" value="<?php echo esc_attr( $document->billing_name ); ?>"/>
	<input type="hidden" name="billing_company" value="<?php echo esc_attr( $document->billing_company ); ?>"/>
	<input type="hidden" name="billing_address_1" value="<?php echo esc_attr( $document->billing_address_1 ); ?>"/>
	<input type="hidden" name="billing_address_2" value="<?php echo esc_attr( $document->billing_address_2 ); ?>"/>
	<input type="hidden" name="billing_city" value="<?php echo esc_attr( $document->billing_city ); ?>"/>
	<input type="hidden" name="billing_state" value="<?php echo esc_attr( $document->billing_state ); ?>"/>
	<input type="hidden" name="billing_postcode" value="<?php echo esc_attr( $document->billing_postcode ); ?>"/>
	<input type="hidden" name="billing_country" value="<?php echo esc_attr( $document->billing_country ); ?>"/>
	<input type="hidden" name="billing_phone" value="<?php echo esc_attr( $document->billing_phone ); ?>"/>
	<input type="hidden" name="billing_email" value="<?php echo esc_attr( $document->billing_email ); ?>"/>
	<input type="hidden" name="billing_vat_number" value="<?php echo esc_attr( $document->billing_vat_number ); ?>"/>

	<input type="hidden" name="id" value="<?php echo esc_attr( $document->id ); ?>"/>
	<input type="hidden" name="action" value="eac_edit_invoice"/>
</form>
<?php
