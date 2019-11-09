<?php
defined('ABSPATH') || exit();
?>

<h1><?php _e( 'New Tax', 'wp-ever-accounting' ); ?><a href="<?php echo esc_url( admin_url('admin.php?page=eaccounting-taxes') ); ?>" class="add-new-h2"><?php _e( 'All Taxes', 'wp-ever-accounting' ); ?></a></h1>
<div class="ea-card">
	<form id="ea-add-tax" action="" method="post">
		<?php do_action( 'eaccounting_add_tax_form_top' ); ?>
		<div class="ea-row">
			<div class="ea-col-6 required">
				<label for="account_name" class="ea-control-label"><?php _e('Name', 'wp-ever-accounting');?></label>
				<div class="ea-input-group">
					<div class="ea-input-group-addon">
						<i class="fa fa-id-card-o"></i>
					</div>
					<input class="ea-form-control" id='tax_name' type="text" name="name" placeholder="<?php _e('Account Name', 'wp-ever-accounting');?>" required>
				</div>
			</div>

			<div class="ea-col-6 required">
				<label for="account_number" class="ea-control-label"><?php _e('Rate', 'wp-ever-accounting');?></label>
				<div class="ea-input-group">
					<div class="ea-input-group-addon">
						<i class="fa fa-percent"></i>
					</div>
					<input class="ea-form-control" id='tax_rate' type="text" name="rate" placeholder="<?php _e('Tax Rate', 'wp-ever-accounting');?>" required>
				</div>
			</div>


			<div class="ea-col-6 required">
				<label for="currency_code" class="ea-control-label"><?php _e('Type', 'wp-ever-accounting');?></label>
				<div class="ea-input-group">
					<div class="ea-input-group-addon">
						<i class="fa fa-bars"></i>
					</div>
					<select class="ea-form-control" id='tax_type' name="type" required>
						<option value="normal">Normal</option>
						<option value="inclusive">Inclusive</option>
						<option value="compound">Compound</option>
					</select>
				</div>
			</div>

			<div class="ea-col-6">
				<label for="status" class="ea-control-label"><?php _e('Status', 'wp-ever-accounting');?></label>
				<div class="ea-form-group ea-switch">
					<fieldset>
						<label for="status">
							<input type="checkbox" class="" id="status" name="status" value="1" />
							<span class="ea-switch-view"></span>
						</label>
					</fieldset>
				</div>
			</div>

		</div>


		<?php do_action( 'eaccounting_add_tax_form_bottom' ); ?>
		<p>
			<input type="hidden" name="action" value="eaccounting_add_tax"/>
			<?php wp_nonce_field('eaccounting_tax_nonce', 'nonce');?>
			<input class="button button-primary" type="submit" value="<?php _e('Submit', 'wp-ever-accounting');?>">
		</p>

	</form>
</div>

