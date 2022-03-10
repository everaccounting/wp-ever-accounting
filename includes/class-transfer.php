<?php
/**
 * Transfer data handler class.
 *
 * @version     1.0.2
 * @package     EverAccounting
 * @class       Transfer
 */

namespace EverAccounting;

defined( 'ABSPATH' ) || exit;

/**
 * Transfer class.
 */
class Transfer extends Data {
	/**
	 * This is the name of this object type.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	protected $object_type = 'transfer';

	/**
	 * Table name.
	 *
	 * @since 1.1.3
	 * @var string
	 */
	protected $table = 'ea_transfers';

	/**
	 * Meta type.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	protected $meta_type = false;

	/**
	 * Cache group.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	protected $cache_group = 'ea_transfers';


	/**
	 * Core data for this object. Name value pairs (name + default value).
	 *
	 * @since 1.1.3
	 * @var array
	 */
	protected $core_data = [
		'date'            => null,
		'from_account_id' => null,
		'amount'          => null,
		'to_account_id'   => null,
		'income_id'       => null,
		'expense_id'      => null,
		'payment_method'  => null,
		'reference'       => null,
		'description'     => null,
		'creator_id'      => '1',
		'date_created'    => null,
	];

	protected $category_id;

	/**
	 * Contact constructor.
	 *
	 * @param int|transfer|object|null $this transfer instance.
	 *
	 * @since 1.0.0
	 */
	public function __construct(  $transfer = 0 ) {
		// Call early so default data is set.
		parent::__construct();

		if ( is_numeric( $transfer ) && $transfer > 0 ) {
			$this->set_id( $transfer );
		} elseif ( $transfer instanceof self ) {
			$this->set_id( absint( $transfer->get_id() ) );
		} elseif ( ! empty( $transfer->ID ) ) {
			$this->set_id( absint( $transfer->ID ) );
		} else {
			$this->set_object_read( true );
		}

		$this->read();
	}

	/**
	 * Get transfer category.
	 *
	 * @since 1.1.0
	 *
	 * @return integer
	 */
	public function get_category_id() {
		return absint( $this->category_id );
	}

	/**
	 * Set income id.
	 *
	 * @since 1.0.2
	 *
	 * @param int $value income_id.
	 */
	public function set_income_id( $value ) {
		$this->set_prop( 'income_id', absint( $value ) );
	}

