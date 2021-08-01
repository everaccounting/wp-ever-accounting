<?php
/**
 * EverAccounting Notes Functions.
 *
 * All notes related function of the plugin.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

use \EverAccounting\Note;

defined( 'ABSPATH' ) || exit;

/**
 * Retrieves note data given a note id or note object.
 *
 * @param int|object|Note $note note to retrieve
 * @param string $output The required return type. One of OBJECT, ARRAY_A, or ARRAY_N.Default OBJECT.
 * @param string $filter Type of filter to apply. Accepts 'raw', 'edit', 'db', or 'display'. Default 'raw'.
 *
 * @return Note|array|null
 * @since 1.1.0
 */
function eaccounting_get_note( $note, $output = OBJECT, $filter = 'raw' ) {
	if ( empty( $note ) ) {
		return null;
	}

	if ( $note instanceof Note ) {
		$_note = $note;
	} elseif ( is_object( $note ) ) {
		$_note = new Note( $note );
	} else {
		$_note = Note::get_instance( $note );
	}

	if ( ! $_note ) {
		return null;
	}

	$_note = $_note->filter( $filter );

	if ( ARRAY_A === $output ) {
		return $_note->to_array();
	}

	if ( ARRAY_N === $output ) {
		return array_values( $_note->to_array() );
	}

	return $_note->filter( $filter );
}

/**
 *  Insert or update a note.
 *
 * @param array|object|Note $note_data An array, object, or note object of data arguments.
 *
 * @return Note|WP_Error The note object or WP_Error otherwise.
 * @global wpdb $wpdb WordPress database abstraction object.
 * @since 1.1.0
 */
