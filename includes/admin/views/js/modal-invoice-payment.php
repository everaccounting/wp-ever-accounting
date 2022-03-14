<?php
/**
 * Invoice payment.
 *
 * @package     Ever_Accounting
 * @subpackage  Admin/Js Templates
 * @since       1.0.2
 * @var \Ever_Accounting\Invoice $invoice;
 */

use Ever_Accounting\Helpers\Form;
use Ever_Accounting\Helpers\Price;

defined( 'ABSPATH' ) || exit();
?>
<script type="text/template" id="ea-modal-add-invoice-payment" data-title="<?php esc_html_e( 'Add Payment', 'wp-ever-accounting' ); ?>">
	<form action="" method="post">
		<?php
		Form::text_input(
			array(
				'label'       => __( 'Date', 'wp-ever-accounting' ),
				'name'        => 'date',
				'placeholder' => __( 'Enter Date', 'wp-ever-accounting' ),
				'data_type'   => 'date',
				'value'       => date_i18n( 'Y-m-d' ),
				'required'    => true,
			)
		);
		Form::text_input(
			array(
				'label'       => __( 'Amount', 'wp-ever-accounting' ),
				'name'        => 'amount',
				'value'       => $invoice->get_total_due(),
				'data_type'   => 'price',
				'required'    => true,
				'placeholder' => __( 'Enter Amount', 'wp-ever-accounting' ),
				/* translators: %s amount */
				'desc'        => sprintf( __( 'Total amount due:%s', 'wp-ever-accounting' ), Price::price( $invoice->get_total_due(), $invoice->get_currency_code() ) ),
			)
		);
		Form::account_dropdown(
			array(
				'label'       => __( 'Account', 'wp-ever-accounting' ),
				'name'        => 'account_id',
				'creatable'   => false,
				'placeholder' => __( 'Select Account', 'wp-ever-accounting' ),
				'required'    => true,
			)
		);
		Form::payment_method_dropdown(
			array(
				'label'    => __( 'Payment Method', 'wp-ever-accounting' ),
				'name'     => 'payment_method',
				'required' => true,
				'value'    => '',
			)
		);
		Form::textarea(
			array(
				'label'       => __( 'Description', 'wp-ever-accounting' ),
				'name'        => 'description',
				'value'       => '',
				'required'    => false,
				'placeholder' => __( 'Enter description', 'wp-ever-accounting' ),
			)
		);
		Form::hidden_input(
			array(
				'name'  => 'invoice_id',
				'value' => $invoice->get_id(),
			)
		);

		Form::hidden_input(
			array(
				'name'  => 'action',
				'value' => 'ever_accounting_add_invoice_payment',
			)
		);
		wp_nonce_field( 'ea_add_invoice_payment' );
		?>
	</form>
</script>

