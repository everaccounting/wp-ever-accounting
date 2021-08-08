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
 * @property int $id
 * @property int $parent_id
 * @property string $type
 * @property string $note
 * @property string $extra
 * @property int $creator_id
 * @property string $date_created
 */
class Note extends Data {
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
	 * Note constructor.
	 *
	 * Get the note if id is passed, otherwise the note is new and empty.
	 *
	 * @param int|object|array|Note $item object to read.
	 *
	 * @since 1.1.0
	 */
	public function __construct( $item = 0 ) {
		parent::__construct();
		if ( $item instanceof self ) {
			$this->set_id( $item->get_id() );
		} elseif ( is_object( $item ) && ! empty( $item->id ) ) {
			$this->set_id( $item->id );
		} elseif ( is_array( $item ) && ! empty( $item['id'] ) ) {
			$this->set_props( $item );
		} elseif ( is_numeric( $item ) ) {
			$this->set_id( $item );
		} else {
			$this->set_object_read( true );
		}

		$data = self::get_raw( $this->get_id() );
		if ( $data ) {
			$this->set_props( $data );
			$this->set_object_read( true );
		} else {
			$this->set_id( 0 );
		}
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	|
	| Methods which create, read, update and delete items from the database.
	|
	*/

	/**
	 * Retrieve the note from database instance.
	 *
	 * @param int $note_id Note id.
	 * @param string $field Database field.
	 *
	 * @return object|false Object, false otherwise.
	 * @since 1.2.1
	 *
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 *
	 */
	static function get_raw( $note_id, $field = 'id' ) {
		global $wpdb;

		$note_id = (int) $note_id;
		if ( ! $note_id ) {
			return false;
		}

		$note = wp_cache_get( $note_id, 'ea_notes' );

		if ( ! $note ) {
			$note = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ea_notes WHERE id = %d LIMIT 1", $note_id ) );

			if ( ! $note ) {
				return false;
			}

			wp_cache_add( $note->id, $note, 'ea_items' );
		}

		return apply_filters( 'eaccounting_item_raw_note', $note );
	}

	/**
	 *  Insert an note in the database.
	 *
	 * This method is not meant to call publicly instead call save
	 * which will conditionally decide which method to call.
	 *
	 * @param array $fields An array of database fields and type.
	 *
	 * @return \WP_Error|true True on success, WP_Error on failure.
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 * @since 1.1.0
	 */
	protected function insert( $fields ) {
		global $wpdb;
		$data_arr = $this->to_array();
		$data     = wp_array_slice_assoc( $data_arr, array_keys( $fields ) );
		$format   = wp_array_slice_assoc( $fields, array_keys( $data ) );
		$data     = wp_unslash( $data );

		// Bail if nothing to save
		if ( empty( $data ) ) {
			return true;
		}

		/**
		 * Fires immediately before a note is inserted in the database.
		 *
		 * @param array $data Note data to be inserted.
		 * @param string $data_arr Sanitized note data.
		 * @param Note $item Note object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_insert_note', $data, $data_arr, $this );

		if ( false === $wpdb->insert( $wpdb->prefix . 'ea_notes', $data, $format ) ) {
			return new \WP_Error( 'eaccounting_note_db_insert_error', __( 'Could not insert note into the database.', 'wp-ever-accounting' ), $wpdb->last_error );
		}

		$this->set_id( $wpdb->insert_id );

		/**
		 * Fires immediately after a note is inserted in the database.
		 *
		 * @param int $note_id Note id.
		 * @param array $data Note has been inserted.
		 * @param array $data_arr Sanitized note data.
		 * @param Note $note Note object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_insert_note', $this->id, $data, $data_arr, $this );

		return true;
	}

	/**
	 *  Update an object in the database.
	 *
	 * This method is not meant to call publicly instead call save
	 * which will conditionally decide which method to call.
	 *
	 * @param array $fields An array of database fields and type.
	 *
	 * @return \WP_Error|true True on success, WP_Error on failure.
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 * @since 1.1.0
	 */
	protected function update( $fields ) {
		global $wpdb;
		$changes = $this->get_changes();
		$data    = wp_array_slice_assoc( $changes, array_keys( $fields ) );
		$format  = wp_array_slice_assoc( $fields, array_keys( $data ) );
		$data    = wp_unslash( $data );
		// Bail if nothing to save
		if ( empty( $data ) ) {
			return true;
		}

		/**
		 * Fires immediately before an existing note is updated in the database.
		 *
		 * @param int $note_id Note id.
		 * @param array $data Note data.
		 * @param array $changes The data will be updated.
		 * @param Note $note Note object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_update_note', $this->get_id(), $this->to_array(), $changes, $this );

		if ( false === $wpdb->update( $wpdb->prefix . 'ea_notes', $data, [ 'id' => $this->get_id() ], $format, [ 'id' => '%d' ] ) ) {
			return new \WP_Error( 'eaccounting_note_db_update_error', __( 'Could not update note in the database.', 'wp-ever-accounting' ), $wpdb->last_error );
		}

		/**
		 * Fires immediately after an existing note is updated in the database.
		 *
		 * @param int $note_id Note id.
		 * @param array $data Note data.
		 * @param array $changes The data will be updated.
		 * @param Note $note Note object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_update_note', $this->get_id(), $this->to_array(), $changes, $this );
	}

	/**
	 * Saves an object in the database.
	 *
	 * @return \WP_Error|int id on success, WP_Error on failure.
	 * @since 1.1.0
	 */
	public function save() {
		$user_id = get_current_user_id();
		$fields  = array(
			'id'           => '%d',
			'parent_id'    => '%d',
			'type'         => '%s',
			'content'      => '%s',
			'extra'        => '%s',
			'creator_id'   => '%d',
			'date_created' => '%s',
		);

		if ( empty( $this->get_prop( 'type' ) ) ) {
			return new \WP_Error( 'invalid_note_type', esc_html__( 'Note type is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_prop( 'parent_id' ) ) ) {
			return new \WP_Error( 'invalid_note_parent_id', esc_html__( 'Note parent id is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_prop( 'date_created' ) ) || '0000-00-00 00:00:00' === $this->get_prop( 'date_created' ) ) {
			$this->set_date_prop( 'date_created', current_time( 'mysql' ) );
		}

		if ( empty( $this->get_prop( 'creator_id' ) ) ) {
			$this->set_prop( 'creator_id', $user_id );
		}

		if ( $this->exists() ) {
			$is_error = $this->update( $fields );
		} else {
			$is_error = $this->insert( $fields );
		}

		if ( is_wp_error( $is_error ) ) {
			return $is_error;
		}


		$this->apply_changes();

		// Clear cache.
		wp_cache_delete( $this->get_id(), 'ea_notes' );
		wp_cache_set( 'last_changed', microtime(), 'ea_notes' );

		/**
		 * Fires immediately after a note is inserted or updated in the database.
		 *
		 * @param int $note_id Note id.
		 * @param Note $note Note object.
		 *
		 * @since 1.2.1
		 *
		 */
		do_action( 'eaccounting_saved_note', $this->get_id(), $this );

		return $this->get_id();
	}

	/**
	 * Deletes the object from database.
	 *
	 * @return array|false true on success, false on failure.
	 * @since 1.1.0
	 */
	public function delete() {
		global $wpdb;
		if ( ! $this->exists() ) {
			return false;
		}

		$data = $this->to_array();

		/**
		 * Filters whether an item delete should take place.
		 *
		 * @param bool|null $delete Whether to go forward with deletion.
		 * @param int $note_id Item id.
		 * @param array $data Item data array.
		 * @param Note $note Transaction object.
		 *
		 * @since 1.2.1
		 */
		$check = apply_filters( 'eaccounting_check_delete_note', null, $this->get_id(), $data, $this );
		if ( null !== $check ) {
			return $check;
		}

		/**
		 * Fires before a note is deleted.
		 *
		 * @param int $note_id Note id.
		 * @param array $data Note data array.
		 * @param Item $item Note object.
		 *
		 * @since 1.2.1
		 *
		 */
		do_action( 'eaccounting_pre_delete_note', $this->get_id(), $data, $this );

		$result = $wpdb->delete( $wpdb->prefix . 'ea_notes', array( 'id' => $this->get_id() ) );
		if ( ! $result ) {
			return false;
		}

		/**
		 * Fires after an item is deleted.
		 *
		 * @param int $note_id Item id.
		 * @param array $data Item data array.
		 *
		 * @since 1.2.1
		 *
		 */
		do_action( 'eaccounting_delete_note', $this->get_id(), $data );

		// Clear object.
		wp_cache_delete( $this->get_id(), 'ea_notes' );
		wp_cache_set( 'last_changed', microtime(), 'ea_notes' );
		$this->set_id( 0 );
		$this->set_defaults();

		return $data;
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	| Functions for setting note data. These should not update anything in the
	| database itself and should only change what is stored in the class
	| object.
	*/

	/**
	 * set the id.
	 *
	 * @param int $parent_id .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_parent_id( $parent_id ) {
		$this->set_prop( 'parent_id', absint( $parent_id ) );
	}

	/**
	 * set the id.
	 *
	 * @param string $type .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_type( $type ) {
		$this->set_prop( 'type', eaccounting_clean( $type ) );
	}

	/**
	 * set the note.
	 *
	 * @param string $note .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_content( $note ) {
		$this->set_prop( 'content', eaccounting_sanitize_textarea( $note ) );
	}

	/**
	 * set the note.
	 *
	 * @param string $extra .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_extra( $extra ) {
		$this->set_prop( 'extra', maybe_unserialize( $extra ) );
	}

	/**
	 * Set object creator id.
	 *
	 * @param int $creator_id Creator id
	 *
	 * @since 1.0.2
	 *
	 */
	public function set_creator_id( $creator_id = null ) {
		$this->set_prop( 'creator_id', absint( $creator_id ) );
	}
}
