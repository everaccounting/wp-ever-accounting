<tr>
	<td class="col-label" colspan="<?php echo count( $columns ) - 1; ?>"><?php esc_html_e( 'Discount', 'wp-ever-accounting' ); ?></td>
	<td class="col-amount">
		<div class="eac-input-group">
			<select name="discount_type" id="discount_type" class="addon">
				<?php
				foreach (
					array(
						'fixed'   => '($)',
						'percent' => '(%)',
					) as $key => $label
				) :
					?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $bill->discount_type ); ?>>
						<?php echo esc_html( $label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
			<input type="number" name="discount_value" id="discount_value" placeholder="10" style="text-align: right;width: auto;" value="<?php echo esc_attr( $bill->discount_value ); ?>"/>
		</div>
	</td>
</tr>

<tr>
	<td class="col-label" colspan="<?php echo count( $columns ) - 1; ?>"><?php esc_html_e( 'Subtotal', 'wp-ever-accounting' ); ?></td>
	<td class="col-amount"><?php echo esc_html( $bill->formatted_subtotal ); ?></td>
</tr>

<tr>
	<td class="col-label" colspan="<?php echo count( $columns ) - 1; ?>">
		<?php esc_html_e( 'Discount', 'wp-ever-accounting' ); ?>
	</td>
	<td class="col-amount">
		<?php echo esc_html( $bill->formatted_discount ); ?>
	</td>
</tr>

<?php if ( $is_taxed ) : ?>
	<tr>
		<td class="col-label" colspan="<?php echo count( $columns ) - 1; ?>">
			<?php esc_html_e( 'Tax', 'wp-ever-accounting' ); ?>
		</td>
		<td class="col-amount">
			<?php echo esc_html( $bill->formatted_tax ); ?>
		</td>
	</tr>
<?php endif; ?>

<tr>
	<td class="col-label" colspan="<?php echo count( $columns ) - 1; ?>">
		<?php esc_html_e( 'Total', 'wp-ever-accounting' ); ?>
	</td>
	<td class="col-amount">
		<?php echo esc_html( $bill->formatted_total ); ?>
	</td>
</tr>
