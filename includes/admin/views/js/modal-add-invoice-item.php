<?php
/**
 * Add Account Modal.
 *
 * @since       1.0.2
 * @subpackage  Admin/Js Templates
 * @package     EverAccounting
 */
defined( 'ABSPATH' ) || exit();
?>
<script type="text/template" id="modal-add-invoice-item">
	<form action="" method="post">
		<div class="ea-row">
			<?php
			eaccounting_item_dropdown(
				array(
					'wrapper_class' => 'ea-col-9',
					'label'         => __( 'Select Item', 'wp-ever-accounting' ),
					'name'          => 'item_id',
					'value'         => '',
					'placeholder'   => __( 'Select Item', 'wp-ever-accounting' ),
					'required'      => true,
					'ajax'          => true,
					'creatable'     => true,
				)
			);

			eaccounting_text_input(
				array(
					'wrapper_class' => 'ea-col-3',
					'label'         => __( 'Quantity', 'wp-ever-accounting' ),
					'name'          => 'quantity',
					'value'         => '1',
					'default'       => '1',
					'required'      => true,
				)
			);
			?>
		</div>
		<?php
		submit_button( __( 'Submit', 'wp-ever-accounting' ), 'primary', 'submit' );
		?>

	</form>
</script>
