<?php
defined( 'ABSPATH' ) || exit();
$base_url   = admin_url( 'admin.php?page=eaccounting-accounts&tab=accounts' );
$account_id = empty( $_GET['account'] ) ? false : absint( $_GET['account'] );
$account    = new StdClass();
if ( $account_id ) {
	$account = eaccounting_get_account( $account_id );
}
$title = $account_id ? __( 'Update  Account', 'wp-ever-accounting' ) : __( 'Add Account', 'wp-ever-accounting' );

?>

<?php echo sprintf( '<h1 class="wp-heading-inline">%s</h1>', $title ); ?>
<?php echo sprintf( '<a href="%s" class="page-title-action">%s</a>', $base_url, __( 'All Accounts', 'wp-ever-accounting' ) ); ?>

<div class="ea-card">
	<form id="ea-add-account" action="" method="post">
		<?php do_action( 'eaccounting_add_account_form_top' ); ?>
		<div class="ea-row">
			<?php
			echo EAccounting_Form::input_control( array(
				'label'         => __( 'Name', 'wp-ever-accounting' ),
				'name'          => 'name',
				'value'         => isset( $account->name ) ? $account->name : '',
				'placeholder'   => __( 'Account Name', 'wp-ever-accounting' ),
				'icon'          => 'fa fa-id-card-o',
				'required'      => true,
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::price_control( array(
				'label'         => __( 'Opening Balance', 'wp-ever-accounting' ),
				'name'          => 'opening_balance',
				'value'         => isset( $account->opening_balance ) ? eaccounting_price($account->opening_balance) : '',
				'icon'          => 'fa fa-money',
				'required'      => true,
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::input_control( array(
				'label'         => __( 'Account Number', 'wp-ever-accounting' ),
				'name'          => 'number',
				'value'         => isset( $account->number ) ? $account->number : '',
				'placeholder'   => __( 'Account Number', 'wp-ever-accounting' ),
				'icon'          => 'fa fa-pencil',
				'required'      => false,
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::input_control( array(
				'label'         => __( 'Bank Name', 'wp-ever-accounting' ),
				'name'          => 'bank_name',
				'value'         => isset( $account->bank_name ) ? $account->bank_name : '',
				'icon'          => 'fa fa-university',
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::input_control( array(
				'label'         => __( 'Bank Phone', 'wp-ever-accounting' ),
				'name'          => 'bank_phone',
				'value'         => isset( $account->bank_phone ) ? $account->bank_phone : '',
				'icon'          => 'fa fa-phone',
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::status_control( array(
				'label'         => __( 'Status', 'wp-ever-accounting' ),
				'value'         => isset( $account->status ) ? $account->status : 'active',
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::textarea_control( array(
				'label'         => __( 'Bank Address', 'wp-ever-accounting' ),
				'name'          => 'bank_address',
				'value'         => isset( $account->bank_address ) ? $account->bank_address : '',
				'wrapper_class' => 'ea-col-12',
			) );

			?>
		</div>

		<?php do_action( 'eaccounting_add_account_form_bottom' ); ?>
		<p>
			<input type="hidden" name="id" value="<?php echo $account_id; ?>"/>
			<input type="hidden" name="eaccounting-action" value="edit_account"/>
			<?php wp_nonce_field( 'eaccounting_account_nonce'); ?>
			<input class="button button-primary" type="submit" value="<?php _e( 'Submit', 'wp-ever-accounting' ); ?>">
		</p>

	</form>
</div>

