<?php
/**
 * Admin Taxes Page.
 *
 * @since       1.1.0
 * @subpackage  Admin/Misc/Taxes
 * @package     EverAccounting
 */

defined( 'ABSPATH' ) || exit();

$tax_id = isset( $_REQUEST['tax_id'] ) ? absint( $_REQUEST['tax_id'] ) : null;
try {
	$tax = new \EverAccounting\Models\Tax( $tax_id );
} catch ( Exception $e ) {
	wp_die( $e->getMessage() );
}
$back_url = remove_query_arg( array( 'action', 'tax_id' ) );
?>

<div class="ea-form-card">
	<div class="ea-card ea-form-card__header is-compact">
		<h3 class="ea-form-card__header-title"><?php echo $tax->exists() ? __( 'Update Tax', 'wp-ever-accounting' ) : __( 'Add Tax', 'wp-ever-accounting' ); ?></h3>
		<a href="<?php echo $back_url; ?>" class="button button-secondary"><span class="dashicons dashicons-arrow-left-alt"></span><?php _e( 'Back', 'wp-ever-accounting' ); ?></a>
	</div>

	<div class="ea-card">
		<form id="ea-tax-form" class="ea-ajax-form" method="post">
			<div class="ea-row">
				<?php
				eaccounting_text_input(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Name', 'wp-ever-accounting' ),
						'name'          => 'name',
						'placeholder'   => __( 'Enter Name', 'wp-ever-accounting' ),
						'value'         => $tax->get_name(),
						'required'      => true,
					)
				);
				eaccounting_text_input(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Rate', 'wp-ever-accounting' ),
						'name'          => 'rate',
						'placeholder'   => __( 'Enter Rate', 'wp-ever-accounting' ),
						'value'         => $tax->get_rate(),
						'required'      => true,
					)
				);
				eaccounting_select2(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Type', 'wp-ever-accounting' ),
						'name'          => 'type',
						'value'         => $tax->get_type(),
						'options'       => eaccounting_get_tax_types(),
						'placeholder'   => __( 'Select Type', 'wp-ever-accounting' ),
						'required'      => true,
					)
				);
				eaccounting_hidden_input(
					array(
						'name'  => 'id',
						'value' => $tax->get_id(),
					)
				);

				eaccounting_hidden_input(
					array(
						'name'  => 'action',
						'value' => 'eaccounting_edit_tax',
					)
				);
				?>
			</div>
			<?php
			wp_nonce_field( 'ea_edit_tax' );
			submit_button( __( 'Submit', 'wp-ever-accounting' ), 'primary', 'submit' );
			?>
		</form>
	</div>
