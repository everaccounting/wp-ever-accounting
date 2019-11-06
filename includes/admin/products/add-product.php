<?php
defined('ABSPATH') || exit();
?>

<h1><?php _e( 'New Product', 'wp-ever-accounting' ); ?><a href="<?php echo esc_url( admin_url('admin.php?page=eaccounting-products') ); ?>" class="add-new-h2"><?php _e( 'All Products', 'wp-ever-accounting' ); ?></a></h1>
<div class="ea-card">
	<form id="ea-add-account" action="" method="post">
		<?php do_action( 'eaccounting_add_account_form_top' ); ?>
		<div class="ea-row">
			<div class="ea-col-6 required">
				<label for="product_name" class="ea-control-label"><?php _e('Name', 'wp-ever-accounting');?></label>
				<div class="ea-input-group">
					<div class="ea-input-group-addon">
						<i class="fa fa-shopping-basket"></i>
					</div>
					<input class="ea-form-control" id='product_name' type="text" name="name" placeholder="<?php _e('Product Name', 'wp-ever-accounting');?>" required>
				</div>
			</div>

			<div class="ea-col-6 required">
				<label for="product_sku" class="ea-control-label"><?php _e('SKU', 'wp-ever-accounting');?></label>
				<div class="ea-input-group">
					<div class="ea-input-group-addon">
						<i class="fa fa-key"></i>
					</div>
					<input class="ea-form-control" id='product_sku' type="text" name="sku" placeholder="<?php _e('Product SKU', 'wp-ever-accounting');?>" required>
				</div>
			</div>


			<div class="ea-col-6 required">
				<label for="sale_price" class="ea-control-label"><?php _e('Sale Price', 'wp-ever-accounting');?></label>
				<div class="ea-input-group">
					<div class="ea-input-group-addon">
						<i class="fa fa-money"></i>
					</div>
					<input class="ea-form-control" id='sale_price' type="text" name="sale_price" required>
				</div>
			</div>

			<div class="ea-col-6 required">
				<label for="purchase_price" class="ea-control-label"><?php _e('Purchase Price', 'wp-ever-accounting');?></label>
				<div class="ea-input-group">
					<div class="ea-input-group-addon">
						<i class="fa fa-money"></i>
					</div>
					<input class="ea-form-control" id='purchase_price' type="text" name="sale_price" required>
				</div>
			</div>


			<div class="ea-col-6 required">
				<label for="quantity" class="ea-control-label"><?php _e('Quantity', 'wp-ever-accounting');?></label>
				<div class="ea-input-group">
					<div class="ea-input-group-addon">
						<i class="fa fa-cubes"></i>
					</div>
					<input class="ea-form-control" id='quantity' type="text" name="quantity" placeholder="<?php _e('Product Quantity', 'wp-ever-accounting');?>">
				</div>
			</div>


			<div class="ea-col-6">
				<label for="tax" class="ea-control-label"><?php _e('Tax', 'wp-ever-accounting');?></label>
				<div class="ea-input-group">
					<div class="ea-input-group-addon">
						<i class="fa fa-percent"></i>
					</div>
					<input class="ea-form-control" id='tax' type="text" name="tax" placeholder="<?php _e('Tax', 'wp-ever-accounting');?>">
				</div>
			</div>

			<div class="ea-col-6">
				<label for="category" class="ea-control-label"><?php _e('Category', 'wp-ever-accounting');?></label>
				<div class="ea-input-group">
					<div class="ea-input-group-addon">
						<i class="fa fa-folder-open-o"></i>
					</div>
					<select class="ea-form-control" id='category' name="category">
						<option value="">--Select Category--</option>
					</select>
				</div>
			</div>

			<div class="ea-col-12">
				<div class="ea-form-group">
				<label for="description" class="ea-control-label"><?php _e('Description', 'wp-ever-accounting');?></label>
				<textarea class="ea-form-control" id='description' name="description"></textarea>
				</div>
			</div>


			<div class="ea-col-6">
				<label for="status" class="ea-control-label"><?php _e('Status', 'wp-ever-accounting');?></label>
				<div class="ea-form-group ea-switch">
					<fieldset><label for="status"><input type="checkbox" class="" id="status" name="status" value="1" /><span class="ea-switch-view"></span></label></fieldset>
				</div>
			</div>

		</div>


		<?php do_action( 'eaccounting_add_account_form_bottom' ); ?>
		<p>
			<input type="hidden" name="action" value="eaccounting_add_account"/>
			<?php wp_nonce_field('eaccounting_account_nonce', 'nonce');?>
			<input class="button button-primary" type="submit" value="<?php _e('Submit', 'wp-ever-accounting');?>">
		</p>

	</form>
</div>

