<?php
defined( 'ABSPATH' ) || exit();
?>
<tr class="line-item" id="line-item-<?php echo $item_row; ?>">

	<td>
		<input type="hidden" name="item[<?php echo $item_row; ?>][line_id]" id="item-id-<?php echo $line_id; ?>">
		<button class="item-line-remove"><i class="fa fa-trash" aria-hidden="true"></i></button>
	</td>
	<td>
		<input class="ea-form-control" type="text" name="item[<?php echo $item_row; ?>][name]"
		       value="<?php echo $name; ?>" autocomplete="off">
		<input type="hidden" name="item[<?php echo $item_row; ?>][item_id]" id="item-id-<?php echo $item_row; ?>">
	</td>
	<td>
		<input class="ea-form-control" type="text" class="line-item-qty" name="item[<?php echo $item_row; ?>][quantity]"
		       value="<?php echo $quantity; ?>">
	</td>
	<td>
		<input class="ea-form-control ea-price-control" type="text" name="item[<?php echo $item_row; ?>][price]" value="<?php echo $price; ?>">
	</td>

	<td>
		<span id="item-total-<?php echo $item_row; ?>">0</span>
	</td>

</tr>
