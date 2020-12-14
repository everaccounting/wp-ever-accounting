<?php
/**
 * Add Account Modal.
 *
 * @package     EverAccounting
 * @subpackage  Admin/Js Templates
 * @since       1.0.2
 */
defined( 'ABSPATH' ) || exit();
?>
<script type="text/template" id="modal-add-invoice-item">
	<form action="" method="post">
		<?php
		eaccounting_item_dropdown(
			array(
				'label'       => __( 'Select Item', 'wp-ever-accounting' ),
				'name'        => 'item_id',
				'value'       => '',
				'placeholder' => __( 'Select Item', 'wp-ever-accounting' ),
				'ajax'        => true,
			)
		);

		eaccounting_text_input(
			array(
				'label'    => __( 'Quantity', 'wp-ever-accounting' ),
				'name'     => 'opening_balance',
				'value'    => '',
				'default'  => '1',
				'required' => true,
			)
		);
		submit_button( __( 'Submit', 'wp-ever-accounting' ), 'primary', 'submit' );
		?>
	</form>
</script>
