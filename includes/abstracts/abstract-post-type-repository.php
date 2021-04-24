<?php
/**
 * Post Repository
 *
 * An Abstract class for repository class.
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Abstracts
 */

namespace EverAccounting\Abstracts;

defined( 'ABSPATH' ) || exit;

/**
 * Class Resource_Repository
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Abstracts
 */
abstract class Post_Type_Repository extends Resource_Repository {
	/**
	 * Post type
	 *
	 * @var string
	 */
	protected $post_type = '';

	/**
	 * Meta type.
	 *
	 * @var string
	 */
	protected $meta_type = 'post';

	/**
	 * Internal.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $internal_meta_keys = array();


	/**
	 * A map of meta keys to data props.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $meta_key_to_props = array();

	/**
	 * A map of database fields to data props.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $fields_to_props = array();

	/**
	 * Method to read a item from the database.
	 *
	 * @param Resource_Model $item Item object.
	 *
	 * @throws \Exception
	 */
	public function read( &$item ) {
		$item->set_defaults();
		$object = get_post( $item->get_id() );
		if ( ! $item->get_id() || ! $object || $this->post_type !== $object->post_type ) {
			$item->set_id( 0 );
			throw new \Exception( 'Invalid id' );
		}
		foreach ( $this->fields_to_props as $prop => $field ) {
			$method = "set_$prop";
			$item->$method( $object->$field );
		}

		$item->set_props(
			[
				'id'          => $object->ID,
				'title'       => $object->post_title,
				'description' => $object->post_content,
			]
		);

		$this->read_object_meta( $item );
		$item->set_object_read( true );
		do_action( 'eaccounting_read_' . $item->get_object_type(), $item->get_id(), $item );
	}

	/**
	 * Inset project.
	 *
	 * @param Resource_Model $item project item.
	 *
	 * @return bool|void
	 */
	public function insert( &$item ) {
		// Create a project.
		$field_map = $this->fields_to_props;
		$post_data = [];
		unset( $field_map['id'] );
		foreach ( $field_map as $prop => $field ) {
			$method              = "get_$prop";
			$data                = $item->$method();
			$post_data[ $field ] = $data;
		}
		$post_data = array_merge(
			$post_data,
			[
				'post_type'   => $this->post_type,
				'post_status' => 'publish',
				'ping_status' => 'closed',
			]
		);
		$id        = wp_insert_post(
			apply_filters(
				'eaccounting_new_' . $item->get_object_type() . '_data',
				$post_data
			),
			true
		);

		if ( $id && ! is_wp_error( $id ) ) {

			// Update meta data.
			$item->set_id( $id );
			$this->update_object_meta( $item );
			$item->save_meta_data();
			$item->apply_changes();
			do_action( 'eaccounting_insert_' . $item->get_object_type(), $id, $item, $post_data );

			return true;
		}

		return false;
	}

	/**
	 * Method to update an item in the database.
	 *
	 * @param Resource_Model $item object.
	 */
	public function update( &$item ) {
		$changes = $item->get_changes();

		// Only update the post when the post data changes.
		if ( array_intersect(
			array_keys( $this->fields_to_props ),
			array_keys( $changes )
		) ) {
			$post_data = [];
			foreach ( $this->fields_to_props as $prop => $field ) {
				$method              = "get_$prop";
				$data                = $item->$method();
				$post_data[ $field ] = $data;
			}
			if ( doing_action( 'save_post' ) ) {
				$GLOBALS['wpdb']->update( $GLOBALS['wpdb']->posts, $post_data, array( 'ID' => $item->get_id() ) );
				clean_post_cache( $item->get_id() );
			} else {
				wp_update_post( array_merge( array( 'ID' => $item->get_id() ), $post_data ) );
			}
			$this->update_object_meta( $item );
			// Apply the changes.
			$item->apply_changes();

			// Delete cache.
			$item->clear_cache();
			$item->read_meta_data( true ); // Refresh internal meta data, in case things were hooked into `save_post` or another WP hook.

			// Fire a hook.
			do_action( 'eaccounting_update_' . $item->get_object_type(), $item->get_id(), $item, $changes );
		}
	}

	/**
	 * Method to delete a subscription from the database.
	 *
	 * @param Resource_Model $item Project item.
	 */
	public function delete( &$item ) {
		$id = $item->get_id();
		wp_delete_post( $id, true );
		// Delete cache.
		$item->clear_cache();
		// Fire a hook.
		do_action( 'eaccounting_delete' . $item->get_object_type(), $item->get_id(), $item );
		$item->set_id( 0 );
		$item->set_defaults();
	}
}
