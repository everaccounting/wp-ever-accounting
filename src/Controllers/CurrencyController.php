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
		add_filter( 'eaccounting_prepare_currency_data', array( __CLASS__, 'prepare_currency_data' ), 10, 2 );
		add_action( 'eaccounting_validate_currency_data', array( __CLASS__, 'validate_currency_data' ), 10, 3 );
		add_action( 'eaccounting_delete_currency', array( __CLASS__, 'delete_default_currency' ), 10, 2 );
	}

	/**
	 * Prepare currency data before inserting into database.
	 *
	 * @since 1.1.0
	 *
	 * @param int   $id
	 * @param array $data
	 *
	 * @return array
	 */
	public static function prepare_currency_data( $data, $id = null ) {
		if ( empty( $data['date_created'] ) ) {
			$data['date_created'] = current_time( 'mysql' );
		}

		return eaccounting_clean( $data );
	}


	/**
	 * Validate currency data.
	 *
	 * @since 1.1.0
	 *
	 * @param array     $data
	 * @param null      $id
	 * @param \WP_Error $errors
	 */
	public static function validate_currency_data( $errors, $data, $id = null ) {
		if ( empty( $data['code'] ) ) {
			$errors->add( 'empty_prop', __( 'Currency code is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['rate'] ) ) {
			$errors->add( 'empty_prop', __( 'Currency rate is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['symbol'] ) ) {
			$errors->add( 'empty_prop', __( 'Currency symbol is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['position'] ) || ! in_array( $data['position'], array( 'before', 'after' ), true ) ) {
			$errors->add( 'empty_prop', __( 'Currency position is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['precision'] ) ) {
			$errors->add( 'empty_prop', __( 'Currency precision is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['decimal_separator'] ) ) {
			$errors->add( 'empty_prop', __( 'Currency decimal separator is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['thousand_separator'] ) ) {
			$errors->add( 'empty_prop', __( 'Currency thousand separator is required.', 'wp-ever-accounting' ) );
		}

		if ( ! empty( $data['code'] ) ) {
			if ( intval( $id ) !== (int) Currencies::instance()->get_var(
				'id',
				array(
					'code' => $data['code'],
				)
			) ) {
				$errors->add( 'invalid_prop', __( 'Duplicate currency.', 'wp-ever-accounting' ) );
			}
		}

		return $errors;
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
