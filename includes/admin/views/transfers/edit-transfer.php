<?php
/**
 * Admin Transfers Edit Page.
 *
 * @since       1.0.2
 * @subpackage  Admin/Banking/Transfers
 * @package     EverAccounting
 */
defined( 'ABSPATH' ) || exit();

$transfer_id = isset( $_REQUEST['transfer_id'] ) ? absint( $_REQUEST['transfer_id'] ) : null;
try {
	$transfer = new \EverAccounting\Models\Transfer( $transfer_id );
} catch ( Exception $e ) {
	wp_die( $e->getMessage() );
}
$back_url = remove_query_arg( array( 'action', 'id' ) );
?>

<div class="ea-card">
	<div class="ea-card__header">
		<h3 class="ea-card__title"><?php echo $transfer->exists() ? __( 'Update Transfer', 'wp-ever-accounting' ) : __( 'Add Transfer', 'wp-ever-accounting' ); ?></h3>
		<a href="<?php echo $back_url; ?>" class="button button-secondary"><span class="dashicons dashicons-arrow-left-alt"></span><?php _e( 'Back', 'wp-ever-accounting' ); ?></a>
	</div>

	<div class="ea-card__inside">
		<form id="ea-transfer-form" method="post" enctype="multipart/form-data">
			<div class="ea-row">
				<?php
				eaccounting_account_dropdown(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'From Account', 'wp-ever-accounting' ),
						'name'          => 'from_account_id',
						'value'         => $transfer->get_from_account_id(),
						'required'      => true,
						'placeholder'   => __( 'Select Account', 'wp-ever-accounting' ),
					)
				);

				eaccounting_account_dropdown(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'To Account', 'wp-ever-accounting' ),
						'name'          => 'to_account_id',
						'value'         => $transfer->get_to_account_id(),
						'default'       => '',
						'required'      => true,
						'placeholder'   => __( 'Select Account', 'wp-ever-accounting' ),
					)
				);

				eaccounting_text_input(
					array(
						'label'         => __( 'Amount', 'wp-ever-accounting' ),
						'name'          => 'amount',
						'value'         => $transfer->get_amount(),
						'data_type'     => 'price',
						'required'      => true,
						'wrapper_class' => 'ea-col-6',
						'placeholder'   => __( 'Enter amount', 'wp-ever-accounting' ),
					)
				);

				eaccounting_text_input(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Date', 'wp-ever-accounting' ),
						'name'          => 'date',
						'placeholder'   => __( 'Enter date', 'wp-ever-accounting' ),
						'data_type'     => 'date',
						'value'         => $transfer->get_date() ? $transfer->get_date() : null,
						'required'      => true,
					)
				);
				eaccounting_payment_method_dropdown(
					array(
						'label'         => __( 'Payment Method', 'wp-ever-accounting' ),
						'name'          => 'payment_method',
						'placeholder'   => __( 'Enter payment method', 'wp-ever-accounting' ),
						'wrapper_class' => 'ea-col-6',
						'required'      => true,
						'value'         => $transfer->get_payment_method(),
					)
				);
				eaccounting_text_input(
					array(
						'label'         => __( 'Reference', 'wp-ever-accounting' ),
						'name'          => 'reference',
						'value'         => $transfer->get_reference(),
						'required'      => false,
						'wrapper_class' => 'ea-col-6',
						'placeholder'   => __( 'Enter reference', 'wp-ever-accounting' ),
					)
				);
				eaccounting_textarea(
					array(
						'label'         => __( 'Description', 'wp-ever-accounting' ),
						'name'          => 'description',
						'value'         => $transfer->get_description(),
						'required'      => false,
						'wrapper_class' => 'ea-col-12',
						'placeholder'   => __( 'Enter description', 'wp-ever-accounting' ),
					)
				);

				eaccounting_hidden_input(
					array(
						'name'  => 'id',
						'value' => $transfer->get_id(),
					)
				);
				eaccounting_hidden_input(
					array(
						'name'  => 'action',
						'value' => 'eaccounting_edit_transfer',
					)
				);
				?>
			</div>
			<?php

			wp_nonce_field( 'ea_edit_transfer' );

			submit_button( __( 'Submit', 'wp-ever-accounting' ), 'primary', 'submit' );
			?>

		</form>
	</div>
</div>