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

$text_color = get_option( 'eac_email_text_color', '#3c3c3c' );
$logo       = get_option( 'eac_business_logo' );
$phone      = get_option( 'eac_business_phone' );
$email      = get_option( 'eac_business_email' );
$columns    = EAC()->invoices->get_columns();
?>
<style type="text/css">
	.text-left {
		text-align: <?php echo is_rtl() ? 'right' : 'left'; ?>;
	}

	.text-right {
		text-align: <?php echo is_rtl() ? 'left' : 'right'; ?>;
	}

	hr {
		border-top: 1px solid #e5e7eb;
		border-bottom: 0;
		margin: 30px 0;
	}

	.item-col-item,
	.item-col-quantity,
	.item-col-price,
	.item-col-tax{
		text-align: <?php echo is_rtl() ? 'right' : 'left'; ?>;
	}
	.item-col-subtotal{
		text-align: <?php echo is_rtl() ? 'left' : 'right'; ?>;
	}
</style>
<div class="eac-invoice" dir="<?php echo is_rtl() ? 'rtl' : 'ltr'; ?>" style="font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; margin-bottom: 40px; background-color: #ffffff; border: 1px solid #e5e7eb; padding: 2rem; color: #3c3c3c; font-size: 14px;">
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<!-- Header -->
		<tr>
			<td valign="top">
				<table border="0" cellspacing="0" cellpadding="0" width="100%">
					<tr>
						<td align="left" valign="top">
							<?php if ( $logo && filter_var( $logo, FILTER_VALIDATE_URL ) ) : ?>
								<p style="margin: 0; height: 100px;">
									<img src="<?php echo esc_url( $logo ); ?>" alt="<?php esc_attr_e( 'Business Logo', 'wp-ever-accounting' ); ?>" style="max-height: 100px; max-width: 100%;"/>
								</p>
							<?php endif; ?>
							<?php if ( $phone ) : ?>
								<p style="margin: 0;"><a style="color: <?php echo esc_attr( $text_color ); ?>" href="tel:<?php echo esc_attr( $phone ); ?>"><?php echo esc_html( $phone ); ?></a></p>
							<?php endif; ?>
							<?php if ( $email ) : ?>
								<p style="margin: 0;"><a style="color: <?php echo esc_attr( $text_color ); ?>" href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></p>
							<?php endif; ?>
							<p style="margin: 0;"><a style="color: <?php echo esc_attr( $text_color ); ?>" href="<?php echo esc_url( home_url() ); ?>"><?php echo esc_html( home_url() ); ?></a></p>
						</td>
						<td align="right" valign="top">
							<h2 style="color: #3c3c3c; font-size: 24px; margin: 0 0 10px 0;"><?php esc_html_e( 'Invoice', 'wp-ever-accounting' ); ?></h2>
							<p style="margin: 0;">#&nbsp;<?php echo esc_html( $invoice->number ); ?></p>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<!-- End Header -->

		<tr>
			<td>
				<hr>
			</td>
		</tr>

		<!--Invoice Meta-->
		<tr>
			<td>
				<table cellspacing="0" cellpadding="0" width="100%">
					<tbody>
					<tr>
						<td width="33.33%">
							<table cellspacing="0" cellpadding="0" width="100%">
								<tr>
									<td>
										<p style="margin: 0 0 5px 0;"><strong><?php esc_html_e( 'Issue Date:', 'wp-ever-accounting' ); ?></strong></p>
									</td>
									<td>
										<p style="margin: 0 0 5px 0;"><?php echo esc_html( $invoice->issue_date ? wp_date( 'Y-m-d', strtotime( $invoice->issue_date ) ) : '' ); ?></p>
									</td>
								</tr>
								<tr>
									<td>
										<p style="margin: 0 0 5px 0;"><strong><?php esc_html_e( 'Due Date:', 'wp-ever-accounting' ); ?></strong></p>
									</td>
									<td>
										<p style="margin: 0 0 5px 0;"><?php echo esc_html( $invoice->due_date ? wp_date( 'Y-m-d', strtotime( $invoice->due_date ) ) : '' ); ?></p>
									</td>
								</tr>
							</table>
						</td>
						<td width="33.33%"></td>
						<td width="33.33%">
							<table cellspacing="0" cellpadding="0" width="100%" align="right" style="text-align: right;">
								<tr>
									<td>
										<p style="margin: 0 0 5px 0;"><strong><?php esc_html_e( 'Order #:', 'wp-ever-accounting' ); ?></strong></p>
									</td>
									<td class="eac-text-right">
										<p style="margin: 0 0 5px 0;"><?php echo esc_html( $invoice->order_number ? $invoice->order_number : 'N/A' ); ?></p>
									</td>
								</tr>
								<tr>
									<td>
										<p style="margin: 0 0 5px 0;"><strong><?php esc_html_e( 'Status:', 'wp-ever-accounting' ); ?></strong></p>
									</td>
									<td class="eac-text-right">
										<p style="margin: 0 0 5px 0;"><?php echo esc_html( $invoice->status_label ); ?></p>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<!--End Invoice Meta-->

		<tr>
			<td>
				<hr>
			</td>
		</tr>

		<!-- Payment Details Section -->
		<tr>
			<td>
				<table border="0" cellspacing="0" cellpadding="0" width="100%">
					<tr>
						<td width="50%" valign="top">
							<h3 style="color: #3c3c3c; font-size: 16px; margin: 0 0 6px;"><?php esc_html_e( 'From', 'wp-ever-accounting' ); ?></h3>
							<address style="color: #636363; font-size: 13px;line-height:1.5;font-style:normal;">
								<?php
								$address = eac_get_formatted_address(
									array(
										'name'       => $invoice->contact_name,
										'company'    => $invoice->contact_company,
										'address'    => $invoice->contact_address,
										'city'       => $invoice->contact_city,
										'state'      => $invoice->contact_state,
										'zip'        => $invoice->contact_zip,
										'country'    => $invoice->contact_country,
										'tax_number' => $invoice->contact_tax_number,
									)
								);
								echo wp_kses_post( $address );
								?>
							</address>
						</td>
						<td width="50%" valign="top">
							<h3 style="color: #3c3c3c; font-size: 16px; margin: 0 0 6px;"><?php esc_html_e( 'To', 'wp-ever-accounting' ); ?></h3>
							<address style="color: #636363; font-size: 13px;line-height:1.5;font-style:normal;">
								<?php
								$address = eac_get_formatted_address(
									array(
										'name'       => get_option( 'eac_business_name', get_bloginfo( 'name' ) ),
										'address'    => get_option( 'eac_business_address' ),
										'city'       => get_option( 'eac_business_city' ),
										'state'      => get_option( 'eac_business_state' ),
										'zip'        => get_option( 'eac_business_postcode' ),
										'country'    => get_option( 'eac_business_country' ),
										'tax_number' => get_option( 'eac_business_tax_number' ),
									)
								);
								echo wp_kses_post( $address );
								?>
							</address>
						</td>
					</tr>
				</table>
			</td>
		</tr>

		<tr>
			<td>
				<div style="margin: 30px 0;"></div>
			</td>
		</tr>

		<!--Invoice Items-->
		<tr>
			<td>
				<table cellspacing="0" cellpadding="0" width="100%" style="table-layout: fixed;">
					<tr>
						<?php foreach ( $columns as $key => $label ) : ?>
							<td class="item-col-<?php echo esc_attr( $key ); ?>" style="color: #3c3c3c; font-size: 14px; font-weight: bold;padding: 10px 0; border-bottom: 1px solid #263353;border-top: 1px solid #263353;">
								<?php echo esc_html( $label ); ?>
							</td>
						<?php endforeach; ?>
					</tr>
					<?php foreach ( $invoice->items as $item ) : ?>
						<tr>
							<?php foreach ( array_keys( $columns ) as $column ) : ?>
								<td class="item-col-<?php echo esc_attr( $column ); ?>" style="color: #636363; font-size: 13px; padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
									100
								</td>
							<?php endforeach; ?>
						</tr>
					<?php endforeach; ?>
				</table>
			</td>
		</tr>
		<!--End Invoice Items-->

	</table>
</div>
