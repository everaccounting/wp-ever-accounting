<?php
/**
 * Handle the Transaction object.
 *
 * @package     EverAccounting
 * @class       Transaction
 * @version     1.2.1
 */

namespace EverAccounting;

defined( 'ABSPATH' ) || exit;

/**
 * Core class used to implement the Transaction object.
 *
 * @package EverAccounting
 *
 * @since 1.2.1
 *
 * @property string $type
 * @property string $payment_date
 * @property float $amount
 * @property string $currency_code
 * @property float $currency_rate
 * @property int $account_id
 * @property int $document_id
 * @property int $contact_id
 * @property int $category_id
 * @property string $description
 * @property string $payment_method
 * @property string $reference
 * @property int $attachment_id
 * @property int $parent_id
 * @property boolean $reconciled
 * @property int $creator_id
 * @property string $date_created
 */
class Transaction {
	/**
	 * Transaction data container.
	 *
	 * @since 1.2.1
	 * @var \stdClass
	 */
	public $data;

	/**
	 * Transaction id.
	 *
	 * @since 1.2.1
	 * @var int
	 */
	public $id = null;

	/**
	 * Transaction constructor.
	 *
	 * @param object $transaction Transaction Object
	 *
	 * @return void
	 * @since 1.2.1
	 */
	public function __construct( $transaction ) {
		if ( $transaction instanceof self ) {
			$this->id = (int) $transaction->id;
		} elseif ( is_numeric( $transaction ) ) {
			$this->id = $transaction;
		} elseif ( ! empty( $transaction->id ) ) {
			$this->id = (int) $transaction->id;
		} else {
			$this->id = 0;
		}

		if ( $this->id > 0 ) {
			$data = self::load( $this->id );
			if ( ! $data ) {
				$this->id = null;

				return;
			}
			$this->data = $data;
			$this->id   = (int) $data->id;
		}
	}

	/**
	 * Return only the main transaction fields
	 *
	 * @param int $id The id of the transaction
	 *
	 * @return object|false Raw transaction object
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 * @since 1.2.1
	 */
	public static function load( $id ) {
		global $wpdb;

		if ( ! absint( $id ) ) {
			return false;
		}

		$data = wp_cache_get( $id, 'ea_transactions' );
		if ( $data ) {
			return $data;
		}

		$data = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->prefix}ea_transactions WHERE id = %d LIMIT 1",
				$id
			)
		);

		if ( ! $data ) {
			return false;
		}

		eaccounting_set_cache( 'ea_transactions', $data );

		return $data;
	}

	/**
	 * Magic method for checking the existence of a certain field.
	 *
	 * @param string $key Transaction field to check if set.
	 *
	 * @return bool Whether the given Transaction field is set.
	 * @since 1.2.1
	 */
	public function __isset( $key ) {
		if ( isset( $this->data->$key ) ) {
			return true;
		}

		return metadata_exists( 'transaction', $this->id, $key );
	}

	/**
	 * Magic method for setting transaction fields.
	 *
	 * This method does not update custom fields in the database.
	 *
	 * @param string $key Transaction key.
	 * @param mixed  $value Transaction value.
	 *
	 * @since 1.2.1
	 */
	public function __set( $key, $value ) {
		if ( is_callable( array( $this, 'set_' . $key ) ) ) {
			$this->$key( $value );
		} elseif ( isset( $this->data->$key ) ) {
			$this->data->$key = $value;
		} else {
			$this->update_meta( $key, $value );
		}
	}

	/**
	 * Magic method for accessing custom fields.
	 *
	 * @param string $key Transaction field to retrieve.
	 *
	 * @return mixed Value of the given Transaction field (if set).
	 * @since 1.2.1
	 */
	public function __get( $key ) {

		if ( is_callable( array( $this, 'get_' . $key ) ) ) {
			$value = $this->$key();
		} elseif ( isset( $this->data->$key ) ) {
			$value = $this->data->$key;
		} else {
			$value = $this->get_meta( $key, true );
		}

		return $value;
	}

	/**
	 * Magic method for unsetting a certain field.
	 *
	 * @param string $key Transaction key to unset.
	 *
	 * @since 1.2.1
	 */
	public function __unset( $key ) {
		if ( isset( $this->data->$key ) ) {
			unset( $this->data->$key );
		}
	}

	/**
	 * Get meta data.
	 *
	 * @param string  $meta_key Meta key
	 * @param boolean $single Single
	 *
	 * @return array|false|mixed
	 * @since 1.2.1
	 */
	protected function get_meta( string $meta_key = '', bool $single = true ) {
		return get_metadata( 'transaction', $this->id, $meta_key, $single );
	}

	/**
	 * Update meta value.
	 *
	 * @param string $meta_key Meta key
	 * @param string $meta_value Meta value
	 * @param string $prev_value Previous value
	 *
	 * @return bool|int
	 * @since 1.2.1
	 */
	protected function update_meta( string $meta_key, string $meta_value, string $prev_value = '' ) {
		return update_metadata( 'transaction', $this->id, $meta_key, $meta_value, $prev_value );
	}

	/**
	 * Determine whether a property or meta key is set
	 *
	 * Consults the transactions and transaction_meta tables.
	 *
	 * @param string $key Property
	 *
	 * @return bool
	 * @since 1.2.1
	 */
	public function has_prop( string $key ) {
		return $this->__isset( $key );
	}

	/**
	 * Determine whether the transaction exists in the database.
	 *
	 * @return bool True if transaction exists in the database, false if not.
	 * @since 1.2.1
	 */
	public function exists() {
		return ! empty( $this->id );
	}

	/**
	 * Return an array representation.
	 *
	 * @return array Array representation.
	 * @since 1.2.1
	 */
	public function to_array() {
		return get_object_vars( $this->data );
	}
}
