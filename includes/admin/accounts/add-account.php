<?php
defined( 'ABSPATH' ) || exit();
?>

<h1><?php _e( 'New Account', 'wp-ever-accounting' ); ?><a
		href="<?php echo esc_url( admin_url( 'admin.php?page=eaccounting-accounts' ) ); ?>"
		class="add-new-h2"><?php _e( 'All Accounts', 'wp-ever-accounting' ); ?></a></h1>
<div class="ea-card">
	<form id="ea-add-account" action="" method="post">
		<?php do_action( 'eaccounting_add_account_form_top' ); ?>
		<div class="ea-row">
			<?php
			echo eaccounting_input_field( array(
				'label'         => __( 'Name', 'wp-ever-accounting' ),
				'name'          => 'name',
				'placeholder'   => __( 'Account Name', 'wp-ever-accounting' ),
				'icon'          => 'fa fa-id-card-o',
				'required'      => true,
				'wrapper_class' => 'ea-col-6',
			) );

			echo eaccounting_input_field( array(
				'label'         => __( 'Number', 'wp-ever-accounting' ),
				'name'          => 'number',
				'placeholder'   => __( 'Account Number', 'wp-ever-accounting' ),
				'icon'          => 'fa fa-pencil',
				'required'      => true,
				'wrapper_class' => 'ea-col-6',
			) );

			echo eaccounting_input_field( array(
				'label'         => __( 'Currency', 'wp-ever-accounting' ),
				'name'          => 'currency_code',
				'placeholder'   => __( 'Currency Code', 'wp-ever-accounting' ),
				'icon'          => 'fa fa-exchange',
				'required'      => true,
				'wrapper_class' => 'ea-col-6',
			) );

			echo eaccounting_input_field( array(
				'label'         => __( 'Opening Balance', 'wp-ever-accounting' ),
				'name'          => 'opening_balance',
				//'placeholder'   => __( 'Currency Code', 'wp-ever-accounting' ),
				'icon'          => 'fa fa-money',
				'required'      => true,
				'wrapper_class' => 'ea-col-6',
			) );

			echo eaccounting_input_field( array(
				'label'         => __( 'Bank Name', 'wp-ever-accounting' ),
				'name'          => 'bank_name',
				'icon'          => 'fa fa-university',
				'wrapper_class' => 'ea-col-6',
			) );

			echo eaccounting_input_field( array(
				'label'         => __( 'Bank Phone', 'wp-ever-accounting' ),
				'name'          => 'bank_phone',
				'icon'          => 'fa fa-phone',
				'wrapper_class' => 'ea-col-6',
			) );

			echo eaccounting_textarea_field( array(
				'label'         => __( 'Bank Address', 'wp-ever-accounting' ),
				'name'          => 'bank_address',
				'wrapper_class' => 'ea-col-12',
			) );

			echo eaccounting_switch_field( array(
				'label'         => __( 'Default Account', 'wp-ever-accounting' ),
				'name'          => 'default_account',
				'wrapper_class' => 'ea-col-6',
			) );

			echo eaccounting_switch_field( array(
				'label'         => __( 'Status', 'wp-ever-accounting' ),
				'name'          => 'status',
				'check'          => '1',
				'value'          => '1',
				'wrapper_class' => 'ea-col-6',
			) );

			?>
		</div>

		<?php do_action( 'eaccounting_add_account_form_bottom' ); ?>
		<p>
			<input type="hidden" name="action" value="eaccounting_add_account"/>
			<?php wp_nonce_field( 'eaccounting_account_nonce', 'nonce' ); ?>
			<input class="button button-primary" type="submit" value="<?php _e( 'Submit', 'wp-ever-accounting' ); ?>">
		</p>

	</form>
</div>

