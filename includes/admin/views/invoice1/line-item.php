<?php
/**
 * Invoice Line Items
 *
 * @package eaccounting\Admin\Views
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
?>
<tr class="ea-invoice-line">
	<?php if ( $invoice->is_editable() ) : ?>
		<td class="line-actions">
			<a href="#" class="save-line"><span class="dashicons dashicons-yes">&nbsp;</span></a>
			<a href="#" class="edit-line"><span class="dashicons dashicons-edit">&nbsp;</span></a>
			<a href="#" class="delete-line"><span class="dashicons dashicons-no">&nbsp;</span></a>
		</td>
	<?php endif; ?>
	<td class="line-name" colspan="2">
		<input type="hidden" class="line-id" name="line_items[<?php echo $index; ?>][id]" value="<?php echo esc_html( $item->get_id() ); ?>">
		<input type="hidden" class="item-name" name="line_items[<?php echo $index; ?>][item_name]" value="<?php echo esc_html( $item->get_item_name() ); ?>">
		<div class="edit">
			<select class="item-id ea-select2"
					name="line_items[<?php echo $index; ?>][item_id]"
					data-url="<?php echo esc_attr(eaccounting()->ajax_url());?>"
					data-ajax_action="eaccounting_get_items"
					data-map="return {text: option.name, id:option.id, item:option}"
					data-nonce_action="<?php echo esc_attr(wp_create_nonce('ea_get_items'));?>"
					data-modal_id="#ea-modal-add-item"
					placeholder="<?php esc_html_e( 'Search item', 'wp-ever-accounting' ); ?>"
					data-placeholder="<?php esc_html_e( 'Search item', 'wp-ever-accounting' ); ?>"
					required>
					<?php echo $item->get_item_id()? sprintf('<option value="%d">%s</option>', $item->get_item_id(), $item->get_item_name()): '';?>
			</select>
		</div>
		<div class="view">
			<?php echo esc_html( $item->get_item_name() ); ?>
		</div>
	</td>
	<td class="line-price">
		<div class="edit">
			<input type="text" class="item-price" name="line_items[<?php echo $index; ?>][item_price]" value="<?php echo $item->get_item_price(); ?>" required>
		</div>
		<div class="view">
			<?php echo esc_html( eaccounting_format_price( $item->get_item_price(), $invoice->get_currency_code() ) ); ?>
		</div>
	</td>
	<td class="line-quantity">
		<div class="edit">
			<input type="text" class="item-quantity" name="line_items[<?php echo $index; ?>][quantity]" value="<?php echo esc_html( $item->get_quantity() ); ?>" required>
		</div>
		<div class="view">
			<small class="times">×</small>
			<?php echo esc_html( $item->get_quantity() ); ?>
		</div>
	</td>
	<td class="line-tax">
		<div class="edit">
			<input type="text" class="item-vat" name="line_items[<?php echo $index; ?>][tax_rate]" value="<?php echo esc_html( $item->get_tax_rate() ); ?>" required>
		</div>
		<div class="view">
			<?php echo esc_html( $item->get_tax_rate() ); ?>
		</div>
	</td>
	<td class="line-vat">
		<div class="edit">
			<input type="text" class="item-tax" name="line_items[<?php echo $index; ?>][vat_rate]" value="<?php echo esc_html( $item->get_vat_rate() ); ?>">
		</div>
		<div class="view">
			<?php echo esc_html( $item->get_vat_rate() ); ?>
		</div>
	</td>
	<td class="line-subtotal">
		<div class="view">
			<span class="item-subtotal"><?php echo esc_html( eaccounting_format_price( $item->get_subtotal(), $invoice->get_currency_code() ) ); ?></span>
		</div>
	</td>
</tr>