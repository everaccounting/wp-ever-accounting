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
class Account extends Data {
	/**
	 * Account id.
	 *
	 * @since 1.2.1
	 * @var int
	 */
	protected $id = null;

	/**
	 * Account data container.
	 *
	 * @since 1.2.1
	 * @var array
	 */
	protected $data = array(
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

		$_account = wp_cache_get( $account_id, 'ea_accounts' );

		if ( ! $_account ) {
			$_account = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ea_accounts WHERE id = %d LIMIT 1", $account_id ) );

			if ( ! $_account ) {
				return false;
			}

			$_account = eaccounting_sanitize_account( $_account, 'raw' );
			wp_cache_add( $_account->id, $_account, 'ea_accounts' );
		}

		$account = new Account;
		$account->set_props( $_account );
		$account->object_read = true;

		return $account;
	}

	/**
	 * Account constructor.
	 *
	 * @param $account
	 *
	 * @since 1.2.1
	 */
	public function __construct( $account = null ) {
		parent::__construct();
		if ( is_object( $account ) ) {
			$this->set_props( $account );
		}

	}

}
