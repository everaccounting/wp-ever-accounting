<?php
/**
 * Account data handler class.
 *
 * @version     1.0.2
 * @package     Ever_Accounting
 * @class       Account
 */

namespace Ever_Accounting;

defined( 'ABSPATH' ) || exit;

/**
 * Account class.
 */
class Account extends Abstracts\Data {
	/**
	 * This is the name of this object type.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	protected $object_type = 'account';

	/**
	 * Table name.
	 *
	 * @since 1.1.3
	 * @var string
	 */
	protected $table = 'ea_accounts';

	/**
	 * Cache group.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	protected $cache_group = 'ea_accounts';


	/**
	 * Core data for this object. Name value pairs (name + default value).
	 *
	 * @since 1.1.3
	 * @var array
	 */
	protected $core_data = [
		'name'            => '',
		'number'          => '',
		'opening_balance' => 0.0000,
		'bank_name'       => '',
		'bank_phone'      => '',
		'bank_address'    => '',
		'currency_code'   => '',
		'thumbnail_id'    => null,
		'enabled'         => 1,
		'creator_id'      => null,
		'date_created'    => null,
	];

	protected $balance = null;

	/**
	 * Account constructor.
	 *
	 * @param int|Account|object|null $account account instance.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $account = 0 ) {
		// Call early so default data is set.
		parent::__construct();

		if ( is_numeric( $account ) && $account > 0 ) {
			$this->set_id( $account );
		} elseif ( $account instanceof self ) {
			$this->set_id( absint( $account->get_id() ) );
		} elseif ( ! empty( $account->ID ) ) {
			$this->set_id( absint( $account->ID ) );
		} else {
			$this->set_object_read( true );
		}

		$this->read();
	}

	/**
	 * Get balance.
	 *
	 * @since 1.0.2
	 *
	 * @since 1.1.0
	 * @return float|string
	 *
	 */
	public function get_balance() {
		if ( null !== $this->balance ) {
			return $this->balance;
		}
		global $wpdb;
		$transaction_total = (float) $wpdb->get_var(
			$wpdb->prepare( "SELECT SUM(CASE WHEN type='income' then amount WHEN type='expense' then - amount END) as total from {$wpdb->prefix}ea_transactions WHERE account_id=%d", $this->get_id() )
		);
		$balance           = $this->get_opening_balance() + $transaction_total;
		$this->set_balance( $balance );

		return $balance;
	}

	/**
	 * Set balance.
	 *
	 * @param $balance
	 *
	 * @since 1.1.0
	 *
	 */
	protected function set_balance( $balance ) {
		$this->balance = $balance;
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
	 * @since 1.1.3
	 * @return \WP_Error|int id on success, WP_Error on failure.
	 */
	public function save() {
		// check if anything missing before save.
		if ( ! $this->is_date_valid( $this->date_created ) ) {
			$this->date_created = current_time( 'mysql' );
		}

		$requires = [ 'name', 'number', 'currency_code' ];
		foreach ( $requires as $required ) {
			if ( empty( $this->$required ) ) {
				return new \WP_Error( 'missing_required_params', sprintf( __( 'Account %s is required.', 'wp-ever-accounting' ), $required ) );
			}
		}

		$duplicate = Accounts::get( $this->number );

		if ( $duplicate && $duplicate->exists() && $duplicate->get_id() !== $this->get_id() ) {
			return new \WP_Error( 'duplicate_account', __( 'Account already exists.', 'wp-ever-accounting' ) );
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
		 * Fires immediately after a account is inserted or updated in the database.
		 *
		 * @param int $id Account id.
		 * @param array $data Account data array.
		 * @param Account $account Account object.
		 *
		 * @since 1.0.0
		 */
		do_action( 'ever_accounting_saved_' . $this->object_type, $this->get_id(), $this );

		return $this->get_id();
	}
}
