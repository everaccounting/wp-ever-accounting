<?php
/**
 * Admin Currency Edit Page.
 *
 * @package     Ever_Accounting
 * @subpackage  Admin/Settings/Currencies
 * @since       1.0.2
 */

use \Ever_Accounting\Helpers\Form;
use \Ever_Accounting\Helpers\Formatting;

defined( 'ABSPATH' ) || exit();

$currency_id = isset( $_REQUEST['currency_id'] ) ? Formatting::clean( $_REQUEST['currency_id'] ) : null;
try {
	$currency = new \Ever_Accounting\Currency( $currency_id );
} catch ( Exception $e ) {
	wp_die( $e->getMessage() );
}
$currencies = \Ever_Accounting\Currencies::get_codes();
$options    = array();
foreach ( $currencies as $code => $props ) {
	$options[ $code ] = sprintf( '%s (%s)', $props['code'], $props['symbol'] );
}
?>
<div class="ea-title-section">
	<div>
		<h1 class="wp-heading-inline"><?php esc_html_e( 'Currencies', 'wp-ever-accounting' ); ?></h1>
		<?php if ( $currency->exists() ) : ?>
			<a href="<?php echo esc_url( add_query_arg( array( 'tab' => 'currencies', 'page' => 'ea-settings', 'action' => 'add' ), admin_url( 'admin.php' ) ) );//phpcs:ignore ?>" class="page-title-action">
				<?php esc_html_e( 'Add New', 'wp-ever-accounting' ); ?>
			</a>
		<?php else : ?>
			<a href="<?php echo remove_query_arg( array( 'action', 'id' ) ); ?>" class="page-title-action"><?php esc_html_e( 'View All', 'wp-ever-accounting' ); ?></a>
		<?php endif; ?>

	</div>
</div>
<hr class="wp-header-end">

<div class="notice notice-warning notice-large">
	<?php
	echo sprintf(
		'<p><strong>%s:</strong> %s',
		__( 'Note', 'wp-ever-accounting' ),
		__(
			'Default currency rate should be always 1 & additional currency rates should be equivalent of default currency.
		e.g. If USD is your default currency then USD rate is 1 & GBP rate will be 0.77',
			'wp-ever-accounting'
		)
	);
	?>
</div>
<form id="ea-currency-form" method="post">
<div class="ea-card">
	<div class="ea-card__header">
		<h3 class="ea-card__title"><?php echo $currency->exists() ? __( 'Update Currency', 'wp-ever-accounting' ) : __( 'Add Currency', 'wp-ever-accounting' ); ?></h3>
	</div>

	<div class="ea-card__inside">

			<div class="ea-row">
				<?php
				Form::select2(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Currency Code', 'wp-ever-accounting' ),
						'name'          => 'code',
						'value'         => $currency->get_code(),
						'options'       => array( '' => __( 'Select', 'wp-ever-accounting' ) ) + $options,
						'required'      => true,
					)
				);
				Form::text_input(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Name', 'wp-ever-accounting' ),
						'name'          => 'name',
						'placeholder'   => __( 'Enter Name', 'wp-ever-accounting' ),
						'value'         => $currency->get_name(),
						'required'      => true,
					)
				);
				Form::text_input(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Currency Rate', 'wp-ever-accounting' ),
						'name'          => 'rate',
						'tooltip'       => __( 'For better precision use full conversion rate. Like 1 USD = 1.2635835 CAD', 'wp-ever-accounting'),
						'value'         => $currency->get_rate(),
						'required'      => true,
					)
				);
				Form::text_input(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Precision', 'wp-ever-accounting' ),
						'name'          => 'precision',
						'type'          => 'number',
						'value'         => $currency->get_precision(),
						'required'      => true,
					)
				);
				Form::text_input(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Symbol', 'wp-ever-accounting' ),
						'name'          => 'symbol',
						'value'         => $currency->get_symbol(),
						'required'      => true,
					)
				);
				Form::select2(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Symbol Position', 'wp-ever-accounting' ),
						'name'          => 'position',
						'value'         => $currency->get_position(),
						'options'       => array(
							'before' => __( 'Before', 'wp-ever-accounting' ),
							'after'  => __( 'After', 'wp-ever-accounting' ),
						),
						'required'      => true,
					)
				);
				Form::text_input(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Decimal Separator', 'wp-ever-accounting' ),
						'name'          => 'decimal_separator',
						'value'         => $currency->get_decimal_separator(),
						'required'      => true,
					)
				);
				Form::text_input(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Thousands Separator', 'wp-ever-accounting' ),
						'name'          => 'thousand_separator',
						'value'         => $currency->get_thousand_separator(),
						'required'      => true,
					)
				);
				Form::hidden_input(
					array(
						'name'  => 'id',
						'value' => $currency->get_id(),
					)
				);

				Form::hidden_input(
					array(
						'name'  => 'action',
						'value' => 'ever_accounting_edit_currency',
					)
				);

				?>
			</div>



	</div>
	<div class="ea-card__footer">
		<?php
		wp_nonce_field( 'ea_edit_currency' );
		submit_button( __( 'Submit', 'wp-ever-accounting' ), 'primary', 'submit' );
		?>
	</div>
</div>
</form>
