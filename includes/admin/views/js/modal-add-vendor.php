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
<script type="text/template" id="ea-modal-add-vendor">
	<form id="ea-modal-contact-form" class="ea-ajax-form" action="" method="post">
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
				'name'  => 'type',
				'value' => 'vendor',
			)
		);
		eaccounting_hidden_input(
			array(
				'name'  => 'action',
				'value' => 'eaccounting_edit_contact',
			)
		);
		wp_nonce_field( 'ea_edit_contact' );
		?>
		<?php
		wp_nonce_field( 'ea_edit_invoice', 'nonce' );
		submit_button( __( 'Submit', 'wp-ever-accounting' ), 'primary', 'submit' );
		?>
		Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aliquam aliquid commodi earum excepturi ipsam itaque modi nesciunt, nostrum nulla numquam repudiandae saepe sequi voluptas! Consectetur corporis dolor enim eos fugit impedit laudantium maxime molestiae neque nesciunt omnis pariatur possimus praesentium provident, qui quis rerum saepe, sit unde velit. Aliquam cupiditate ex exercitationem fugit harum labore molestiae mollitia, qui? Accusantium adipisci consectetur deleniti dolor doloribus eius enim error facilis harum id iste, iusto molestiae mollitia necessitatibus nihil perspiciatis quaerat quas reprehenderit rerum suscipit ullam veniam voluptatum. At deleniti dicta distinctio, dolore earum eos harum inventore labore, quaerat recusandae sed sit velit.
	</form>
</script>
