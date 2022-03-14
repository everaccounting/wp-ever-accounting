<?php
/**
 * Add Category Modal.
 *
 * @since       1.1.0
 * @subpackage  Admin/Js Templates
 * @package     Ever_Accounting
 */

use Ever_Accounting\Helpers\Form;
use Ever_Accounting\Helpers\Misc;

defined( 'ABSPATH' ) || exit();
?>
<script type="text/template" id="ea-modal-add-expense-category" data-title="<?php esc_html_e( 'Add Expense Category', 'wp-ever-accounting' ); ?>">
	<form action="" method="post">
		<div class="ea-row">
			<?php
			Form::text_input(
				array(
					'wrapper_class' => 'ea-col-12',
					'label'         => __( 'Category Name', 'wp-ever-accounting' ),
					'name'          => 'name',
					'value'         => '',
					'required'      => true,
				)
			);
			Form::text_input(
				array(
					'wrapper_class' => 'ea-col-12',
					'label'         => __( 'Color', 'wp-ever-accounting' ),
					'name'          => 'color',
					'data_type'     => 'color',
					'value'         => Misc::get_random_color(),
					'required'      => true,
				)
			);
			Form::hidden_input(
				array(
					'name'  => 'type',
					'value' => 'expense',
				)
			);
			Form::hidden_input(
				array(
					'name'  => 'action',
					'value' => 'ever_accounting_edit_category',
				)
			);
			wp_nonce_field( 'ea_edit_category' );
			?>
		</div>
	</form>
</script>
