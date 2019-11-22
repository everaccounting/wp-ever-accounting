<?php
defined( 'ABSPATH' ) || exit();

/**
 * Class EAccounting_Account
 */
class EAccounting_Account {
	/**
	 * @var
	 */
	protected $id;

	/**
	 * @var
	 */
	protected $name;

	/**
	 * @var
	 */
	protected $number;

	/**
	 * @var
	 */
	protected $opening_balance;

	/**
	 * @var
	 */
	protected $bank_name;

	/**
	 * @var
	 */
	protected $bank_phone;

	/**
	 * @var
	 */
	protected $bank_address;

	/**
	 * @var
	 */
	protected $status;

	/**
	 * @var
	 */
	protected $created_at;

	/**
	 * @var
	 */
	protected $updated_at;

	/**
	 * @var null
	 */
	public $account = null;

	/**
	 * EAccounting_Account constructor.
	 *
	 * @param int $account
	 */
	public function __construct( $account = 0 ) {
		$this->init( $account );
	}

	/**
	 * Init/load the account object. Called from the constructor.
	 *
	 * @param $account
	 *
	 * @since 1.0.0
	 */
	protected function init( $account ) {
		if ( is_numeric( $account ) ) {
			$this->id      = absint( $account );
			$this->account = eaccounting_get_account( $account );
			$this->get_account( $this->id );
		} elseif ( $account instanceof EAccounting_Account ) {
			$this->id      = absint( $account->id );
			$this->account = $account->account;
			$this->get_account( $this->id );
		} elseif ( isset( $account->id ) ) {
			$this->account = $account;
			$this->id      = absint( $this->account->id );
			$this->populate( $account );
		}
	}

	/**
	 * Gets an call from the database.
	 *
	 * @param int $id (default: 0).
	 *
	 * @return bool
	 */
	public function get_account( $id = 0 ) {

		if ( ! $id ) {
			return false;
		}

		if ( $account = eaccounting_get_account( $id ) ) {
			$this->populate( $account );

			return true;
		}

		return false;
	}

	/**
	 * Populates an call from the loaded post data.
	 *
	 * @param mixed $account
	 */
	public function populate( $account ) {
		$this->id = $account->id;
		foreach ( $account as $key => $value ) {
			$this->$key = $value;
		}
	}

	/**
	 * Magic __get function to dispatch a call to retrieve a private property
	 *
	 * @since 1.0.0
	 */
	public function __get( $key ) {
		if ( method_exists( $this, 'get_' . $key ) ) {
			return call_user_func( array( $this, 'get_' . $key ) );
		} else if ( property_exists( $this, $key ) ) {
			return $this->{$key};
		} else {
			return new \WP_Error( 'invalid-property', sprintf( __( 'Can\'t get property %s', 'wp-ever-accounting' ), $key ) );
		}

	}

	/**
	 * @return int
	 * @since 1.0.0
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * @return string
	 * @since 1.0.0
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * @return string
	 * @since 1.0.0
	 */
	public function get_number() {
		return $this->number;
	}

	/**
	 * @return string
	 * @since 1.0.0
	 */
	public function get_opening_balance() {
		return eaccounting_price( $this->opening_balance );
	}

	/**
	 * @return string
	 * @since 1.0.0
	 */
	public function get_bank_name() {
		return $this->bank_name;
	}

	/**
	 * @return string
	 * @since 1.0.0
	 */
	public function get_bank_phone() {
		return $this->bank_phone;
	}

	/**
	 * @return string
	 * @since 1.0.0
	 */
	public function get_bank_address() {
		return $this->bank_address;
	}

	/**
	 * @return string
	 * @since 1.0.0
	 */
	public function get_status() {
		return empty( $this->status ) ? 'active' : $this->status;
	}

	/**
	 * @return string
	 * @since 1.0.0
	 */
	public function get_created_at() {
		return $this->created_at;
	}

	/**
	 * @return string
	 * @since 1.0.0
	 */
	public function get_updated_at() {
		return $this->updated_at;
	}

	/**
	 * Get current balance
	 * since 1.0.0
	 * @param string $context
	 *
	 * @return string
	 */
	public function get_current_balance( $context = 'edit' ) {
		global $wpdb;
		// Opening Balance
		$total = $this->opening_balance;
		// Sum Incomes
		$total += $wpdb->get_var( $wpdb->prepare("SELECT SUM(amount) from $wpdb->ea_revenues WHERE account_id=%d", $this->get_id()) );
		//sum expense
		$total -= $wpdb->get_var( $wpdb->prepare("SELECT SUM(amount) from $wpdb->ea_payments WHERE account_id=%d", $this->get_id()) );

		return 'edit' == $context ? eaccounting_sanitize_price( $total ) : eaccounting_price( $total );
	}

}
