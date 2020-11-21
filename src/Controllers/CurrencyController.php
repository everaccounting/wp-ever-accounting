<?php
/**
 * Currency Controller
 *
 * Handles currency's insert, update and delete events.
 *
 * @package     EverAccounting\Controllers
 * @class       CurrencyController
 * @version     1.1.0
 */

namespace EverAccounting\Controllers;

use EverAccounting\Abstracts\Singleton;
use EverAccounting\Models\Currency;
use EverAccounting\Repositories\Currencies;

defined( 'ABSPATH' ) || exit;

/**
 * Class CurrencyController
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Controllers
 */
class CurrencyController extends Singleton {
	/**
	 * CurrencyController constructor.
	 */
	public function __construct() {
		add_action( 'eaccounting_pre_save_currency', array( __CLASS__, 'validate_currency_data' ), 10, 3 );
		add_action( 'eaccounting_delete_currency', array( __CLASS__, 'delete_default_currency' ), 10, 2 );
	}

	/**
	 * Validate currency data.
	 *
	 * @since 1.1.0
	 *
	 * @param array    $data
	 * @param int      $id
	 * @param Currency $currency
	 */
	public static function validate_currency_data( $data, $id, $currency ) {
		global $wpdb;
		if ( empty( $data['code'] ) ) {
			$currency->error( 'empty_prop', __( 'Currency code is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['rate'] ) ) {
			$currency->error( 'empty_prop', __( 'Currency rate is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['symbol'] ) ) {
			$currency->error( 'empty_prop', __( 'Currency symbol is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['position'] ) || ! in_array( $data['position'], array( 'before', 'after' ), true ) ) {
			$currency->error( 'empty_prop', __( 'Currency position is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['precision'] ) ) {
			$currency->error( 'empty_prop', __( 'Currency precision is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['decimal_separator'] ) ) {
			$currency->error( 'empty_prop', __( 'Currency decimal separator is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['thousand_separator'] ) ) {
			$currency->error( 'empty_prop', __( 'Currency thousand separator is required.', 'wp-ever-accounting' ) );
		}

		if ( $currency->get_id() != (int) $wpdb->get_var( $wpdb->prepare( "SELECT id from {$wpdb->prefix}ea_currencies WHERE code='%s'", $currency->get_code() ) ) ) { // @codingStandardsIgnoreLine
			$currency->error( 'duplicate_item', __( 'Duplicate currency.', 'wp-ever-accounting' ) );
		}
	}

	/**
	 * Delete currency id from settings.
	 *
	 * @since 1.0.2
	 *
	 * @param $id
	 * @param $data
	 */
	function update_transaction_currency( $id, $data ) {
		$default_currency = eaccounting()->settings->get( 'default_currency' );
		if ( $default_currency === $data['code'] ) {
			eaccounting()->settings->set( array( array( 'default_currency' => '' ) ), true );
		}
	}

}
