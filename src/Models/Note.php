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
 * @property int    $parent_id ID of the parent.
 * @property string $parent_type Type of the parent.
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
		'parent_id',
		'parent_type',
		'content',
		'note_metadata',
		'author_id',
	);

	/**
	 * Model's data container.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $data = array(
		'note_metadata' => array(),
	);

	/**
	 * Model's casts data.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	protected $casts = array(
		'id'            => 'int',
		'parent_id'     => 'int',
		'author_id'     => 'int',
		'note_metadata' => 'array',
	);

	/**
	 * Searchable properties.
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
	public $timestamps = true;

	/**
	 * Save the object to the database.
	 *
	 * @since 1.0.0
	 * @return \WP_Error|true True on success, WP_Error on failure.
	 */
	public function save() {
		if ( ! $this->parent_id ) {
			return new \WP_Error( 'missing_parent_id', __( 'Missing parent ID.', 'wp-ever-accounting' ) );
		}
		if ( ! $this->parent_type ) {
			return new \WP_Error( 'missing_parent_type', __( 'Missing parent type.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->author_id ) && is_user_logged_in() ) {
			$this->author_id = get_current_user_id();
		}

		return parent::save();
	}
}
