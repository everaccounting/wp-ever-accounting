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
	 * @since 1.1.0
	 * 
	 * @param array $data
	 * @param int $id
	 * @param Currency $currency
	 *
	 * @throws \Exception
	 */
	public static function validate_currency_data( $data, $id ) {
		global $wpdb;
		if ( $id != (int) $wpdb->get_var( $wpdb->prepare( "SELECT id from {$wpdb->prefix}ea_currencies WHERE code='%s'", eaccounting_clean( $data['code'] ) ) ) ) { // @codingStandardsIgnoreLine
			throw new \Exception( __( 'Duplicate currency.', 'wp-ever-accounting' ) );
		}
	}

	/**
	 * Delete currency id from settings.
	 *
	 * @since 1.1.0
	 * 
	 * @param $id
	 * @param $data
	 *
	 */
	public static function delete_default_currency( $id, $data ) {
		$default_currency = eaccounting()->settings->get( 'default_currency' );
		if ( $default_currency === $data['code'] ) {
			eaccounting()->settings->set( array( array( 'default_currency' => '' ) ), true );
		}
	}
}
