<?php
/**
 * Handle the Note object.
 *
 * @package     EverAccounting
 * @class       Note
 * @version     1.2.1
 */

namespace EverAccounting;

use EverAccounting\Abstracts\Data;

defined( 'ABSPATH' ) || exit;

/**
 * Core class used to implement the Note object.
 *
 * @package EverAccounting
 *
 * @since 1.2.1
 * @property int $parent_id
 * @property string $type
 * @property string $note
 * @property string $extra
 * @property int $creator_id
 * @property string $date_created
 */
class Note extends Data {
	/**
	 * Note id.
	 *
	 * @since 1.2.1
	 * @var int
	 */
	public $id = null;

	/**
	 * Note data container.
	 *
	 * @since 1.2.1
	 * @var array
	 */
	public $data = array(
		'parent_id'    => null,
		'type'         => '',
		'note'         => '',
		'extra'        => '',
		'creator_id'   => '',
		'date_created' => null,
	);

	/**
	 * Stores the note object's sanitization level.
	 *
	 * Does not correspond to a DB field.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $filter;

	/**
	 * Retrieve Note instance.
	 *
	 * @param int $item_id Note id.
	 *
	 * @return Note|false Note object, false otherwise.
	 * @since 1.2.1
	 *
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 *
	 */
	public static function get_instance( $item_id ) {
		global $wpdb;

		$item_id = (int) $item_id;
		if ( ! $item_id ) {
			return false;
		}

		$_item = wp_cache_get( $item_id, 'ea_notes' );

		if ( ! $_item ) {
			$_item = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ea_notes WHERE id = %d LIMIT 1", $item_id ) );

			if ( ! $_item ) {
				return false;
			}

			$_item = eaccounting_sanitize_note( $_item, 'raw' );
			wp_cache_add( $_item->id, $_item, 'ea_notes' );
		} elseif ( empty( $_item->filter ) ) {
			$_item = eaccounting_sanitize_note( $_item, 'raw' );
		}

		return new Note( $_item );
	}

	/**
	 * Note constructor.
	 *
	 * @param $note
	 *
	 * @since 1.2.1
	 */
	public function __construct( $note ) {
		parent::__construct();
		foreach ( get_object_vars( $note ) as $key => $value ) {
			$this->$key = $value;
		}
	}

	/**
	 * Filter note object based on context.
	 *
	 * @param string $filter Filter.
	 *
	 * @return Note|Object
	 * @since 1.2.1
	 */
	public function filter( $filter ) {
		if ( $this->filter === $filter ) {
			return $this;
		}

		if ( 'raw' === $filter ) {
			return self::get_instance( $this->id );
		}

		return new self( eaccounting_sanitize_note( (object) $this->to_array(), $filter ) );
	}
}
