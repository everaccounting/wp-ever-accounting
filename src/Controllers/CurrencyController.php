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
use EverAccounting\Core\Exception;

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
		add_action( 'eaccounting_pre_save_currency', array( __CLASS__, 'validate_currency_data' ), 10, 2 );
		add_action( 'eaccounting_delete_currency', array( __CLASS__, 'delete_default_currency' ), 10, 2 );
	}

	/**
	 * Validate currency data.
	 *
	 * @param array $data
	 * @param int $id
	 * @param Currency $currency
	 *
	 * @since 1.1.0
	 *
	 */
	public static function validate_currency_data( $data, $id ) {
		global $wpdb;
		if ( empty( $data['code'] ) ) {
			throw new Exception( 'empty_prop', __( 'Currency code is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['rate'] ) ) {
			throw new Exception( 'empty_prop', __( 'Currency rate is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['symbol'] ) ) {
			throw new Exception( 'empty_prop', __( 'Currency symbol is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['position'] ) || ! in_array( $data['position'], array( 'before', 'after' ), true ) ) {
			throw new Exception( 'empty_prop', __( 'Currency position is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['precision'] ) ) {
			throw new Exception( 'empty_prop', __( 'Currency precision is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['decimal_separator'] ) ) {
			throw new Exception( 'empty_prop', __( 'Currency decimal separator is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['thousand_separator'] ) ) {
			throw new Exception( 'empty_prop', __( 'Currency thousand separator is required.', 'wp-ever-accounting' ) );
		}

		if ( $id != (int) $wpdb->get_var( $wpdb->prepare( "SELECT id from {$wpdb->prefix}ea_currencies WHERE code='%s'", eaccounting_clean( $data['code'] ) ) ) ) { // @codingStandardsIgnoreLine
			throw new Exception( 'duplicate_item', __( 'Duplicate currency.', 'wp-ever-accounting' ) );
		}
	}

	/**
	 * Delete currency id from settings.
	 *
	 * @param $id
	 * @param $data
	 *
	 * @since 1.1.0
	 *
	 */
	function delete_default_currency( $id, $data ) {
		$default_currency = eaccounting()->settings->get( 'default_currency' );
		if ( $default_currency === $data['code'] ) {
			eaccounting()->settings->set( array( array( 'default_currency' => '' ) ), true );
		}
	}


}
