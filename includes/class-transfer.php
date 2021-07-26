<?php
/**
 * Handle the Transfer object.
 *
 * @package     EverAccounting
 * @class       Transfer
 * @version     1.2.1
 */

namespace EverAccounting;

defined( 'ABSPATH' ) || exit;

/**
 * Core class used to implement the Transfer object.
 *
 * @package EverAccounting
 *
 * @since 1.2.1
 *
 * @property int $income_id
 * @property int $expense_id
 * @property int $creator_id
 * @property string $date_created
 */
class Transfer {
	/**
	 * Transfer data container.
	 *
	 * @since 1.2.1
	 * @var \stdClass
	 */
	public $data;

	/**
	 * Transfer id.
	 *
	 * @since 1.2.1
	 * @var int
	 */
	public $id = null;

	/**
	 * Transfer category_id
	 *
	 * @since 1.2.1
	 * @var int
	 */
	protected $category_id = null;

	/**
	 * Transfer constructor.
	 *
	 * @param object $transfer Transfer Object
	 *
	 * @return void
	 * @since 1.2.1
	 */
	public function __construct( $transfer ) {
		if ( $transfer instanceof self ) {
			$this->id = (int) $transfer->id;
		} elseif ( is_numeric( $transfer ) ) {
			$this->id = $transfer;
		} elseif ( ! empty( $transfer->id ) ) {
			$this->id = (int) $transfer->id;
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
	 * Return only the main transfer fields
	 *
	 * @param int $id The id of the transfer
	 *
	 * @return object|false Raw transfer object
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 * @since 1.2.1
	 */
	public static function load( $id ) {
		global $wpdb;

		if ( ! absint( $id ) ) {
			return false;
		}

		$data = wp_cache_get( $id, 'ea_transfers' );
		if ( $data ) {
			return $data;
		}

		$data = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->prefix}ea_transfers WHERE id = %d LIMIT 1",
				$id
			)
		);

		if ( ! $data ) {
			return false;
		}

		eaccounting_set_cache( 'ea_transfers', $data );

		return $data;
	}

	/**
	 * Magic method for checking the existence of a certain field.
	 *
	 * @param string $key Transfer field to check if set.
	 *
	 * @return bool Whether the given Transfer field is set.
	 * @since 1.2.1
	 */
	public function __isset( $key ) {
		if ( isset( $this->data->$key ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Magic method for setting transfer fields.
	 *
	 * This method does not update custom fields in the database.
	 *
	 * @param string $key Transfer key.
	 * @param mixed  $value Transfer value.
	 *
	 * @since 1.2.1
	 */
	public function __set( $key, $value ) {
		if ( is_callable( array( $this, 'set_' . $key ) ) ) {
			$this->$key( $value );
		} else {
			$this->data->$key = $value;
		}
	}

	/**
	 * Magic method for accessing custom fields.
	 *
	 * @param string $key Transfer field to retrieve.
	 *
	 * @return mixed Value of the given Transfer field (if set).
	 * @since 1.2.1
	 */
	public function __get( $key ) {

		if ( is_callable( array( $this, 'get_' . $key ) ) ) {
			$value = $this->$key();
		} else {
			$value = $this->data->$key;
		}

		return $value;
	}

	/**
	 * Magic method for unsetting a certain field.
	 *
	 * @param string $key Transfer key to unset.
	 *
	 * @since 1.2.1
	 */
	public function __unset( $key ) {
		if ( isset( $this->data->$key ) ) {
			unset( $this->data->$key );
		}
	}

	/**
	 * Determine whether a property or meta key is set
	 *
	 * Consults the transfers.
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
	 * Determine whether the transfer exists in the database.
	 *
	 * @return bool True if transfer exists in the database, false if not.
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

	/**
	 * Get formatted transaction amount.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_calculated_formatted_amount() {
		return eaccounting_format_price( $this->amount, $this->currency_code );
	}

	/**
	 * Save should create or update based on object existence.
	 *
	 * @return \Exception|bool|void
	 * @throws \Exception If any error happens
	 * @since  1.1.0
	 */
	public function save() {
		if ( ! $this->from_account_id || ! $this->to_account_id ) {
			throw new \Exception( __( 'Transfer from and to account can not be same.', 'wp-ever-accounting' ) );
		}

		$this->maybe_set_transfer_category();
	}

	/**
	 * Set transfer category.
	 *
	 * @throws \Exception If any error happens
	 * @since 1.1.0
	 */
	protected function maybe_set_transfer_category() {
		global $wpdb;
		$cache_key   = md5( 'other' . __( 'Transfer', 'wp-ever-accounting' ) );
		$category_id = wp_cache_get( $cache_key, 'ea_categories' );
		if ( false === $category_id ) {
			$category_id = $wpdb->get_var( $wpdb->prepare( "SELECT id from {$wpdb->prefix}ea_categories WHERE type=%s AND name=%s", 'other', __( 'Transfer', 'wp-ever-accounting' ) ) );
			wp_cache_add( $cache_key, $category_id, 'ea_categories' );
		}
		if ( empty( $category_id ) ) {
			throw new \Exception(
				sprintf(
				/* translators: %s: category name %s: category type */
					__( 'Transfer category is missing please create a category named "%1$s" and type"%2$s".', 'wp-ever-accounting' ),
					__( 'Transfer', 'wp-ever-accounting' ),
					'other'
				)
			);
		}

		$this->category_id = $category_id;
	}
}
