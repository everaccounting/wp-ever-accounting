<?php
defined( 'ABSPATH' ) || exit();
?>
<tr class="line-item" id="line-item <?php echo $item_row;?>">

	<td>
		<button class="item-line-remove"><i class="fa fa-trash" aria-hidden="true"></i></button>
	</td>
	<td>
		<input type="text" name="line_item[<?php echo $item_row;?>][item]" value="<?php echo $item;?>">
	</td>
	<td>
		<input type="text" class="line-item-qty" name="line_item[<?php echo $item_row;?>][qty]" <?php echo $qty;?>>
	</td>
	<td>
		<input type="text" class="line-item-unit-price" name="line_item[<?php echo $item_row;?>][unit_price]" <?php echo $unit_price;?>>
	</td>

	<td>
		<input type="text" class="line-item-line-total" name="line_item[<?php echo $item_row;?>][line_total]" readonly="" value="<?php echo floatval($qty*$unit_price);?>" placeholder="0.00">
	</td>

</tr>
