<?php
/**
 * Abstract Model.
 *
 * Handles generic data interaction which is implemented by the different repository classes.
 * @since 1.1.0
 */

namespace EverAccounting\Abstracts;

defined( 'ABSPATH' ) || exit;

/**
 * Class Data
 *
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Abstracts
 */
class Data {
	/**
	 * Stores the item object's sanitization level.
	 *
	 * Does not correspond to a DB field.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $context;

	/**
	 * Determine whether the object exists in the database.
	 *
	 * @return bool True if object exists in the database, false if not.
	 * @since 1.2.1
	 */
	public function exists() {
		return ! empty( $this->id );
	}

	/**
	 * Convert object to array.
	 *
	 * @return array Object as array.
	 * @since 1.2.1
	 *
	 */
	public function to_array() {
		return get_object_vars( $this );
	}
}
