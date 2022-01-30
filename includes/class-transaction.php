<?php
/**
 * Transaction data handler class.
 *
 * @version     1.0.2
 * @package     EverAccounting
 * @class       Contact
 */

namespace EverAccounting;

defined( 'ABSPATH' ) || exit;

/**
 * Transaction class.
 */
class Transaction extends Data {
	/**
	 * This is the name of this object type.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	protected $object_type = 'transaction';

	/**
	 * Table name.
	 *
	 * @since 1.1.3
	 * @var string
	 */
	protected $table = 'ea_transactions';

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
	protected $cache_group = 'ea_transactions';


	/**
	 * Core data for this object. Name value pairs (name + default value).
	 *
	 * @since 1.1.3
	 * @var array
	 */
	protected $core_data = [
		'type'           => '',
		'payment_date'   => '',
		'amount'         => '',
		'currency_code'  => '', // protected
		'currency_rate'  => '', // protected
		'account_id'     => '',
		'document_id'    => '',
		'contact_id'     => '',
		'category_id'    => '',
		'description'    => '',
		'payment_method' => '',
		'reference'      => '',
		'attachment_id'  => '',
		'parent_id'      => '',
		'reconciled'     => '',
		'creator_id'     => '',
		'date_created'   => '',
	];

	/**
	 * Transaction constructor.
	 *
	 * @param int|contact|object|null $transaction transaction instance.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $transaction = 0 ) {
		// Call early so default data is set.
		parent::__construct();

		if ( is_numeric( $transaction ) && $transaction > 0 ) {
			$this->set_id( $transaction );
		} elseif ( $transaction instanceof self ) {
			$this->set_id( absint( $transaction->get_id() ) );
		} elseif ( ! empty( $transaction->ID ) ) {
			$this->set_id( absint( $transaction->ID ) );
		} else {
			$this->set_object_read( true );
		}

		$this->read();
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

		$requires = [ 'type', 'payment_date', 'account_id', 'category_id', 'payment_method' ];

		foreach ( $requires as $required ) {
			if ( empty( $this->$required ) ) {
				return new \WP_Error( 'missing_required_param', sprintf( __( '%s is required', 'wp-ever-accounting' ), $required ) );
			}
		}

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
		 * @param int $id Contact id.
		 * @param array $data Contact data array.
		 * @param Contact $contact Contact object.
		 *
		 * @since 1.0.0
		 */
		do_action( 'eaccounting_saved_' . $this->object_type, $this->get_id(), $this );

		return $this->get_id();
	}

	/**
	 * Set contact type.
	 *
	 * @param $type
	 *
	 * @since 1.0.2
	 *
	 */
	protected function set_type( $type ) {
		if ( array_key_exists( $type, Transactions::get_transaction_types() ) ) {
			$this->set_prop( 'type', $type );
		}
	}
}
