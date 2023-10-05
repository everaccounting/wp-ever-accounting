<?php
/**
 * The Template for displaying a bill.
 *
 * This template can be overridden by copying it to yourtheme/eac/content-bill.php
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
 * @var \EverAccounting\Models\Bill $bill The bill object.
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
if ( ! $bill->is_calculating_tax() ) {
	unset( $columns['tax'] );
}

?>
<div class="eac-panel is-large eac-document is--bill mt-0">
	<div class="eac-document__header">
		<div class="eac-document__logo">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php if ( $logo ) : ?>
					<img src="<?php echo esc_url( $logo ); ?>" alt="<?php echo esc_attr( $company_name ); ?>">
				<?php else : ?>
					<h2><?php echo esc_html( $company_name ); ?></h2>
				<?php endif; ?>
			</a>
		</div>
		<div class="eac-document__title">
			<div
				class="eac-document__title-text"><?php esc_html_e( 'Bill', 'wp-ever-accounting' ); ?></div>
			<div class="eac-document__title-meta">#<?php echo esc_html( $bill->get_number() ); ?></div>
		</div>
	</div>

	<div class="eac-document__body">
		<div class="eac-document__section document-details">
			<div class="eac-document__from">
				<h4 class="eac-document__section-title"><?php esc_html_e( 'From', 'wp-ever-accounting' ); ?></h4>
				<?php echo wp_kses_post( $bill->get_formatted_billing_address() ); ?>
			</div>
			<div class="eac-document__to">
				<h4 class="eac-document__section-title"><?php esc_html_e( 'To', 'wp-ever-accounting' ); ?></h4>
				<?php echo wp_kses_post( eac_get_formatted_company_address() ); ?>
			</div>
			<div class="eac-document__data">
				<div>
					<span><?php esc_html_e( 'Bill Date', 'wp-ever-accounting' ); ?></span>
					<span><?php echo esc_html( $bill->get_issue_date() ); ?></span>
				</div>
				<div>
					<span><?php esc_html_e( 'Due Date', 'wp-ever-accounting' ); ?></span>
					<span><?php echo esc_html( $bill->get_due_date() ); ?></span>
				</div>
				<div>
					<span><?php esc_html_e( 'Ref. No', 'wp-ever-accounting' ); ?></span>
					<span>
						<?php
						if ( $bill->get_reference() ) {
							echo esc_html( substr( $bill->get_reference(), 0, 20 ) );
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
				<?php if ( $bill->get_items( 'line_item' ) ) : ?>
					<?php foreach ( $bill->get_items( 'line_item' ) as $item ) : ?>
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
				<?php if ( $bill->get_note() ) : ?>
					<h4><?php esc_html_e( 'Notes:', 'wp-ever-accounting' ); ?></h4>
					<?php echo wp_kses_post( wpautop( wptexturize( $bill->get_note() ) ) ); ?>
				<?php endif; ?>
			</div>
			<div class="eac-document__totals">
				<div>
					<span><?php esc_html_e( 'Subtotal', 'wp-ever-accounting' ); ?></span>
					<span><?php echo esc_html( $bill->get_formatted_items_total() ); ?></span>
				</div>
				<?php if ( ! empty( absint( $bill->get_discount_total() ) ) ) : ?>
					<div>
						<span><?php esc_html_e( 'Discount', 'wp-ever-accounting' ); ?></span>
						<span>&minus;<?php echo esc_html( $bill->get_formatted_discount_total() ); ?></span>
					</div>
				<?php endif; ?>
				<?php if ( ! empty( absint( $bill->get_shipping_total() ) ) ) : ?>
					<div>
						<span><?php esc_html_e( 'Shipping', 'wp-ever-accounting' ); ?></span>
						<span><?php echo esc_html( $bill->get_formatted_shipping_total() ); ?></span>
					</div>
				<?php endif; ?>
				<?php if ( ! empty( absint( $bill->get_fees_total() ) ) ) : ?>
					<div>
						<span><?php esc_html_e( 'Fees', 'wp-ever-accounting' ); ?></span>
						<span><?php echo esc_html( $bill->get_formatted_fees_total() ); ?></span>
					</div>
				<?php endif; ?>
				<?php if ( ! empty(absint(  $bill->get_tax_total() ) ) ) : ?>
					<?php if ( 'single' === get_option( 'eac_tax_display_totals' ) ) : ?>
						<div>
							<span><?php esc_html_e( 'Tax', 'wp-ever-accounting' ); ?></span>
							<span><?php echo esc_html( $bill->get_formatted_tax_total() ); ?></span>
						</div>
					<?php else : ?>
						<?php foreach ( $bill->get_formatted_itemized_taxes() as $label => $amount ) : ?>
							<div>
								<span><?php echo esc_html( $label ); ?></span>
								<span><?php echo esc_html( $amount ); ?></span>
							</div>
						<?php endforeach; ?>
					<?php endif; ?>
				<?php endif; ?>
				<div>
					<span><?php esc_html_e( 'Total', 'wp-ever-accounting' ); ?></span>
					<span><?php echo esc_html( $bill->get_formatted_total() ); ?></span>
				</div>
				<div>
					<span><?php esc_html_e( 'Paid', 'wp-ever-accounting' ); ?></span>
					<span><?php echo esc_html( $bill->get_formatted_total_paid() ); ?></span>
				</div>
				<div>
					<span><?php esc_html_e( 'Due', 'wp-ever-accounting' ); ?></span>
					<span><?php echo esc_html( $bill->get_formatted_balance() ); ?></span>
				</div>
			</div>
		</div>

	</div>
</div>
