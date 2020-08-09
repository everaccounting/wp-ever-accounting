<?php
/**
 * Admin Payment Edit Page
 *
 * @package     EverAccounting
 * @subpackage  Admin/sales/Payments
 * @since       1.0.2
 */
defined( 'ABSPATH' ) || exit();
$revenue_id = isset( $_REQUEST['revenue_id'] ) ? absint( $_REQUEST['revenue_id'] ) : null;
try {
	$revenue = new \EverAccounting\Transaction( $revenue_id );
} catch ( Exception $e ) {
	wp_die( $e->getMessage() );
}
$back_url = remove_query_arg( array( 'action', 'id' ) );
?>

<div class="ea-form-card">
	<div class="ea-card ea-form-card__header is-compact">
		<h3 class="ea-form-card__header-title"><?php echo $revenue->exists() ? __( 'Update Payment', 'wp-ever-accounting' ) : __( 'Add Payment', 'wp-ever-accounting' ); ?></h3>
		<a href="<?php echo $back_url; ?>" class="button button-secondary"><span class="dashicons dashicons-arrow-left-alt"></span><?php _e( 'Back', 'wp-ever-accounting' ); ?></a>
	</div>

	<div class="ea-card">
		<form id="ea-revenue-form" method="post">
			<div class="ea-row">
				<?php
				eaccounting_text_input( array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Date', 'wp-ever-accounting' ),
						'name'          => 'paid_at',
						'tooltip'     => 'lorem ipsum dolor sit amet',
						'data_type'     => 'date',
						'value'         => $revenue->get_paid_at(),
						'required'      => true,
				) );

				eaccounting_select( array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Account', 'wp-ever-accounting' ),
						'name'          => 'account_id',
						'class'         => 'ea-select-account enable-create ea-ajax-select2',
						'value'         => $revenue->get_account_id(),
						'options'       => [],
						'required'      => true,
						'attr'          => array(
								'data-nonce'      => wp_create_nonce( 'get_account' ),
								'data-footer'      => true,
								'data-search'      => eaccounting_esc_json( json_encode( array(
										'nonce'  => wp_create_nonce( 'dropdown-search' ),
										'type'   => 'account',
										'action' => 'eaccounting_dropdown_search',
								) ), true ),
								'data-modal'       => eaccounting_esc_json( json_encode( array(
										'event' => 'ea-init-account-modal',
										'type'  => 'account',
										'nonce'  => 'edit_account',
								) ), true ),
								'data-placeholder' => __( 'Select Account', 'wp-ever-accounting' ),
						)
				) );
				eaccounting_text_input( array(
						'label'         => __( 'Amount', 'wp-ever-accounting' ),
						'name'          => 'amount',
						'value'         => $revenue->get_amount(),
						'data_type'     => 'price',
						'required'      => true,
						'wrapper_class' => 'ea-col-6',
				) );
				eaccounting_select( array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Customer', 'wp-ever-accounting' ),
						'name'          => 'contact_id',
						'class'         => 'ea-select-customer enable-create ea-ajax-select2',
						'value'         => $revenue->get_account_id(),
						'options'       => [],
						'attr'          => array(
								'data-footer'      => true,
								'data-search'      => eaccounting_esc_json( json_encode( array(
										'nonce'  => wp_create_nonce( 'dropdown-search' ),
										'type'   => 'customer',
										'action' => 'eaccounting_dropdown_search',
								) ), true ),
								'data-modal'       => eaccounting_esc_json( json_encode( array(
										'event' => 'ea-init-contact-modal',
										'type'  => 'customer',
								) ), true ),
								'data-placeholder' => __( 'Select Customer', 'wp-ever-accounting' ),
						)
				) );
				eaccounting_select( array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Category', 'wp-ever-accounting' ),
						'name'          => 'category_id',
						'class'         => 'ea-select-category enable-create ea-ajax-select2',
						'value'         => $revenue->get_account_id(),
						'options'       => [],
						'required'      => true,
						'attr'          => array(
								'data-footer'      => true,
								'data-search'      => eaccounting_esc_json( json_encode( array(
										'nonce'  => wp_create_nonce( 'dropdown-search' ),
										'type'   => 'income_category',
										'action' => 'eaccounting_dropdown_search',
								) ), true ),
								'data-modal'       => eaccounting_esc_json( json_encode( array(
										'event' => 'ea_request_category_modal',
										'type'  => 'income',
								) ), true ),
								'data-placeholder' => __( 'Select Category', 'wp-ever-accounting' ),
						)
				) );

				eaccounting_text_input( array(
						'label'         => __( 'Reference', 'wp-ever-accounting' ),
						'name'          => 'reference',
						'value'         => $revenue->get_reference(),
						'required'      => false,
						'wrapper_class' => 'ea-col-6',
				) );
				eaccounting_textarea( array(
						'label'         => __( 'Description', 'wp-ever-accounting' ),
						'name'          => 'description',
						'value'         => $revenue->get_description(),
						'required'      => false,
						'wrapper_class' => 'ea-col-12',
				) );

				wp_create_nonce('edit_revenue');

				submit_button( __( 'Submit', 'wp-ever-accounting' ), 'primary', 'submit',  );
				?>
			</div>
		</form>
	</div>
</div>

<div id="existing-element">
	<button id="my-button">Button</button>
</div>
