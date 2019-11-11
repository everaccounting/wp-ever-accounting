<?php
defined( 'ABSPATH' ) || exit();

/**
 * Class EAccounting_Category
 */
class EAccounting_Category {
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
	protected $type;

	/**
	 * @var
	 */
	protected $color;

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
	public $category = null;

	/**
	 * EAccounting_Category constructor.
	 *
	 * @param int $category
	 */
	public function __construct( $category = 0 ) {
		$this->init( $category );
	}

	/**
	 * Init/load the category object. Called from the constructor.
	 *
	 * @param $category
	 *
	 * @since 1.0.0
	 */
	protected function init( $category ) {
		if ( is_numeric( $category ) ) {
			$this->id      = absint( $category );
			$this->category = eaccounting_get_category( $category );
			$this->get_category( $this->id );
		} elseif ( $category instanceof EAccounting_Category ) {
			$this->id      = absint( $category->id );
			$this->category = $category->category;
			$this->get_category( $this->id );
		} elseif ( isset( $category->id ) ) {
			$this->category = $category;
			$this->id      = absint( $this->category->id );
			$this->populate( $category );
		}
	}

	/**
	 * Gets an call from the database.
	 *
	 * @param int $id (default: 0).
	 *
	 * @return bool
	 */
	public function get_category( $id = 0 ) {

		if ( ! $id ) {
			return false;
		}

		if ( $category = eaccounting_get_category( $id ) ) {
			$this->populate( $category );

			return true;
		}

		return false;
	}

	/**
	 * Populates an call from the loaded post data.
	 *
	 * @param mixed $category
	 */
	public function populate( $category ) {
		$this->id      = $category->id;
		foreach ( $category as $key => $value ) {
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
			return new \WP_Error( 'invalid-property', sprintf( __( 'Can\'t get property %s', 'wp-ecategorying' ), $key ) );
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
	public function get_type(){
		return $this->type;
	}

	/**
	 * @since 1.0.0
	 * @return string
	 */
	public function get_color(){
		return $this->color;
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
