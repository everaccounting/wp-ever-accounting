<?php
/**
 * Initializes a temporary data storage engine used by core in various capacities.
 *
 * @package EAccounting
 * @subpackage Utilities
 * @since 1.0.2
 */

namespace EAccounting\Utilities;

defined( 'ABSPATH' ) || exit();

/**
 * Class Data
 *
 * @since 1.0.2
 * @package EAccounting\Utilities
 */
class Data {

	/**
	 * Retrieves stored data by key.
	 *
	 * Given a key, get the information from the database directly.
	 *
	 * @param string     $key The stored option key.
	 * @param null|mixed $default Optional. A default value to retrieve should `$value` be empty. Default null.
	 *
	 * @return mixed|false The stored data, value of `$default` if not null, otherwise false.
	 * @since  1.0.2
	 */
	public function get( $key, $default = null ) {
		global $wpdb;
		$value = $wpdb->get_var( $wpdb->prepare( "SELECT option_value FROM $wpdb->options WHERE option_name = %s", $key ) );

		if ( empty( $value ) && ! is_null( $default ) ) {
			$value = $default;
		}

		return empty( $value ) ? false : maybe_unserialize( $value );
	}

	/**
	 * Write some data based on key and value.
	 *
	 * @param string $key The option_name.
	 * @param mixed  $value The value to store.
	 *
	 * @since  1.0.2
	 */
	public function write( $key, $value ) {
		global $wpdb;

		$value = maybe_serialize( $value );

		$data = array(
			'option_name'  => $key,
			'option_value' => $value,
			'autoload'     => 'no',
		);

		$formats = $this->get_data_formats( $value );

		$wpdb->replace( $wpdb->options, $data, $formats );
	}

	/**
	 * Derives the formats array based on the type of $value.
	 *
	 * @param mixed $value Value to store.
	 *
	 * @return array Formats array. First and last values will always be string ('%s').
	 * @since  1.0.2
	 */
	public function get_data_formats( $value ) {

		switch ( gettype( $value ) ) {
			case 'integer':
				$formats = array( '%s', '%d', '%s' );
				break;

			case 'double':
				$formats = array( '%s', '%f', '%s' );
				break;

			default:
			case 'string':
				$formats = array( '%s', '%s', '%s' );
				break;
		}

		return $formats;
	}

	/**
	 * Deletes a piece of stored data by key.
	 *
	 * @param string $key The stored option name to delete.
	 *
	 * @return int|false The number of rows deleted, or false on error.
	 * @since  1.0.2
	 */
	public function delete( $key ) {
		global $wpdb;

		return $wpdb->delete( $wpdb->options, array( 'option_name' => $key ) );
	}

	/**
	 * Deletes all options matching a given RegEx pattern.
	 *
	 * @param string $pattern Pattern to match against option keys.
	 *
	 * @return int|false The number of rows deleted, or false on error.
	 * @since 1.0.2
	 */
	public function delete_by_match( $pattern ) {
		global $wpdb;

		// Double check to make sure the batch_id got included before proceeding.
		if ( '^[0-9a-z\\_]+' !== $pattern && ! empty( $pattern ) ) {
			$result = $wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->options WHERE option_name REGEXP %s", $pattern ) );
		} else {
			$result = false;
		}

		return $result;
	}

}
