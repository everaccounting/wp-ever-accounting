<?php
/**
 * Admin Currency Edit Page.
 *
 * @package     EverAccounting
 * @subpackage  Admin/Settings/Currencies
 * @since       1.0.2
 */

defined( 'ABSPATH' ) || exit();
$currency_id = isset( $_REQUEST['currency_id'] ) ? absint( $_REQUEST['currency_id'] ) : null;
try {
	$currency = new \EverAccounting\Currency( $currency_id );
} catch ( Exception $e ) {
	wp_die( $e->getMessage() );
}
$back_url = remove_query_arg( array( 'action', 'id' ) );

$currencies = eaccounting_get_global_currencies();
$options    = array();
foreach ( $currencies as $code => $props ) {
	$options[ $code ] = sprintf( '%s (%s)', $props['code'], $props['symbol'] );
}
?>

<div class="notice notice-warning notice-large">
	<?php echo sprintf( '<p><strong>%s:</strong> %s',
			__( 'Note', 'wp-ever-accounting' ),
			__( 'Default currency rate should be always 1 & additional currency rates should be equivalent of default currency.
		e.g. If USD is your default currency then USD rate is 1 & GBP rate will be 0.77', 'wp-ever-accounting' )
	); ?>
</div>

<div class="ea-form-card">
	<div class="ea-card ea-form-card__header is-compact">
		<h3 class="ea-form-card__header-title"><?php echo $currency->exists() ? __( 'Update Currency', 'wp-ever-accounting' ) : __( 'Add Currency', 'wp-ever-accounting' ); ?></h3>
		<a href="<?php echo $back_url; ?>" class="button button-secondary"><span class="dashicons dashicons-arrow-left-alt"></span><?php _e( 'Back', 'wp-ever-accounting' ); ?></a>
	</div>

	<div class="ea-card">
		<form id="ea-currency-form" class="ea-ajax-form" method="post">
			<div class="ea-row">
				<?php
				eaccounting_text_input( array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Name', 'wp-ever-accounting' ),
						'name'          => 'name',
						'placeholder'   => __( 'Enter Name', 'wp-ever-accounting' ),
						'value'         => $currency->get_name(),
						'required'      => true,
				) );

				eaccounting_select2( array(
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
				eaccounting_select2( array(
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
			<?php
			wp_nonce_field( 'ea_edit_currency' );
			submit_button( __( 'Submit', 'wp-ever-accounting' ), 'primary', 'submit' );
			?>

		</form>
	</div>
</div>