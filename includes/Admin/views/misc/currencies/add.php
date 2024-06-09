<?php
/**
 * Admin Add Currency Form View.
 * Page: Misc
 * Tab: Currencies
 *
 * @since 1.0.0
 * @package EverAccounting
 * @var $currency \EverAccounting\Models\Currency Currency object.
 */

use EverAccounting\Utilities\I18n;

defined( 'ABSPATH' ) || exit;

$codes      = I18n::get_currencies();
$currencies = eac_get_currencies( array( 'limit' => - 1 ) );
foreach ( $currencies as $currency ) {
	if ( isset( $codes[ $currency->code ] ) ) {
		unset( $codes[ $currency->code ] );
	}
}
foreach ( $codes as $code => $currency ) {
	$codes[ $code ] = sprintf( '%s (%s)', $currency['name'], $code );
}

?>
<h1 class="wp-heading-inline">
	<?php esc_html_e( 'Add Currency', 'wp-ever-accounting' ); ?>
	<a href="<?php echo esc_attr( remove_query_arg( 'edit' ) ); ?>" title="<?php esc_attr_e( 'Go back', 'wp-ever-accounting' ); ?>">
		<span class="dashicons dashicons-undo"></span>
	</a>
</h1>
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
							'id'            => 'code',
							'label'         => __( 'Currency', 'wp-ever-accounting' ),
							'options'       => $codes,
							'type'          => 'select',
							'class'         => 'eac_select2',
							'placeholder'   => __( 'Select a currency', 'wp-ever-accounting' ),
							'required'      => true,
							'readonly'      => true,
							'wrapper_class' => 'is--full',
						)
					);
					eac_form_field(
						array(
							'id'            => 'exchange_rate',
							'label'         => __( 'Exchange Rate', 'wp-ever-accounting' ),
							'data_type'     => 'decimal',
							'required'      => true,
							'class'         => 'eac_decimal_input',
							// translators: %s is the base currency.
							'prefix'        => sprintf( __( '1 %s =', 'wp-ever-accounting' ), eac_get_base_currency() ),
							'wrapper_class' => 'is--full',
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

				<div class="eac-card__footer">
					<input type="hidden" name="action" value="eac_add_currency"/>
					<?php wp_nonce_field( 'eac_add_currency' ); ?>
					<button class="button button-primary tw-w-[100%]"><?php esc_html_e( 'Submit', 'wp-ever-accounting' ); ?></button>
				</div>
			</div>
		</div><!-- .column-2 -->
	</div><!-- .eac-poststuff -->
</form>
