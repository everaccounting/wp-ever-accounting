<?php

namespace EverAccounting\Interfaces;

/**
 * Interface Controller
 *
 * @package EverAccounting\Interfaces
 */
interface Controller {

	/**
	 * Insert item.
	 *
	 * @since 1.1.0
	 *
	 * @param $args
	 *
	 * @return mixed
	 */
	public function insert( $args );

	/**
	 * Retrieve any item for database.
	 *
	 * @since 1.1.0
	 *
	 * @param $id
	 *
	 * @return mixed
	 */
	public function get( $id );

	/**
	 * Delete's the entry.
	 *
	 * @since 1.1.0
	 *
	 * @param $id
	 *
	 * @return bool
	 */
	public function delete( $id );

	/**
	 * This will be used for queering
	 * the items. it will return items
	 * and if count params is available
	 * then with total numbers.
	 *
	 * @since 1.1.0
	 *
	 * @param $args
	 *
	 * @return array
	 *
	 */
	public function get_items( $args = array() );
}
