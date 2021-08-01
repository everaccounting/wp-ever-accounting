<?php
/**
 * Handle the Currency object.
 *
 * @package     EverAccounting
 * @class       Contact
 * @version     1.2.1
 */

namespace EverAccounting;

use EverAccounting\Abstracts\Data;

defined( 'ABSPATH' ) || exit;

/**
 * Core class used to implement the Currency object.
 * @package EverAccounting
 *
 * @since 1.2.1
 *
 * @property string $name
 */
class Currency extends Data {
	/**
	 * Currency id.
	 *
	 * @since 1.2.1
	 * @var int
	 */
	public $id = null;

	/**
	 * Currency data container.
	 *
	 * @since 1.2.1
	 * @var array
	 */
	public $data = array(
		'name'               => '',
		'code'               => '',
		'rate'               => 1,
		'number'             => '',
		'precision'          => 2,
		'subunit'            => 100,
		'symbol'             => '',
		'position'           => 'before',
		'decimal_separator'  => '.',
		'thousand_separator' => ',',
		'date_created'       => null,
	);

	/**
	 * Stores the currency object's sanitization level.
	 *
	 * Does not correspond to a DB field.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $filter;

	/**
	 * Return only the main currency fields
	 *
	 * @param int|string $value
	 * @param string $field
	 *
	 * @return object|false Raw currency object
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 */
	public static function get_data_by( $value, $field = 'code' ) {
		global $wpdb;
		if ( 'id' === $field ) {
			$value = (int) $value;
		} else {
			$value = trim( $value );
		}
		if ( ! $value ) {
			return false;
		}
		switch ( $field ) {
			case 'code':
				$value    = eaccounting_sanitize_currency_code( $value );
				$db_field = 'code';
				break;
			case 'id':
			default:
				$db_field = 'id';
				break;
		}

		$_item = wp_cache_get( "{$db_field}_{$value}", 'ea_items' );

		if ( ! $_item ) {
			$_item = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ea_currencies WHERE $db_field = %s LIMIT 1", $value ) );

			if ( ! $_item ) {
				return false;
			}

			$_item = eaccounting_sanitize_currency( $_item, 'raw' );
			wp_cache_add( "id_{$_item->id}", $_item, 'ea_items' );
			wp_cache_add( "code_{$_item->code}", $_item, 'ea_items' );
		} elseif ( empty( $_item->filter ) ) {
			$_item = eaccounting_sanitize_currency( $_item, 'raw' );
		}

		return new Currency( $_item );
	}

	/**
	 * Item constructor.
	 *
	 * @param $item
	 *
	 * @since 1.2.1
	 */
	public function __construct( $item ) {
		foreach ( get_object_vars( $item ) as $key => $value ) {
			$this->$key = $value;
		}
	}

	/**
	 * Filter currency object based on context.
	 *
	 * @param string $filter Filter.
	 *
	 * @return Currency|Object
	 * @since 1.2.1
	 */
	public function filter( $filter ) {
		if ( $this->filter === $filter ) {
			return $this;
		}

		if ( 'raw' === $filter ) {
			return self::get_data_by( $this->id );
		}

		return new self( eaccounting_sanitize_currency( (object) $this->to_array(), $filter ) );
	}

}
