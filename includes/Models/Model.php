<?php

namespace EverAccounting\Models;

/**
 * Abstract class Model.
 *
 * @since 1.2.0
 * @package EverAccounting
 * @subpackage Models
 */
abstract class Model extends \ByteKit\Models\Model {
	/**
	 * Whether the model have author.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	protected $has_author = false;

	/**
	 * The name of the "author_id" column.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const AUTHOR_ID = 'author_id';

	/**
	 * Get the table columns.
	 *
	 * @return array
	 */
	public function get_columns() {
		$columns = parent::get_columns();
		if ( $this->has_author ) {
			$columns[] = static::AUTHOR_ID;
		}

		return $columns;
	}

	/**
	 * Get the casts array.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_casts() {
		$casts = parent::get_casts();
		if ( $this->has_author ) {
			$casts[ static::AUTHOR_ID ] = 'int';
		}

		return $casts;
	}

	/**
	 * Get hook prefix. Default is the object type.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_hook_prefix() {
		return 'ever_accounting_' . $this->get_object_type();
	}

	/**
	 * Save the object to the database.
	 *
	 * @since 1.0.0
	 * @return \WP_Error|true True on success, WP_Error on failure.
	 */
	public function save() {
		if ( $this->has_author && empty( $this->get_attribute( self::AUTHOR_ID ) ) && is_user_logged_in() ) {
			$this->set_attribute_value( self::AUTHOR_ID, get_current_user_id() );
		}

		return parent::save();
	}
}
