<?php
/**
 * Admin Currency Form.
 * Page: Misc
 * Tab: Currencies
 *
 * @package EverAccounting
 * @since 1.0.0
 * @var $currency \EverAccounting\Models\Currency Currency object.
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
							'value'    => $currency->code,
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
							'value'    => $currency->name,
							'required' => true,
						)
					);
					eac_form_field(
						array(
							'id'        => 'exchange_rate',
							'label'     => __( 'Exchange Rate', 'wp-ever-accounting' ),
							'data_type' => 'decimal',
							'value'     => $currency->is_base_currency() ? 1 : $currency->exchange_rate,
							'readonly'  => $currency->is_base_currency() ? 'readonly' : false,
							'required'  => true,
							'class'     => 'eac_decimal_input',
							// translators: %s is the base currency.
							'prefix'    => sprintf( __( '1 %s =', 'wp-ever-accounting' ), eac_base_currency() ),
							'suffix'    => $currency->code,
						)
					);
					eac_form_field(
						array(
							'id'       => 'symbol',
							'label'    => __( 'Symbol', 'wp-ever-accounting' ),
							'type'     => 'text',
							'value'    => $currency->symbol,
							'required' => true,
						)
					);
					eac_form_field(
						array(
							'id'       => 'thousand_separator',
							'label'    => __( 'Thousand Separator', 'wp-ever-accounting' ),
							'type'     => 'text',
							'value'    => $currency->thousand_separator,
							'required' => true,
						)
					);

					eac_form_field(
						array(
							'id'       => 'decimal_separator',
							'label'    => __( 'Decimal Separator', 'wp-ever-accounting' ),
							'type'     => 'text',
							'value'    => $currency->decimal_separator,
							'required' => true,
						)
					);

					eac_form_field(
						array(
							'id'       => 'decimals',
							'label'    => __( 'Number of Decimals', 'wp-ever-accounting' ),
							'type'     => 'number',
							'value'    => $currency->decimals,
							'required' => true,
						)
					);

					eac_form_field(
						array(
							'id'       => 'position',
							'label'    => __( 'Symbol Position', 'wp-ever-accounting' ),
							'type'     => 'select',
							'value'    => $currency->position,
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
				<div class="eac-card__header">
					<h2 class="eac-card__title"><?php esc_html_e( 'Actions', 'wp-ever-accounting' ); ?></h2>
				</div>

				<div class="eac-card__body">
					<?php
					eac_form_field(
						array(
							'type'     => 'select',
							'id'       => 'status',
							'label'    => __( 'Status', 'wp-ever-accounting' ),
							'options'  => array(
								'active'   => __( 'Active', 'wp-ever-accounting' ),
								'inactive' => __( 'Inactive', 'wp-ever-accounting' ),
							),
							'value'    => $currency->status,
							'required' => true,
						)
					);
					?>
				</div>

				<div class="eac-card__footer">
					<input type="hidden" name="id" value="<?php echo esc_attr( $currency->id ); ?>"/>
					<input type="hidden" name="action" value="eac_edit_currency"/>
					<?php wp_nonce_field( 'eac_edit_currency' ); ?>
					<?php if ( $currency->exists() && $currency->is_deletable() ) : ?>
						<a class="eac_confirm_delete del" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'action', 'delete', admin_url( 'admin.php?page=eac-misc&tab=currencies&action=delete&id=' . $currency->id ) ), 'bulk-currencies' ) ); ?>"><?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?></a>
					<?php endif; ?>
					<button class="button button-primary eac-w-100"><?php esc_html_e( 'Update', 'wp-ever-accounting' ); ?></button>
				</div>
			</div>
		</div><!-- .column-2 -->
	</div><!-- .eac-poststuff -->
</form>
