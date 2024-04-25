<?php
/**
 * Currency view.
 *
 * @package EverAccounting\Admin\Views\Currencies
 * @version 1.0.0
 * @var $currency \EverAccounting\Models\Currency Currency object.
 */

defined( 'ABSPATH' ) || exit;
?>
<form id="eac-currency-form" method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
	<span data-wp-text="name"></span>
	<div class="bkit-poststuff">
		<div class="column-1">
			<div class="bkit-card">
				<div class="bkit-card__header">
					<h2 class="bkit-card__title"><?php esc_html_e( 'Currency Details', 'wp-ever-accounting' ); ?></h2>
				</div>
				<div class="bkit-card__body grid--fields">

					<?php
					eac_form_group(
						array(
							'id'       => 'code',
							'label'    => __( 'Code', 'wp-ever-accounting' ),
							'value'    => $currency->code,
							'class'    => 'eac-col-6',
							'required' => true,
							'readonly' => true,
						)
					);
					eac_form_group(
						array(
							'id'       => 'name',
							'label'    => __( 'Name', 'wp-ever-accounting' ),
							'type'     => 'text',
							'value'    => $currency->get_name(),
							'class'    => 'eac-col-6',
							'required' => true,
						)
					);
					eac_form_group(
						array(
							'id'        => 'exchange_rate',
							'label'     => __( 'Exchange Rate', 'wp-ever-accounting' ),
							'data_type' => 'decimal',
							'value'     => $currency->is_base_currency() ? 1 : $currency->exchange_rate,
							'readonly'  => $currency->is_base_currency() ? 'readonly' : false,
							'required'  => true,
							// translators: %s is the base currency.
							'prefix'    => sprintf( __( '1 %s =', 'wp-ever-accounting' ), eac_get_base_currency() ),
						)
					);
					eac_form_group(
						array(
							'id'       => 'symbol',
							'label'    => __( 'Symbol', 'wp-ever-accounting' ),
							'type'     => 'text',
							'value'    => $currency->symbol,
							'class'    => 'eac-col-6',
							'required' => true,
						)
					);
					eac_form_group(
						array(
							'id'       => 'thousand_separator',
							'label'    => __( 'Thousand Separator', 'wp-ever-accounting' ),
							'type'     => 'text',
							'value'    => $currency->thousand_separator,
							'class'    => 'eac-col-6',
							'required' => true,
						)
					);

					eac_form_group(
						array(
							'id'       => 'decimal_separator',
							'label'    => __( 'Decimal Separator', 'wp-ever-accounting' ),
							'type'     => 'text',
							'value'    => $currency->decimal_separator,
							'class'    => 'eac-col-6',
							'required' => true,
						)
					);

					eac_form_group(
						array(
							'id'       => 'precision',
							'label'    => __( 'Number of Decimals', 'wp-ever-accounting' ),
							'type'     => 'number',
							'value'    => $currency->precision,
							'class'    => 'eac-col-6',
							'required' => true,
						)
					);

					eac_form_group(
						array(
							'id'       => 'position',
							'label'    => __( 'Symbol Position', 'wp-ever-accounting' ),
							'type'     => 'select',
							'value'    => $currency->position,
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
		</div><!-- .column-1 -->

		<div class="column-2">
			<div class="bkit-card">
				<div class="bkit-card__header">
					<h2 class="bkit-card__title"><?php esc_html_e( 'Actions', 'wp-ever-accounting' ); ?></h2>
				</div>

				<div class="bkit-card__body">
					<?php
					eac_form_group(
						array(
							'type'        => 'select',
							'id'          => 'status',
							'label'       => __( 'Status', 'wp-ever-accounting' ),
							'options'     => array(
								'active'   => __( 'Active', 'wp-ever-accounting' ),
								'inactive' => __( 'Inactive', 'wp-ever-accounting' ),
							),
							'value'       => $currency->status,
							'placeholder' => __( 'Select status', 'wp-ever-accounting' ),
						)
					);
					?>
				</div>

				<div class="bkit-card__footer">
					<input type="hidden" name="id" value="<?php echo esc_attr( $currency->id ); ?>"/>
					<input type="hidden" name="action" value="eac_edit_currency"/>
					<?php wp_nonce_field( 'eac_edit_currency' ); ?>
					<button class="button button-primary bkit-w-100"><?php esc_html_e( 'Update', 'wp-ever-accounting' ); ?></button>
				</div>
			</div>
		</div><!-- .column-2 -->
	</div><!-- .bkit-poststuff -->
</form>
