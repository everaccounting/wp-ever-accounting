<?php
/**
 * Handle the Account object.
 *
 * @package     EverAccounting
 * @class       Account
 * @version     1.2.1
 */

namespace EverAccounting;

use EverAccounting\Abstracts\Data;

defined( 'ABSPATH' ) || exit;

/**
 * Core class used to implement the Account object.
 *
 * @package EverAccounting
 *
 * @since 1.2.1
 *
 * @property string $currency_code
 * @property string $name
 * @property string $number
 * @property float $opening_balance
 * @property string $bank_name
 * @property string $bank_phone
 * @property string $bank_address
 * @property int $thumbnail_id
 * @property boolean $enabled
 * @property int $creator_id
 * @property string $date_created
 */
class Account_BK extends Data {
	/**
	 * Account id.
	 *
	 * @since 1.2.1
	 * @var int
	 */
	public $id = null;

	/**
	 * Account data container.
	 *
	 * @since 1.2.1
	 * @var array
	 */
	public $data = array(
		'name'            => '',
		'number'          => '',
		'currency_code'   => '',
		'opening_balance' => 0.0000,
		'bank_name'       => null,
		'bank_phone'      => null,
		'bank_address'    => null,
		'thumbnail_id'    => null,
		'enabled'         => 1,
		'creator_id'      => null,
		'date_created'    => null,
	);

	/**
	 * Stores the account object's sanitization level.
	 *
	 * Does not correspond to a DB field.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $filter;

	/**
	 * Retrieve Account instance.
	 *
	 * @param int $account_id Account id.
	 *
	 * @return Account|false Account object, false otherwise.
	 * @since 1.2.1
	 *
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 *
	 */
	public static function get_instance( $account_id ) {
		global $wpdb;

		$account_id = (int) $account_id;
		if ( ! $account_id ) {
			return false;
		}

		$_item = wp_cache_get( $account_id, 'ea_accounts' );

		if ( ! $_item ) {
			$_item = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ea_accounts WHERE id = %d LIMIT 1", $account_id ) );

			if ( ! $_item ) {
				return false;
			}

			$_item = eaccounting_sanitize_account( $_item, 'raw' );
			wp_cache_add( $_item->id, $_item, 'ea_accounts' );
		} elseif ( empty( $_item->filter ) ) {
			$_item = eaccounting_sanitize_account( $_item, 'raw' );
		}

		return new Account( $_item );
	}

	/**
	 * Account constructor.
	 *
	 * @param $account
	 *
	 * @since 1.2.1
	 */
	public function __construct( $account ) {
		foreach ( get_object_vars( $account ) as $key => $value ) {
			$this->$key = $value;
		}
	}

	/**
	 * Filter account object based on context.
	 *
	 * @param string $filter Filter.
	 *
	 * @return Account|Object
	 * @since 1.2.1
	 */
	public function filter( $filter ) {
		if ( $this->filter === $filter ) {
			return $this;
		}

		if ( 'raw' === $filter ) {
			return self::get_instance( $this->id );
		}

		return new self( eaccounting_sanitize_account( (object) $this->to_array(), $filter ) );
	}

	/**
	 * Get account balance
	 *
	 * @return float|string
	 * @since 1.0.2
	 */
	public function get_calculated_balance() {
		if ( null !== $this->balance ) {
			return $this->balance;
		}
		global $wpdb;
		$transaction_total = (float) $wpdb->get_var(
			$wpdb->prepare( "SELECT SUM(CASE WHEN type='income' then amount WHEN type='expense' then - amount END) as total from {$wpdb->prefix}ea_transactions WHERE account_id=%d", $this->id )
		);
		$this->balance     = $this->opening_balance + $transaction_total;

		return $this->balance;
	}
}
