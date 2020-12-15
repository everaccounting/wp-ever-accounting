<?php
/**
 * Add Contact Modal.
 *
 * @since       1.0.2
 * @subpackage  Admin/Js Templates
 * @package     EverAccounting
 */
defined( 'ABSPATH' ) || exit();
?>
<script type="text/template" id="ea-modal-add-customer" data-title="<?php esc_html_e( 'Add Customer', 'wp-ever-accounting' ); ?>">
	<form action="" method="post" >
		<?php
		eaccounting_text_input(
				array(
						'label'    => __( 'Name', 'wp-ever-accounting' ),
						'name'     => 'name',
						'value'    => '',
						'required' => true,
				)
		);
		eaccounting_currency_dropdown(
				array(
						'label'       => __( 'Currency', 'wp-ever-accounting' ),
						'name'        => 'currency_code',
						'value'       => '',
						'placeholder' => __( 'Select Currency', 'wp-ever-accounting' ),
						'ajax'        => true,
						'type'        => 'currency',
				)
		);
		eaccounting_text_input(
				array(
						'label'    => __( 'Email', 'wp-ever-accounting' ),
						'name'     => 'email',
						'type'     => 'email',
						'value'    => '',
						'required' => false,
				)
		);
		eaccounting_text_input(
				array(
						'label'    => __( 'Phone', 'wp-ever-accounting' ),
						'name'     => 'phone',
						'value'    => '',
						'required' => false,
				)
		);
		eaccounting_textarea(
				array(
						'wrapper_class' => 'ea-col-12',
						'label'         => __( 'Address', 'wp-ever-accounting' ),
						'name'          => 'address',
						'value'         => '',
						'required'      => false,
				)
		);
		eaccounting_hidden_input(
				array(
						'name'  => 'action',
						'value' => 'eaccounting_edit_customer',
				)
		);
		wp_nonce_field( 'ea_edit_customer' );
		?>
	</form>
</script>
