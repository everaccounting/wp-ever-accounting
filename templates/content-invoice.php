<?php
/**
 * The Template for displaying an invoice.
 *
 * This template can be overridden by copying it to yourtheme/eac/content-invoice.php
 *
 * HOWEVER, on occasion EverAccounting will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://wpeveraccounting.com/docs/
 * @package EverAccounting\Templates
 * @version 1.0.0
 *
 * @var \EverAccounting\Models\Invoice $invoice The invoice object.
 */

defined( 'ABSPATH' ) || exit;

$company_name = get_option( 'eac_company_name', get_bloginfo( 'name' ) );
$logo         = get_option( 'eac_company_logo', '' );
$columns      = array(
	'item'     => __( 'Item', 'wp-ever-accounting' ),
	'price'    => __( 'Price', 'wp-ever-accounting' ),
	'quantity' => __( 'Quantity', 'wp-ever-accounting' ),
	'tax'      => __( 'Tax', 'wp-ever-accounting' ),
	'subtotal' => __( 'Subtotal', 'wp-ever-accounting' ),
);
// If not collecting tax, remove the tax column.
if ( ! $invoice->is_calculating_tax() ) {
	unset( $columns['tax'] );
}

?>
<div class="eac-panel is-large eac-document is--invoice mt-0">
	<div class="eac-document__header">
		<div class="eac-document__logo">
			<a href="https://pluginever.com">
				<?php if ( $logo ) : ?>
					<img src="<?php echo esc_url( $logo ); ?>" alt="<?php echo esc_attr( $company_name ); ?>">
				<?php else : ?>
					<h2><?php echo esc_html( $company_name ); ?></h2>
				<?php endif; ?>
			</a>
		</div>
		<div class="eac-document__title">
			<div
				class="eac-document__title-text"><?php esc_html_e( 'Invoice', 'wp-ever-accounting' ); ?></div>
			<div class="eac-document__title-meta">#<?php echo esc_html( $invoice->get_number() ); ?></div>
		</div>
	</div>

	<div class="eac-document__body">
		<div class="eac-document__section document-details">
			<div class="eac-document__from">
				<h4 class="eac-document__section-title"><?php esc_html_e( 'From', 'wp-ever-accounting' ); ?></h4>
				<?php echo wp_kses_post( eac_get_formatted_company_address() ); ?>
			</div>
			<div class="eac-document__to">
				<h4 class="eac-document__section-title"><?php esc_html_e( 'To', 'wp-ever-accounting' ); ?></h4>
				<?php echo wp_kses_post( $invoice->get_formatted_billing_address() ); ?>
			</div>
			<div class="eac-document__data">
				<div>
					<span><?php esc_html_e( 'Invoice Date', 'wp-ever-accounting' ); ?></span>
					<span><?php echo esc_html( $invoice->get_issue_date() ); ?></span>
				</div>
				<div>
					<span><?php esc_html_e( 'Due Date', 'wp-ever-accounting' ); ?></span>
					<span><?php echo esc_html( $invoice->get_due_date() ); ?></span>
				</div>
				<div>
					<span><?php esc_html_e( 'Ref. No', 'wp-ever-accounting' ); ?></span>
					<span>
						<?php
						if ( $invoice->get_reference() ) {
							echo esc_html( substr( $invoice->get_reference(), 0, 20 ) );
						} else {
							esc_html_e( 'N/A', 'wp-ever-accounting' );
						}
						?>
					</span>
				</div>
			</div>
		</div>

		<div class="eac-document__section document-items">
			<div class="eac-document__subject"></div>
			<table class="eac-document__items">
				<thead>
				<tr>
					<?php foreach ( $columns as $key => $label ) : ?>
						<?php if ( 'item' === $key ) : ?>
							<th class="line-<?php echo esc_attr( $key ); ?>"
								colspan="2"><?php echo esc_html( $label ); ?></th>
						<?php else : ?>
							<th class="line-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></th>
						<?php endif; ?>
					<?php endforeach; ?>
				</thead>
				<tbody>
				<?php if ( $invoice->get_items( 'line_item' ) ) : ?>
					<?php foreach ( $invoice->get_items( 'line_item' ) as $item ) : ?>
						<tr>
							<?php foreach ( $columns as $key => $label ) : ?>
								<?php if ( 'item' === $key ) : ?>
									<td class="line-<?php echo esc_attr( $key ); ?>" colspan="2">
										<?php echo esc_html( $item->get_name() ); ?>
										<?php if ( $item->get_description() ) : ?>
											<div class="line-description">
												<?php echo esc_html( wptexturize( $item->get_description() ) ); ?>
											</div>
										<?php endif; ?>
									</td>
								<?php else : ?>
									<td class="line-<?php echo esc_attr( $key ); ?>">
										<?php
										switch ( $key ) {
											case 'price':
												echo esc_html( $item->get_formatted_price() );
												break;
											case 'quantity':
												echo esc_html( $item->get_quantity() );
												break;
											case 'tax':
												echo esc_html( $item->get_formatted_tax_total() );
												break;
											case 'subtotal':
												echo esc_html( $item->get_formatted_subtotal() );
												break;
											default:
												// code...
												break;
										}
										?>
									</td>

								<?php endif; ?>
							<?php endforeach; ?>
						</tr>

					<?php endforeach; ?>
				<?php else : ?>
					<td colspan="<?php echo count( $columns ); ?>"><?php esc_html_e( 'No items found.', 'wp-ever-accounting' ); ?></td>
				<?php endif; ?>
			</table>
		</div>

		<div class="eac-document__section document-totals">
			<div class="eac-document__notes">
				<?php if ( $invoice->get_note() ) : ?>
					<h4><?php esc_html_e( 'Notes:', 'wp-ever-accounting' ); ?></h4>
					<?php echo wp_kses_post( wpautop( wptexturize( $invoice->get_note() ) ) ); ?>
				<?php endif; ?>
			</div>
			<div class="eac-document__totals">
				<div>
					<span><?php esc_html_e( 'Subtotal', 'wp-ever-accounting' ); ?></span>
					<span><?php echo esc_html( $invoice->get_formatted_items_total() ); ?></span>
				</div>
				<?php if ( ! empty( absint( $invoice->get_discount_total() ) ) ) : ?>
					<div>
						<span><?php esc_html_e( 'Discount', 'wp-ever-accounting' ); ?></span>
						<span>&minus;<?php echo esc_html( $invoice->get_formatted_discount_total() ); ?></span>
					</div>
				<?php endif; ?>
				<?php if ( ! empty( absint( $invoice->get_shipping_total() ) ) ) : ?>
					<div>
						<span><?php esc_html_e( 'Shipping', 'wp-ever-accounting' ); ?></span>
						<span><?php echo esc_html( $invoice->get_formatted_shipping_total() ); ?></span>
					</div>
				<?php endif; ?>
				<?php if ( ! empty( absint( $invoice->get_fees_total() ) ) ) : ?>
					<div>
						<span><?php esc_html_e( 'Fees', 'wp-ever-accounting' ); ?></span>
						<span><?php echo esc_html( $invoice->get_formatted_fees_total() ); ?></span>
					</div>
				<?php endif; ?>
				<?php if ( ! empty(absint(  $invoice->get_tax_total() ) ) ) : ?>
					<?php if ( 'single' === get_option( 'eac_tax_display_totals' ) ) : ?>
						<div>
							<span><?php esc_html_e( 'Tax', 'wp-ever-accounting' ); ?></span>
							<span><?php echo esc_html( $invoice->get_formatted_tax_total() ); ?></span>
						</div>
					<?php else : ?>
						<?php foreach ( $invoice->get_formatted_itemized_taxes() as $label => $amount ) : ?>
							<div>
								<span><?php echo esc_html( $label ); ?></span>
								<span><?php echo esc_html( $amount ); ?></span>
							</div>
						<?php endforeach; ?>
					<?php endif; ?>
				<?php endif; ?>
				<div>
					<span><?php esc_html_e( 'Total', 'wp-ever-accounting' ); ?></span>
					<span><?php echo esc_html( $invoice->get_formatted_total() ); ?></span>
				</div>
			</div>
		</div>

	</div>
</div>
