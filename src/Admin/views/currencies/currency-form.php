<?php
/**
 * View: Currency Form
 *
 * @since 1.0.0
 * @package EverAccounting
 * @var string $code Currency object.
 */

defined( 'ABSPATH' ) || exit;

$addable_currencies = array();
$global_currencies  = eac_get_global_currencies();
$currencies         = eac_get_currencies();
foreach ( $global_currencies as $code => $data ) {
	if ( ! isset( $currencies[ $code ] ) ) {
		$addable_currencies[ $code ] = $data;
	}
}
$default = array(
	'code'         => '',
	'name'         => '',
	'symbol'       => '',
	'rate'         => '1',
	'thousand_sep' => ',',
	'decimal_sep'  => '.',
	'precision'    => '2',
	'position'     => 'before',
);
var_dump( $code );
$currency = ! empty( $code ) && ! empty( $global_currencies[ $code ] ) ? $global_currencies[ $code ] : array();
$currency = wp_parse_args( $currency, $default );
?>

<form id="eac-currency-form" class="eac-form" method="post">
	<div class="eac-card">
		<div class="eac-card__header">
			<h2 class="eac-card__title"><?php esc_html_e( 'Currency Details', 'wp-ever-accounting' ); ?></h2>
		</div>
		<div class="eac-card__body">
			<div class="eac-columns">
				<?php
				eac_input_field(
					array(
						'type'     => 'select',
						'id'       => 'code',
						'label'    => __( 'Code', 'wp-ever-accounting' ),
						'value'    => $currency['code'],
						'options'  => wp_list_pluck( $addable_currencies, 'code', 'code' ),
						'class'    => 'eac-col-6',
						'select2'  => true,
						'required' => true,
					)
				);
				eac_input_field(
					array(
						'id'       => 'name',
						'label'    => __( 'Name', 'wp-ever-accounting' ),
						'type'     => 'text',
						'value'    => $currency['name'],
						'class'    => 'eac-col-6',
						'required' => true,
					)
				);
				eac_input_field(
					array(
						'id'       => 'symbol',
						'label'    => __( 'Symbol', 'wp-ever-accounting' ),
						'type'     => 'text',
						'value'    => $currency['symbol'],
						'class'    => 'eac-col-6',
						'required' => true,
					)
				);
				eac_input_field(
					array(
						'id'       => 'rate',
						'label'    => __( 'Rate', 'wp-ever-accounting' ),
						'type'     => 'number',
						'value'    => $currency['rate'],
						'class'    => 'eac-col-6',
						'required' => true,
						// translators: %s is the base currency.
						'prefix'   => sprintf( __( '1 %s =', 'wp-ever-accounting' ), eac_get_base_currency() ),
						'suffix'   => $currency['symbol'],
					)
				);
				?>
			</div>
		</div>
		<div class="eac-card__separator"></div>
		<div class="eac-card__section">
			<h2 class="eac-card__title"><?php esc_html_e( 'More Details', 'wp-ever-accounting' ); ?></h2>
		</div>
		<div class="eac-card__body">
			<div class="eac-columns">
				<?php
				eac_input_field(
					array(
						'id'       => 'thousand_sep',
						'label'    => __( 'Thousand Separator', 'wp-ever-accounting' ),
						'type'     => 'text',
						'value'    => $currency['thousand_sep'],
						'class'    => 'eac-col-6',
						'required' => true,
					)
				);

				eac_input_field(
					array(
						'id'       => 'decimal_sep',
						'label'    => __( 'Decimal Separator', 'wp-ever-accounting' ),
						'type'     => 'text',
						'value'    => $currency['decimal_sep'],
						'class'    => 'eac-col-6',
						'required' => true,
					)
				);

				eac_input_field(
					array(
						'id'       => 'precision',
						'label'    => __( 'Number of Decimals', 'wp-ever-accounting' ),
						'type'     => 'number',
						'value'    => $currency['precision'],
						'class'    => 'eac-col-6',
						'required' => true,
					)
				);

				eac_input_field(
					array(
						'id'       => 'position',
						'label'    => __( 'Symbol Position', 'wp-ever-accounting' ),
						'type'     => 'select',
						'value'    => $currency['position'],
						'class'    => 'eac-col-6',
						'required' => true,
						'options'  => array(
							'before' => __( 'Before amount', 'wp-ever-accounting' ),
							'after'  => __( 'After amount', 'wp-ever-accounting' ),
						),
					)
				);
				?>
			</div>
		</div>
	</div>

	<?php wp_nonce_field( 'eac_edit_currency' ); ?>
	<input type="hidden" name="action" value="eac_edit_currency">
</form>

