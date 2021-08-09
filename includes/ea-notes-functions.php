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
 * @param string $output The required return type. One of OBJECT, ARRAY_A, or ARRAY_N. Default OBJECT.
 *
 * @return Note|array|null
 * @since 1.1.0
 */
function eaccounting_get_note( $note, $output = OBJECT ) {
	if ( empty( $note ) ) {
		return null;
	}

	if ( $note instanceof Note ) {
		$_note = $note;
	} else {
		$_note = new Note( $note );
	}

	if ( !$_note->exists() ) {
		return null;
	}

	if ( ARRAY_A === $output ) {
		return $_note->to_array();
	}

	if ( ARRAY_N === $output ) {
		return array_values( $_note->to_array() );
	}

	return $_note;
}

/**
 *  Insert or update a note.
 *
 * @param array|object|Note $data An array, object, or note object of data arguments.
 *
 * @return Note|WP_Error The note object or WP_Error otherwise.
 * @global wpdb $wpdb WordPress database abstraction object.
 * @since 1.1.0
 */
function eaccounting_insert_note( $data ) {
	if ( $data instanceof Note ) {
		$data = $data->to_array();
	} elseif ( is_object( $data ) ) {
		$data = get_object_vars( $data );
	}

	if ( empty( $data ) || ! is_array( $data ) ) {
		return new WP_Error( 'invalid_note_data', __( 'Note could not be saved.', 'wp-ever-accounting' ) );
	}

	$data = wp_parse_args( $data, array( 'id' => null ) );
	$note = new Note( (int) $data['id'] );
	$note->set_props( $data );
	$is_error = $note->save();
	if ( is_wp_error( $is_error ) ) {
		return $is_error;
	}

	return $note;
}

/**
 * Delete an note.
 *
 * @param int $note_id Note ID
 *
 * @return array|false Note array data on success, false on failure.
 * @since 1.1.0
 */
function eaccounting_delete_note( $note_id ) {
	if ( $note_id instanceof Note ) {
		$note_id = $note_id->get_id();
	}

	if ( empty( $note_id ) ) {
		return false;
	}

	$note = new Note( (int) $note_id );
	if ( ! $note->exists() ) {
		return false;
	}

	return $note->delete();
}

/**
 * Retrieves an array of the notes matching the given criteria.
 *
 * @param array $args Arguments to retrieve notes.
 *
 * @return Note[]|int Array of note objects or count.
 * @since 1.1.0
 *
 */
function eaccounting_get_notes( $args = array() ) {
	$defaults = array(
		'number'        => 20,
		'orderby'       => 'name',
		'order'         => 'DESC',
		'include'       => array(),
		'exclude'       => array(),
		'no_found_rows' => false,
		'count_total'   => false,
	);

	$parsed_args = wp_parse_args( $args, $defaults );
	$query       = new \EverAccounting\Note_Query( $parsed_args );
	if ( true === $parsed_args['count_total'] ) {
		return $query->get_total();
	}


	return $query->get_results();
}
