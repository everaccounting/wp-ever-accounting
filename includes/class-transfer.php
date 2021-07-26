<?php
/**
 * Handle the transfer object.
 *
 * @package     EverAccounting
 * @class       Transfer
 * @version     1.0.2
 */

namespace EverAccounting;

defined( 'ABSPATH' ) || exit;

/**
 * Class Transaction
 * @package EverAccounting
 */
class Transfer {
	/**
	 * ID of the transaction.
	 * @since 1.2.1
	 * @var int
	 */
	public $id;

	/**
	 * Transfer date.
	 * @since 1.2.1
	 * @var string
	 */
	public $date = '0000-00-00 00:00:00';

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
	public $to_account_id = null;

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
	 * Retrieve Transfer instance.
	 *
	 * @param int $transfer_id Post ID.
	 *
	 * @return Transfer|bool Transfer object, false otherwise.
	 * @since 1.2.1
	 *
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 *
	 */
	public static function get_instance( $transfer_id ) {
		global $wpdb;

		$transfer_id = (int) $transfer_id;
		if ( ! $transfer_id ) {
			return false;
		}

		$_transfer = wp_cache_get( $transfer_id, 'transfers' );

		if ( ! $_transfer ) {
			$_transfer = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ea_transfers WHERE ID = %d LIMIT 1", $transfer_id ) );
			if ( ! $_transfer ) {
				return false;
			}

			$income  = eaccounting_get_transaction( $_transfer->income_id );
			$expense = eaccounting_get_transaction( $_transfer->expense_id );
			if ( ! $income || ! $expense ) {
				return false;
			}

			$_transfer->to_account_id   = $income->account_id;
			$_transfer->from_account_id = $expense->account_id;
			$_transfer->amount          = $expense->amount;
			$_transfer->currency_code   = $expense->currency_code;
			$_transfer->currency_rate   = $expense->currency_rate;
			$_transfer->date            = $expense->payment_date;
			$_transfer->description     = $expense->description;
			$_transfer->reference       = $expense->reference;
			$_transfer->payment_method  = $expense->payment_method;
			$_transfer->category_id     = $expense->category_id;

			$_transfer = eaccounting_sanitize_fields( $_transfer );


			wp_cache_add( $_transfer->id, $_transfer, 'eaccounting_transfers' );
		} elseif ( empty( $_transfer->filter ) ) {
			$_transfer = eaccounting_sanitize_fields( $_transfer );
		}

		return new Transfer( $_transfer );
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
