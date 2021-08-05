<?php
/**
 * Handle the Note object.
 *
 * @package     EverAccounting
 * @class       Note
 * @version     1.2.1
 */

namespace EverAccounting;

use EverAccounting\Abstracts\Data;

defined( 'ABSPATH' ) || exit;

class Transfer extends Data {
	/**
	 * Transfer id.
	 *
	 * @since 1.2.1
	 * @var int
	 */
	public $id = null;

	/**
	 * Transfer data container.
	 *
	 * @since 1.2.1
	 * @var array
	 */
	public $data = array(
		'date'            => null,
		'from_account_id' => null,
		'amount'          => null,
		'to_account_id'   => null,
		'income_id'       => null,
		'expense_id'      => null,
		'payment_method'  => null,
		'reference'       => null,
		'description'     => null,
		'creator_id'      => null,
		'date_created'    => null,
	);

	/**
	 * Stores the transfer object's sanitization level.
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
	 * @param int $transfer_id Transfer id.
	 *
	 * @return Transfer|false Transfer object, false otherwise.
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

		$_transfer = wp_cache_get( $transfer_id, 'ea_transfers' );

		if ( ! $_transfer ) {
			$_transfer = $wpdb->get_row( $wpdb->prepare( "SELECT transfer.* FROM {$wpdb->prefix}ea_transfers transfer  WHERE id = %d LIMIT 1", $transfer_id ) );

			if ( ! $_transfer ) {
				return false;
			}

			$_transfer = eaccounting_sanitize_transfer( $_transfer, 'raw' );
			wp_cache_add( $_transfer->id, $_transfer, 'ea_transfers' );
		} elseif ( empty( $_transfer->filter ) ) {
			$_transfer = eaccounting_sanitize_transfer( $_transfer, 'raw' );
		}

		return new Transfer( $_transfer );
	}

	/**
	 * Transfer constructor.
	 *
	 * @param object $transfer
	 *
	 * @since 1.2.1
	 */
	public function __construct( $transfer = null ) {
		foreach ( get_object_vars( $transfer ) as $key => $value ) {
			$this->$key = $value;
		}
	}
}
