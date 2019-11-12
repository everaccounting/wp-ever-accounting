<?php
defined( 'ABSPATH' ) || exit();
$base_url    = admin_url( 'admin.php?page=eaccounting-income&tab=revenues' );
$revenue_id = empty( $_GET['revenue'] ) ? false : absint( $_GET['revenue'] );
$revenue   = new StdClass();
if ( $revenue_id ) {
	$revenue = eaccounting_get_revenue( $revenue_id );
}
$title = ! empty( $revenue->id ) ? __( 'Update Revenue' ) : __( 'Add Revenue', 'wp-eaccounting' );
echo sprintf( '<h1 class="wp-heading-inline">%s</h1>', $title );
echo sprintf( '<a href="%s" class="page-title-action">%s</a>', $base_url, __( 'All Revenues', 'wp-eaccounting' ) ); ?>

<div class="ea-card">
	<form action="" method="post">
		<?php do_action( 'eaccounting_add_revenue_form_top' ); ?>
		<div class="ea-row">
			<?php
			echo EAccounting_Form::date_control( array(
				'label'         => __( 'Date', 'wp-eaccounting' ),
				'name'          => 'date',
				'value'         => isset( $revenue->date ) ? $revenue->date : '',
				'icon'          => 'fa fa-calendar',
				'required'      => true,
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::price_control( array(
				'label'         => __( 'Amount', 'wp-eaccounting' ),
				'name'          => 'amount',
				'value'         => isset( $revenue->amount ) ? $revenue->amount : '',
				'icon'          => 'fa fa-money',
				'required'      => true,
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::accounts_dropdown( array(
				'label'         => __( 'Account', 'wp-eaccounting' ),
				'name'          => 'account_id',
				'value'         => isset( $revenue->account_id ) ? $revenue->account_id : '',
				'icon'          => 'fa fa-university',
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::vendor_dropdown( array(
				'label'         => __( 'Customer', 'wp-eaccounting' ),
				'name'          => 'contact_id',
				'value'         => isset( $revenue->contact_id ) ? $revenue->contact_id : '',
				'icon'          => 'fa fa-user',
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::textarea_control( array(
				'label'         => __( 'Description', 'wp-eaccounting' ),
				'name'          => 'description',
				'value'         => isset( $revenue->description ) ? $revenue->description : '',
				'wrapper_class' => 'ea-col-12',
			) );

			echo EAccounting_Form::categories_dropdown( array(
				'label'         => __( 'Category', 'wp-eaccounting' ),
				'name'          => 'category_id',
				'type'          => 'expense',
				'value'         => isset( $revenue->category_id ) ? $revenue->category_id : '',
				'icon'          => 'fa fa-folder-open-o',
				'required'      => true,
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::payment_methods_dropdown( array(
				'label'         => __( 'Payment Method', 'wp-eaccounting' ),
				'name'          => 'payment_method_id',
				'type'          => 'expense',
				'value'         => isset( $revenue->payment_method_id ) ? $revenue->payment_method_id : '',
				'icon'          => 'fa fa-credit-card',
				'required'      => true,
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::input_control( array(
				'label'         => __( 'Reference', 'wp-eaccounting' ),
				'name'          => 'reference',
				'value'         => isset( $revenue->reference ) ? $revenue->reference : '',
				'icon'          => 'fa fa-file-text-o',
				'wrapper_class' => 'ea-col-6',
			) );

			?>
		</div>
		<?php do_action( 'eaccounting_add_revenue_form_bottom' ); ?>
		<p>
			<input type="hidden" name="eaccounting-action" value="edit_revenue"/>
			<input type="hidden" name="id" value="<?php echo $revenue_id; ?>"/>
			<?php wp_nonce_field( 'eaccounting_revenue_nonce' ); ?>
			<input class="button button-primary" type="submit" value="<?php _e( 'Submit', 'wp-ever-accounting' ); ?>">
		</p>
	</form>
</div>
