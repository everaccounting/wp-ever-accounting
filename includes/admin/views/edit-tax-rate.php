<?php
defined( 'ABSPATH' ) || exit();
$base_url    = admin_url( 'admin.php?page=eaccounting-misc&tab=tax_rates' );
$tax_rate_id = empty( $_GET['tax_rate'] ) ? false : absint( $_GET['tax_rate'] );
$tax_rate    = new StdClass();
if ( $tax_rate_id ) {
	$tax_rate = eaccounting_get_tax_rate( $tax_rate_id );
}
$title = ! empty( $tax_rate->id ) ? __( 'Update Tax Rate' ) : __( 'Add Tax Rate', 'wp-eaccounting' );
echo sprintf( '<h1 class="wp-heading-inline">%s</h1>', $title );
echo sprintf( '<a href="%s" class="page-title-action">%s</a>', $base_url, __( 'All Tax Rates', 'wp-ever-accounting' ) ); ?>

<div class="ea-card">
	<form action="<?php echo add_query_arg( [ 'eaccounting-action' => 'add_tax_rate' ], $base_url ); ?>" method="post">
		<?php do_action( 'eaccounting_add_tax_rate_form_top' ); ?>
		<div class="ea-row">
			<?php
			echo EAccounting_Form::input_control( array(
				'label'         => __( 'Name', 'wp-eaccounting' ),
				'name'          => 'name',
				'value'         => isset( $tax_rate->name ) ? $tax_rate->name : '',
				'icon'          => 'fa fa-id-card-o',
				'required'      => true,
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::input_control( array(
				'label'         => __( 'Rate', 'wp-eaccounting' ),
				'name'          => 'rate',
				'value'         => isset( $tax_rate->rate ) ? $tax_rate->rate : '',
				'icon'          => 'fa fa-percent',
				'required'      => true,
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::select_control( array(
				'label'         => __( 'Type', 'wp-eaccounting' ),
				'name'          => 'type',
				'options'       => eaccounting_get_tax_rate_types(),
				'selected'      => isset( $tax_rate->type ) ? $tax_rate->type : '',
				'icon'          => 'fa fa-bars',
				'required'      => true,
				'select2'       => true,
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::status_control( array(
				'wrapper_class' => 'ea-col-6',
				'value'      => isset( $tax_rate->status ) ? $tax_rate->status : 'active',
			) );

			?>
		</div>
		<?php do_action( 'eaccounting_add_tax_rate_form_bottom' ); ?>
		<p>
			<input type="hidden" name="eaccounting-action" value="edit_tax_rate"/>
			<input type="hidden" name="id" value="<?php echo $tax_rate_id; ?>"/>
			<?php wp_nonce_field( 'eaccounting_tax_rate_nonce' ); ?>
			<input class="button button-primary" type="submit" value="<?php _e( 'Submit', 'wp-ever-accounting' ); ?>">
		</p>
	</form>
</div>
