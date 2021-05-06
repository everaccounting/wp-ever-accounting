<?php
/**
 * Taxonomy Repository
 *
 * An Abstract class for repository class.
 *
 * @since   1.1.3
 *
 * @package EverAccounting\Abstracts
 */

namespace EverAccounting\Abstracts;

defined( 'ABSPATH' ) || exit;

/**
 * Class Taxonomy_Repository
 *
 * @since   1.1.3
 *
 * @package EverAccounting\Abstracts
 */
abstract class Taxonomy_Repository extends Resource_Repository {
	/**
	 * Taxonomy type
	 *
	 * @var string
	 */
	protected $taxonomy_type = '';

	/**
	 * Meta type.
	 *
	 * @var string
	 */
	protected $meta_type = 'term';

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
	 * @throws \Exception If any
	 */
	public function read( &$item ) {
		$item->set_defaults();
		$object = get_term( $item->get_id() );
		if ( ! $item->get_id() || ! $object || $this->taxonomy_type !== $object->taxonomy ) {
			$item->set_id( 0 );
			throw new \Exception( __( 'Invalid id', 'wp-ever-accounting' ) );
		}
		foreach ( $this->fields_to_props as $prop => $field ) {
			$method = "set_$prop";
			$item->$method( $object->$field );
		}

		$item->set_props(
			[
				'id'          => $object->term_id,
				'title'       => $object->name,
				'parent'      => $object->parent,
				'description' => $object->description,
				'post_count'  => $object->count,
			]
		);

		$this->read_object_meta( $item );
		$item->set_object_read( true );
		do_action( 'eaccounting_read_' . $item->get_object_type(), $item->get_id(), $item );
	}

	/**
	 * Inset term.
	 *
	 * @param Resource_Model $item project item.
	 *
	 * @return bool|void
	 */
	public function insert( &$item ) {
		// Create a term
		$field_map = $this->fields_to_props;
		$post_data = [];
		unset( $field_map['id'] );
		foreach ( $field_map as $prop => $field ) {
			$method              = "get_$prop";
			$data                = $item->$method();
			$post_data[ $field ] = $data;
		}
		$term        = $post_data['term'];
		$description = isset( $post_data['description'] ) ? $post_data['description'] : '';
		$parent      = isset( $post_data['parent'] ) ? $post_data['parent'] : 0;

		$term = wp_insert_term( $term, $this->taxonomy_type, apply_filters('eaccounting_new_'.$item->get_object_type().'_data',array( 'description' => $description, 'parent' => $parent ) ) ); //phpcs:ignore

		if ( $term && ! is_wp_error( $term ) ) {
			// update meta data
			$item->set_id( $term['term_id'] );
			$this->update_object_meta( $item );
			$item->save_meta_data();

			$item->apply_changes();

			do_action( 'eaccounting_insert_' . $item->get_object_type(), $term['term_id'], $item, $post_data );

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
		if ( array_intersect( array_keys( $this->fields_to_props ), array_keys( $changes ) ) ) {
			$post_data = [];
			foreach ( $this->fields_to_props as $prop => $field ) {
				$method              = "get_$prop";
				$data                = $item->$method();
				$post_data[ $field ] = $data;
			}
			if ( doing_action( 'saved_term' ) ) {
				$GLOBALS['wpdb']->update( $GLOBALS['wpdb']->term_taxonomy, $post_data, array( 'term_id' => $item->get_id() ) );
				clean_term_cache( $item->get_id() );
			} else {
				wp_update_term( $item->get_id(), $this->taxonomy_type, $post_data );
			}

			$this->update_object_meta( $item );
			// Apply the changes.
			$item->apply_changes();

			// Delete cache.
			$item->clear_cache();
			$item->read_meta_data( true ); // Refresh internal meta data, in case things were hooked into `saved_term` or another WP hook.

			// Fire a hook.
			do_action( 'eaccounting_update_' . $item->get_object_type(), $item->get_id(), $item, $changes );
		}
	}

	/**
	 * Method to delete a Taxonomy from the database.
	 *
	 * @param Resource_Model $item Taxonomy item.
	 */
	public function delete( &$item ) {
		$id = $item->get_id();
		wp_delete_term( $id, $this->taxonomy_type );
		// Delete cache.
		$item->clear_cache();
		// Fire a hook.
		do_action( 'eaccounting_delete' . $item->get_object_type(), $item->get_id(), $item );
		$item->set_id( 0 );
		$item->set_defaults();
	}

}
