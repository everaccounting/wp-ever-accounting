<?php
defined( 'ABSPATH' ) || exit();
$taxes    = eaccounting_get_taxes( array(
	'fields' => array( 'id', 'name' ),
	'status' => 'active'
) );
$taxes    = wp_list_pluck( $taxes, 'name', 'id' );
?>
<tr class="line-item" id="line-item-<?php echo $item_row; ?>">

	<td class="line-action">
		<input type="hidden" name="item[<?php echo $item_row; ?>][line_id]">
		<button class="item-line-remove"><i class="fa fa-trash" aria-hidden="true"></i></button>
	</td>
	<td class="line-item">
		<input class="ea-form-control" type="text" name="item[<?php echo $item_row; ?>][name]" value="<?php echo $name; ?>" autocomplete="off">
		<input type="hidden" name="item[<?php echo $item_row; ?>][item_id]" id="item-id-<?php echo $item_row; ?>">
	</td>
	<td class="line-quantity">
		<input class="ea-form-control item-quantity" type="text" name="item[<?php echo $item_row; ?>][quantity]"
		       value="<?php echo $quantity; ?>">
	</td>
	<td class="line-price">
		<input class="ea-form-control ea-price-control" type="text" name="item[<?php echo $item_row; ?>][price]"
		       value="<?php echo $price; ?>">
	</td>
	<td class="line-tax">
		<select class="ea-form-control ea-tax-control" name="<?php echo sprintf( 'item[%d][tax_id][]', $item_row ); ?>"
		        id="<?php echo sprintf( 'item[%d][tax_id][]', $item_row ); ?>" multiple>
			<?php foreach ( $taxes as $tax_id => $tax_name ) {
				echo sprintf( '<option value="%s">%s</option>', $tax_id, $tax_name );
			} ?>
		</select>
	</td>

	<td class="line-total">
		<span id="item-total-<?php echo $item_row; ?>"><?php echo eaccounting_price( 0 ); ?></span>
	</td>

</tr>
