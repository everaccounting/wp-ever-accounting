<?php
/**
 * Tax Controller
 *
 * Handles tax's insert, update and delete events.
 *
 * @package     EverAccounting\Controllers
 * @class       TaxController
 * @version     1.1.0
 */

namespace EverAccounting\Controllers;

use EverAccounting\Abstracts\Singleton;
use EverAccounting\Core\Exception;
use EverAccounting\Models\Tax;

defined( 'ABSPATH' ) || exit;

/**
 * Class TaxController
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Controllers
 */
class TaxController extends Singleton {
	/**
	 * AccountController constructor.
	 */
	public function __construct() {
		add_action( 'eaccounting_pre_save_tax', array( __CLASS__, 'validate_tax_data' ), 10, 2 );
		add_action( 'eaccounting_delete_tax', array( __CLASS__, 'update_invoice_item_tax' ) );
		add_action( 'eaccounting_delete_tax', array( __CLASS__, 'update_bill_item_tax' ) );
	}

	/**
	 * Validate tax data.
	 *
	 * @param array $data
	 * @param int $id
	 * @param Tax $tax
	 *
	 * @since 1.1.0
	 */
	public static function validate_tax_data( $data, $id ) {
		error_log(print_r($data,true));
		global $wpdb;
		if ( empty( $data['name'] ) ) {
			throw new Exception( 'empty_prop', __( 'Tax name is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['rate'] ) ) {
			throw new Exception( 'empty_prop', __( 'Tax rate is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['type'] ) ) {
			throw new Exception( 'empty_prop', __( 'Tax type is required.', 'wp-ever-accounting' ) );
		}

	}

	/**
	 * Delete tax id from invoice items.
	 *
	 * @param $id
	 *
	 * @return bool
	 * @since 1.0.2
	 */
	public static function update_invoice_item_tax( $id ) {
		global $wpdb;
		$id = absint( $id );
		if ( empty( $id ) ) {
			return false;
		}

		return $wpdb->update( $wpdb->prefix . 'ea_invoice_items', array( 'tax_id' => '' ), array( 'tax_id' => absint( $id ) ) );
	}

	/**
	 * Delete tax id from bill items.
	 *
	 * @param $id
	 *
	 * @return bool
	 * @since 1.0.2
	 */
	public static function update_bill_item_tax( $id ) {
		global $wpdb;
		$id = absint( $id );
		if ( empty( $id ) ) {
			return false;
		}

		return $wpdb->update( $wpdb->prefix . 'ea_bill_items', array( 'tax_id' => '' ), array( 'tax_id' => absint( $id ) ) );
	}

}
