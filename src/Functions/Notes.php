<?php
/**
 * EverAccounting Notes Functions.
 *
 * All notes related function of the plugin.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

use EverAccounting\Models\Note;

defined( 'ABSPATH' ) || exit;

/**
 * Main function for returning note.
 *
 * @param mixed $item Note item.
 *
 * @return Note|null
 * @since 1.1.0
 */
function eac_get_note( $item ) {
	return Note::get( $item );
}

/**
 * Insert note.
 *
 * @param  array $args Note arguments.
 * @param bool  $wp_error Return WP_Error on failure.
 * @since 1.1.0
 *
 * @return Note|false|int|WP_Error
 */
function eac_insert_note( $args, $wp_error = true ) {
	return Note::insert( $args, $wp_error );
}

/**
 * Delete an item.
 *
 * @param int $note_id Item ID.
 *
 * @return bool
 * @since 1.1.0
 */
function eac_delete_note( $note_id ) {
	$note = eac_get_note( $note_id );

	if ( ! $note ) {
		return false;
	}

	return $note->delete();
}

/**
 * Get notes.
 *
 * @param array $args Query arguments.
 * @param bool  $count Optional. Whether to return count or not. Default false.
 * @since 1.1.0
 *
 * @return array|int|Note[]
 */
function eac_get_notes( $args = array(), $count = false ) {
	$defaults = array(
		'limit'   => 20,
		'orderby' => 'id',
		'order'   => 'DESC',
		'fields'  => 'all',
	);

	$args = wp_parse_args( $args, $defaults );

	if ( $count ) {
		return Note::count( $args );
	}

	return Note::query( $args );
}
