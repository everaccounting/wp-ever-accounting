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
		add_action( 'update_option_eac_base_currency', array( __CLASS__, 'update_base_currency' ), 10, 2 );
	}

	/**
	 * Update base currency.
	 *
	 * @param string $old_value Old value.
	 * @param string $new_value New value.
	 *
	 * @since 1.0.0
	 */
	public static function update_base_currency( $old_value, $new_value ) {
		// If the transaction table is not empty, we will revert the base currency to the old value.
		// Otherwise, we will update currencies rate based on the new base currency.
		$old_value         = strtoupper( $old_value );
		$new_value         = strtoupper( $new_value );
		$transaction_count = eac_get_transactions( [], true );
		$base_currency     = eac_get_currency( $new_value );
		if ( $transaction_count > 0 ) {
			remove_action( 'update_option_eac_base_currency', array( __CLASS__, 'update_base_currency' ) );
			update_option( 'eac_base_currency', $old_value );

			// die with a message.
			return;
		}
		// If new currency is not found, we will revert the base currency to the old value.
		if ( ! $base_currency ) {
			remove_action( 'update_option_eac_base_currency', array( __CLASS__, 'update_base_currency' ) );
			update_option( 'eac_base_currency', $old_value );

			// die with a message.
			return;
		}
		$currencies = eac_get_currencies( [ 'limit' => - 1 ] );
		foreach ( $currencies as $currency ) {
			$currency->set_exchange_rate( $currency->get_exchange_rate() / $base_currency->get_exchange_rate() );
			$currency->save();
		}

		// Update the base currency rate to 1.
		$base_currency->set_exchange_rate( 1 );
		$base_currency->save();
	}
}
