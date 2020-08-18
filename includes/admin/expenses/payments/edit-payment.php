<?php
/**
 * Admin Payment Edit Page.
 *
 * @since       1.0.2
 * @subpackage  Admin/Sales/Payments
 * @package     EverAccounting
 */

use EverAccounting\Query_Account;

defined( 'ABSPATH' ) || exit();
$payment_id = isset( $_REQUEST['payment_id'] ) ? absint( $_REQUEST['payment_id'] ) : null;
try {
	$payment = new \EverAccounting\Transaction( $payment_id );
} catch ( Exception $e ) {
	wp_die( $e->getMessage() );
}
if ( $payment->exists() && 'expense' !== $payment->get_type() ) {
	echo __( 'Unknown payment ID', 'wp-ever-accounting' );
	exit();
}
$back_url = remove_query_arg( array( 'action', 'id' ) );
?>

<div class="ea-form-card">
	<div class="ea-card ea-form-card__header is-compact">
		<h3 class="ea-form-card__header-title"><?php echo $payment->exists() ? __( 'Update Payment', 'wp-ever-accounting' ) : __( 'Add Payment', 'wp-ever-accounting' ); ?></h3>
		<a href="<?php echo $back_url; ?>" class="button button-secondary"><span class="dashicons dashicons-arrow-left-alt"></span><?php _e( 'Back', 'wp-ever-accounting' ); ?></a>
	</div>

	<div class="ea-card">
		<form id="ea-payment-form" method="post">
			<div class="ea-row">
				<?php
				eaccounting_text_input( array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Date', 'wp-ever-accounting' ),
						'name'          => 'paid_at',
						'placeholder'   => __( 'Enter date', 'wp-ever-accounting' ),
						'data_type'     => 'date',
						'value'         => $payment->get_paid_at()->format( 'Y-m-d' ),
						'required'      => true,
				) );

				$default_account_id = (int) eaccounting()->settings->get( 'default_account' );
				$account_id         = (int) $payment->get_account_id();
				$accounts           = Query_Account::init()
												   ->select( 'id, name' )
												   ->whereIn( 'id', [ $default_account_id, $account_id ] )
												   ->get();
				eaccounting_select2( array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Account', 'wp-ever-accounting' ),
						'name'          => 'account_id',
						'value'         => $payment->get_account_id(),
						'default'       => $default_account_id,
						'options'       => wp_list_pluck( $accounts, 'name', 'id' ),
						'placeholder'   => __( 'Select Account', 'wp-ever-accounting' ),
						'ajax'          => true,
						'type'          => 'account',
						'creatable'     => true,
						'template'      => 'add-account'
				) );

				eaccounting_text_input( array(
						'label'         => __( 'Amount', 'wp-ever-accounting' ),
						'name'          => 'amount',
						'value'         => $payment->get_amount(),
						'data_type'     => 'price',
						'required'      => true,
						'wrapper_class' => 'ea-col-6',
						'placeholder'   => __( 'Enter amount', 'wp-ever-accounting' ),
				) );

				$customer_id = (int) $payment->get_contact_id();
				$customers   = \EverAccounting\Query_Contact::init()
															->select( 'id, name' )
															->isCustomer()
															->where( 'id', [ $customer_id ] )
															->get();
				eaccounting_select2( array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Customer', 'wp-ever-accounting' ),
						'name'          => 'contact_id',
						'value'         => $payment->get_contact_id(),
						'options'       => wp_list_pluck( $customers, 'name', 'id' ),
						'placeholder'   => __( 'Select Customer', 'wp-ever-accounting' ),
						'ajax'          => true,
						'type'          => 'customer',
						'creatable'     => true,
						'template'      => 'add-contact',
						'data'          => array(
								'data-contact_type' => 'customer'
						),
				) );


				$category_id = (int) $payment->get_category_id();
				$categories  = \EverAccounting\Query_Category::init()
															 ->select( 'id, name' )
															 ->isExpense()
															 ->where( 'id', [ $category_id ] )
															 ->get();
				eaccounting_select2( array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Category', 'wp-ever-accounting' ),
						'name'          => 'category_id',
						'value'         => $payment->get_category_id(),
						'options'       => wp_list_pluck( $categories, 'name', 'id' ),
						'placeholder'   => __( 'Select Category', 'wp-ever-accounting' ),
						'ajax'          => true,
						'type'          => 'expense_category',
						'creatable'     => true,
						'template'      => 'add-category',
						'data'          => array(
								'data-category_type' => 'expense'
						),
				) );

				eaccounting_select2( array(
						'label'         => __( 'Payment Method', 'wp-ever-accounting' ),
						'name'          => 'payment_method',
						'placeholder'   => __( 'Enter payment method', 'wp-ever-accounting' ),
						'wrapper_class' => 'ea-col-6',
						'required'      => true,
						'value'         => $payment->get_payment_method(),
						'default'       => eaccounting()->settings->get( 'default_payment_method' ),
						'options'       => eaccounting_get_payment_methods(),
				) );

				eaccounting_textarea( array(
						'label'         => __( 'Description', 'wp-ever-accounting' ),
						'name'          => 'description',
						'value'         => $payment->get_description(),
						'required'      => false,
						'wrapper_class' => 'ea-col-12',
						'placeholder'   => __( 'Enter description', 'wp-ever-accounting' ),
				) );

				eaccounting_text_input( array(
						'label'         => __( 'Reference', 'wp-ever-accounting' ),
						'name'          => 'reference',
						'value'         => $payment->get_reference(),
						'required'      => false,
						'wrapper_class' => 'ea-col-6',
						'placeholder'   => __( 'Enter reference', 'wp-ever-accounting' ),
				) );
				eaccounting_hidden_input( array(
						'name'  => 'id',
						'value' => $payment->get_id()
				) );

				eaccounting_hidden_input( array(
						'name'  => 'action',
						'value' => 'eaccounting_edit_payment'
				) );

				?>
			</div>
			<?php
			wp_nonce_field( 'ea_edit_payment' );
			submit_button( __( 'Submit', 'wp-ever-accounting' ), 'primary', 'submit' );
			?>

		</form>
	</div>
</div>
