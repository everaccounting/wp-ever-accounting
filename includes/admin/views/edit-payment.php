<?php
defined( 'ABSPATH' ) || exit();
$base_url   = admin_url( 'admin.php?page=eaccounting-misc&tab=categories' );
$payment_id = empty( $_GET['payment'] ) ? false : absint( $_GET['payment'] );
$payment    = new StdClass();
if ( $payment_id ) {
	$payment = eaccounting_get_payment( $payment_id );
}
$title = ! empty( $payment->id ) ? __( 'Update Category' ) : __( 'Add Category', 'wp-eaccounting' );
echo sprintf( '<h1 class="wp-heading-inline">%s</h1>', $title );
echo sprintf( '<a href="%s" class="page-title-action">%s</a>', $base_url, __( 'All Categories', 'wp-ever-accounting' ) ); ?>

<div class="ea-card">
	<form action="<?php echo add_query_arg( [ 'eaccounting-action' => 'add_payment' ], $base_url ); ?>" method="post">
		<?php do_action( 'eaccounting_add_payment_form_top' ); ?>
		<div class="ea-row">
			<?php
			echo EAccounting_Form::date_control( array(
				'label'         => __( 'Date', 'wp-eaccounting' ),
				'name'          => 'date',
				'value'         => isset( $payment->date ) ? $payment->date : '',
				'icon'          => 'fa fa-calendar',
				'required'      => true,
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::date_control( array(
				'label'         => __( 'Amount', 'wp-eaccounting' ),
				'name'          => 'amount',
				'value'         => isset( $payment->amount ) ? $payment->amount : '',
				'icon'          => 'fa fa-money',
				'required'      => true,
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::accounts_dropdown( array(
				'label'         => __( 'Account', 'wp-eaccounting' ),
				'name'          => 'account_id',
				'value'         => isset( $payment->account_id ) ? $payment->account_id : '',
				'icon'          => 'fa fa-university',
				'required'      => true,
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::vendor_dropdown( array(
				'label'         => __( 'Vendor', 'wp-eaccounting' ),
				'name'          => 'contact_id',
				'value'         => isset( $payment->contact_id ) ? $payment->contact_id : '',
				'icon'          => 'fa fa-user',
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::categories_dropdown( array(
				'label'         => __( 'Category', 'wp-eaccounting' ),
				'name'          => 'category_id',
				'type'          => 'expense',
				'value'         => isset( $payment->category_id ) ? $payment->category_id : '',
				'icon'          => 'fa fa-folder-open-o',
				'required'      => true,
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::status_control( array(
				'label'         => __( 'Status', 'wp-eaccounting' ),
				'value'         => isset( $payment->status ) ? $payment->status : 'active',
				'wrapper_class' => 'ea-col-6',
			) );



			?>
		</div>
		<?php do_action( 'eaccounting_add_payment_form_bottom' ); ?>
		<p>
			<input type="hidden" name="eaccounting-action" value="edit_payment"/>
			<input type="hidden" name="id" value="<?php echo $payment_id; ?>"/>
			<?php wp_nonce_field( 'eaccounting_payment_nonce' ); ?>
			<input class="button button-primary" type="submit" value="<?php _e( 'Submit', 'wp-ever-accounting' ); ?>">
		</p>
	</form>
</div>
