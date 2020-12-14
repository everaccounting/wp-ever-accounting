<?php
$margin_side = is_rtl() ? 'left' : 'right';

foreach ( $invoice->get_line_items() as $item_id => $item ) : ?>
	<tr>
		<td class="td" style="text-align:left; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
			<?php echo esc_html( $item->get_item_name() ); ?>
		</td>

		<td class="td text-right" style="text-align:right; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
			<?php echo esc_html( $invoice->get_formatted_line_amount( $item, 'item_price' ) ); ?>
		</td>

		<td class="td text-right" style="text-align:right; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
			<?php echo esc_html( $item->get_quantity() ); ?>
		</td>

		<td class="td text-right" style="text-align:right; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
			<?php echo wp_kses_post( $invoice->get_formatted_line_amount( $item ) ); ?>
		</td>
	</tr>

<?php endforeach; ?>
