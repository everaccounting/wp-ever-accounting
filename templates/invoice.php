<?php
/**
 * The Template for displaying an invoice.
 *
 * This template can be overridden by copying it to yourtheme/eac/invoice.php
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
if ( ! $invoice->is_calculating_tax() && isset( $columns['tax'] ) ) {
	unset( $columns['tax'] );
}

?>
<div class="bkit-panel tw-p-10">
	<table cellspacing="0" cellpadding="0" width="100%">
		<tbody>
		<tr>
			<td valign="bottom">
				<h4 style="font-size: 16px; margin: 0; text-align: left; font-weight: 600; line-height: 1.6;">
					<img style="height: auto;width: auto;max-width: 200px; max-height: 100px;" width="200" height="50" src="https://pluginever.com/wp-content/uploads/2023/10/pluginever-logo.svg" class="custom-logo svg-logo-image" alt="PluginEver Logo">
				</h4>
			</td>
			<td style="vertical-align: top;" valign="top">
				<h2 style="font-size: 28px; margin: 0; text-align: right; font-weight: 600; line-height: 1.4;">Invoice #</h2>
				<p style="text-align: right;font-size: 16px;margin:0;color:#6b7280;"><?php echo esc_html( $invoice->number ); ?></p>
			</td>
		</tr>
		<tr>
			<td colspan="2" height="40px" style="vertical-align: top;" valign="top"></td>
		</tr>
		<tr style="">
			<td colspan="2" style="vertical-align: top; " valign="top">
				<table cellspacing="0" cellpadding="0" width="100%">
					<tbody>
					<tr>
						<td width="40%" style="vertical-align: top;" valign="top">
							<h3 style="font-size: 16px; color:#333; font-weight: 600; line-height: 22px; margin:0 0 10px;">Invoice to</h3>
							<p style="font-size: 13px; font-weight: 400; color: #777; line-height: 20px; margin: 0 0 10px 0;">
								<?php echo wp_kses_post( $invoice->formatted_billing_address ); ?>
							</p>
						</td>
						<td width="40%" style="vertical-align: top;" valign="top">
							<h3 style="font-size: 16px; color:#333; font-weight: 600; line-height: 22px; margin:0 0 10px;">Invoice from</h3>
							<p style="font-size: 13px; font-weight: 400;  color: #777;line-height: 20px; margin: 0 0 10px 0;">
								<?php echo wp_kses_post( $invoice->formatted_billing_address ); ?>
							</p>
						</td>
						<td width="20%" style="vertical-align: top;" valign="top">
							<p style="font-size: 13px; font-weight: 400; color: #777; line-height: 1.75; margin: 0 0 10px 0; text-align: right;">
								<strong>Issue Date:</strong><br>
								<?php echo esc_html( $invoice->issue_date ); ?><br>
								<strong>Due Date:</strong><br>
								<?php echo esc_html( $invoice->due_date ); ?> <br>
								<strong>Reference:</strong><br>
								<?php echo $invoice->reference ? esc_html( $invoice->reference ) : '&mdash;'; ?><br>
								<strong>Status:</strong><br>
								<span style="color: #28a745;"><?php echo esc_html( $invoice->status ); ?></span>
							</p>
						</td>
					</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2" height="15px" style="vertical-align: top;" valign="top"></td>
		</tr>
		<tr>
			<td colspan="2" height="30px" style="vertical-align: top;" valign="top"></td>
		</tr>
		<tr>
			<td colspan="2" style="vertical-align: top;" valign="top">
				<table cellspacing="0" cellpadding="0" width="100%" style="border:1px solid #ddd; border-collapse: collapse;">
					<thead style="">
					<tr style="page-break-inside: avoid;">
						<th width="55%" style="line-height: 18px; border-bottom: 1px solid #D2D4DE; font-weight: 400; padding: .8em 1em; text-align: left; font-size: 13px; background: #415164;color: #fff;" align="left">
							Item
						</th>
						<th width="15%" style="line-height: 18px; border-bottom: 1px solid #D2D4DE; font-weight: 400; padding: .8em 1em; text-align: left; font-size: 13px; background: #415164;color: #fff;" align="left">
							Price
						</th>
						<th width="15%" style="line-height: 18px; border-bottom: 1px solid #D2D4DE; font-weight: 400; padding: .8em 1em; text-align: left; font-size: 13px; background: #415164;color: #fff;" align="left">
							Quantity
						</th>
						<th width="15%" style="line-height: 18px; border-bottom: 1px solid #D2D4DE; font-weight: 400; padding: .8em 1em; text-align: left; font-size: 13px; background: #415164;color: #fff;" align="left">
							Tax
						</th>
						<th width="15%" style="line-height: 18px; border-bottom: 1px solid #D2D4DE; font-weight: 400; padding: .8em 1em; text-align: left; font-size: 13px; background: #415164;color: #fff;" align="left">
							Subtotal
						</th>
					</tr>
					</thead>
					<tbody style="">
					<?php foreach ( $invoice->items as $item ) : ?>
						<tr style="page-break-inside: avoid;">
							<td style="line-height: 18px; border-bottom: 1px solid #D2D4DE; font-weight: 400; padding: .8em 1em; text-align: left; font-size: 13px;" align="left">
								<?php echo esc_html( $item->name ); ?>
								<?php if ( $item->description ) : ?>
									<p style="font-size: 12px; font-weight: 400; color: #777; line-height: 1.75; margin: 0 0 10px 0;">
										<?php echo esc_html( $item->description ); ?>
									</p>
								<?php endif; ?>
							</td>
							<td style="line-height: 18px; border-bottom: 1px solid #D2D4DE; font-weight: 400; padding: .8em 1em; text-align: left; font-size: 13px;" align="left">
								<?php echo esc_html( eac_format_amount( $item->price, $invoice->currency_code ) ); ?>
							</td>
							<td style="line-height: 18px; border-bottom: 1px solid #D2D4DE; font-weight: 400; padding: .8em 1em; text-align: left; font-size: 13px;" align="left">
								<?php echo esc_html( $item->quantity ); ?>
							</td>
							<td style="line-height: 18px; border-bottom: 1px solid #D2D4DE; font-weight: 400; padding: .8em 1em; text-align: left; font-size: 13px;" align="left">
								<?php echo esc_html( $item->tax_total ); ?>
							</td>
							<td style="line-height: 18px; border-bottom: 1px solid #D2D4DE; font-weight: 400; padding: .8em 1em; text-align: left; font-size: 13px;" align="left">
								<?php echo esc_html( eac_format_amount( $item->subtotal, $invoice->currency_code ) ); ?>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</td>
		</tr>

		<tr>
			<td width="60%" style="padding:15px 0 10px">
				<?php echo wp_kses_post( $invoice->note ); ?>
			</td>
			<td width="40%">
				<table cellspacing="0" cellpadding="0" width="100%" style="text-align: right;">
					<tbody>
					<tr>
						<td style="padding: .8em 1em; text-align: right; font-size: 13px; font-weight: 600;">Subtotal:</td>
						<td style="padding: .8em 1em; text-align: right; font-size: 13px;"><?php echo esc_html( eac_format_amount( $invoice->items_total, $invoice->currency_code ) ); ?></td>
					</tr>
					<?php if ( $invoice->discount ) : ?>
						<tr>
							<td style="padding: .8em 1em; text-align: right; font-size: 13px; font-weight: 600;">Discount:</td>
							<td style="padding: .8em 1em; text-align: right; font-size: 13px;"><?php echo esc_html( eac_format_amount( $invoice->discount_total, $invoice->currency_code ) ); ?></td>
						</tr>
					<?php endif; ?>
					<?php if ( ! empty( absint( $invoice->shipping_total ) ) ) : ?>
						<tr>
							<td style="padding: .8em 1em; text-align: right; font-size: 13px; font-weight: 600;">Shipping:</td>
							<td style="padding: .8em 1em; text-align: right; font-size: 13px;"><?php echo esc_html( eac_format_amount( $invoice->shipping_total, $invoice->currency_code ) ); ?></td>
						</tr>
					<?php endif; ?>
					<?php if ( ! empty( absint( $invoice->fees_total ) ) ) : ?>
						<tr>
							<td style="padding: .8em 1em; text-align: right; font-size: 13px; font-weight: 600;">Fees:</td>
							<td style="padding: .8em 1em; text-align: right; font-size: 13px;"><?php echo esc_html( eac_format_amount( $invoice->fees_total, $invoice->currency_code ) ); ?></td>
						</tr>
					<?php endif; ?>
					<?php if ( $invoice->is_calculating_tax() && ! empty( absint( $invoice->tax_total ) ) ) : ?>
						<?php if ( 'single' === get_option( 'eac_tax_display_totals' ) ) : ?>
							<tr>
								<td style="padding: .8em 1em; text-align: right; font-size: 13px; font-weight: 600;">Tax:</td>
								<td style="padding: .8em 1em; text-align: right; font-size: 13px;"><?php echo esc_html( eac_format_amount( $invoice->tax_total, $invoice->currency_code ) ); ?></td>
							</tr>
						<?php else : ?>
							<?php foreach ( $invoice->formatted_itemized_taxes as $label => $amount ) : ?>
								<tr>
									<td style="padding: .8em 1em; text-align: right; font-size: 13px; font-weight: 600;"><?php echo esc_html( $label ); ?>:</td>
									<td style="padding: .8em 1em; text-align: right; font-size: 13px;"><?php echo esc_html( $amount ); ?></td>
								</tr>
							<?php endforeach; ?>
						<?php endif; ?>
					<?php endif; ?>
					<tr>
						<td style="padding: .8em 1em; text-align: right; font-size: 13px; font-weight: 600;">Total:</td>
						<td style="padding: .8em 1em; text-align: right; font-size: 13px;"><?php echo esc_html( eac_format_amount( $invoice->total, $invoice->currency_code ) ); ?></td>
					</tr>
					<tr>
						<td style="padding: .8em 1em; text-align: right; font-size: 13px; font-weight: 600;">Paid:</td>
						<td style="padding: .8em 1em; text-align: right; font-size: 13px;"><?php echo esc_html( eac_format_amount( $invoice->total_paid, $invoice->currency_code ) ); ?></td>
					</tr>
					<tr>
						<td style="padding: .8em 1em; text-align: right; font-size: 13px; font-weight: 600;">Due:</td>
						<td style="padding: .8em 1em; text-align: right; font-size: 13px;"><?php echo esc_html( eac_format_amount( $invoice->balance, $invoice->currency_code ) ); ?></td>
					</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2" height="50px" style="vertical-align: top;" valign="top"></td>
		</tr>
		<tr>
			<td style="padding:15px 0 15px" colspan="2">
				<hr class="data-table__StyledSeparator-sc-11th571-6 jcLHeJ" style="border: none; border-bottom: 1px solid #D2D4DE; margin: 0; padding: 0;">
			</td>
		</tr>
		<tr>
			<td colspan="2" style="vertical-align: top;" valign="top">
				<p style="font-size: 12px; font-weight: 200; color: #777; line-height: 1.75; margin: 0 0 10px 0;">
					Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ad architecto, atque debitis delectus dolore ea enim eum expedita iste iusto maxime neque non placeat praesentium quam quasi qui tempora voluptatem!
				</p>
			</td>
		</tr>
		</tbody>
	</table>
</div>
