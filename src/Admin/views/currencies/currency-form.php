<?php
/**
 * View: Currency Form
 *
 * @since 1.0.0
 * @package EverAccounting
 * @var Currency $currency Currency data.
 */

use EverAccounting\Models\Currency;

defined( 'ABSPATH' ) || exit;
?>

<form id="eac-currency-form" class="eac-ajax-form" method="post">
	<div class="eac-card">
		<div class="eac-card__header">
			<h2 class="eac-card__title"><?php esc_html_e( 'Currency Details', 'wp-ever-accounting' ); ?></h2>
		</div>
		<div class="eac-card__body">
			<div class="eac-columns">
				<?php
				eac_form_field(
					array(
						'id'       => 'code',
						'label'    => __( 'Code', 'wp-ever-accounting' ),
						'value'    => $currency->get_code(),
						'class'    => 'eac-col-6',
						'required' => true,
						'readonly' => true,
					)
				);
				eac_form_field(
					array(
						'id'       => 'name',
						'label'    => __( 'Name', 'wp-ever-accounting' ),
						'type'     => 'text',
						'value'    => $currency->get_name(),
						'class'    => 'eac-col-6',
						'required' => true,
					)
				);
				eac_form_field(
					array(
						'id'        => 'exchange_rate',
						'label'     => __( 'Exchange Rate', 'wp-ever-accounting' ),
						'data_type' => 'decimal',
						'value'     => $currency->is_base_currency() ? 1 : $currency->get_exchange_rate(),
						'class'     => 'eac-col-6',
						'readonly'  => $currency->is_base_currency() ? 'readonly' : false,
						'required'  => true,
						// translators: %s is the base currency.
						'prefix'    => sprintf( __( '1 %s =', 'wp-ever-accounting' ), eac_get_base_currency() ),
					)
				);
				eac_form_field(
					array(
						'id'       => 'symbol',
						'label'    => __( 'Symbol', 'wp-ever-accounting' ),
						'type'     => 'text',
						'value'    => $currency->get_symbol(),
						'class'    => 'eac-col-6',
						'required' => true,
					)
				);
				?>
			</div>
		</div>
	</div>

	<div class="eac-card">
		<div class="eac-card__header">
			<h2 class="eac-card__title"><?php esc_html_e( 'More Details', 'wp-ever-accounting' ); ?></h2>
		</div>
		<div class="eac-card__body">
			<div class="eac-columns">
				<?php
				eac_form_field(
					array(
						'id'       => 'thousand_separator',
						'label'    => __( 'Thousand Separator', 'wp-ever-accounting' ),
						'type'     => 'text',
						'value'    => $currency->get_thousand_separator(),
						'class'    => 'eac-col-6',
						'required' => true,
					)
				);

				eac_form_field(
					array(
						'id'       => 'decimal_separator',
						'label'    => __( 'Decimal Separator', 'wp-ever-accounting' ),
						'type'     => 'text',
						'value'    => $currency->get_decimal_separator(),
						'class'    => 'eac-col-6',
						'required' => true,
					)
				);

				eac_form_field(
					array(
						'id'       => 'precision',
						'label'    => __( 'Number of Decimals', 'wp-ever-accounting' ),
						'type'     => 'number',
						'value'    => $currency->get_precision(),
						'class'    => 'eac-col-6',
						'required' => true,
					)
				);

				eac_form_field(
					array(
						'id'       => 'position',
						'label'    => __( 'Symbol Position', 'wp-ever-accounting' ),
						'type'     => 'select',
						'value'    => $currency->get_position(),
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
	<input type="hidden" name="id" value="<?php echo esc_attr( $currency->get_id() ); ?>">
	<input type="hidden" name="action" value="eac_edit_currency">
</form>

