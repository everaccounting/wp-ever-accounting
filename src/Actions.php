<?php

namespace EverAccounting;

defined( 'ABSPATH' ) || exit();

/**
 * Class Actions
 *
 * @package EverAccounting
 */
class Actions extends Singleton {

	/**
	 * Actions constructor.
	 */
	protected function __construct() {
		// when base currency is changed.
		add_action( 'update_option_eac_base_currency', array( $this, 'update_base_currency' ), 10, 2 );
		// when a payment is made or deleted
	}

	/**
	 * Update base currency.
	 *
	 * @param string $old_value Old value.
	 * @param string $new_value New value.
	 *
	 * @since 1.0.0
	 */
	public function update_base_currency( $old_value, $new_value ) {
		$currencies = get_option( 'eac_currencies' );
		if ( isset( $currencies[ $new_value ] ) ) {
			eac_update_currency(
				array(
					'base' => 'yes',
					'code' => $new_value,
					'rate' => 1,
				)
			);
		}
	}
}
