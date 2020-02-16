<?php
defined( 'ABSPATH' ) || exit();
$id       = empty( $_GET['transfer'] ) ? null : absint( $_GET['transfer'] );
$transfer = new StdClass();
if ( ! empty( $id ) ) {
	$transfer = eaccounting_get_transfer( $id );
}

$transfer_url = admin_url( 'admin.php?page=eaccounting-banking&tab=transfers' );
$title        = ! empty( $transfer->id ) ? __( 'Update Transfer', 'wp-ever-accounting' ) : __( 'Add Transfer', 'wp-ever-accounting' );
?>

<?php echo sprintf( '<h1 class="wp-heading-inline">%s</h1>', $title ); ?>
<?php echo sprintf( '<a href="%s" class="page-title-action">%s</a>', $transfer_url, __( 'All Transfers', 'wp-ever-accounting' ) ); ?>
<?php if ( ! empty( $transfer->id ) ): ?>
	<a href="<?php echo esc_url( add_query_arg( array( 'eaccounting-action' => 'add_transfer'  ), $transfer_url )); ?>"
	   class="page-title-action">
		<?php _e( 'Add New', 'wp-ever-accounting' ); ?>
	</a>
<?php endif; ?>
<div class="ea-mb-20"></div>
<div class="ea-card">
	<div class="ea-card-body">
		<form id="ea-transfer-form" action="" method="post" autocomplete="off">
			<div class="ea-row">
				<?php
				echo EAccounting_Form::accounts_dropdown( array(
					'label'         => __( 'From Account', 'wp-ever-accounting' ),
					'name'          => 'from_account_id',
					'selected'      => isset( $transfer->from_account_id ) ? $transfer->from_account_id : '',
					'icon'          => 'fa fa-university',
					'required'      => true,
					'wrapper_class' => 'ea-col-6',
				) );

				echo EAccounting_Form::accounts_dropdown( array(
					'label'         => __( 'To Account', 'wp-ever-accounting' ),
					'name'          => 'to_account_id',
					'selected'      => isset( $transfer->to_account_id ) ? $transfer->to_account_id : '',
					'icon'          => 'fa fa-university',
					'required'      => true,
					'wrapper_class' => 'ea-col-6',
				) );

				echo EAccounting_Form::price_control( array(
					'label'         => __( 'Amount', 'wp-ever-accounting' ),
					'name'          => 'amount',
					'value'         => isset( $transfer->amount ) ? eaccounting_sanitize_price( $transfer->amount ) : '',
					'icon'          => 'fa fa-money',
					'required'      => true,
					'wrapper_class' => 'ea-col-6',
				) );

				echo EAccounting_Form::date_control( array(
					'label'         => __( 'Date', 'wp-ever-accounting' ),
					'name'          => 'transferred_at',
					'value'         => isset( $transfer->transferred_at ) ? $transfer->transferred_at : date( 'Y-m-d' ),
					'icon'          => 'fa fa-calendar',
					'required'      => true,
					'wrapper_class' => 'ea-col-6',
				) );

				echo EAccounting_Form::select_control( array(
					'label'         => __( 'Payment Method', 'wp-ever-accounting' ),
					'name'          => 'payment_method',
					'selected'      => isset( $transfer->payment_method ) ? $transfer->payment_method : '',
					'icon'          => 'fa fa-credit-card',
					'required'      => true,
					'options'       => eaccounting_get_payment_methods(),
					'wrapper_class' => 'ea-col-6',
				) );

				echo EAccounting_Form::input_control( array(
					'label'         => __( 'Reference', 'wp-ever-accounting' ),
					'name'          => 'reference',
					'value'         => isset( $transfer->reference ) ? $transfer->reference : '',
					'icon'          => 'fa fa-file-text-o',
					'required'      => false,
					'wrapper_class' => 'ea-col-6',
				) );

				echo EAccounting_Form::textarea_control( array(
					'label'         => __( 'Description', 'wp-ever-accounting' ),
					'name'          => 'description',
					'value'         => isset( $transfer->description ) ? $transfer->description : '',
					'required'      => false,
					'wrapper_class' => 'ea-col-12',
				) );
				?>
			</div>

			<?php do_action( 'eaccounting_add_transfer_form_bottom' ); ?>

			<p>
				<input type="hidden" name="eaccounting-action" value="edit_transfer"/>
				<input type="hidden" name="id" value="<?php echo $id; ?>"/>
				<?php wp_nonce_field( 'eaccounting_transfer_nonce' ); ?>
				<input class="button button-primary" type="submit"
				       value="<?php _e( 'Submit', 'wp-ever-accounting' ); ?>">
			</p>
		</form>

	</div>
</div>
