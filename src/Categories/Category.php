<?php
/**
 * Handle the category object.
 *
 * @package     EverAccounting
 * @class       EAccounting_Category
 * @version     1.0.2
 *
 */

namespace EverAccounting\Categories;

use EverAccounting\Abstracts\Model;

defined( 'ABSPATH' ) || exit();

/**
 * Class EAccounting_Category
 *
 * @since 1.0.2
 */
class Category extends Model {
	/**
	 * This is the name of this object type.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $object_type = 'category';

	/***
	 * Object table name.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $table = 'ea_categories';

	/**
	 * Category Data array.
	 *
	 * @since 1.0.2
	 * @var array
	 */
	protected $data = array(
		'name'         => '',
		'type'         => '',
		'color'        => '',
		'enabled'      => 1,
		'date_created' => null,
	);

	/**
	 * Get the category if ID is passed, otherwise the category is new and empty.
	 * This class should NOT be instantiated, but the eaccounting_get_category function
	 * should be used. It is possible, but the aforementioned are preferred and are the only
	 * methods that will be maintained going forward.
	 *
	 * @param int|object|Category $data object to read.
	 */
	public function __construct( $data = 0 ) {
		parent::__construct( $data );

		if ( is_numeric( $data ) && $data > 0 ) {
			$this->set_id( $data );
		} elseif ( $data instanceof self ) {
			$this->set_id( $data->get_id() );
		} elseif ( ! empty( $data->id ) ) {
			$this->set_id( $data->id );
		} else {
			$this->set_id( 0 );
		}

		if ( $this->get_id() > 0 && ! $this->object_read ) {
			$this->read();
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get category name.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 */
	public function get_name( $context = 'edit' ) {
		return $this->get_prop( 'name', $context );
	}

	/**
	 * Get the category type.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 */
	public function get_type( $context = 'edit' ) {
		return $this->get_prop( 'type', $context );
	}

	/**
	 * Get the category color.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 */
	public function get_color( $context = 'edit' ) {
		return $this->get_prop( 'color', $context );
	}


	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Set the category name.
	 *
	 * @since 1.0.2
	 *
	 * @param $value
	 *
	 */
	public function set_name( $value ) {
		$this->set_prop( 'name', eaccounting_clean( $value ) );
	}

	/**
	 * Set the category type.
	 *
	 * @since 1.0.2
	 *
	 * @param $value
	 *
	 */
	public function set_type( $value ) {
		if ( array_key_exists( $value, eaccounting_get_category_types() ) ) {
			$this->set_prop( 'type', eaccounting_clean( $value ) );
		}
	}

	/**
	 * Set the category color.
	 *
	 * @since 1.0.2
	 *
	 * @param $value
	 *
	 */
	public function set_color( $value ) {
		$this->set_prop( 'color', eaccounting_clean( $value ) );
	}
}
