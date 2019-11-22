<?php
defined( 'ABSPATH' ) || exit();

/**
 * Class EAccounting_Account
 */
class EAccounting_Revenue {
	/**
	 * @var string
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $account_id;

	/**
	 * @var string
	 */
	protected $paid_at;

	/**
	 * @var string
	 */
	protected $amount;

	/**
	 * @var string
	 */
	protected $contact_id;

	/**
	 * @var string
	 */
	protected $description;

	/**
	 * @var string
	 */
	protected $category_id;

	/**
	 * @var string
	 */
	protected $method_id;

	/**
	 * @var string
	 */
	protected $reference;

	/**
	 * @var string
	 */
	protected $parent_id;

	/**
	 * @var string
	 */
	protected $reconciled;

	/**
	 * @var string
	 */
	protected $attachment_url;

	/**
	 * @var string
	 */
	protected $created_at;


	/**
	 * @var string
	 */
	protected $updated_at;

	/**
	 * @var null
	 */
	public $revenue = null;

	/**
	 * EAccounting_Account constructor.
	 *
	 * @param int $revenue
	 */
	public function __construct( $revenue = 0 ) {
		$this->init( $revenue );
	}

	/**
	 * Init/load the revenue object. Called from the constructor.
	 *
	 * @param $revenue
	 *
	 * @since 1.0.0
	 */
	protected function init( $revenue ) {
		if ( is_numeric( $revenue ) ) {
			$this->id      = absint( $revenue );
			$this->revenue = eaccounting_get_revenue( $revenue );
			$this->get_revenue( $this->id );
		} elseif ( $revenue instanceof EAccounting_Revenue ) {
			$this->id      = absint( $revenue->id );
			$this->revenue = $revenue->revenue;
			$this->get_revenue( $this->id );
		} elseif ( isset( $revenue->id ) ) {
			$this->revenue = $revenue;
			$this->id      = absint( $this->revenue->id );
			$this->populate( $revenue );
		}
	}

	/**
	 * Gets an call from the database.
	 *
	 * @param int $id (default: 0).
	 *
	 * @return bool
	 */
	public function get_revenue( $id = 0 ) {

		if ( ! $id ) {
			return false;
		}

		if ( $revenue = eaccounting_get_revenue( $id ) ) {
			$this->populate( $revenue );

			return true;
		}

		return false;
	}

	/**
	 * Populates an call from the loaded post data.
	 *
	 * @param mixed $revenue
	 */
	public function populate( $revenue ) {
		$this->id = $revenue->id;
		foreach ( $revenue as $key => $value ) {
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
	 * @since 1.0.0
	 * @param string $context
	 *
	 * @return bool|string
	 */
	public function get_account( $context = 'edit' ) {
		if ( 'edit' == $context ) {
			return $this->account_id;
		}

		if ( $this->account_id && $account = eaccounting_get_account( $this->account_id ) ) {
			return $account->name;
		}

		return false;
	}

	/**
	 * @return string
	 * @since 1.0.0
	 */
	public function get_contact($context = 'edit') {
		return 'edit' == $context ? $this->contact_id : new EAccounting_Contact($this->contact_id);
	}


	/**
	 * @since 1.0.0
	 * @param string $context
	 *
	 * @return bool|string
	 */
	public function get_category( $context = 'edit' ) {
		if ( 'edit' == $context ) {
			return $this->category_id;
		}

		if ( $this->category_id && $category = eaccounting_get_category( $this->category_id ) ) {
			return $category->name;
		}

		return false;
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
	 * @since 1.0.0
	 * @return string
	 */
	public function get_attachment_url(){
		return empty( $this->attachment_url ) ? '' : esc_url($this->attachment_url);
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
