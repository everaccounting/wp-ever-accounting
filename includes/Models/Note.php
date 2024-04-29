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
 * @property int    $object_id Object ID of the object.
 * @property string $object_type Object type of the object.
 * @property string $content Content of the note.
 * @property array  $note_metadata Metadata of the note.
 * @property int    $author_id ID of the author.
 * @property string $date_created Date created of the note.
 * @property string $date_updated Date updated of the note.
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
		'object_id',
		'object_type',
		'content',
		'note_metadata',
		'author_id',
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
		'author_id'     => 'int',
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
	protected $timestamps = true;

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
	 * @return \WP_Error|true True on success, WP_Error on failure.
	 */
	public function save() {
		if ( ! $this->object_id ) {
			return new \WP_Error( 'missing_object_id', __( 'Missing object ID.', 'wp-ever-accounting' ) );
		}
		if ( ! $this->object_type ) {
			return new \WP_Error( 'missing_object_type', __( 'Missing object type.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->author_id ) && is_user_logged_in() ) {
			$this->author_id = get_current_user_id();
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
