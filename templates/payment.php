<?php
/**
 * The Template for displaying revenue.
 *
 * This template can be overridden by copying it to yourtheme/eac/revenue.php
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
 * @var \EverAccounting\Models\Payment $payment Payment object.
 */

defined( 'ABSPATH' ) || exit;

// Load colors.
$text_color = get_option( 'eac_email_text_color', '#3c3c3c' );

$logo  = get_option( 'eac_business_logo' );
$phone = get_option( 'eac_business_phone' );
$email = get_option( 'eac_business_email' );
?>
<style>
	.eac-payment {
		background-color: #ffffff;
		border: 1px solid #e5e7eb;
		padding: 2rem;
		color: #3c3c3c;
		font-size: 14px;
	}
</style>
<div class="eac-payment" dir="<?php echo is_rtl() ? 'rtl' : 'ltr'; ?>" style="font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; margin-bottom: 40px;">
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
							<h2 style="color: #3c3c3c; font-size: 24px; margin: 0 0 10px 0;"><?php esc_html_e( 'Payment Receipt', 'wp-ever-accounting' ); ?></h2>
							<p style="margin: 0;">#&nbsp;<?php echo esc_html( $payment->number ); ?></p>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<!-- End Header -->

		<tr>
			<td>
				<hr style="border-top: 1px solid #e5e7eb; margin: 30px 0;">
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
								$customer = $payment->customer;
								if ( $customer ) {
									$address = eac_get_formatted_address(
										array(
											'name'       => $customer->name,
											'company'    => $customer->company,
											'address'    => $customer->address,
											'city'       => $customer->city,
											'state'      => $customer->state,
											'zip'        => $customer->zip,
											'country'    => $customer->country,
											'tax_number' => $customer->tax_number,
										)
									);
									echo wp_kses_post( $address );
								} else {
									echo esc_html( 'N/A' );
								}
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
				<hr style="border-top: 1px solid #e5e7eb; margin: 20px 0;">
			</td>
		</tr>

		<!-- Payment Summary -->
		<tr>
			<td>
				<table border="0" cellspacing="0" cellpadding="0" width="100%">
					<tr>
						<td style="width: 30%; padding: 10px 0;"><strong><?php esc_attr_e( 'Amount:', 'wp-ever-accounting' ); ?></strong></td>
						<td style="width: 70%; padding: 10px 0; border-bottom: 1px dashed #e5e7eb;"><?php echo esc_html( $payment->formatted_amount ); ?></td>
					</tr>
					<tr>
						<td style="padding: 10px 0;"><strong><?php esc_attr_e( 'Date:', 'wp-ever-accounting' ); ?></strong></td>
						<td style="padding: 10px 0; border-bottom: 1px dashed #e5e7eb;"><?php echo esc_html( wp_date( get_option( 'date_format' ), strtotime( $payment->date ) ) ); ?></td>
					</tr>
					<tr>
						<td style="padding: 10px 0;"><strong><?php esc_attr_e( 'Method:', 'wp-ever-accounting' ); ?></strong></td>
						<td style="padding: 10px 0; border-bottom: 1px dashed #e5e7eb;"><?php echo esc_html( $payment->mode ); ?></td>
					</tr>
					<tr>
						<td style="padding: 10px 0;"><strong><?php esc_attr_e( 'Status:', 'wp-ever-accounting' ); ?></strong></td>
						<td style="padding: 10px 0; border-bottom: 1px dashed #e5e7eb;"><?php echo esc_html( $payment->status ); ?></td>
					</tr>
					<tr>
						<td style="padding: 10px 0;"><strong><?php esc_attr_e( 'Reference:', 'wp-ever-accounting' ); ?></strong></td>
						<td style="padding: 10px 0; border-bottom: 1px dashed #e5e7eb;"><?php echo esc_html( $payment->reference ? $payment->reference : 'N/A' ); ?></td>
					</tr>
				</table>
			</td>
		</tr>

		<tr>
			<td>
				<hr style="border-top: 1px solid #e5e7eb; margin: 30px 0;">
			</td>
		</tr>

		<!-- Notes Section -->
		<tr>
			<td><strong><?php esc_attr_e( 'Notes:', 'wp-ever-accounting' ); ?></strong><br/><br/><?php echo wp_kses_post( $payment->note ? $payment->note : 'N/A' ); ?></td>
		</tr>

	</table>
</div>
