<?php
/**
 * Categoryable Trait
 */

namespace EverAccounting\Traits;

defined( 'ABSPATH' ) || exit;

trait Categoryable {

	/**
	 * Get category object.
	 *
	 * @return \EverAccounting\Models\Category|\stdClass
	 */
	public function get_category(){
		if ( ! is_callable( array( $this, 'get_category_id' ) ) ) {
			return new \stdClass();
		}

		$category_id = $this->get_category_id();
		$category =  eaccounting_get_category( $category_id );
		return empty( $category ) ? new \stdClass() : $category;
	}

	/**
	 * Set category object.
	 *
	 * @param array|object $category the category object.
	 */
	public function set_category( $category = null ){
		if ( ! is_callable( array( $this, 'set_category_id' ) ) ) {
			return;
		}
		if( empty( $category) || !is_array( $category )  || !is_object( $category ) ){
			return;
		}
		$category = get_object_vars( $category );
		if( empty( $category['id'] ) ){
			return;
		}

		$this->set_category_id( absint( $category['id'] ));
	}
}