	/**
	 * Set expense id.
	 *
	 * @since 1.0.2
	 *
	 * @param int $value expense_id.
	 */
	public function set_expense_id( $value ) {
		$this->set_prop( 'expense_id', absint( $value ) );
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	|
	| Methods which create, read, update and delete documents from the database.
	| Written in abstract fashion so that the way documents are stored can be
	| changed more easily in the future.
	|
	| A save method is included for convenience (chooses update or create based
	| on if the order exists yet).
	|
	*/
	/**
	 *  Create an item in the database.
	 *
	 * This method is not meant to call publicly instead call save
	 * which will conditionally decide which method to call.
	 *
	 * @since 1.0.0
	 * @return \WP_Error|true True on success, WP_Error on failure.
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 */
	protected function create() {
		global $wpdb;

		$data = wp_unslash( $this->get_core_data() );

		/**
		 * Fires immediately before an item is inserted in the database.
		 *
		 * @param array $data Data data to be inserted.
		 * @param string $data_arr Sanitized item data.
		 * @param Data $item Data object.
		 *
		 * @since 1.0.0
		 */
		do_action( 'eaccounting_pre_insert_' . $this->object_type, $data, $this->get_data(), $this );
		$from_account = new Account( $data['from_account_id'] );
		$to_account   = new Account( $data['to_account_id'] );

		$expense = new Payment();
		$expense->set_props(
			array(
				'account_id'     => $data['from_account_id'],
				'payment_date'   => $data['date'],
				'amount'         => $data['amount'],
				'description'    => !empty( $data['description'] ) ? $data['description'] : '',
				'category_id'    => $this->category_id,
				'payment_method' => $data['payment_method'],
				'reference'      => !empty($data['reference']) ? $data['reference']:'',
			)
		);
		$expense->save();
		$transfer_data['expense_id'] = $expense->get_id();
		$amount = $data['amount'];

		if ( $from_account->get_currency_code() !== $to_account->get_currency_code() ) {
			$expense_currency = eaccounting_get_currency( $from_account->get_currency_code() );
			$income_currency  = eaccounting_get_currency( $to_account->get_currency_code() );
			$amount           = eaccounting_price_convert( $amount, $from_account->get_currency_code(), $to_account->get_currency_code(), $expense_currency->get_rate(), $income_currency->get_rate() );
		}

		$income = new Revenue();
		$income->set_props(
			array(
				'account_id'     => $data['to_account_id'],
				'payment_date'   => $data['date'],
				'amount'         => $amount,
				'description'    => !empty( $data['description'] ) ? $data['description'] : '',
				'category_id'    => $this->category_id,
				'payment_method' => $data['payment_method'],
				'reference'      => !empty($data['reference']) ? $data['reference']:'',
			)
		);
		$income->save();

		$transfer_data['income_id'] = $income->get_id();
		$transfer_data['date_created'] = $data['date_created'];



		if ( false === $wpdb->insert( $wpdb->prefix. $this->table, $transfer_data, array() ) ) {
			return new \WP_Error( 'db_insert_error', __( 'Could not insert item into the database.', 'wp-ever-accounting' ), $wpdb->last_error );
		}

		$this->set_id( $wpdb->insert_id );
		$this->set_income_id( $income->get_id());
		$this->set_expense_id( $expense->get_id());
		$this->update_meta_data();
		$this->apply_changes();

		/**
		 * Fires immediately after an item is inserted in the database.
		 *
		 * @param array $data Data data to be inserted.
		 * @param string $data_arr Sanitized item data.
		 * @param Data $item Data object.
		 *
		 * @since 1.0.0
		 */
		do_action( 'eaccounting_insert_'. $this->object_type, $this->get_id(), $data, $this->get_data(), $this );

		return $this->exists();
	}

	/**
	 * Retrieve the object from database instance.
	 *
	 * @since 1.0.0
	 *
	 * @return object|false Object, false otherwise.
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 */
	protected function read() {
		global $wpdb;

		$this->set_defaults();
		// Bail early if no id is set
		if ( ! $this->get_id() ) {
			return false;
		}

		$data = wp_cache_get( $this->get_id(), $this->cache_group );

		if ( false === $data ) {
			$data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}{$this->table} WHERE id = %d LIMIT 1;", $this->get_id() ) ); // WPCS: cache ok, DB call ok.
			wp_cache_add( $this->get_id(), $data, $this->cache_group );
		}

		if ( ! $data ) {
			$this->set_id( 0 );
			return  false;
		}


		try {
			$income = Transactions::get_revenue( $data->income_id );
			$expense = Transactions::get_payment( $data->expense_id );
			if ( $income ) {
				$this->set_to_account_id( $income->get_account_id() );
			}
			if( $expense ) {
				$this->set_from_account_id( $expense->get_account_id() );
				$this->set_amount( $expense->get_amount() );
				$this->set_date( $expense->get_payment_date() );
				$this->set_payment_method( $expense->get_payment_method() );
				$this->set_description( $expense->get_description() );
				$this->set_reference( $expense->get_reference() );
			}

			$this->set_props ( $data );
			$this->read_meta_data();
			$this->set_object_read( true );
			do_action( 'eaccounting_read_' . $this->object_type . '_item', $this->get_id(), $this );

		} catch( \Exception $e ) {
			throw new \Exception( $e->getMessage() );
		}


		return $data;
	}

