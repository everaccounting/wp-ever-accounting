<?php
/**
 * Admin Currency Form.
 * Page: Misc
 * Tab: Currencies
 *
 * @since 1.0.0
 * @package EverAccounting
 * @var $currency array Currency object.
 */

defined( 'ABSPATH' ) || exit;
?>
<form id="eac-currency-form" method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
	<div class="eac-poststuff">
		<div class="column-1">
			<div class="eac-card">
				<div class="eac-card__header">
					<h2 class="eac-card__title"><?php esc_html_e( 'Currency Details', 'wp-ever-accounting' ); ?></h2>
				</div>
				<div class="eac-card__body grid--fields">

					<?php
					eac_form_field(
						array(
							'id'       => 'code',
							'label'    => __( 'Code', 'wp-ever-accounting' ),
							'value'    => $currency['code'],
							'class'    => 'eac-col-6',
							'required' => true,
							'readonly' => true,
						)
					);
					eac_form_field(
						array(
							'id'       => 'symbol',
							'label'    => __( 'Symbol', 'wp-ever-accounting' ),
							'type'     => 'text',
							'value'    => $currency['symbol'],
							'required' => true,
							'readonly' => true,
						)
					);
					eac_form_field(
						array(
							'id'        => 'rate',
							'label'     => __( 'Exchange Rate', 'wp-ever-accounting' ),
							'data_type' => 'decimal',
							'value'     => $currency['rate'],
							'required'  => true,
							'class'     => 'eac_decimal_input',
							// translators: %s is the base currency.
							'prefix'    => sprintf( __( '1 %s =', 'wp-ever-accounting' ), eac_base_currency() ),
							'suffix'    => $currency['code'],
						)
					);
					eac_form_field(
						array(
							'id'       => 'thousand_separator',
							'label'    => __( 'Thousand Separator', 'wp-ever-accounting' ),
							'type'     => 'text',
							'value'    => $currency['thousand_separator'],
							'required' => true,
						)
					);

					eac_form_field(
						array(
							'id'       => 'decimal_separator',
							'label'    => __( 'Decimal Separator', 'wp-ever-accounting' ),
							'type'     => 'text',
							'value'    => $currency['decimal_separator'],
							'required' => true,
						)
					);

					eac_form_field(
						array(
							'id'       => 'precision',
							'label'    => __( 'Number of Decimals', 'wp-ever-accounting' ),
							'type'     => 'number',
							'value'    => $currency['precision'],
							'required' => true,
						)
					);

					eac_form_field(
						array(
							'id'       => 'position',
							'label'    => __( 'Symbol Position', 'wp-ever-accounting' ),
							'type'     => 'select',
							'value'    => $currency['position'],
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
		</div><!-- .column-1 -->

		<div class="column-2">
			<div class="eac-card">

			</div>
		</div><!-- .column-2 -->
	</div><!-- .eac-poststuff -->
</form>