function eaccounting_insert_note( $note_data ) {
	global $wpdb;
	$user_id = get_current_user_id();
	if ( $note_data instanceof Note ) {
		$note_data = $note_data->to_array();
	} elseif ( $note_data instanceof stdClass ) {
		$note_data = get_object_vars( $note_data );
	}

	$defaults = array(
		'parent_id'    => null,
		'type'         => '',
		'note'         => '',
		'extra'        => '',
		'creator_id'   => $user_id,
		'date_created' => null,
	);

	// Are we updating or creating?
	$id          = null;
	$update      = false;
	$data_before = array();
	if ( ! empty( $item_data['id'] ) ) {
		$update      = true;
		$id          = absint( $item_data['id'] );
		$data_before = eaccounting_get_note( $id, ARRAY_A );

		if ( is_null( $data_before ) ) {
			return new WP_Error( 'invalid_note_id', __( 'Invalid note id to update.' ) );
		}

		// Merge old and new fields with new fields overwriting old ones.
		$note_data   = array_merge( $data_before, $note_data );
		$data_before = $data_before->to_array();
	}

	$item_data = wp_parse_args( $note_data, $defaults );
	$data_arr  = eaccounting_sanitize_note( $note_data, 'db' );

	// Check required
	if ( empty( $data_arr['parent_id'] ) ) {
		return new WP_Error( 'invalid_note_parent_id', esc_html__( 'Note parent id is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data_arr['content'] ) ) {
		return new WP_Error( 'invalid_note_content', esc_html__( 'Note content id is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data_arr['type'] ) ) {
		return new WP_Error( 'invalid_note_type', esc_html__( 'Note type is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data_arr['date_created'] ) || '0000-00-00 00:00:00' === $data_arr['date_created'] ) {
		$data_arr['date_created'] = current_time( 'mysql' );
	}

	$fields = array_keys( $defaults );
	$data   = wp_array_slice_assoc( $data_arr, $fields );

	/**
	 * Filters note data before it is inserted into the database.
	 *
	 * @param array $data Data to be inserted.
	 * @param array $data_arr Sanitized data.
	 *
	 * @since 1.2.1
	 */
	$data = apply_filters( 'eaccounting_insert_note', $data, $data_arr );

	$data  = wp_unslash( $data );
	$where = array( 'id' => $id );


	if ( $update ) {

		/**
		 * Fires immediately before an existing note item is updated in the database.
		 *
		 * @param int $id Invoice item id.
		 * @param array $data Invoice item data to be inserted.
		 * @param array $changes Invoice item data to be updated.
		 * @param array $data_arr Sanitized invoice item data.
		 * @param array $data_before Invoice item previous data.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_update_invoice_item', $id, $data, $data_arr, $data_before );
		if ( false === $wpdb->update( $wpdb->prefix . 'ea_invoice_items', $data, $where, $data_before ) ) {
			new WP_Error( 'db_update_error', __( 'Could not update invoice item in the database.' ), $wpdb->last_error );
		}

		/**
		 * Fires immediately after an existing note is updated in the database.
		 *
		 * @param int $id Note id.
		 * @param array $data Note data to be inserted.
		 * @param array $changes Note data to be updated.
		 * @param array $data_arr Sanitized Note data.
		 * @param array $data_before Note previous data.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_update_note', $id, $data, $data_arr, $data_before );
	} else {

		/**
		 * Fires immediately before an existing note is inserted in the database.
		 *
		 * @param array $data Invoice item data to be inserted.
		 * @param string $data_arr Sanitized Invoice item data.
		 * @param array $item_data Invoice item data as originally passed to the function.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_insert_note', $data, $data_arr, $item_data );

		if ( false === $wpdb->insert( $wpdb->prefix . 'ea_notes', $data ) ) {
			new WP_Error( 'db_insert_error', __( 'Could not insert note into the database.' ), $wpdb->last_error );
		}

		$id = (int) $wpdb->insert_id;

		/**
		 * Fires immediately after an existing note is inserted in the database.
		 *
		 * @param int $id Note id.
		 * @param array $data Note has been inserted.
		 * @param array $data_arr Sanitized Note data.
		 * @param array $item_data Note data as originally passed to the function.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_insert_note', $id, $data, $data_arr, $item_data );
	}

	// Clear cache.
	wp_cache_delete( $id, 'ea_notes' );
	wp_cache_set( 'last_changed', microtime(), 'ea_notes' );

	// Get new item object.
	$note = eaccounting_get_note( $id );

	/**
	 * Fires once a note has been saved.
	 *
	 * @param int $id Note id.
	 * @param Note $note Note object.
	 * @param bool $update Whether this is an existing note being updated.
	 *
	 * @since 1.2.1
	 */
	do_action( 'eaccounting_saved_invoice_item', $id, $note, $update, $data_arr, $data_before );

	return $note;
}

/**
 * Delete a note.
 *
 * @param int $note_id Note id.
 *
 * @return Note |false|null Note data on success, false or null on failure.
 * @since 1.1.0
 */
function eaccounting_delete_note( $note_id ) {
	global $wpdb;

	$note = eaccounting_get_note( $note_id );
	if ( ! $note || ! $note->exists() ) {
		return false;
	}

	/**
	 * Filters whether an note delete should take place.
	 *
	 * @param bool|null $delete Whether to go forward with deletion.
	 * @param Note $note contact object.
	 *
	 * @since 1.2.1
	 */
	$check = apply_filters( 'eaccounting_pre_delete_note', null, $note );
	if ( null !== $check ) {
		return $check;
	}

	/**
	 * Fires before an note is deleted.
	 *
	 * @param int $note_id Contact id.
	 * @param Note $note note object.
	 *
	 * @since 1.2.1
	 *
	 * @see eaccounting_delete_note()
	 */
	do_action( 'eaccounting_before_delete_note', $note_id, $note );

	$result = $wpdb->delete( $wpdb->prefix . 'ea_notes', array( 'id' => $note_id ) );
	if ( ! $result ) {
		return false;
	}

	wp_cache_delete( $note_id, 'ea_notes' );
	wp_cache_set( 'last_changed', microtime(), 'ea_notes' );

	/**
	 * Fires after an note is deleted.
	 *
	 * @param int $note_id contact id.
	 * @param Note $note contact object.
	 *
	 * @since 1.2.1
	 *
	 * @see eaccounting_delete_note()
	 */
	do_action( 'eaccounting_delete_note', $note_id, $note );

	return $note;
}

/**
 * Sanitizes every note field.
 *
 * If the context is 'raw', then the note object or array will get minimal
 * sanitization of the integer fields.
 *
 * @param object|array $note The invoice item object or array
 * @param string $context Optional. How to sanitize post fields. Accepts 'raw', 'edit', 'db', 'display'. Default 'display'.
 *
 * @return object|Note|array The now sanitized note object or array
 * @see eaccounting_sanitize_note_field()
 *
 * @since 1.2.1
 *
 */
function eaccounting_sanitize_note( $note, $context = 'raw' ) {
	if ( is_object( $note ) ) {
		// Check if post already filtered for this context.
		if ( isset( $note->filter ) && $context == $note->filter ) {
			return $note;
		}
		if ( ! isset( $note->id ) ) {
			$note->id = 0;
		}

		foreach ( array_keys( get_object_vars( $note ) ) as $field ) {
			$note->$field = eaccounting_sanitize_note_field( $field, $note->$field, $note->id, $context );
		}
		$note->filter = $context;
	} elseif ( is_array( $note ) ) {
		// Check if post already filtered for this context.
		if ( isset( $note['filter'] ) && $context == $note['filter'] ) {
			return $note;
		}
		if ( ! isset( $note['id'] ) ) {
			$note['id'] = 0;
		}
		foreach ( array_keys( $note ) as $field ) {
			$note[ $field ] = eaccounting_sanitize_note_field( $field, $note[ $field ], $note['id'], $context );
		}
		$note['filter'] = $context;
	}

	return $note;
}

/**
 * Sanitizes note field based on context.
 *
 * Possible context values are:  'raw', 'edit', 'db', 'display'.
 *
 * @param string $field The note Object field name.
 * @param mixed $value The note Object value.
 * @param int $note_id note id.
 * @param string $context Optional. How to sanitize the field. Possible values are 'raw', 'edit','db', 'display'. Default 'display'.
 *
 * @return mixed Sanitized value.
 * @since 1.2.1
 *
 */
function eaccounting_sanitize_note_field( $field, $value, $note_id, $context ) {
	if ( false !== strpos( $field, '_id' ) || $field === 'id' ) {
		$value = absint( $value );
	}

	$context = strtolower( $context );

	if ( 'raw' === $context ) {
		if ( $field === 'extra' ) {
			$value = maybe_unserialize( $value );
		}

		return $value;
	}

	if ( 'edit' === $context ) {

		/**
		 * Filters note field to edit before it is sanitized.
		 *
		 * @param mixed $value Value of the note field.
		 * @param int $note_id Note id.
		 *
		 * @since 1.2.1
		 *
		 */
		$value = apply_filters( "eaccounting_edit_note_{$field}", $value, $note_id );

	} elseif ( 'db' === $context ) {

		/**
		 * Filters note field value before it is sanitized.
		 *
		 * @param mixed $value Value of the note field.
		 * @param int $note_id Note id.
		 *
		 * @since 1.2.1
		 *
		 */
		$value = apply_filters( "eaccounting_pre_note_{$field}", $value, $note_id );
	} else {
		// Use display filters by default.

		/**
		 * Filters the note field sanitized for display.
		 *
		 * @param mixed $value Value of the note field.
		 * @param int $note_id Note id.
		 * @param string $context Context to retrieve the account field value.
		 *
		 * @since 1.2.1
		 *
		 */
		$value = apply_filters( "eaccounting_note_{$field}", $value, $note_id, $context );
	}

	return $value;
}