	/**
	 *  Update an object in the database.
	 *
	 * This method is not meant to call publicly instead call save
	 * which will conditionally decide which method to call.
	 *
	 * @since 1.0.0
	 * @return \WP_Error|true True on success, WP_Error on failure.
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 */
	protected function update() {
		global $wpdb;
		$changes = $this->get_changes();

		// Bail if nothing to save
		if ( empty( $changes ) ) {
			return true;
		}

		/**
		 * Fires immediately before an existing item is updated in the database.
		 *
		 * @param int $id Data id.
		 * @param array $data Data data.
		 * @param array $changes The data will be updated.
		 * @param Data $item Data object.
		 *
		 * @since 1.0.0
		 */
		do_action( 'eaccounting_pre_update_' . $this->object_type, $this->get_id(), $this->get_data(), $changes, $this );
		$this->date_updated = current_time( 'mysql' );
		$data               = wp_unslash( $this->get_core_data() );

		$from_account = new Account( $data['from_account_id'] );
		$to_account   = new Account( $data['to_account_id'] );

		$expense = new Payment( $data['expense_id']);
		$expense->set_props(
			array(
				'account_id'     => $data['from_account_id'],
				'payment_date'   => $data['date'],
				'amount'         => $data['amount'],
				'description'    => !empty( $data['description'] ) ? $data['description'] : '',
				'category_id'    => $this->category_id,
				'payment_method' => $data['payment_method'],
				'reference'      => !empty($data['reference']) ? $data['reference']:'',
			)
		);
		$expense->save();
		$transfer_data['expense_id'] = $expense->get_id();
		$amount = $data['amount'];

		if ( $from_account->get_currency_code() !== $to_account->get_currency_code() ) {
			$expense_currency = eaccounting_get_currency( $from_account->get_currency_code() );
			$income_currency  = eaccounting_get_currency( $to_account->get_currency_code() );
			$amount           = eaccounting_price_convert( $amount, $from_account->get_currency_code(), $to_account->get_currency_code(), $expense_currency->get_rate(), $income_currency->get_rate() );
		}

		$income = new Revenue( $data['income_id']);
		$income->set_props(
			array(
				'account_id'     => $data['to_account_id'],
				'payment_date'   => $data['date'],
				'amount'         => $amount,
				'description'    => !empty( $data['description'] ) ? $data['description'] : '',
				'category_id'    => $this->category_id,
				'payment_method' => $data['payment_method'],
				'reference'      => !empty($data['reference']) ? $data['reference']:'',
			)
		);
		$income->save();

		$transfer_data['income_id'] = $income->get_id();
		$transfer_data['date_created'] = $data['date_created'];

		if( false === $wpdb->update( $wpdb->prefix . $this->table, $transfer_data, [ 'id' => $this->get_id() ] , array(), ['id' => '%d'])) {
			return new \WP_Error( 'db_update_error', __( 'Could not update transfer in the database.', 'wp-ever-accounting' ), $wpdb->last_error );
		}

		$this->update_meta_data();

		/**
		 * Fires immediately after an existing item is updated in the database.
		 *
		 * @param int $id Transfer id.
		 * @param array $data Transfer data.
		 * @param array $changes Transfer data will be updated.
		 * @param Data $item Data object.
		 *
		 * @since 1.0.0
		 */
		do_action( 'eaccounting_update_' . $this->object_type, $this->get_id(), $this->get_data(), $changes, $this );

		return true;
	}

