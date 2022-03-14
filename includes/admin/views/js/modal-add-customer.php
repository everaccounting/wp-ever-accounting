<?php
/**
 * Add Contact Modal.
 *
 * @since       1.0.2
 * @subpackage  Admin/Js Templates
 * @package     Ever_Accounting
 */

use Ever_Accounting\Helpers\Form;

defined( 'ABSPATH' ) || exit();
?>
<script type="text/template" id="ea-modal-add-customer" data-title="<?php esc_html_e( 'Add Customer', 'wp-ever-accounting' ); ?>">
	<form action="" method="post" >
		<?php
		Form::text_input(
				array(
						'label'    => __( 'Name', 'wp-ever-accounting' ),
						'name'     => 'name',
						'value'    => '',
						'required' => true,
				)
		);
		Form::currency_dropdown(
				array(
						'label'       => __( 'Currency', 'wp-ever-accounting' ),
						'name'        => 'currency_code',
						'value'       => '',
						'placeholder' => __( 'Select Currency', 'wp-ever-accounting' ),
						'ajax'        => true,
						'type'        => 'currency',
						'creatable'   => false,
						'required' => true,
				)
		);
		Form::text_input(
				array(
						'label'    => __( 'Company', 'wp-ever-accounting' ),
						'name'     => 'company',
						'value'    => '',
						'required' => false,
				)
		);
		Form::text_input(
				array(
						'label'    => __( 'Email', 'wp-ever-accounting' ),
						'name'     => 'email',
						'type'     => 'email',
						'value'    => '',
						'required' => false,
				)
		);
		Form::text_input(
				array(
						'label'    => __( 'Phone', 'wp-ever-accounting' ),
						'name'     => 'phone',
						'value'    => '',
						'required' => false,
				)
		);
		Form::hidden_input(
				array(
						'name'  => 'action',
						'value' => 'ever_accounting_edit_customer',
				)
		);
		wp_nonce_field( 'ea_edit_customer' );
		?>
	</form>
</script>
