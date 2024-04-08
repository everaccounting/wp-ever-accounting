<?php

namespace EverAccounting\Models\Traits;

/**
 * HasColumns trait.
 *
 * @since 1.0.0
 * @package ByteKit\Models
 */
class HasColumns {

	/**
	 * Table columns.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $columns = array();

	/**
	 * Determine if the attribute is a column.
	 *
	 * @param string $key Key.
	 *
	 * @return bool
	 */
	public function has_column( $key ) {
		// if contains . then we have to remove it.
		if ( strpos( $key, '.' ) !== false ) {
			$key = explode( '.', $key );
			$key = end( $key );
		}

		return in_array( $key, $this->get_columns(), true );
	}

	/**
	 * Get the table columns.
	 *
	 * @return array
	 */
	public function get_columns() {
		return $this->columns;
	}


	/**
	 * Qualify the given column name by the model's table.
	 *
	 * @param string $column Column.
	 *
	 * @return string
	 */
	public function qualify_column( $column ) {
		if ( strpos( $column, '.' ) !== false ) {
			return $column;
		}

		return $this->get_table() . '.' . $column;
	}
}
