<?php
/**
 * Transfer data handler class.
 *
 * @version     1.0.2
 * @package     Ever_Accounting
 * @class       Transfer
 */

namespace Ever_Accounting;

defined( 'ABSPATH' ) || exit;

/**
 * Transfer class.
 */
class Transfer extends Abstracts\Data  {
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
		'creator_id'      => null,
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

		$this->maybe_set_category();

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
		return \Ever_Accounting\Helpers\Price::format_price( $this->get_amount(), $this->get_currency_code() );
	}

	/**
	 * Set transfer category.
	 *
	 * @since 1.1.0
	 *
	 * @throws \Exception
	 */
	protected function maybe_set_category() {
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
