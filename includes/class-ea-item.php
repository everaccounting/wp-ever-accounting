<?php
defined( 'ABSPATH' ) || exit();

/**
 * Class EAccounting_Item
 */
class EAccounting_Item {
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
	protected $tax_id;

	/**
	 * @var
	 */
	protected $purchase_price;

	/**
	 * @var
	 */
	protected $sale_price;

	/**
	 * @var
	 */
	protected $description;

	/**
	 * @var
	 */
	protected $picture;

	/**
	 * @var
	 */
	protected $category_id;

	/**
	 * @var
	 */
	protected $quantity;

	/**
	 * @var
	 */
	protected $image_id;


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
	public $item = null;

	/**
	 * EAccounting_Item constructor.
	 *
	 * @param int $item
	 */
	public function __construct( $item = 0 ) {
		$this->init( $item );
	}

	/**
	 * Init/load the account object. Called from the constructor.
	 *
	 * @param $account
	 *
	 * @since 1.0.0
	 */
	protected function init( $item ) {
		if ( is_numeric( $item ) ) {
			$this->id      = absint( $item );
			$this->item = eaccounting_get_item( $item );
			$this->get_item( $this->id );
		} elseif ( $item instanceof EAccounting_Item ) {
			$this->id      = absint( $item->id );
			$this->item = $item->item;
			$this->get_item( $this->id );
		} elseif ( isset( $item->id ) ) {
			$this->item = $item;
			$this->id      = absint( $this->item->id );
			$this->populate( $item );
		}
	}

	/**
	 * Gets an call from the database.
	 *
	 * @param int $id (default: 0).
	 *
	 * @return bool
	 */
	public function get_item( $id = 0 ) {

		if ( ! $id ) {
			return false;
		}

		if ( $item = eaccounting_get_item( $id ) ) {
			$this->populate( $item );

			return true;
		}

		return false;
	}

	/**
	 * Populates an call from the loaded post data.
	 *
	 * @param mixed $item
	 */
	public function populate( $item ) {
		$this->id = $item->id;
		foreach ( $item as $key => $value ) {
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
	public function get_tax_id() {
		return $this->tax_id;
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
	public function get_sale_price() {
		return $this->sale_price;
	}

	/**
	 * @return string
	 * @since 1.0.0
	 */
	public function get_purchase_price() {
		return $this->purchase_price;
	}

	/**
	 * @return string
	 * @since 1.0.0
	 */
	public function get_category_id() {
		return $this->category_id;
	}

	/**
	 * @return string
	 * @since 1.0.0
	 */
	public function get_image_id() {
		return $this->image_id;
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
