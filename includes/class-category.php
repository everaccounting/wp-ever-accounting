<?php
/**
 * Handle the Category object.
 *
 * @package     EverAccounting
 * @class       Category
 * @version     1.2.1
 */

namespace EverAccounting;

use EverAccounting\Abstracts\Data;

defined( 'ABSPATH' ) || exit;

/**
 * Core class used to implement the Category object.
 *
 * @package EverAccounting
 *
 * @since 1.2.1
 *
 */
class Category extends Data {
	/**
	 * Category id.
	 *
	 * @since 1.2.1
	 * @var int
	 */
	public $id = null;

	/**
	 * Category data container.
	 *
	 * @since 1.2.1
	 * @var array
	 */
	public $data = array(
		'name'         => '',
		'type'         => '',
		'color'        => '',
		'enabled'      => 1,
		'date_created' => null,
	);

	/**
	 * Stores the category object's sanitization level.
	 *
	 * Does not correspond to a DB field.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $filter;

	/**
	 * Retrieve Category instance.
	 *
	 * @param int $category_id Category id.
	 *
	 * @return Category|false Category object, false otherwise.
	 * @since 1.2.1
	 *
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 *
	 */
	public static function get_instance( $category_id ) {
		global $wpdb;

		$category_id = (int) $category_id;
		if ( ! $category_id ) {
			return false;
		}

		$_item = wp_cache_get( $category_id, 'ea_categories' );

		if ( ! $_item ) {
			$_item = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ea_categories WHERE id = %d LIMIT 1", $category_id ) );

			if ( ! $_item ) {
				return false;
			}

			$_item = eaccounting_sanitize_category( $_item, 'raw' );
			wp_cache_add( $_item->id, $_item, 'ea_categories' );
		} elseif ( empty( $_item->filter ) ) {
			$_item = eaccounting_sanitize_category( $_item, 'raw' );
		}

		return new Category( $_item );
	}

	/**
	 * Category constructor.
	 *
	 * @param $category
	 *
	 * @since 1.2.1
	 */
	public function __construct( $category ) {
		foreach ( get_object_vars( $category ) as $key => $value ) {
			$this->$key = $value;
		}
	}

	/**
	 * Filter category object based on context.
	 *
	 * @param string $filter Filter.
	 *
	 * @return Category|Object
	 * @since 1.2.1
	 */
	public function filter( $filter ) {
		if ( $this->filter === $filter ) {
			return $this;
		}

		if ( 'raw' === $filter ) {
			return self::get_instance( $this->id );
		}

		return new self( eaccounting_sanitize_category( (object) $this->to_array(), $filter ) );
	}
}
