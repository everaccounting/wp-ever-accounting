<?php
defined('ABSPATH') || exit();
?>

<h1><?php _e( 'New Account', 'wp-ever-accounting' ); ?><a href="<?php echo esc_url( admin_url('admin.php?page=eaccounting-accounts') ); ?>" class="add-new-h2"><?php _e( 'All Accounts', 'wp-ever-accounting' ); ?></a></h1>
<div class="ea-card">
	<form id="ea-add-account" action="" method="post">
		<?php do_action( 'eaccounting_add_account_form_top' ); ?>
		<div class="ea-row">
			<div class="ea-col-6 required">
				<label for="account_name" class="ea-control-label"><?php _e('Name', 'wp-ever-accounting');?></label>
				<div class="ea-input-group">
					<div class="ea-input-group-addon">
						<i class="fa fa-id-card-o"></i>
					</div>
					<input class="ea-form-control" id='account_name' type="text" name="name" placeholder="<?php _e('Account Name', 'wp-ever-accounting');?>">
				</div>
			</div>

			<div class="ea-col-6 required">
				<label for="account_number" class="ea-control-label"><?php _e('Number', 'wp-ever-accounting');?></label>
				<div class="ea-input-group">
					<div class="ea-input-group-addon">
						<i class="fa fa-pencil"></i>
					</div>
					<input class="ea-form-control" id='account_number' type="text" name="number" placeholder="<?php _e('Account Number', 'wp-ever-accounting');?>">
				</div>
			</div>


			<div class="ea-col-6 required">
				<label for="currency_code" class="ea-control-label"><?php _e('Currency', 'wp-ever-accounting');?></label>
				<div class="ea-input-group">
					<div class="ea-input-group-addon">
						<i class="fa fa-exchange"></i>
					</div>
					<input class="ea-form-control" id='currency_code' type="text" name="currency_code">
				</div>
			</div>


			<div class="ea-col-6 required">
				<label for="opening_balance" class="ea-control-label"><?php _e('Opening Balance', 'wp-ever-accounting');?></label>
				<div class="ea-input-group">
					<div class="ea-input-group-addon">
						<i class="fa fa-money"></i>
					</div>
					<input class="ea-form-control" id='opening_balance' type="text" name="opening_balance" placeholder="<?php _e('Enter Opening Balance', 'wp-ever-accounting');?>">
				</div>
			</div>

			<div class="ea-col-6">
				<label for="bank_name" class="ea-control-label"><?php _e('Bank Name', 'wp-ever-accounting');?></label>
				<div class="ea-input-group">
					<div class="ea-input-group-addon">
						<i class="fa fa-university"></i>
					</div>
					<input class="ea-form-control" id='bank_name' type="text" name="bank_name">
				</div>
			</div>

			<div class="ea-col-6">
				<label for="bank_phone" class="ea-control-label"><?php _e('Bank Phone', 'wp-ever-accounting');?></label>
				<div class="ea-input-group">
					<div class="ea-input-group-addon">
						<i class="fa fa-phone"></i>
					</div>
					<input class="ea-form-control" id='bank_phone' type="text" name="bank_phone">
				</div>
			</div>

			<div class="ea-col-12">
				<div class="ea-form-group">
				<label for="bank_address" class="ea-control-label"><?php _e('Bank Address', 'wp-ever-accounting');?></label>
				<textarea class="ea-form-control" id='bank_address' name="bank_address"></textarea>
				</div>
			</div>

			<div class="ea-col-6">
				<label for="default_account" class="ea-control-label"><?php _e('Default Account', 'wp-ever-accounting');?></label>
				<div class="ea-form-group ea-switch">
					<fieldset>
						<label for="default_account">
							<input type="checkbox" class="" id="default_account" name="default_account" value="on" />
							<span class="ea-switch-view"></span>
						</label>
					</fieldset>
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


		<?php do_action( 'eaccounting_add_account_form_bottom' ); ?>
		<p>
			<input type="hidden" name="action" value="eaccounting_add_account"/>
			<?php wp_nonce_field('eaccounting_account_nonce', 'nonce');?>
			<input class="button button-primary" type="submit" value="<?php _e('Submit', 'wp-ever-accounting');?>">
		</p>

	</form>
</div>

