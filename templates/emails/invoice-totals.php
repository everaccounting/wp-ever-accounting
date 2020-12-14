<?php
$totals = apply_filters(
	'eaccounting_email_invoice_totals',
	array(
		__( 'Subtotal', 'wp-ever-accounting' ) => array( $invoice, 'get_formatted_subtotal' ),
		__( 'Tax', 'wp-ever-accounting' )      => array( $invoice, 'get_formatted_total_tax' ),
		__( 'Discount', 'wp-ever-accounting' ) => array( $invoice, 'get_formatted_total_tax' ),
		__( 'Discount', 'wp-ever-accounting' ) => array( $invoice, 'get_formatted_total_discount' ),
		__( 'Total', 'wp-ever-accounting' )    => array( $invoice, 'get_formatted_total' ),
	),
	$invoice
)
?>
<?php foreach ( $totals as $label => $total ) : ?>
	<tr>
		<td class="td" colspan="3" style="text-align:right; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
			<strong><?php echo esc_html( $label ); ?></strong>
		</td>
		<td class="td" style="text-align:right; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
			<?php echo is_callable( $total ) ? call_user_func( $total ) : '&mdash'; ?>
		</td>
	</tr>
	<?php
endforeach;
