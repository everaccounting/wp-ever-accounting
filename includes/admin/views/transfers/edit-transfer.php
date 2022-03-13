<?php
/**
 * Admin Transfers Edit Page.
 *
 * @since       1.0.2
 * @subpackage  Admin/Banking/Transfers
 * @package     Ever_Accounting
 */

use Ever_Accounting\Helpers\Form;
use Ever_Accounting\Helpers\Formatting;

defined( 'ABSPATH' ) || exit();

$transfer_id = isset( $_REQUEST['transfer_id'] ) ? absint( $_REQUEST['transfer_id'] ) : null;
try {
	$transfer = \Ever_Accounting\Transfers::get( $transfer_id );
} catch ( Exception $e ) {
	wp_die( $e->getMessage() );
}
?>
<div class="ea-title-section">
	<div>
		<h1 class="wp-heading-inline"><?php esc_html_e( 'Transfers', 'wp-ever-accounting' ); ?></h1>
		<?php if ( $transfer->exists() ) : ?>
			<a href="<?php echo esc_url( add_query_arg( array( 'tab' => 'transfers', 'page' => 'ea-sales', 'action' => 'add' ), admin_url( 'admin.php' ) ) );//phpcs:ignore ?>" class="page-title-action">
				<?php esc_html_e( 'Add New', 'wp-ever-accounting' ); ?>
			</a>
		<?php else : ?>
			<a href="<?php echo remove_query_arg( array( 'action', 'id' ) ); ?>" class="page-title-action"><?php esc_html_e( 'View All', 'wp-ever-accounting' ); ?></a>
		<?php endif; ?>
	</div>
</div>
<hr class="wp-header-end">

<form id="ea-transfer-form" method="post" enctype="multipart/form-data">
	<div class="ea-card">
		<div class="ea-card__header">
			<h3 class="ea-card__title"><?php echo $transfer->exists() ? __( 'Update Transfer', 'wp-ever-accounting' ) : __( 'Add Transfer', 'wp-ever-accounting' ); ?></h3>
		</div>

		<div class="ea-card__inside">

			<div class="ea-row">
				<?php
				Form::account_dropdown(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'From Account', 'wp-ever-accounting' ),
						'name'          => 'from_account_id',
						'value'         => $transfer->get_from_account_id(),
						'required'      => true,
						'placeholder'   => __( 'Select Account', 'wp-ever-accounting' ),
						'creatable'     => true,
					)
				);

				Form::account_dropdown(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'To Account', 'wp-ever-accounting' ),
						'name'          => 'to_account_id',
						'value'         => $transfer->get_to_account_id(),
						'default'       => '',
						'required'      => true,
						'placeholder'   => __( 'Select Account', 'wp-ever-accounting' ),
						'creatable'     => true,
					)
				);

				Form::text_input(
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

				Form::text_input(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Date', 'wp-ever-accounting' ),
						'name'          => 'date',
						'placeholder'   => __( 'Enter date', 'wp-ever-accounting' ),
						'data_type'     => 'date',
						'value'         => $transfer->get_date() ? Formatting::date( $transfer->get_date(), 'Y-m-d' ) : null,
						'required'      => true,
					)
				);
				Form::payment_method_dropdown(
					array(
						'label'         => __( 'Payment Method', 'wp-ever-accounting' ),
						'name'          => 'payment_method',
						'placeholder'   => __( 'Enter payment method', 'wp-ever-accounting' ),
						'wrapper_class' => 'ea-col-6',
						'required'      => true,
						'value'         => $transfer->get_payment_method(),
					)
				);
				Form::text_input(
					array(
						'label'         => __( 'Reference', 'wp-ever-accounting' ),
						'name'          => 'reference',
						'value'         => $transfer->get_reference(),
						'required'      => false,
						'wrapper_class' => 'ea-col-6',
						'placeholder'   => __( 'Enter reference', 'wp-ever-accounting' ),
					)
				);
				Form::textarea(
					array(
						'label'         => __( 'Description', 'wp-ever-accounting' ),
						'name'          => 'description',
						'value'         => $transfer->get_description(),
						'required'      => false,
						'wrapper_class' => 'ea-col-12',
						'placeholder'   => __( 'Enter description', 'wp-ever-accounting' ),
					)
				);

				Form::hidden_input(
					array(
						'name'  => 'id',
						'value' => $transfer->get_id(),
					)
				);
				Form::hidden_input(
					array(
						'name'  => 'action',
						'value' => 'ever_accounting_edit_transfer',
					)
				);
				?>
			</div>


		</div>
		<div class="ea-card__footer">
			<?php

			wp_nonce_field( 'ea_edit_transfer' );

			submit_button( __( 'Submit', 'wp-ever-accounting' ), 'primary', 'submit' );
			?>
		</div>
	</div>
</form>
