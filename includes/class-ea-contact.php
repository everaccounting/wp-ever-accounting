<?php
defined( 'ABSPATH' ) || exit();

/**
 * Class EAccounting_Contact
 */
class EAccounting_Contact {
	/**
	 * @var
	 */
	protected $id;

	/**
	 * @var
	 */
	protected $user_id;

	/**
	 * @var
	 */
	protected $first_name;

	/**
	 * @var
	 */
	protected $last_name;

	/**
	 * @var
	 */
	protected $email;

	/**
	 * @var
	 */
	protected $phone;

	/**
	 * @var
	 */
	protected $tax_number;

	/**
	 * @var
	 */
	protected $address;

	/**
	 * @var
	 */
	protected $city;

	/**
	 * @var
	 */
	protected $state;

	/**
	 * @var
	 */
	protected $postcode;

	/**
	 * @var
	 */
	protected $country;

	/**
	 * @var
	 */
	protected $website;

	/**
	 * @var
	 */
	protected $note;

	/**
	 * @var
	 */
	protected $status;

	/**
	 * @var
	 */
	protected $types;

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
	public $user = null;

	/**
	 * @var null
	 */
	public $contact = null;

	/**
	 * EAccounting_Contact constructor.
	 *
	 * @param int $contact
	 */
	public function __construct( $contact = 0 ) {
		$this->init( $contact );
	}

	/**
	 * Init/load the contact object. Called from the constructor.
	 *
	 * @param $contact
	 *
	 * @since 1.0.0
	 */
	protected function init( $contact ) {
		if ( is_numeric( $contact ) ) {
			$this->id      = absint( $contact );
			$this->contact = eaccounting_get_contact( $contact );
			$this->get_contact( $this->id );
		} elseif ( $contact instanceof EAccounting_Contact ) {
			$this->id      = absint( $contact->id );
			$this->contact = $contact->contact;
			$this->get_contact( $this->id );
		} elseif ( isset( $contact->ID ) ) {
			$this->contact = eaccounting_get_contact( $contact->ID, 'user_id' );
			$this->id      = absint( ! is_null( $this->contact ) ? absint( $this->contact->id ) : 0 );
			$this->get_contact( $this->id );
		} elseif ( isset( $contact->id ) ) {
			$this->contact = $contact;
			$this->id      = absint( $this->contact->id );
			$this->populate( $contact );
		}
	}

	/**
	 * Gets an call from the database.
	 *
	 * @param int $id (default: 0).
	 *
	 * @return bool
	 */
	public function get_contact( $id = 0 ) {

		if ( ! $id ) {
			return false;
		}

		if ( $contact = eaccounting_get_contact( $id ) ) {
			$this->populate( $contact );

			return true;
		}

		return false;
	}

	/**
	 * Populates an call from the loaded post data.
	 *
	 * @param mixed $contact
	 */
	public function populate( $contact ) {
		$this->id      = $contact->id;
		$this->user_id = $contact->user_id;
		foreach ( $contact as $key => $value ) {
			$this->$key = $value;
		}

		if ( $this->user_id > 0 ) {
			$data = get_user_by( 'id', $this->user_id );
			if ( $data ) {
				$this->user       = $data;
				$this->user_login = $data->user_login;
				$this->user_email = $data->user_email;
				$this->user_url   = $data->user_url;
			}
		} else {
			$this->user_login = __( 'Guest', 'wp-eaccounting' );
			$this->user_email = $this->email;
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
			return new \WP_Error( 'invalid-property', sprintf( __( 'Can\'t get property %s', 'wp-eaccounting' ), $key ) );
		}

	}

	/**
	 * @since 1.0.0
	 * @return mixed
	 */
	public function get_id(){
		return $this->id;
	}

	/**
	 * @return mixed
	 * @since 1.0.0
	 */
	public function get_first_name() {
		return $this->first_name;
	}

	/**
	 * @return mixed
	 * @since 1.0.0
	 */
	public function get_last_name() {
		return $this->last_name;
	}

	/**
	 * Get name
	 * @return string
	 * @since 1.0.0
	 */
	public function get_name() {
		return implode( ' ', array( $this->first_name, $this->last_name ) );
	}

	/**
	 * @return mixed
	 * @since 1.0.0
	 */
	public function get_email() {
		return $this->email;
	}

	/**
	 * @return mixed
	 * @since 1.0.0
	 */
	public function get_phone() {
		return $this->phone;
	}

	/**
	 * @return mixed
	 * @since 1.0.0
	 */
	public function get_tax_number() {
		return $this->tax_number;
	}

	/**
	 * @return mixed
	 * @since 1.0.0
	 */
	public function get_address() {
		return $this->address;
	}

	/**
	 * @return mixed
	 * @since 1.0.0
	 */
	public function get_city() {
		return $this->city;
	}

	/**
	 * @return mixed
	 * @since 1.0.0
	 */
	public function get_state() {
		return $this->state;
	}

	/**
	 * @return mixed
	 * @since 1.0.0
	 */
	public function get_postcode() {
		return $this->postcode;
	}

	/**
	 * @return mixed
	 * @since 1.0.0
	 */
	public function get_country() {
		return $this->country;
	}

	/**
	 * @return mixed
	 * @since 1.0.0
	 */
	public function get_website() {
		return $this->website;
	}

	/**
	 * @return mixed
	 * @since 1.0.0
	 */
	public function get_note() {
		return $this->note;
	}

	/**
	 * @return string
	 * @since 1.0.0
	 */
	public function get_status() {
		return empty( $this->status ) ? 'active' : $this->status;
	}

	/**
	 * @return mixed
	 * @since 1.0.0
	 */
	public function get_types() {
		$types = maybe_unserialize( $this->types );
		return empty($types)? ['customer', 'vendor']: $types;
	}

}
