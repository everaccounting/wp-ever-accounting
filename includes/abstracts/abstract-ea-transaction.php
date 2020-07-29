<?php
/**
 * Handle the transaction object
 *
 * @class       EAccounting_Transaction
 * @version     1.0.0
 * @package     EverAccounting/Classes
 */

defined( 'ABSPATH' ) || exit();

/**
 * Class EAccounting_Transaction
 * @since 1.0.2
 */
class EAccounting_Transaction extends EAccounting_Object {
	/**
	 * Transaction Data array.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $data = array(
		'type'           => '',
		'paid_at'        => null,
		'amount'         => null,
		'currency_code'  => 'USD',
		'currency_rate'  => 1,
		'account_id'     => null,
		'invoice_id'     => null,
		'contact_id'     => null,
		'category_id'    => null,
		'description'    => '',
		'payment_method' => '',
		'reference'      => '',
		'file_id'        => '',
		'parent_id'      => null,
		'reconciled'     => 0,
		'creator_id'     => '',
		'company_id'     => '',
		'date_created'   => '',
	);


	/**
	 * EAccounting_Transaction constructor.
	 *
	 * @param mixed $data
	 */
	public function __construct( $data = 0 ) {
		parent::__construct( $data );

		if ( is_numeric( $data ) && $data > 0 ) {
			$this->set_id( $data );
		} elseif ( $data instanceof self ) {
			$this->set_id( $data->get_id() );
		} elseif ( ! empty( $data->id ) ) {
			$this->set_id( $data->id );
		} else {
			$this->set_id( 0 );
		}

		if ( $this->get_id() > 0 ) {
			$this->read( $this->get_id() );
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/

	public function get_type( $context = 'view' ) {
		return $this->get_prop( 'type', $context );
	}

	public function get_paid_at( $context = 'view' ) {
		return $this->get_prop( 'paid_at', $context );
	}

	public function get_amount( $context = 'view' ) {
		return $this->get_prop( 'amount', $context );
	}

	public function get_currency_code( $context = 'view' ) {
		return $this->get_prop( 'currency_code', $context );
	}

	public function get_currency_rate( $context = 'view' ) {
		return $this->get_prop( 'currency_rate', $context );
	}

	public function get_account_id( $context = 'view' ) {
		return $this->get_prop( 'account_id', $context );
	}

	public function get_invoice_id( $context = 'view' ) {
		return $this->get_prop( 'invoice_id', $context );
	}

	public function get_contact_id( $context = 'view' ) {
		return $this->get_prop( 'contact_id', $context );
	}

	public function get_category_id( $context = 'view' ) {
		return $this->get_prop( 'category_id', $context );
	}

	public function get_description( $context = 'view' ) {
		return $this->get_prop( 'description', $context );
	}

	public function get_payment_method( $context = 'view' ) {
		return $this->get_prop( 'payment_method', $context );
	}

	public function get_reference( $context = 'view' ) {
		return $this->get_prop( 'reference', $context );
	}

	public function get_file_id( $context = 'view' ) {
		return $this->get_prop( 'file_id', $context );
	}

	public function get_parent_id( $context = 'view' ) {
		return $this->get_prop( 'parent_id', $context );
	}

	public function get_reconciled( $context = 'view' ) {
		return (bool) $this->get_prop( 'reconciled', $context );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Set contact's email.
	 *
	 * @param string $value Email.
	 *
	 * @since 1.0.2
	 */
	public function set_type( $value ) {
		$this->set_prop( 'type', $value );
	}

	/**
	 * Set transaction paid.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 */
	public function set_paid_at( $value ) {
		$this->set_date_prop( 'paid_at', $value );
	}

	/**
	 * Set transaction amount.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 */
	public function set_amount( $value ) {
		$this->set_prop( 'amount', eaccounting_sanitize_price( $value, $this->get_currency_code( 'edit' ) ) );
	}

//	/**
//	 * Set currency code.
//	 *
//	 * @param $value
//	 *
//	 * @since 1.0.2
//	 */
//	public function set_currency_code( $value ) {
//		$this->set_prop( 'currency_code', eaccounting_clean( $value ) );
//	}
//
//	/**
//	 * Set currency rate.
//	 *
//	 * @param $value
//	 *
//	 * @since 1.0.2
//	 */
//	public function set_currency_rate( $value ) {
//		$this->set_prop( 'currency_rate', eaccounting_sanitize_number( $value, true ) );
//	}

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

	public function set_file_id( $value ) {
		$this->set_prop( 'file_id', absint( $value ) );
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

	/*
	|--------------------------------------------------------------------------
	| Crud
	|--------------------------------------------------------------------------
	*/

	/**
	 * Load item from database.
	 *
	 * @param int $id
	 *
	 * @throws Exception
	 */
	public function read( $id ) {
		$this->set_defaults();
		global $wpdb;

		// Get from cache if available.
		$item = 0 < $this->get_id() ? wp_cache_get( 'transaction-item-' . $this->get_id(), 'transactions' ) : false;
		if ( false === $item ) {
			$item = $wpdb->get_row(
				$wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ea_transactions WHERE id = %d;", $this->get_id() )
			);

			if ( 0 < $item->id ) {
				wp_cache_set( 'transaction-item-' . $item->id, $item, 'transactions' );
			}
		}

		if ( ! $item || ! $item->id ) {
			throw new Exception( __( 'Invalid transaction.', 'wp-ever-accounting' ) );
		}

		// Gets extra data associated with the order if needed.
		foreach ( $item as $key => $value ) {
			$function = 'set_' . $key;
			if ( is_callable( array( $this, $function ) ) ) {
				$this->{$function}( $value );
			} else {
				$this->set_prop( $key, $value );
			}
		}

		$this->set_object_read( true );
	}

	/**
	 * Validate the properties before saving the object
	 * in the database.
	 *
	 * @return void
	 * @since 1.0.2
	 */
	protected function validate_props() {
		if ( ! $this->get_date_created( 'edit' ) ) {
			$this->set_date_created( time() );
		}

		if ( ! $this->get_company_id( 'edit' ) ) {
			$this->set_company_id( 1 );
		}

		if ( ! $this->get_prop( 'creator_id' ) ) {
			$this->set_prop( 'creator_id', eaccounting_get_current_user_id() );
		}

		if ( ! $this->get_currency_code( 'edit' ) ) {
			$this->set_prop( 'currency_code', eaccounting_get_currency_code() );
		}

		if ( empty( $this->get_paid_at( 'edit' ) ) ) {
			throw new Exception( __( 'Paid date is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_category_id( 'edit' ) ) ) {
			throw new Exception( __( 'Category is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_payment_method( 'edit' ) ) ) {
			throw new Exception( __( 'Payment method is required', 'wp-ever-accounting' ) );
		}

		$account = eaccounting_get_account( $this->get_account_id( 'edit' ) );
		if ( ! $account || ! $account->exists() ) {
			throw new Exception( __( 'Account is required.', 'wp-ever-accounting' ) );
		}

		$currency = eaccounting_get_currency( $account->get_currency_code( 'edit' ), 'code' );
		if ( ! $currency || ! $currency->exists() ) {
			throw new Exception( __( 'Account associated currency does not exist.', 'wp-ever-accounting' ) );
		}

		$category = eaccounting_get_category( $this->get_category_id( 'edit' ) );
		if ( ! $category->exists() ) {
			throw new Exception( __( 'Category does not exist.', 'wp-ever-accounting' ) );
		}

		if ( ! in_array( $category->get_type( 'edit' ), [ 'expense', 'other', 'income' ] ) ) {
			throw new Exception( __( 'Invalid category type category type must be expense.', 'wp-ever-accounting' ) );
		}

		$contact = eaccounting_get_contact( $this->get_contact_id( 'edit' ) );
		if ( ! empty( $this->get_contact_id( 'edit' ) ) && ! $contact->exists() ) {
			throw new Exception( __( 'Contact does not exist.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_type( 'edit' ) ) ) {
			throw new Exception( __( 'Type is required', 'wp-ever-accounting' ) );
		}

		$this->set_prop( 'currency_code', $currency->get_code( 'edit' ) );
		$this->set_prop( 'currency_rate', $currency->get_rate( 'edit' ) );
		$this->set_prop( 'amount', eaccounting_sanitize_price( $this->get_amount( 'edit' ), $currency->get_code( 'edit' ) ) );
	}

	/**
	 * Create a new transaction in the database.
	 *
	 * @throws Exception
	 * @since 1.0.0
	 */
	public function create() {
		$this->validate_props();
		global $wpdb;
		$contact_data = array(
			'type'           => $this->get_type( 'edit' ),
			'paid_at'        => $this->get_paid_at( 'edit' )->format( 'Y-m-d H:i:s' ),
			'amount'         => $this->get_amount( 'edit' ),
			'currency_code'  => $this->get_currency_code( 'edit' ),
			'currency_rate'  => $this->get_currency_rate( 'edit' ),
			'account_id'     => $this->get_account_id( 'edit' ),
			'invoice_id'     => $this->get_invoice_id( 'edit' ),
			'contact_id'     => $this->get_contact_id( 'edit' ),
			'category_id'    => $this->get_category_id( 'edit' ),
			'description'    => $this->get_description( 'edit' ),
			'payment_method' => $this->get_payment_method( 'edit' ),
			'reference'      => $this->get_reference( 'edit' ),
			'file_id'        => '',
			'parent_id'      => $this->get_parent_id( 'edit' ),
			'reconciled'     => $this->get_reconciled( 'edit' ),
			'company_id'     => $this->get_company_id( 'edit' ),
			'creator_id'     => $this->get_prop( 'creator_id' ),
			'date_created'   => $this->get_date_created( 'edit' )->format( 'Y-m-d H:i:s' ),
		);

		do_action( 'eaccounting_pre_insert_transaction', $this->get_id(), $this );

		$data = wp_unslash( apply_filters( 'eaccounting_new_transaction_data', $contact_data ) );
		if ( false === $wpdb->insert( $wpdb->prefix . 'ea_transactions', $data ) ) {
			throw new Exception( $wpdb->last_error );
		}

		do_action( 'eaccounting_insert_transaction', $this->get_id(), $this );

		$this->set_id( $wpdb->insert_id );
		$this->apply_changes();
		$this->set_object_read( true );
	}

	/**
	 * Update a transaction in the database.
	 *
	 * @throws Exception
	 * @since 1.0.0
	 *
	 */
	public function update() {
		global $wpdb;

		$this->validate_props();
		$changes = $this->get_changes();

		if ( ! empty( $changes ) ) {
			do_action( 'eaccounting_pre_update_transaction', $this->get_id(), $changes );

			try {
				$wpdb->update( $wpdb->prefix . 'ea_transactions', $changes, array( 'id' => $this->get_id() ) );
			} catch ( Exception $e ) {
				throw new Exception( __( 'Could not update transaction.', 'wp-ever-accounting' ) );
			}

			do_action( 'eaccounting_update_transaction', $this->get_id(), $changes, $this->data );

			$this->apply_changes();
			$this->set_object_read( true );
			wp_cache_delete( 'transaction-item-' . $this->get_id(), 'transactions' );
		}
	}

	/**
	 * Conditionally save contact in the database
	 * if exist then update otherwise create.
	 *
	 * @return int|mixed
	 * @throws Exception
	 * @since 1.0.0
	 */
	public function save() {
		if ( $this->get_id() ) {
			$this->update();
		} else {
			$this->create();
		}

		return $this->get_id();
	}


	/**
	 * Remove a transaction from the database.
	 *
	 * @param array $args
	 *
	 * @since 1.0.
	 */
	public function delete( $args = array() ) {
		if ( $this->get_id() ) {
			global $wpdb;
			do_action( 'eaccounting_pre_delete_transaction', $this->get_id() );
			$wpdb->delete( $wpdb->prefix . 'ea_transactions', array( 'id' => $this->get_id() ) );
			do_action( 'eaccounting_delete_transaction', $this->get_id() );
			$this->set_id( 0 );
		}
	}
}
