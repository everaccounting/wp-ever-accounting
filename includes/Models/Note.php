<?php

namespace EverAccounting\Models;

defined( 'ABSPATH' ) || exit;

/**
 * Class Note.
 *
 * @since 1.0.0
 * @package EverAccounting
 *
 * @property int    $id ID of the note.
 * @property int    $parent_id Object ID of the object.
 * @property string $parent_type Object type of the object.
 * @property string $content Content of the note.
 * @property array  $note_metadata Metadata of the note.
 * @property int    $creator_id ID of the author.
 * @property string $created_at Date created of the note.
 * @property string $updated_at Date updated of the note.
 */
class Note extends Model {
	/**
	 * The table associated with the model.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $table = 'ea_notes';

	/**
	 * Table columns.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $columns = array(
		'id',
		'parent_id',
		'parent_type',
		'content',
		'note_metadata',
		'creator_id',
	);

	/**
	 * The model's attributes.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $attributes = array(
		'note_metadata' => array(),
	);

	/**
	 * The attributes that should be cast.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $casts = array(
		'id'            => 'int',
		'object_id'     => 'int',
		'creator_id'    => 'int',
		'note_metadata' => 'array',
	);

	/**
	 * Searchable attributes.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $searchable = array(
		'content',
		'note_metadata',
	);

	/**
	 * Whether the model should be timestamped.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	protected $has_timestamps = true;

	/*
	|--------------------------------------------------------------------------
	| Attributes & Relations
	|--------------------------------------------------------------------------
	| Define the attributes and relations of the model.
	*/

	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	| Methods for saving, updating, and deleting objects.
	*/
	/**
	 * Save the object to the database.
	 *
	 * @since 1.0.0
	 * @return \WP_Error|static WP_Error on failure, or the object on success.
	 */
	public function save() {
		if ( ! $this->parent_id ) {
			return new \WP_Error( 'missing_required', __( 'Missing parent ID.', 'wp-ever-accounting' ) );
		}
		if ( ! $this->parent_type ) {
			return new \WP_Error( 'missing_required', __( 'Missing parent type.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->creator_id ) && is_user_logged_in() ) {
			$this->creator_id = get_current_user_id();
		}

		return parent::save();
	}

	/*
	|--------------------------------------------------------------------------
	| Helper methods.
	|--------------------------------------------------------------------------
	| Utility methods which don't directly relate to this object but may be
	| used by this object.
	*/
}