	/**
	 * Deletes the object from database.
	 *
	 * @param array $args Array of args to pass to the delete method.
	 *
	 * @since 1.0.0
	 * @return array|false true on success, false on failure.
	 */
	public function delete( $args = array() ) {
		if ( ! $this->exists() ) {
			return false;
		}

		$data = $this->get_data();

		/**
		 * Filters whether an item delete should take place.
		 *
		 * @param bool|null $delete Whether to go forward with deletion.
		 * @param int $id Data id.
		 * @param array $data Data data array.
		 * @param Data $item Data object.
		 *
		 * @since 1.0.0
		 */
		$check = apply_filters( 'eaccounting_check_delete_' . $this->object_type, null, $this->get_id(), $data, $this );
		if ( null !== $check ) {
			return $check;
		}

		/**
		 * Fires before an item is deleted.
		 *
		 * @param int $id Data id.
		 * @param array $data Data data array.
		 * @param Data $item Data object.
		 *
		 * @since 1.0.0
		 */
		do_action( 'eaccounting_pre_delete_'. $this->object_type, $this->get_id(), $data, $this );

		global $wpdb;
		$wpdb->delete(
			$wpdb->prefix . 'ea_transactions',
			array(
				'id' => $this->get_income_id()
			),
			array( '%d' )
		);

		$wpdb->delete(
			$wpdb->prefix . 'ea_transactions',
			array(
				'id' => $this->get_expense_id()
			),
			array( '%d' )
		);

		$wpdb->delete(
			$wpdb->prefix . $this->table,
			array(
				'id' => $this->get_id(),
			),
			array( '%d')
		);

		/**
		 * Fires after a item is deleted.
		 *
		 * @param int $id Data id.
		 * @param array $data Data data array.
		 *
		 * @since 1.0.0
		 */
		do_action( 'eaccounting_delete_' . $this->object_type, $this->get_id(), $data );

		wp_cache_delete( $this->get_id(), $this->cache_group );
		wp_cache_set( 'last_changed', microtime(), $this->cache_group );
		$this->set_defaults();

		return $data;
	}


	/**
	 * Saves an object in the database.
	 *
	 * @return \WP_Error|int id on success, WP_Error on failure.
	 * @since 1.1.3
	 */
	public function save() {
		// check if anything missing before save.
		if ( ! $this->is_date_valid( $this->date_created ) ) {
			$this->date_created = current_time( 'mysql' );
		}

		$requires = [ 'date','from_account_id', 'to_account_id', 'amount', 'payment_method' ];

		foreach ( $requires as $required ) {
			if ( empty( $this->$required ) ) {
				return new \WP_Error( 'missing_required_param', sprintf( __( '%s is required', 'wp-ever-accounting' ), $required ) );
			}
		}

		if( ! $this->get_from_account_id() || ! $this->get_to_account_id() ) {
			throw new \Exception( __( 'Transfer from and to account can not be same.', 'wp-ever-accounting' ) );
		}

		$this->maybe_set_transfer_category();

		if ( ! $this->exists() ) {
			$is_error = $this->create();
		} else {
			$is_error = $this->update();
		}

		if ( is_wp_error( $is_error ) ) {
			return $is_error;
		}

		$this->apply_changes();

		// Clear cache.
		wp_cache_delete( $this->get_id(), $this->cache_group );
		wp_cache_set( 'last_changed', microtime(), $this->cache_group );

		/**
		 * Fires immediately after a contact is inserted or updated in the database.
		 *
		 * @param int $id Transfer id.
		 * @param array $data Transfer data array.
		 * @param Contact $this Transfer object.
		 *
		 * @since 1.0.0
		 */
		do_action( 'eaccounting_saved_' . $this->object_type, $this->get_id(), $this );

		return $this->get_id();
	}

	/**
	 * Get formatted transaction amount.
	 *
	 * @since 1.0.2
	 *
	 * @return string
	 */
	public function get_formatted_amount() {
		return eaccounting_format_price( $this->get_amount(), $this->get_currency_code() );
	}

	/**
	 * Set transfer category.
	 *
	 * @since 1.1.0
	 *
	 * @throws \Exception
	 */
	protected function maybe_set_transfer_category() {
		global $wpdb;
		$cache_key   = md5( 'other' . __( 'Transfer', 'wp-ever-accounting' ) );
		$category_id = wp_cache_get( $cache_key, 'ea_categories' );
		if ( false === $category_id ) {
			$category_id = $wpdb->get_var( $wpdb->prepare( "SELECT id from {$wpdb->prefix}ea_categories WHERE type=%s AND name=%s", 'other', __( 'Transfer', 'wp-ever-accounting' ) ) );
			wp_cache_add( $cache_key, $category_id, 'eaccounting_categories' );
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
