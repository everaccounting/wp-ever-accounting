<?php
/**
 * Invoice header.
 *
 * @var $invoice \EverAccounting\Models\Invoice
 * @package EverAccounting\Admin
 */

defined( 'ABSPATH' ) || exit;

$company_address  = eaccounting_format_address(
	array(
		'street'   => eaccounting()->settings->get( 'company_address' ),
		'city'     => eaccounting()->settings->get( 'company_city' ),
		'state'    => eaccounting()->settings->get( 'company_state' ),
		'postcode' => eaccounting()->settings->get( 'company_postcode' ),
		'country'  => eaccounting()->settings->get( 'company_country' ),
	)
);
$company_name     = eaccounting()->settings->get( 'company_name' );
$vat_number       = eaccounting()->settings->get( 'company_vat_number' );
$customer_address = eaccounting_format_address( $invoice->get_address() );
?>
<table class="ea-document__address-table">
	<tbody>
	<tr>
		<th>
			<?php _e( 'From', 'wp-ever-accounting' ); ?>
		</th>
		<td class="spacer-col">&nbsp;</td>
		<td>
			<div class="ea-document__company-name">
				<?php echo empty( $company_name ) ? '&mdash;' : esc_html( $company_name ); ?>
			</div>
			<div class="ea-document__company-address">
				<?php echo $company_address; ?><br>
				<?php _e( 'VAT#', 'wp-ever-accounting' ); ?> <?php echo empty( $vat_number ) ? '&mdash;' : esc_html( $vat_number ); ?>
			</div>
		</td>
	</tr>
	<tr>
		<th>
			<?php _e( 'To', 'wp-ever-accounting' ); ?>
		</th>
		<td class="spacer-col">&nbsp;</td>
		<td>
			<div class="ea-document__contact-name">
				<?php echo empty( $invoice->get_name() ) ? '&mdash;' : esc_html( $invoice->get_name() ); ?>
			</div>
			<div class="ea-document__contact-address">
				<?php echo $customer_address; ?><br/>
				<?php _e( 'VAT#', 'wp-ever-accounting' ); ?> <?php echo empty( $invoice->get_vat_number() ) ? '&mdash;' : esc_html( $invoice->get_vat_number() ); ?>
			</div>
		</td>
	</tr>
	</tbody>
</table>
