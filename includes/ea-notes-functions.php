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
 * @param $item
 *
 * @return EverAccounting\Models\Note|null
 * @since 1.1.0
 *
 */
function eaccounting_get_note( $item ) {
	if ( empty( $item ) ) {
		return null;
	}
	try {
		$result = new EverAccounting\Models\Note( $item );

		return $result->exists() ? $result : null;
	} catch ( \Exception $e ) {
		return null;
	}
}

/**
 * Insert note.
 *
 * @param      $args
 * @param bool $wp_error
 * @since 1.1.0
 *
 * @return \EverAccounting\Models\Note|false|int|WP_Error
 */
function eaccounting_insert_note( $args, $wp_error = true ) {
	// Ensure that we have data.
	if ( empty( $args ) ) {
		return false;
	}
	try {
		// The  id will be provided when updating an item.
		$args = wp_parse_args( $args, array( 'id' => null ) );

		// Retrieve the item.
		$item = new \EverAccounting\Models\Note( $args['id'] );

		// Load new data.
		$item->set_props( $args );

		// Save the item
		$item->save();

		return $item;
	} catch ( \Exception $e ) {
		return $wp_error ? new WP_Error( $e->getMessage(), array( 'status' => $e->getCode() ) ) : 0;
	}
}

/**
 * Delete an item.
 *
 * @param $note_id
 *
 * @return bool
 * @since 1.1.0
 *
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
 * @param array $args
 * @since 1.1.0
 *
 * @return array|void
 */
function eaccounting_get_notes( $args = array() ) {
	try {
		/* @var $repository \EverAccounting\Repositories\Notes */
		$repository = \EverAccounting\Core\Repositories::load( 'notes' );
		return $repository->get_notes( $args );

	} catch ( \Exception $e ) {
		return array();
	}
}
