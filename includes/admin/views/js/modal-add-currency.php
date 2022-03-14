<?php
/**
 * Add Currency Modal.
 *
 * @since       1.0.2
 * @subpackage  Admin/Js Templates
 * @package     Ever_Accounting
 */

use Ever_Accounting\Helpers\Form;

defined( 'ABSPATH' ) || exit();
$currencies = \Ever_Accounting\Currencies::get_codes();
$options    = array();
foreach ( $currencies as $code => $props ) {
	$options[ $code ] = sprintf( '%s (%s)', $props['code'], $props['symbol'] );
}
ksort( $options, SORT_STRING );
$options = array_merge( array( '' => __( 'Select Currency', 'wp-ever-accounting' ) ), $options );
?>
	<script type="text/template" id="ea-modal-add-currency" data-title="<?php esc_html_e( 'Add Currency', 'wp-ever-accounting' ); ?>">
		<form action="" method="post">
			<div class="ea-row">
				<?php
				Form::select(
					array(
						'wrapper_class' => 'ea-col-12',
						'label'         => __( 'Currency Code', 'wp-ever-accounting' ),
						'name'          => 'code',
						'class'         => 'ea-select2',
						'value'         => '',
						'options'       => $options,
						'required'      => true,
					)
				);
				Form::text_input(
					array(
						'wrapper_class' => 'ea-col-12',
						'label'         => __( 'Currency Rate', 'wp-ever-accounting' ),
						'name'          => 'rate',
						'value'         => '',
						'required'      => true,
					)
				);
				Form::hidden_input(
					array(
						'name'  => 'action',
						'value' => 'ever_accounting_edit_currency',
					)
				);
				wp_nonce_field( 'ea_edit_currency' );
				?>
			</div>
		</form>
	</script>
<?php
