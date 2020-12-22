<?php
/**
 * Admin Revenue Edit Page.
 *
 * @since       1.0.2
 * @subpackage  Admin/Sales/Revenues
 * @package     EverAccounting
 */
defined( 'ABSPATH' ) || exit();

$income_id = isset( $_REQUEST['income_id'] ) ? absint( $_REQUEST['income_id'] ) : null;
try {
	$income = new \EverAccounting\Models\Income( $income_id );
} catch ( Exception $e ) {
	wp_die( $e->getMessage() );
}

$back_url = remove_query_arg( array( 'action', 'id' ) );
?>

<div class="ea-card">
	<div class="ea-card__header">
		<h3 class="ea-card__title"><?php echo $income->exists() ? __( 'Update Income', 'wp-ever-accounting' ) : __( 'Add Income', 'wp-ever-accounting' ); ?></h3>
		<a href="<?php echo $back_url; ?>" class="button button-secondary"><span class="dashicons dashicons-arrow-left-alt"></span><?php _e( 'Back', 'wp-ever-accounting' ); ?></a>
	</div>

	<div class="ea-card__inside">
		<form id="ea-income-form" method="post" enctype="multipart/form-data">
			<div class="ea-row">
				<?php
				eaccounting_text_input(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Date', 'wp-ever-accounting' ),
						'name'          => 'payment_date',
						'placeholder'   => __( 'Enter Date', 'wp-ever-accounting' ),
						'data_type'     => 'date',
						'value'         => $income->get_payment_date() ? $income->get_payment_date() : null,
						'required'      => true,
					)
				);
				eaccounting_account_dropdown(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Account', 'wp-ever-accounting' ),
						'name'          => 'account_id',
						'value'         => $income->get_account_id(),
						'creatable'     => true,
						'placeholder'   => __( 'Select Account', 'wp-ever-accounting' ),
						'required'      => true,
					)
				);

				eaccounting_text_input(
					array(
						'label'         => __( 'Amount', 'wp-ever-accounting' ),
						'name'          => 'amount',
						'value'         => $income->get_amount(),
						'data_type'     => 'price',
						'required'      => true,
						'wrapper_class' => 'ea-col-6',
						'placeholder'   => __( 'Enter Amount', 'wp-ever-accounting' ),
					)
				);
				eaccounting_customer_dropdown(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Customer', 'wp-ever-accounting' ),
						'name'          => 'contact_id',
						'id'            => 'customer_id',
						'value'         => $income->get_contact_id(),
						'placeholder'   => __( 'Select Customer', 'wp-ever-accounting' ),
						'type'          => 'customer',
						'creatable'     => true,
					)
				);
				eaccounting_category_dropdown(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Category', 'wp-ever-accounting' ),
						'name'          => 'category_id',
						'value'         => $income->get_category_id(),
						'required'      => true,
						'type'          => 'income',
						'creatable'     => true,
					)
				);
				eaccounting_payment_method_dropdown(
					array(
						'label'         => __( 'Payment Method', 'wp-ever-accounting' ),
						'name'          => 'payment_method',
						'wrapper_class' => 'ea-col-6',
						'required'      => true,
						'value'         => $income->get_payment_method(),
					)
				);
				eaccounting_textarea(
					array(
						'label'         => __( 'Description', 'wp-ever-accounting' ),
						'name'          => 'description',
						'value'         => $income->get_description(),
						'required'      => false,
						'wrapper_class' => 'ea-col-12',
						'placeholder'   => __( 'Enter description', 'wp-ever-accounting' ),
					)
				);
				eaccounting_text_input(
					array(
						'label'         => __( 'Reference', 'wp-ever-accounting' ),
						'name'          => 'reference',
						'value'         => $income->get_reference(),
						'required'      => false,
						'wrapper_class' => 'ea-col-6',
						'placeholder'   => __( 'Enter reference', 'wp-ever-accounting' ),
					)
				);
				eaccounting_file_input(
					array(
						'label'         => __( 'Attachments', 'wp-ever-accounting' ),
						'name'          => 'attachment_id',
						'value'         => $income->get_attachment_id(),
						'required'      => false,
						'wrapper_class' => 'ea-col-6',
						'placeholder'   => __( 'Upload File', 'wp-ever-accounting' ),
					)
				);
				eaccounting_hidden_input(
					array(
						'name'  => 'id',
						'value' => $income->get_id(),
					)
				);

				eaccounting_hidden_input(
					array(
						'name'  => 'action',
						'value' => 'eaccounting_edit_revenue',
					)
				);
				?>
			</div>
			<?php
			wp_nonce_field( 'ea_edit_revenue' );
			submit_button( __( 'Submit', 'wp-ever-accounting' ), 'primary', 'submit' );
			?>

		</form>
	</div>
</div>
