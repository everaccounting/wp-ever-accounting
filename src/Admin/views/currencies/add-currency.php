<?php
/**
 * Add Currency Template
 *
 * @since  1.0.0
 *
 * @package Ever Accounting
 */

defined( 'ABSPATH' ) || exit;
$currencies     = eac_get_currencies();
$iso_currencies = eac_get_iso_currencies();
$iso_currencies = array_diff_key( $iso_currencies, $currencies );
$iso_currencies = array_map(
	function ( $currency ) {
		return $currency['name'] . ' (' . $currency['symbol'] . ')';
	},
	$iso_currencies
);
?>
<div class="eac-section-header">
	<div>
		<h2><?php echo esc_html( __( 'Add Currency', 'wp-ever-accounting' ) ); ?></h2>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-settings&tab=currencies' ) ); ?>"><span class="dashicons dashicons-undo"></span></a>
	</div>
	<div>
		<?php submit_button( __( 'Add Currency', 'wp-ever-accounting' ), 'primary', 'submit', false, array( 'form' => 'eac-add-currency-form' ) ); ?>
	</div>
</div>

<form id="eac-add-currency-form" class="eac-ajax-form" method="post">
	<div class="eac-card">
		<div class="eac-card__header">
			<h2 class="eac-card__title"><?php esc_html_e( 'Currency Details', 'wp-ever-accounting' ); ?></h2>
		</div>
		<div class="eac-card__body">
			<div class="eac-columns">
				<?php
				eac_form_field(
					array(
						'type'        => 'select',
						'id'          => 'code',
						'label'       => __( 'Code', 'wp-ever-accounting' ),
						'class'       => 'eac-col-6',
						'input_class' => 'eac_select2',
						'required'    => true,
						'readonly'    => true,
						'options'     => $iso_currencies,
					)
				);
				eac_form_field(
					array(
						'id'       => 'rate',
						'label'    => __( 'Rate', 'wp-ever-accounting' ),
						'type'     => 'decimal',
						'value'    => '',
						'class'    => 'eac-col-6',
						'required' => true,
						// translators: %s is the base currency.
						'prefix'   => sprintf( __( '1 %s =', 'wp-ever-accounting' ), eac_get_base_currency() ),
					)
				);
				?>
			</div>
		</div>
	</div>

	<?php wp_nonce_field( 'eac_edit_currency' ); ?>
	<input type="hidden" name="action" value="eac_edit_currency">
</form>


