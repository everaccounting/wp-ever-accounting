<?php
/**
 * Handle the Transaction object.
 *
 * @package     EverAccounting
 * @class       Transaction
 * @version     1.0.5
 */

namespace EverAccounting;

use EverAccounting\Abstracts\MetaData;

defined( 'ABSPATH' ) || exit;

/**
 * Class Transaction
 *
 * @package EverAccounting
 *
 * @property string $type
 * @property string $payment_date
 * @property float $amount
 * @property string $currency_code
 * @property float $currency_rate
 * @property int $account_id
 * @property int $invoice_id
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
class Transaction extends MetaData {
	/**
	 * Item Data array.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $data = array(
		'type'           => '',
		'payment_date'   => null,
		'amount'         => 0.00,
		'currency_code'  => '', // protected
		'currency_rate'  => 0.00, // protected
		'account_id'     => null,
		'invoice_id'     => null,
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
	 * A map of database fields to data types.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $data_type = array(
		'id'             => '%d',
		'type'           => '%s',
		'payment_date'   => '%s',
		'amount'         => '%.4f',
		'currency_code'  => '%s', // protected
		'currency_rate'  => '%.8f', // protected
		'account_id'     => '%d',
		'document_id'    => '%d',
		'contact_id'     => '%d',
		'category_id'    => '%d',
		'description'    => '%s',
		'payment_method' => '%s',
		'reference'      => '%s',
		'attachment_id'  => '%d',
		'parent_id'      => '%d',
		'reconciled'     => '%d',
		'creator_id'     => '%d',
		'date_created'   => '%s',
	);

	/**
	 * Meta type.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	protected $meta_type = 'transaction';

	/**
	 * Transaction constructor.
	 *
	 * Get the note if id is passed, otherwise the note is new and empty.
	 *
	 * @param int|object|array|Transaction $transaction object to read.
	 *
	 * @since 1.1.0
	 */
	public function __construct( $transaction = 0 ) {
		parent::__construct();
		if ( $transaction instanceof self ) {
			$this->set_id( $transaction->get_id() );
		} elseif ( is_object( $transaction ) && ! empty( $transaction->id ) ) {
			$this->set_id( $transaction->id );
		} elseif ( is_array( $transaction ) && ! empty( $transaction['id'] ) ) {
			$this->set_props( $transaction );
		} elseif ( is_numeric( $transaction ) ) {
			$this->set_id( $transaction );
		} else {
			$this->set_object_read( true );
		}

		$data = self::get_raw( $this->get_id() );
		if ( $data ) {
			$this->set_props( $data );
			$this->set_object_read( true );
		} else {
			$this->set_id( 0 );
		}
	}

	/**
	 * Retrieve the Transaction from database instance.
	 *
	 * @param int    $transaction_id Transaction id.
	 * @param string $field Database field.
	 *
	 * @return object|false Object, false otherwise.
	 * @since 1.2.1
	 *
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 */
	public static function get_raw( $transaction_id, $field = 'id' ) {
		global $wpdb;

		$transaction_id = (int) $transaction_id;
		if ( ! $transaction_id ) {
			return false;
		}
		$transaction = wp_cache_get( $transaction_id, 'ea_transactions' );

		if ( ! $transaction ) {
			$transaction = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ea_transactions WHERE id = %d LIMIT 1", $transaction_id ) );

			if ( ! $transaction ) {
				return false;
			}

			wp_cache_add( $transaction->id, $transaction, 'ea_transactions' );
		}

		return apply_filters( 'eaccounting_transaction_raw_item', $transaction );
	}

	/**
	 *  Insert an item in the database.
	 *
	 * This method is not meant to call publicly instead call save
	 * which will conditionally decide which method to call.
	 *
	 * @param array $args An array of arguments for internal use case.
	 *
	 * @return \WP_Error|true True on success, WP_Error on failure.
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 * @since 1.1.0
	 */
	protected function insert( $args = [] ) {
		global $wpdb;
		$data_arr = $this->to_array();
		$data     = wp_array_slice_assoc( $data_arr, array_keys( $this->data_type ) );
		$format   = wp_array_slice_assoc( $this->data_type, array_keys( $data ) );
		$data     = wp_unslash( $data );

		// Bail if nothing to save
		if ( empty( $data ) ) {
			return true;
		}

		/**
		 * Fires immediately before a transaction is inserted in the database.
		 *
		 * @param array $data Transaction data to be inserted.
		 * @param string $data_arr Sanitized transaction item data.
		 * @param Transaction $transaction Transaction object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_insert_transaction', $data, $data_arr, $this );

		/**
		 * Fires immediately before a transaction is inserted in the database.
		 *
		 * The dynamic portion of the hook name, `$this->type`, refers to
		 * the type of transaction.
		 *
		 * @param array $data Transaction data to be inserted.
		 * @param string $data_arr Sanitized transaction item data.
		 * @param Transaction $transaction Transaction object.
		 *
		 * @since 1.2.1
		 */
		do_action( "eaccounting_pre_insert_transaction_{$this->type}", $data, $data_arr, $this );

		if ( false === $wpdb->insert( $wpdb->prefix . 'ea_transactions', $data, $format ) ) {
			return new \WP_Error( 'db_insert_error', __( 'Could not insert transaction into the database.', 'wp-ever-accounting' ), $wpdb->last_error );
		}

		$this->set_id( $wpdb->insert_id );

		/**
		 * Fires immediately after an transaction is inserted in the database.
		 *
		 * @param int $id Transaction id.
		 * @param array $data Transaction has been inserted.
		 * @param array $data_arr Sanitized transaction data.
		 * @param Transaction $transaction Transaction object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_insert_transaction', $this->id, $data, $data_arr, $this );

		/**
		 * Fires immediately after an transaction is inserted in the database.
		 *
		 * The dynamic portion of the hook name, `$this->type`, refers to
		 * the type of transaction.
		 *
		 * @param int $id Transaction id.
		 * @param array $data Transaction has been inserted.
		 * @param array $data_arr Sanitized transaction data.
		 * @param Transaction $transaction Transaction object.
		 *
		 * @since 1.2.1
		 */
		do_action( "eaccounting_insert_transaction_{$this->type}", $this->id, $data, $data_arr, $this );

		return true;
	}

	/**
	 *  Update an object in the database.
	 *
	 * This method is not meant to call publicly instead call save
	 * which will conditionally decide which method to call.
	 *
	 * @param array $args An array of arguments for internal use case.
	 *
	 * @return \WP_Error|true True on success, WP_Error on failure.
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 * @since 1.1.0
	 */
	protected function update( $args = [] ) {
		global $wpdb;
		$changes = $this->get_changes();
		$data    = wp_array_slice_assoc( $changes, array_keys( $this->data_type ) );
		$format  = wp_array_slice_assoc( $this->data_type, array_keys( $data ) );
		$data    = wp_unslash( $data );
		// Bail if nothing to save
		if ( empty( $data ) ) {
			return true;
		}

		/**
		 * Fires immediately before an existing transaction is updated in the database.
		 *
		 * @param int $id Transaction id.
		 * @param array $data Transaction data.
		 * @param array $changes The data will be updated.
		 * @param Transaction $transaction Transaction object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_update_transactions', $this->get_id(), $this->to_array(), $changes, $this );

		/**
		 * Fires immediately before an existing transaction is updated in the database.
		 *
		 * The dynamic portion of the hook name, `$this->type`, refers to
		 * the type of transaction.
		 *
		 * @param int $id Transaction id.
		 * @param array $data Transaction data.
		 * @param array $changes The data will be updated.
		 * @param Transaction $transaction Transaction object.
		 *
		 * @since 1.2.1
		 */
		do_action( "eaccounting_pre_update_transactions_{$this->type}", $this->get_id(), $this->to_array(), $changes, $this );

		if ( false === $wpdb->update( $wpdb->prefix . 'ea_transactions', $data, [ 'id' => $this->get_id() ], $format, ['id' => '%d'] ) ) {
			return new \WP_Error( 'db_update_error', __( 'Could not update transaction in the database.', 'wp-ever-accounting' ), $wpdb->last_error );
		}

		/**
		 * Fires immediately after an existing transaction is updated in the database.
		 *
		 * @param int $id Transaction id.
		 * @param array $data Transaction data.
		 * @param array $changes The data will be updated.
		 * @param Transaction $transaction Transaction object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_update_transaction', $this->get_id(), $this->to_array(), $changes, $this );

		/**
		 * Fires immediately after an existing transaction is updated in the database.
		 *
		 * The dynamic portion of the hook name, `$this->type`, refers to
		 * the type of transaction.
		 *
		 * @param int $id Transaction id.
		 * @param array $data Transaction data.
		 * @param array $changes The data will be updated.
		 * @param Transaction $transaction Transaction object.
		 *
		 * @since 1.2.1
		 */
		do_action( "eaccounting_update_transaction_{$this->type}", $this->get_id(), $this->to_array(), $changes, $this );

		return true;
	}

	/**
	 * Saves an object in the database.
	 *
	 * @return \WP_Error|true True on success, WP_Error on failure.
	 * @since 1.1.0
	 */
	public function save() {
		$user_id = get_current_user_id();

		// Check required
		if ( empty( (int) $this->get_prop( 'account_id' ) ) ) {
			return new \WP_Error( 'empty_transaction_account_id', esc_html__( 'Transaction associated account is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_prop( 'payment_date' ) ) ) {
			return new \WP_Error( 'empty_transaction_payment_date', esc_html__( 'Transaction payment date is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_prop( 'type' ) ) ) {
			return new \WP_Error( 'empty_transaction_type', esc_html__( 'Transaction type is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_prop( 'type' ) ) ) {
			return new \WP_Error( 'empty_transaction_payment_method', esc_html__( 'Transaction payment method is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_prop( 'date_created' ) ) || '0000-00-00 00:00:00' === $this->get_prop( 'date_created' ) ) {
			$this->set_date_prop( 'date_created', current_time( 'mysql' ) );
		}

		if ( empty( $this->get_prop( 'creator_id' ) ) ) {
			$this->set_prop( 'creator_id', $user_id );
		}

		$changes = $this->get_changes();
		if ( array_key_exists( 'account_id', $changes ) || ! $this->exists() ) {
			$account = new Account( $this->account_id );
			if ( ! $account->exists() || empty( $account->currency_code ) ) {
				return new \WP_Error( 'invalid_transaction_account_id', esc_html__( 'Transaction associated account does not exist', 'wp-ever-accounting' ) );
			}

			$currency = new Currency( $account->currency_code );
			if ( ! $currency->exists() || empty( $currency->rate ) ) {
				return new \WP_Error( 'invalid_transaction_account_currency', esc_html__( 'Transaction associated account currency does not exist', 'wp-ever-accounting' ) );
			}

			$this->set_prop( 'currency_code', $account->currency_code );
			$this->set_prop( 'currency_rate', $currency->rate );
		}

		if ( $this->exists() ) {
			$is_error = $this->update();
		} else {
			$is_error = $this->insert();
		}

		if ( is_wp_error( $is_error ) ) {
			return $is_error;
		}

		$this->save_meta_data();
		$this->apply_changes();

		// Clear cache.
		wp_cache_delete( $this->get_id(), 'ea_transactions' );
		wp_cache_delete( $this->get_id(), 'ea_transactionmeta' );
		wp_cache_set( 'last_changed', microtime(), 'ea_transactions' );

		/**
		 * Fires immediately after a transaction is inserted or updated in the database.
		 *
		 * @param int $transaction_id Transaction id.
		 * @param Transaction $transaction Transaction object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_saved_transaction', $this->get_id(), $this );

		/**
		 * Fires immediately after a transaction is inserted or updated in the database.
		 *
		 * The dynamic portion of the hook name, `$this->type`, refers to
		 * the type of transaction.
		 *
		 * @param int $transaction_id Transaction id.
		 * @param Transaction $transaction Transaction object.
		 *
		 * @since 1.2.1
		 */
		do_action( "eaccounting_saved_transaction_{$this->type}", $this->get_id(), $this );

		return $this->exists();
	}

	/**
	 * Deletes the object from database.
	 *
	 * @return array|false true on success, false on failure.
	 * @since 1.1.0
	 */
	public function delete() {
		global $wpdb;
		if ( ! $this->exists() ) {
			return false;
		}

		$data = $this->to_array();

		/**
		 * Filters whether an transaction delete should take place.
		 *
		 * @param bool|null $delete Whether to go forward with deletion.
		 * @param int $transaction_id Transaction id.
		 * @param array $data Transaction data array.
		 * @param Transaction $transaction Transaction object.
		 *
		 * @since 1.2.1
		 */
		$check = apply_filters( 'eaccounting_pre_delete_transaction', null, $this->get_id(), $data, $this );
		if ( null !== $check ) {
			return $check;
		}

		/**
		 * Fires before an transaction is deleted.
		 *
		 * @param int $transaction_id Transaction id.
		 * @param array $data Transaction data array.
		 * @param Transaction $transaction Transaction object.
		 *
		 * @since 1.2.1
		 *
		 * @see eaccounting_delete_transaction()
		 */
		do_action( 'eaccounting_before_delete_transaction', $this->get_id(), $data, $this );

		$result = $wpdb->delete( $wpdb->prefix . 'ea_transactions', array( 'id' => $this->get_id() ) );
		if ( ! $result ) {
			return false;
		}

		/**
		 * Fires after an transaction is deleted.
		 *
		 * @param int $transaction_id Transaction id.
		 * @param array $data Transaction data array.
		 *
		 * @since 1.2.1
		 *
		 * @see eaccounting_delete_transaction()
		 */
		do_action( 'eaccounting_delete_transaction', $this->get_id(), $data );

		// Clear object.
		wp_cache_delete( $this->get_id(), 'ea_transactions' );
		wp_cache_set( 'last_changed', microtime(), 'ea_transactions' );
		$this->set_id( 0 );
		$this->set_defaults();

		return $data;
	}


	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	|
	| Functions for setting item data. These should not update anything in the
	| database itself and should only change what is stored in the class
	| object.
	*/

	/**
	 * Set contact's email.
	 *
	 * @param string $value Email.
	 *
	 * @since 1.0.2
	 */
	public function set_type( $value ) {
		if ( array_key_exists( $value, eaccounting_get_transaction_types() ) ) {
			$this->set_prop( 'type', $value );
		}
	}

	/**
	 * Set transaction paid.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 */
	public function set_payment_date( $value ) {
		$this->set_date_prop( 'payment_date', $value, 'Y-m-d' );
	}

	/**
	 * Set transaction amount.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 */
	public function set_amount( $value ) {
		$this->set_prop( 'amount', (float) $value );
	}

	/**
	 * Set currency code.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 */
	public function set_currency_code( $value ) {
		$this->set_prop( 'currency_code', eaccounting_sanitize_currency_code( $value ) );
	}

	/**
	 * Set currency rate.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 */
	public function set_currency_rate( $value ) {
		$this->set_prop( 'currency_rate', (float) $value );
	}

	/**
	 * Set account id.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 */
	public function set_account_id( $value ) {
		$this->set_prop( 'account_id', absint( $value ) );
	}

	/**
	 * Set invoice id.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 */
	public function set_invoice_id( $value ) {
		$this->set_prop( 'invoice_id', absint( $value ) );
	}

	/**
	 * Set contact id.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 */
	public function set_contact_id( $value ) {
		$this->set_prop( 'contact_id', absint( $value ) );
	}

	/**
	 * Set category id.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 */
	public function set_category_id( $value ) {
		$this->set_prop( 'category_id', absint( $value ) );
	}

	/**
	 * Set description.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 */
	public function set_description( $value ) {
		$this->set_prop( 'description', eaccounting_clean( $value ) );
	}

	/**
	 * Set payment method.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 */
	public function set_payment_method( $value ) {
		if ( array_key_exists( $value, eaccounting_get_payment_methods() ) ) {
			$this->set_prop( 'payment_method', $value );
		}
	}

	/**
	 * Set reference.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 */
	public function set_reference( $value ) {
		$this->set_prop( 'reference', eaccounting_clean( $value ) );
	}

	/**
	 * Set attachment.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 */
	public function set_attachment_id( $value ) {
		$this->set_prop( 'attachment_id', intval( $value ) );
	}

	/**
	 * Set parent id.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 */
	public function set_parent_id( $value ) {
		$this->set_prop( 'parent_id', absint( $value ) );
	}

	/**
	 * Set if reconciled.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 */
	public function set_reconciled( $value ) {
		$this->set_prop( 'reconciled', absint( $value ) );
	}

	/**
	 * Set object creator id.
	 *
	 * @param int $creator_id Creator id
	 *
	 * @since 1.0.2
	 */
	public function set_creator_id( $creator_id = null ) {
		if ( null === $creator_id ) {
			$creator_id = get_current_user_id();
		}
		$this->set_prop( 'creator_id', absint( $creator_id ) );
	}

	/**
	 * Set object created date.
	 *
	 * @param string
	 *
	 * @since 1.0.2
	 */
	public function set_date_created( $date = null ) {
		if ( null === $date ) {
			$date = current_time( 'mysql' );
		}
		$this->set_date_prop( 'date_created', $date );
	}


}
