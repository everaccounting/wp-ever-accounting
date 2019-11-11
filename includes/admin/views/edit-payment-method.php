<?php
defined( 'ABSPATH' ) || exit();
$base_url    = admin_url( 'admin.php?page=eaccounting-misc&tab=payment_methods' );
$payment_method_id = empty( $_GET['payment_method'] ) ? false : absint( $_GET['payment_method'] );
$payment_method   = new StdClass();
if ( $payment_method_id ) {
	$payment_method = eaccounting_get_payment_method( $payment_method_id );
}
$title = ! empty( $payment_method->id ) ? __( 'Update Payment Method' ) : __( 'Add Payment Method', 'wp-eaccounting' );
echo sprintf( '<h1 class="wp-heading-inline">%s</h1>', $title );
echo sprintf( '<a href="%s" class="page-title-action">%s</a>', $base_url, __( 'All Payment Methods', 'wp-eaccounting' ) ); ?>

<div class="ea-card">
	<form action="" method="post">
		<?php do_action( 'eaccounting_add_payment_method_form_top' ); ?>
		<div class="ea-row">
			<?php
			echo EAccounting_Form::input_control( array(
				'label'         => __( 'Name', 'wp-eaccounting' ),
				'name'          => 'name',
				'value'         => isset( $payment_method->name ) ? $payment_method->name : '',
				'icon'          => 'fa fa-id-card-o',
				'required'      => true,
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::input_control( array(
				'label'         => __( 'Code', 'wp-eaccounting' ),
				'name'          => 'code',
				'value'         => isset( $payment_method->code ) ? $payment_method->code : '',
				'icon'          => 'fa fa-key',
				'required'      => true,
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::input_control( array(
				'label'         => __( 'Order', 'wp-eaccounting' ),
				'name'          => 'order',
				'value'         => isset( $payment_method->order ) ? $payment_method->order : '',
				'icon'          => 'fa fa-sort',
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::textarea_control( array(
				'label'         => __( 'Description', 'wp-eaccounting' ),
				'name'          => 'description',
				'value'         => isset( $payment_method->description ) ? $payment_method->description : '',
				'wrapper_class' => 'ea-col-12',
			) );

			echo EAccounting_Form::status_control( array(
				'label'         => __( 'Status', 'wp-eaccounting' ),
				'name'          => 'status',
				'value'         => isset( $payment_method->status ) ? $payment_method->status : 'active',
				'wrapper_class' => 'ea-col-6',
			) );

			?>
		</div>
		<?php do_action( 'eaccounting_add_payment_method_form_bottom' ); ?>
		<p>
			<input type="hidden" name="eaccounting-action" value="edit_payment_method"/>
			<input type="hidden" name="id" value="<?php echo $payment_method_id; ?>"/>
			<?php wp_nonce_field( 'eaccounting_payment_method_nonce' ); ?>
			<input class="button button-primary" type="submit" value="<?php _e( 'Submit', 'wp-ever-accounting' ); ?>">
		</p>
	</form>
</div>
