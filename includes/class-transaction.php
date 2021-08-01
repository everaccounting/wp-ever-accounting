<?php
/**
 * Handle the Transaction object.
 *
 * @package     EverAccounting
 * @class       Transaction
 * @version     1.2.1
 */

namespace EverAccounting;

use EverAccounting\Abstracts\Data;

defined( 'ABSPATH' ) || exit;

/**
 * Core class used to implement the Transaction object.
 *
 * @package EverAccounting
 *
 * @since 1.2.1
 *
 */
class Transaction extends Data {
	/**
	 * Transaction id.
	 *
	 * @since 1.2.1
	 * @var int
	 */
	public $id = null;

	/**
	 * Transaction data container.
	 *
	 * @since 1.2.1
	 * @var array
	 */
	public $data = array(
		'type'           => 'income',
		'type_id'        => null,
		'payment_date'   => null,
		'amount'         => 0.00,
		'currency_code'  => '', // protected
		'currency_rate'  => 0.00, // protected
		'account_id'     => null,
		'document_id'    => null,
		'contact_id'     => null,
		'category_id'    => null,
		'description'    => '',
		'payment_method' => '',
		'reference'      => '',
		'attachment_id'  => null,
		'parent_id'      => 0,
		'reconciled'     => 0,
		'creator_id'     => null,
		'date_created'   => null,
	);
	/**
	 * Stores the transaction object's sanitization level.
	 *
	 * Does not correspond to a DB field.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $filter;

	/**
	 * Retrieve Transaction instance.
	 *
	 * @param int $transaction_id Transaction id.
	 *
	 * @return Transaction|false Transaction object, false otherwise.
	 * @since 1.2.1
	 *
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 *
	 */
	public static function get_instance( $transaction_id ) {
		global $wpdb;

		$transaction_id = (int) $transaction_id;
		if ( ! $transaction_id ) {
			return false;
		}

		$_item = wp_cache_get( $transaction_id, 'ea_transactions' );

		if ( ! $_item ) {
			$_item = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ea_transactions WHERE id = %d LIMIT 1", $transaction_id ) );

			if ( ! $_item ) {
				return false;
			}

			$_item = eaccounting_sanitize_transaction( $_item, 'raw' );
			wp_cache_add( $_item->id, $_item, 'ea_transactions' );
		} elseif ( empty( $_item->filter ) ) {
			$_item = eaccounting_sanitize_transaction( $_item, 'raw' );
		}

		return new Transaction( $_item );
	}
}
