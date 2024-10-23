<?php
$logo  = get_option( 'eac_business_logo' );
$phone = get_option( 'eac_business_phone' );
$email = get_option( 'eac_business_email' );

?>

<div class="tw-bg-white tw-p-8 tw-border tw-border-[#e1e2e2] tw-border-solid">
	<div class="tw-flex tw-justify-between">
		<div>
			<?php if ( $logo && filter_var( $logo, FILTER_VALIDATE_URL ) ) : ?>
				<img src="<?php echo esc_url( $logo ); ?>" alt="<?php esc_attr_e( 'Business Logo', 'wp-ever-accounting' ); ?>" style="max-height: 100px; max-width: 100%;"/>
			<?php endif; ?>
			<?php if ( $phone ) : ?>
				<p class="tw-mx-0 tw-my-0.5"> <a class="tw-text-[#3c3c3c]" href="tel:<?php echo esc_attr( $phone ); ?>"><?php echo esc_html( $phone ); ?></a></p>
			<?php endif; ?>
			<?php if ( $email ) : ?>
				<p class="tw-mx-0 tw-my-0.5"><a class="tw-text-[#3c3c3c]" href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></p>
			<?php endif; ?>
			<p class="tw-mx-0 tw-my-0.5"><a class="tw-text-[#3c3c3c]" href="<?php echo esc_url( home_url() ); ?>"><?php echo esc_html( home_url() ); ?></a></p>
		</div>
		<div class="tw-text-right">
			<h2 class="tw-text-2xl tw-font-bold tw-uppercase tw-m-0"><?php esc_html_e( 'Bill', 'wp-ever-accounting' ); ?></h2>
			<p class="tw-mx-0 tw-my-0.5">#&nbsp;<?php echo esc_html( $bill->number ); ?></p>
		</div>
	</div>

	<hr class="tw-my-5 tw-border-gray-300">

	<div class="tw-my-5">
		<h3 class="tw-text-lg tw-font-bold">Bill To:</h3>
		<address style="color: #636363; font-size: 13px;line-height:1.5;font-style:normal;">
			<?php
			$customer = $bill->customer;
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
			}
			?>
		</address>

		<p class="tw-text-gray-600">Client Name</p>
		<p class="tw-text-gray-600">456 Client Address</p>
		<p class="tw-text-gray-600">City, State, Zip</p>
		<p class="tw-text-gray-600">Email: client@example.com</p>
	</div>

	<table class="tw-w-full tw-table-auto tw-border-collapse">
		<thead>
		<tr class="tw-bg-gray-200">
			<th class="tw-py-2 tw-px-4 tw-border tw-border-gray-300">Description</th>
			<th class="tw-py-2 tw-px-4 tw-border tw-border-gray-300">Quantity</th>
			<th class="tw-py-2 tw-px-4 tw-border tw-border-gray-300">Unit Price</th>
			<th class="tw-py-2 tw-px-4 tw-border tw-border-gray-300">Total</th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td class="tw-py-2 tw-px-4 tw-border tw-border-gray-300">Product/Service 1</td>
			<td class="tw-py-2 tw-px-4 tw-border tw-border-gray-300">2</td>
			<td class="tw-py-2 tw-px-4 tw-border tw-border-gray-300">$50.00</td>
			<td class="tw-py-2 tw-px-4 tw-border tw-border-gray-300">$100.00</td>
		</tr>
		<tr>
			<td class="tw-py-2 tw-px-4 tw-border tw-border-gray-300">Product/Service 2</td>
			<td class="tw-py-2 tw-px-4 tw-border tw-border-gray-300">1</td>
			<td class="tw-py-2 tw-px-4 tw-border tw-border-gray-300">$150.00</td>
			<td class="tw-py-2 tw-px-4 tw-border tw-border-gray-300">$150.00</td>
		</tr>
		</tbody>
	</table>

	<div class="tw-my-5">
		<h3 class="tw-text-lg tw-font-bold">Total Amount Due:</h3>
		<p class="tw-text-xl tw-font-bold">$250.00</p>
	</div>

	<div class="tw-mt-10">
		<h3 class="tw-text-md tw-font-bold">Payment Instructions:</h3>
		<p>Please make the payment within 30 days of receiving this invoice.</p>
	</div>
</div>
