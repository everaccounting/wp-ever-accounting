<?php
defined( 'ABSPATH' ) || exit();

/**
 * Class EAccounting_Account
 */
class EAccounting_Payment {
	/**
	 * @var
	 */
	protected $id;

	/**
	 * @var
	 */
	protected $account_id;

	/**
	 * @var
	 */
	protected $paid_at;

	/**
	 * @var
	 */
	protected $amount;

	/**
	 * @var
	 */
	protected $contact_id;

	/**
	 * @var
	 */
	protected $description;

	/**
	 * @var
	 */
	protected $category_id;

	/**
	 * @var
	 */
	protected $method_id;

	/**
	 * @var
	 */
	protected $reference;

	/**
	 * @var
	 */
	protected $parent_id;

	/**
	 * @var
	 */
	protected $reconciled;

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
	public $payment = null;

	/**
	 * EAccounting_Account constructor.
	 *
	 * @param int $payment
	 */
	public function __construct( $payment = 0 ) {
		$this->init( $payment );
	}

	/**
	 * Init/load the payment object. Called from the constructor.
	 *
	 * @param $payment
	 *
	 * @since 1.0.0
	 */
	protected function init( $payment ) {
		if ( is_numeric( $payment ) ) {
			$this->id      = absint( $payment );
			$this->payment = eaccounting_get_payment( $payment );
			$this->get_payment( $this->id );
		} elseif ( $payment instanceof EAccounting_Revenue ) {
			$this->id      = absint( $payment->id );
			$this->payment = $payment->payment;
			$this->get_payment( $this->id );
		} elseif ( isset( $payment->id ) ) {
			$this->payment = $payment;
			$this->id      = absint( $this->payment->id );
			$this->populate( $payment );
		}
	}

	/**
	 * Gets an call from the database.
	 *
	 * @param int $id (default: 0).
	 *
	 * @return bool
	 */
	public function get_payment( $id = 0 ) {

		if ( ! $id ) {
			return false;
		}

		if ( $payment = eaccounting_get_payment( $id ) ) {
			$this->populate( $payment );

			return true;
		}

		return false;
	}

	/**
	 * Populates an call from the loaded post data.
	 *
	 * @param mixed $payment
	 */
	public function populate( $payment ) {
		$this->id = $payment->id;
		foreach ( $payment as $key => $value ) {
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
	public function get_paid_at() {
		return isset($this->paid_at)? date('Y-m-d', strtotime($this->paid_at)): '';
	}

	/**
	 * since 1.0.0
	 * @param string $context
	 *
	 * @return string
	 */
	public function get_amount($context = 'edit') {
		return 'edit' == $context ? eaccounting_format_price($this->amount) : eaccounting_price($this->amount);
	}

	/**
	 * @return string
	 * @since 1.0.0
	 */
	public function get_account($context = 'edit') {
		return 'edit' == $context ? $this->account_id : eaccounting_get_account($this->account_id);
	}

	/**
	 * @return string
	 * @since 1.0.0
	 */
	public function get_contact($context = 'edit') {
		return 'edit' == $context ? $this->contact_id : new EAccounting_Contact($this->contact_id);
	}

	/**
	 * @return string
	 * @since 1.0.0
	 */
	public function get_category($context = 'edit') {
		return 'edit' == $context ? $this->category_id : eaccounting_get_category($this->category_id);
	}

	/**
	 * @return string
	 * @since 1.0.0
	 */
	public function get_payment_method($context = 'edit') {
		$methods = eaccounting_get_payment_methods();
		$display = array_key_exists($this->method_id, $methods)? $methods[$this->method_id]: '';
		return 'edit' == $context ? $this->method_id : $display;
	}

	/**
	 * @return string
	 * @since 1.0.0
	 */
	public function get_reference() {
		return $this->reference;
	}

	/**
	 * @return string
	 * @since 1.0.0
	 */
	public function get_description() {
		return $this->description;
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

}
