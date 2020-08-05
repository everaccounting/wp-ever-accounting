<?php
/**
 * Admin Account Edit Page
 *
 * @package     EverAccounting
 * @subpackage  Admin/Banking/Accounts
 * @since       1.0.2
 */
defined( 'ABSPATH' ) || exit();
$currency_id = isset( $_REQUEST['currency_id'] ) ? absint( $_REQUEST['currency_id'] ) : null;
$currency    = new EAccounting_Currency( $currency_id );

$currencies = eaccounting_get_global_currencies();
$options    = array();
foreach ( $currencies as $code => $props ) {
	$options[ $code ] = sprintf( '%s (%s)', $props['name'], $props['symbol'] );
}
?>
<div class="ea-form-card">
	<div class="ea-card ea-form-card__header is-compact">
		<h3 class="ea-form-card__header-title"><?php echo $currency->exists() ? __( 'Update Currency', 'wp-ever-accounting' ) : __( 'Add Currency', 'wp-ever-accounting' ); ?></h3>
		<?php echo sprintf( '<a href="%s" class="button button-secondary"><span class="dashicons dashicons-arrow-left-alt"></span> %s</a>',
				eaccounting_admin_url( array( 'tab' => 'currencies', 'page' => 'ea-banking' ) ),
				__( 'Back', 'wp-ever-accounting' )
		); ?>
	</div>

	<div class="ea-card">
		<form id="ea-currency-form" method="post">
			<div class="ea-row">
				<?php
				eaccounting_text_input( array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Currency Name', 'wp-ever-accounting' ),
						'name'          => 'name',
						'value'         => $currency->get_name( 'edit' ),
						'required'      => true,
				) );
				eaccounting_select( array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Currency Code', 'wp-ever-accounting' ),
						'name'          => 'code',
						'value'         => $currency->get_code( 'edit' ),
						'options'       => [ '' => __( 'Select' ) ] + $options,
						'required'      => true,
				) );
				eaccounting_text_input( array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Currency Rate', 'wp-ever-accounting' ),
						'name'          => 'rate',
						'value'         => $currency->get_rate( 'edit' ),
						'required'      => true,
				) );
				eaccounting_text_input( array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Precision', 'wp-ever-accounting' ),
						'name'          => 'precision',
						'type'          => 'number',
						'value'         => $currency->get_precision( 'edit' ),
						'required'      => true,
				) );
				eaccounting_text_input( array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Symbol', 'wp-ever-accounting' ),
						'name'          => 'symbol',
						'value'         => $currency->get_symbol( 'edit' ),
						'required'      => true,
				) );
				eaccounting_select( array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Symbol Position', 'wp-ever-accounting' ),
						'name'          => 'position',
						'value'         => $currency->get_position( 'edit' ),
						'options'       => array(
								'before' => __( 'Before', 'wp-ever-accounting' ),
								'after'  => __( 'After', 'wp-ever-accounting' ),
						),
						'required'      => true,
				) );
				eaccounting_text_input( array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Decimal Separator', 'wp-ever-accounting' ),
						'name'          => 'decimal_separator',
						'value'         => $currency->get_decimal_separator( 'edit' ),
						'required'      => true,
				) );
				eaccounting_text_input( array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Thousands Separator', 'wp-ever-accounting' ),
						'name'          => 'thousand_separator',
						'value'         => $currency->get_thousand_separator( 'edit' ),
						'required'      => true,
				) );
				eaccounting_hidden_input( array(
						'name'  => 'id',
						'value' => $currency->get_id()
				) );
				eaccounting_hidden_input( array(
						'name'  => 'action',
						'value' => 'eaccounting_edit_currency'
				) );
				?>
			</div>

			<div class="ea-form-submit">
				<?php wp_nonce_field( 'edit_currency' ); ?>
				<?php submit_button(); ?>
			</div>
		</form>
	</div>

</div>
