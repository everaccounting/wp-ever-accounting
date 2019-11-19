<?php
defined( 'ABSPATH' ) || exit();
$id      = empty( $_GET['revenue'] ) ? null : absint( $_GET['revenue'] );
$revenue = new EAccounting_Revenue($id);
$invoices_page_url = admin_url( 'admin.php?page=eaccounting-revenues' );
$title             = $revenue->get_id() ? __( 'Update Revenue', 'wp-ever-accounting' ) : __( 'Add Revenues', 'wp-ever-accounting' );
?>

<?php echo sprintf( '<h1 class="wp-heading-inline">%s</h1>', $title ); ?>
<?php echo sprintf( '<a href="%s" class="page-title-action">%s</a>', $invoices_page_url, __( 'All Revenues', 'wp-ever-accounting' ) ); ?>

<div class="ea-card">
	<form id="ea-revenue-form" action="" method="post" autocomplete="off">
		<div class="ea-row">
			<?php
			echo EAccounting_Form::date_control( array(
				'label'         => __( 'Date', 'wp-ever-accounting' ),
				'name'          => 'paid_at',
				'value'         => $revenue->get_paid_at() ,
				'icon'          => 'fa fa-calendar',
				'required'      => true,
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::price_control( array(
				'label'         => __( 'Amount', 'wp-ever-accounting' ),
				'name'          => 'amount',
				'value'         => $revenue->get_amount(),
				'icon'          => 'fa fa-money',
				'required'      => true,
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::accounts_dropdown( array(
				'label'         => __( 'Account', 'wp-ever-accounting' ),
				'name'          => 'account_id',
				'selected'         => $revenue->get_account(),
				'icon'          => 'fa fa-university',
				'required'      => true,
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::customer_dropdown( array(
				'label'         => __( 'Customer', 'wp-ever-accounting' ),
				'name'          => 'contact_id',
				'icon'          => 'fa fa-user',
				'selected'         => $revenue->get_contact(),
				'required'      => false,
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::categories_dropdown( array(
				'label'         => __( 'Category', 'wp-ever-accounting' ),
				'name'          => 'category_id',
				'type'          => 'income',
				'selected'         => $revenue->get_category(),
				'icon'          => 'fa fa-folder-open-o',
				'required'      => true,
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::select_control( array(
				'label'         => __( 'Payment Method', 'wp-ever-accounting' ),
				'name'          => 'method_id',
				'selected'      => $revenue->get_payment_method(),
				'icon'          => 'fa fa-credit-card',
				'required'      => true,
				'options'       => eaccounting_get_payment_methods(),
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::input_control( array(
				'label'         => __( 'Reference', 'wp-ever-accounting' ),
				'name'          => 'reference',
				'value'         => $revenue->get_reference(),
				'icon'          => 'fa fa-file-text-o',
				'required'      => false,
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::file_control( array(
				'label'         => __( 'Attachment', 'wp-ever-accounting' ),
				'name'          => 'attachment',
				'type'          => 'file',
				'icon'          => 'fa fa-file-text-o',
				'required'      => false,
				'wrapper_class' => 'ea-col-6',
			) );



			echo EAccounting_Form::textarea_control( array(
				'label'         => __( 'Description', 'wp-ever-accounting' ),
				'name'          => 'description',
				'value'         => $revenue->get_description(),
				'required'      => false,
				'wrapper_class' => 'ea-col-12',
			) );

			?>
		</div>

		<?php do_action( 'eaccounting_add_revenue_form_bottom' ); ?>
		<p>
			<input type="hidden" name="eaccounting-action" value="edit_revenue"/>
			<input type="hidden" name="id" value="<?php echo $id; ?>"/>
			<?php wp_nonce_field( 'eaccounting_revenue_nonce' ); ?>
			<input class="button button-primary" type="submit" value="<?php _e( 'Submit', 'wp-ever-accounting' ); ?>">
		</p>


	</form>
</div>
