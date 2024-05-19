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

// Including invoice styles.
eac_get_template( 'invoice-styles.php' );
?>
<div class="bkit-panel tw-p-10 invoice">
	<table cellspacing="0" cellpadding="0" width="100%">
		<tbody>
		<tr>
			<td>
				<table>
					<tbody>
					<tr>
						<td valign="bottom">
							<h4 style="font-size: 16px; margin: 0; text-align: left; font-weight: 600; line-height: 1.6;">
								<img style="height: auto;width: auto;max-width: 200px; max-height: 100px;" width="200" height="50" src="https://pluginever.com/wp-content/uploads/2023/10/pluginever-logo.svg" class="custom-logo svg-logo-image" alt="PluginEver Logo">
							</h4>
							<ul>
								<li>
									<a href="tel:+901234567432" target="_blank">+90123 4567 432</a>
								</li>
								<li>
									<a href="mailto:contact@byteever.com" target="_blank">contact@byteever.com</a>
								</li>
								<li>
									<a href="https://wpeveraccounting.com" target="_blank">www.wpeveraccounting.com</a>
								</li>
							</ul>
						</td>
						<td style="vertical-align: top;" valign="top">
							<h2 style="font-size: 28px; margin: 0; text-align: right; font-weight: 600; line-height: 1.4;">Invoice #</h2>
							<p style="text-align: right;font-size: 16px;color:#6b7280;"><?php echo esc_html( $invoice->number ); ?></p>
						</td>
					</tr>
					</tbody>
				</table>
			</td>
		</tr>

		<tr>
			<td height="10px" style="vertical-align: top;" valign="top"></td>
		</tr>

		<tr>
			<td>
				<hr class="data-table__StyledSeparator-sc-11th571-6 jcLHeJ">
			</td>
		</tr>

		<tr>
			<td height="10px" style="vertical-align: top;" valign="top"></td>
		</tr>

		<tr>
			<td>
				<table>
					<tbody>
					<tr>
						<td>
							<strong>Issue Date:</strong> 4 April, 2024
						</td>
						<td style="text-align: right;">
							<strong>Reference:</strong> ORD#00892
						</td>
					</tr>
					<tr>
						<td>
							<strong>Due Date:</strong> 5 April, 2024
						</td>
						<td style="text-align: right;">
							<strong>Status:</strong> Draft
						</td>
					</tr>
					</tbody>
				</table>
			</td>
		</tr>

		<tr>
			<td height="10px" style="vertical-align: top;" valign="top"></td>
		</tr>

		<tr>
			<td>
				<hr class="data-table__StyledSeparator-sc-11th571-6 jcLHeJ">
			</td>
		</tr>

		<tr>
			<td height="10px" style="vertical-align: top;" valign="top"></td>
		</tr>

		<tr style="">
			<td style="vertical-align: top;" valign="top">
				<table cellspacing="0" cellpadding="0" width="100%">
					<tbody>
					<tr>
						<td width="40%" style="vertical-align: top;" valign="top">
							<h3 style="font-size: 16px; color:#333; font-weight: 600; line-height: 22px; margin:0 0 10px;">Invoice to</h3>
							<p style="font-size: 13px; font-weight: 400; color: #777; line-height: 20px;">
								<?php echo wp_kses_post( $invoice->formatted_billing_address ); ?>
							</p>
						</td>
						<td width="40%" style="vertical-align: top;" valign="top">
							<h3 style="font-size: 16px; color:#333; font-weight: 600; line-height: 22px; margin:0 0 10px;">Invoice from</h3>
							<p style="font-size: 13px; font-weight: 400;  color: #777;line-height: 20px;">
								<?php echo wp_kses_post( $invoice->formatted_billing_address ); ?>
							</p>
						</td>
						<td width="20%" style="vertical-align: top;" valign="top">
							<p style="font-size: 13px; font-weight: 400; color: #777; line-height: 1.75; text-align: right;">
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
			<td height="20px" style="vertical-align: top;" valign="top"></td>
		</tr>

		<tr>
			<td>
				<hr class="data-table__StyledSeparator-sc-11th571-6 jcLHeJ">
			</td>
		</tr>

		<tr>
			<td height="20px" style="vertical-align: top;" valign="top"></td>
		</tr>

		<tr>
			<td style="vertical-align: top;" valign="top">
				<table cellspacing="0" cellpadding="0" width="100%" style="border:1px solid #ddd; border-collapse: collapse;" class="has-padding">
					<thead style="">
					<tr style="page-break-inside: avoid;">
						<th width="55%" style="line-height: 18px; border-bottom: 1px solid #D2D4DE; font-weight: 400; text-align: left; font-size: 13px; background: #415164;color: #fff;" align="left">
							Item
						</th>
						<th width="15%" style="line-height: 18px; border-bottom: 1px solid #D2D4DE; font-weight: 400; text-align: left; font-size: 13px; background: #415164;color: #fff;" align="left">
							Price
						</th>
						<th width="15%" style="line-height: 18px; border-bottom: 1px solid #D2D4DE; font-weight: 400; text-align: left; font-size: 13px; background: #415164;color: #fff;" align="left">
							Quantity
						</th>
						<th width="15%" style="line-height: 18px; border-bottom: 1px solid #D2D4DE; font-weight: 400; text-align: left; font-size: 13px; background: #415164;color: #fff;" align="left">
							Tax
						</th>
						<th width="15%" style="line-height: 18px; border-bottom: 1px solid #D2D4DE; font-weight: 400; text-align: left; font-size: 13px; background: #415164;color: #fff;" align="left">
							Subtotal
						</th>
					</tr>
					</thead>
					<tbody style="">
					<?php foreach ( $invoice->items as $item ) : ?>
						<tr style="page-break-inside: avoid;">
							<td style="line-height: 18px; border-bottom: 1px solid #D2D4DE; font-weight: 400; text-align: left; font-size: 13px;" align="left">
								<?php echo esc_html( $item->name ); ?>
								<?php if ( $item->description ) : ?>
									<p style="font-size: 12px; font-weight: 400; color: #777; line-height: 1.75;">
										<?php echo esc_html( $item->description ); ?>
									</p>
								<?php endif; ?>
							</td>
							<td style="line-height: 18px; border-bottom: 1px solid #D2D4DE; font-weight: 400; text-align: left; font-size: 13px;" align="left">
								<?php echo esc_html( eac_format_amount( $item->price, $invoice->currency_code ) ); ?>
							</td>
							<td style="line-height: 18px; border-bottom: 1px solid #D2D4DE; font-weight: 400; text-align: left; font-size: 13px;" align="left">
								<?php echo esc_html( $item->quantity ); ?>
							</td>
							<td style="line-height: 18px; border-bottom: 1px solid #D2D4DE; font-weight: 400; text-align: left; font-size: 13px;" align="left">
								<?php echo esc_html( $item->tax_total ); ?>
							</td>
							<td style="line-height: 18px; border-bottom: 1px solid #D2D4DE; font-weight: 400; text-align: left; font-size: 13px;" align="left">
								<?php echo esc_html( eac_format_amount( $item->subtotal, $invoice->currency_code ) ); ?>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</td>
		</tr>

		<tr>
			<td height="10px" style="vertical-align: top;" valign="top"></td>
		</tr>

		<tr>
			<td>
				<table>
					<tbody>
					<tr>
						<td width="60%" style="padding-bottom:10px;position: relative">
							<strong>Notes:</strong>
							<p>Lorem ipsum is a dolor sit, dui a justo hendrerit dui a justo hendrerit.</p>
							<?php echo wp_kses_post( $invoice->note ); ?>
							<div class="invoice-paid-icon" style="position:absolute;bottom:0;right:0;z-index: 1;">
								<svg width="136" height="82" viewBox="0 0 136 82" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M119.746 5.87828L5.85934 39.2555C5.52344 39.3562 5.3275 39.7089 5.42827 40.0448L16.1379 76.5851C16.2386 76.921 16.5913 77.1169 16.9328 77.0161L130.814 43.6446C131.15 43.5438 131.346 43.1911 131.245 42.8552L120.535 6.31495C120.435 5.97905 120.082 5.78311 119.746 5.88388V5.87828ZM129.549 42.4409L17.3415 75.3198L7.12456 40.4591L119.332 7.57457L129.549 42.4353V42.4409ZM118.509 1.65715L4.62211 35.0287C1.95172 35.8125 0.423374 38.6117 1.20154 41.2821L11.9111 77.8223C12.6949 80.4927 15.4941 82.0266 18.1645 81.2429L132.045 47.8713C134.716 47.0875 136.25 44.2884 135.466 41.618L124.762 5.07213C123.978 2.40173 121.179 0.873386 118.509 1.65155V1.65715ZM132.051 42.6201C132.281 43.4038 131.833 44.2268 131.049 44.4563L17.1679 77.8279C16.3842 78.0574 15.5612 77.6096 15.3317 76.8258L4.62211 40.28C4.39258 39.4962 4.84045 38.6732 5.62421 38.4437L119.511 5.07213C120.289 4.8426 121.112 5.29046 121.341 6.07423L132.051 42.6145V42.6201Z" fill="#6E7178"/>
								<path d="M29.9153 58.6032L32.0146 57.9874L28.4205 45.7215L26.3883 46.3149L24.9944 41.5619L33.7613 38.9923C33.8621 38.9643 33.9685 38.9307 34.086 38.8971C34.2036 38.8636 34.2988 38.8356 34.3771 38.8132C34.4555 38.7908 34.5619 38.7572 34.7074 38.718C35.2953 38.5556 35.8775 38.3877 36.4597 38.203C37.0419 38.0238 37.6186 37.8727 38.1952 37.7607C38.7718 37.6431 39.3317 37.5424 39.8803 37.4584C40.4289 37.3688 40.9664 37.3352 41.4982 37.3576C42.0244 37.38 42.5283 37.4416 43.0098 37.5424C43.4912 37.6487 43.9503 37.8279 44.3981 38.0854C44.846 38.3429 45.2547 38.6676 45.6298 39.0483C46.0049 39.429 46.3519 39.916 46.6766 40.5039C47.0014 41.0917 47.2757 41.7635 47.5108 42.5249C47.9195 43.8461 48.0538 45.0553 47.9195 46.1414C47.7851 47.2274 47.3988 48.1736 46.7606 48.9741C46.1224 49.7747 45.3107 50.4633 44.3254 51.0343C43.3401 51.6053 42.1588 52.0924 40.7872 52.4955C40.149 52.6802 39.4604 52.837 38.727 52.9545L39.5556 55.7817L41.5206 55.2051L42.937 60.0364L31.3484 63.4346L29.9321 58.6032H29.9153ZM37.4282 48.5654L37.9825 48.4031C40.0202 47.8041 40.776 46.606 40.2442 44.8034C40.009 43.9972 39.5948 43.471 39.007 43.2358C38.4191 43.0007 37.7753 42.9839 37.0755 43.1855L35.9447 43.5158L37.4226 48.5654H37.4282Z" fill="#6E7178"/>
								<path d="M70.6095 45.3968C70.7607 45.6543 70.8838 45.8503 70.979 45.979C71.0686 46.1078 71.1805 46.2142 71.3149 46.2981C71.4492 46.3821 71.578 46.4045 71.7124 46.3653L72.8712 46.0238L74.2876 50.8552L63.1693 54.1134L61.7418 49.282L63.3597 48.8062C62.9286 47.3338 62.4136 46.6844 61.8089 46.8636L58.1084 47.9496C57.9909 47.9832 57.9181 48.056 57.8789 48.168C57.8453 48.2799 57.8341 48.3863 57.8565 48.4871C57.8789 48.5879 57.9069 48.7334 57.9573 48.9238C58.0301 49.1757 58.1644 49.45 58.3548 49.7411C58.5507 50.0378 58.7411 50.1554 58.9258 50.0994L60.0567 49.7691L61.473 54.6005L52.3478 57.2765L50.9314 52.4451C51.3289 52.3275 51.6648 52.1932 51.9503 52.0308C52.5997 51.6557 52.9748 51.0847 53.07 50.3121C53.1035 50.0658 53.1091 49.4388 53.07 48.4255L52.7061 38.6061L49.8509 39.4402L48.4569 34.6872L61.8369 30.7684L70.6095 45.3912V45.3968ZM60.5605 43.5774L56.9832 37.2009L57.4814 44.4843L60.5661 43.583L60.5605 43.5774Z" fill="#6E7178"/>
								<path d="M83.1665 29.6823L81.1847 30.2645L84.7788 42.5305L86.6767 41.9762L88.093 46.8076L76.4877 50.2114L75.0713 45.38L77.0532 44.7978L73.459 32.5319L71.5332 33.0973L70.1392 28.3443L81.7725 24.9349L83.1665 29.6879V29.6823Z" fill="#6E7178"/>
								<path d="M90.17 40.9462L92.3645 40.3023L88.7648 28.0252L86.4919 28.6914L85.1035 23.9553L96.2778 20.6802C99.9727 19.5942 103.013 19.7061 105.397 21.0105C107.777 22.3149 109.49 24.7446 110.531 28.2995C110.962 29.7607 111.119 31.2219 111.013 32.6774C110.901 34.133 110.537 35.5046 109.91 36.7922C109.283 38.0798 108.325 39.2443 107.043 40.2688C105.756 41.2988 104.205 42.077 102.397 42.6089L91.5976 45.7719L90.1812 40.9406L90.17 40.9462ZM100.197 38.007C100.852 37.8167 101.389 37.52 101.815 37.1169C102.24 36.7138 102.537 36.2716 102.716 35.7845C102.895 35.2975 102.996 34.7488 103.018 34.133C103.041 33.5172 103.007 32.9294 102.917 32.3695C102.828 31.8097 102.693 31.2331 102.52 30.6285C102.341 30.007 102.145 29.4472 101.938 28.949C101.731 28.4507 101.462 27.9413 101.132 27.4206C100.801 26.9056 100.437 26.4913 100.045 26.1834C99.6536 25.8755 99.1721 25.6627 98.6011 25.5508C98.03 25.4388 97.4142 25.4836 96.7536 25.6739L96.121 25.8587L99.7207 38.1358L100.191 37.9958L100.197 38.007Z" fill="#6E7178"/>
							</svg>
							</div>
						</td>
						<td width="40%">
							<table cellspacing="0" cellpadding="0" width="100%" style="text-align: right;">
								<tbody>
								<tr>
									<td style="padding: .5em 1em; text-align: right; font-size: 13px; font-weight: 600;">Subtotal:</td>
									<td style="padding: .5em 1em; text-align: right; font-size: 13px;"><?php echo esc_html( eac_format_amount( $invoice->items_total, $invoice->currency_code ) ); ?></td>
								</tr>
								<?php if ( $invoice->discount ) : ?>
									<tr>
										<td style="padding: .5em 1em; text-align: right; font-size: 13px; font-weight: 600;">Discount:</td>
										<td style="padding: .5em 1em; text-align: right; font-size: 13px;"><?php echo esc_html( eac_format_amount( $invoice->discount_total, $invoice->currency_code ) ); ?></td>
									</tr>
								<?php endif; ?>
								<?php if ( ! empty( absint( $invoice->shipping_total ) ) ) : ?>
									<tr>
										<td style="padding: .5em 1em; text-align: right; font-size: 13px; font-weight: 600;">Shipping:</td>
										<td style="padding: .5em 1em; text-align: right; font-size: 13px;"><?php echo esc_html( eac_format_amount( $invoice->shipping_total, $invoice->currency_code ) ); ?></td>
									</tr>
								<?php endif; ?>
								<?php if ( ! empty( absint( $invoice->fees_total ) ) ) : ?>
									<tr>
										<td style="padding: .5em 1em; text-align: right; font-size: 13px; font-weight: 600;">Fees:</td>
										<td style="padding: .5em 1em; text-align: right; font-size: 13px;"><?php echo esc_html( eac_format_amount( $invoice->fees_total, $invoice->currency_code ) ); ?></td>
									</tr>
								<?php endif; ?>
								<?php if ( $invoice->is_calculating_tax() && ! empty( absint( $invoice->tax_total ) ) ) : ?>
									<?php if ( 'single' === get_option( 'eac_tax_display_totals' ) ) : ?>
										<tr>
											<td style="padding: .5em 1em; text-align: right; font-size: 13px; font-weight: 600;">Tax:</td>
											<td style="padding: .5em 1em; text-align: right; font-size: 13px;"><?php echo esc_html( eac_format_amount( $invoice->tax_total, $invoice->currency_code ) ); ?></td>
										</tr>
									<?php else : ?>
										<?php foreach ( $invoice->formatted_itemized_taxes as $label => $amount ) : ?>
											<tr>
												<td style="padding: .5em 1em; text-align: right; font-size: 13px; font-weight: 600;"><?php echo esc_html( $label ); ?>:</td>
												<td style="padding: .5em 1em; text-align: right; font-size: 13px;"><?php echo esc_html( $amount ); ?></td>
											</tr>
										<?php endforeach; ?>
									<?php endif; ?>
								<?php endif; ?>
								<tr>
									<td style="padding: .5em 1em; text-align: right; font-size: 13px; font-weight: 600;">Total:</td>
									<td style="padding: .5em 1em; text-align: right; font-size: 13px;"><?php echo esc_html( eac_format_amount( $invoice->total, $invoice->currency_code ) ); ?></td>
								</tr>
								<tr>
									<td style="padding: .5em 1em; text-align: right; font-size: 13px; font-weight: 600;">Paid:</td>
									<td style="padding: .5em 1em; text-align: right; font-size: 13px;"><?php echo esc_html( eac_format_amount( $invoice->total_paid, $invoice->currency_code ) ); ?></td>
								</tr>
								<tr>
									<td style="padding: .5em 1em; text-align: right; font-size: 13px; font-weight: 600;">Due:</td>
									<td style="padding: .5em 1em; text-align: right; font-size: 13px;"><?php echo esc_html( eac_format_amount( $invoice->balance, $invoice->currency_code ) ); ?></td>
								</tr>
								</tbody>
							</table>
						</td>
					</tr>
					</tbody>
				</table>
			</td>
		</tr>

		<tr>
			<td height="30px" style="vertical-align: top;" valign="top"></td>
		</tr>

		<tr>
			<td>
				<table style="border-collapse: collapse; border: 1px solid #ddd;" class="has-padding">
					<tbody>
					<tr style="page-break-inside: avoid; border-bottom: 1px solid #ddd; border-top: 1px solid #ddd;">
						<th style="padding-top: 6px;">Payment Date</th>
						<th style="padding-top:6px;">Payment Method</th>
						<th style="padding-top:6px; text-align: right;">Subtotal</th>
					</tr>
					<tr>
						<td>2024-05-02</td>
						<td>Cash</td>
						<td style="text-align: right;">$100 (Paid)</td>
					</tr>
					<tr>
						<td>2024-05-02</td>
						<td>Cash</td>
						<td style="text-align: right;">$100 (Paid)</td>
					</tr>
					<tr>
						<td>2024-05-02</td>
						<td>Cash</td>
						<td style="text-align: right;">$100 (Paid)</td>
					</tr>
					</tbody>
				</table>
			</td>
		</tr>

		<tr>
			<td height="20px" style="vertical-align: top;" valign="top"></td>
		</tr>

		<tr>
			<td>
				<hr class="data-table__StyledSeparator-sc-11th571-6 jcLHeJ">
			</td>
		</tr>

		<tr>
			<td height="10px" style="vertical-align: top;" valign="top"></td>
		</tr>

		<tr>
			<td style="vertical-align: top;" valign="top">
				<p style="font-size: 12px; font-weight: 200; color: #777; line-height: 1.75;">
					Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ad architecto, atque debitis delectus dolore ea enim eum expedita iste iusto maxime neque non placeat praesentium quam quasi qui tempora voluptatem!
				</p>
			</td>
		</tr>
		</tbody>
	</table>
</div>
