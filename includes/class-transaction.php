<?php
/**
 * Handle the transaction object.
 *
 * @package     EverAccounting
 * @class       Transaction
 * @version     1.0.2
 */

namespace EverAccounting;

defined( 'ABSPATH' ) || exit;

/**
 * Class Transaction
 * @package EverAccounting
 */
class Transaction {
	/**
	 * ID of the transaction.
	 * @since 1.2.1
	 * @var int
	 */
	public $id;

	/**
	 * Transaction Payment date.
	 * @since 1.2.1
	 * @var string
	 */
	public $payment_date = '0000-00-00 00:00:00';

	/**
	 * @var string
	 */
	public $type = '';

	/**
	 * Transaction Amount.
	 *
	 * @var double
	 */
	public $amount = 0.00;

	/**
	 * @var string
	 */
	public $currency_code = '';

	/**
	 * @var double
	 */
	public $currency_rate = 1;

	/**
	 * @var int
	 */
	public $account_id = null;

	/**
	 * @var int
	 */
	public $document_id = null;

	/**
	 * @var int
	 */
	public $contact_id = null;

	/**
	 * @var int
	 */
	public $category_id = null;

	/**
	 * @var int
	 */
	public $description = null;

	/**
	 * @var string
	 */
	public $payment_method = '';

	/**
	 * @var string
	 */
	public $reference = '';

	/**
	 * @var string
	 */
	public $attachment_id = null;

	/**
	 * @var int
	 */
	public $parent_id = null;

	/**
	 * @var bool
	 */
	public $reconciled = false;

	/**
	 * @var int
	 */
	public $creator_id = 0;

	/**
	 * @var string
	 */
	public $date_created = '0000-00-00 00:00:00';

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
	 * @param int $transaction_id Post ID.
	 *
	 * @return Transaction|bool Transaction object, false otherwise.
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

		$_transaction = wp_cache_get( $transaction_id, 'transactions' );

		if ( ! $_transaction ) {
			$_transaction = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ea_transactions WHERE ID = %d LIMIT 1", $transaction_id ) );

			if ( ! $_transaction ) {
				return false;
			}

			$_transaction = eaccounting_sanitize_fields( $_transaction );
			wp_cache_add( $_transaction->id, $_transaction, 'eaccounting_transactions' );
		} elseif ( empty( $_transaction->filter ) ) {
			$_transaction = eaccounting_sanitize_fields( $_transaction );
		}

		return new Transaction( $_transaction );
	}

	/**
	 * Constructor.
	 *
	 * @param Transaction|object $post Post object.
	 *
	 * @since 1.2.1
	 *
	 */
	public function __construct( $post ) {
		foreach ( get_object_vars( $post ) as $key => $value ) {
			$this->$key = $value;
		}
	}

	/**
	 * Convert object to array.
	 *
	 * @return array Object as array.
	 * @since 1.2.1
	 *
	 */
	public function to_array() {
		return get_object_vars( $this );
	}
}
