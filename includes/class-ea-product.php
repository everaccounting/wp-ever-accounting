<?php
defined( 'ABSPATH' ) || exit();

/**
 * Class EAccounting_Product
 */
class EAccounting_Product {
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
	protected $sku;

	/**
	 * @var
	 */
	protected $description;

	/**
	 * @var
	 */
	protected $sale_price;

	/**
	 * @var
	 */
	protected $purchase_price;

	/**
	 * @var
	 */
	protected $quantity;

	/**
	 * @var
	 */
	protected $category_id;

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
	public $product = null;

	/**
	 * EAccounting_Product constructor.
	 *
	 * @param int $product
	 */
	public function __construct( $product = 0 ) {
		$this->init( $product );
	}

	/**
	 * Init/load the product object. Called from the constructor.
	 *
	 * @param $product
	 *
	 * @since 1.0.0
	 */
	protected function init( $product ) {
		if ( is_numeric( $product ) ) {
			$this->id      = absint( $product );
			$this->product = eaccounting_get_product( $product );
			$this->get_product( $this->id );
		} elseif ( $product instanceof EAccounting_Product ) {
			$this->id      = absint( $product->id );
			$this->product = $product->product;
			$this->get_product( $this->id );
		} elseif ( isset( $product->id ) ) {
			$this->product = $product;
			$this->id      = absint( $this->product->id );
			$this->populate( $product );
		}
	}

	/**
	 * Gets an call from the database.
	 *
	 * @param int $id (default: 0).
	 *
	 * @return bool
	 */
	public function get_product( $id = 0 ) {

		if ( ! $id ) {
			return false;
		}

		if ( $product = eaccounting_get_product( $id ) ) {
			$this->populate( $product );

			return true;
		}

		return false;
	}

	/**
	 * Populates an call from the loaded post data.
	 *
	 * @param mixed $product
	 */
	public function populate( $product ) {
		$this->id      = $product->id;
		foreach ( $product as $key => $value ) {
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
			return new \WP_Error( 'invalid-property', sprintf( __( 'Can\'t get property %s', 'wp-eaccounting' ), $key ) );
		}

	}

	/**
	 * @since 1.0.0
	 * @return int
	 */
	public function get_id(){
		return $this->id;
	}

	/**
	 * @since 1.0.0
	 * @return string
	 */
	public function get_name(){
		return $this->name;
	}

	/**
	 * @since 1.0.0
	 * @return string
	 */
	public function get_sku(){
		return $this->sku;
	}

	/**
	 * @since 1.0.0
	 * @return string
	 */
	public function get_description(){
		return $this->description;
	}

	/**
	 * @since 1.0.0
	 * @return string
	 */
	public function get_sale_price(){
		return eaccounting_price( $this->sale_price );
	}

	/**
	 * @since 1.0.0
	 * @return string
	 */
	public function get_purchase_price(){
		return eaccounting_price( $this->purchase_price );
	}

	/**
	 * @since 1.0.0
	 * @return int
	 */
	public function get_quantity(){
		return $this->quantity;
	}

	/**
	 * @since 1.0.0
	 * @return int
	 */
	public function get_category_id(){
		return $this->category_id;
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

}
