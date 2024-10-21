<table class="document-address">
	<tbody>
	<?php if ( ! empty( $bill->contact_name ) ) : ?>
		<tr>
			<td class="name">
				<span><?php echo esc_html( $bill->contact_name ); ?></span>
				<input type="hidden" name="contact_name" value="<?php echo esc_attr( $bill->contact_name ); ?>">
			</td>
		</tr>
	<?php endif; ?>

	<?php if ( ! empty( $bill->contact_company ) ) : ?>
		<tr>
			<td class="company">
				<span><?php echo esc_html( $bill->contact_company ); ?></span>
				<input type="hidden" name="contact_company" value="<?php echo esc_attr( $bill->contact_company ); ?>">
			</td>
		</tr>
	<?php endif; ?>

	<tr>
		<td class="address">
			<span><?php echo esc_html( $bill->contact_address ); ?></span><br>
			<span><?php echo esc_html( $bill->contact_city ); ?> <?php echo esc_html( $bill->contact_state ); ?> <?php echo esc_html( $bill->contact_zip ); ?></span>
			<input type="hidden" name="contact_address" value="<?php echo esc_attr( $bill->contact_address ); ?>">
			<input type="hidden" name="contact_city" value="<?php echo esc_attr( $bill->contact_city ); ?>">
			<input type="hidden" name="contact_state" value="<?php echo esc_attr( $bill->contact_state ); ?>">
			<input type="hidden" name="contact_zip" value="<?php echo esc_attr( $bill->contact_zip ); ?>">
		</td>
	</tr>

	<?php if ( ! empty( $bill->contact_country ) ) : ?>
		<tr>
			<td class="country">
				<span><?php echo esc_html( $bill->contact_country ); ?></span>
				<input type="hidden" name="contact_country" value="<?php echo esc_attr( $bill->contact_country ); ?>">
			</td>
		</tr>
	<?php endif; ?>

	<?php if ( ! empty( $bill->contact_phone ) || ! empty( $bill->contact_email ) ) : ?>
		<tr>
			<td class="phone-email">
				<?php if ( ! empty( $bill->contact_phone ) ) : ?>
					<span class="phone"><?php echo esc_html( $bill->contact_phone ); ?></span>
					<input type="hidden" name="contact_phone" value="<?php echo esc_attr( $bill->contact_phone ); ?>">
				<?php endif; ?>

				<?php if ( ! empty( $bill->contact_phone ) && ! empty( $bill->contact_email ) ) : ?>
					<span class="separator"> | </span>
				<?php endif; ?>

				<?php if ( ! empty( $bill->contact_email ) ) : ?>
					<span class="email"><?php echo esc_html( $bill->contact_email ); ?></span>
					<input type="hidden" name="contact_email" value="<?php echo esc_attr( $bill->contact_email ); ?>">
				<?php endif; ?>
			</td>
		</tr>
	<?php endif; ?>

	</tbody>
</table>
