<?php
/**
 * EverAccounting Notes Functions.
 *
 * All notes related function of the plugin.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

defined( 'ABSPATH' ) || exit;

/**
 * Main function for returning note.
 *
 * @param mixed $item Note item.
 *
 * @return EverAccounting\Models\Note|null
 * @since 1.1.0
 */
function eaccounting_get_note( $item ) {
	return \EverAccounting\Models\Note::get( $item );
}

/**
 * Insert note.
 *
 * @param  array $args Note arguments.
 * @param bool  $wp_error Return WP_Error on failure.
 * @since 1.1.0
 *
 * @return \EverAccounting\Models\Note|false|int|WP_Error
 */
function eaccounting_insert_note( $args, $wp_error = true ) {
	return \EverAccounting\Models\Note::insert( $args, $wp_error );
}

/**
 * Delete an item.
 *
 * @param int $note_id Item ID.
 *
 * @return bool
 * @since 1.1.0
 */
function eaccounting_delete_note( $note_id ) {
	try {
		$item = new EverAccounting\Models\Note( $note_id );

		return $item->exists() ? $item->delete() : false;
	} catch ( \Exception $e ) {
		return false;
	}
}

/**
 * Get notes.
 *
 * @param array $args Query arguments.
 * @since 1.1.0
 *
 * @return array|void
 */
function eaccounting_get_notes( $args = array() ) {
	return EverAccounting\Models\Note::query( $args );
}
