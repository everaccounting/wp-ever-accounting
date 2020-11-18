<?php
/**
 * Handle the category object.
 *
 * @package     EverAccounting\Models
 * @class       Category
 * @version     1.0.2
 */

namespace EverAccounting\Models;

use EverAccounting\Abstracts\ResourceModel;
use EverAccounting\Repositories\Categories;

defined( 'ABSPATH' ) || exit;

/**
 * Class Category
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Models
 */
class Category extends ResourceModel {

	/**
	 * Get the category if ID is passed, otherwise the category is new and empty.
	 *
	 * @since 1.0.2
	 *
	 * @param int|object|array| Category $data object to read.
	 */
	public function __construct( $data = 0 ) {
		parent::__construct( $data, Categories::instance() );
	}

	/**
	 * Get repository class.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	public function get_repository() {
		return Categories::class;
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
	 */
	public function set_color( $value ) {
		$this->set_prop( 'color', eaccounting_clean( $value ) );
	}
}
