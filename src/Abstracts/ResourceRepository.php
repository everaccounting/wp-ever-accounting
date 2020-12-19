<?php
/**
 * ResourceRepository
 *
 * An Abstract class for repository class.
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Abstracts
 */

namespace EverAccounting\Abstracts;

/**
 * Class ResourceRepository
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Abstracts
 */
abstract class ResourceRepository {
	/**
	 * Meta type. This should match up with
	 * the types available at https://developer.wordpress.org/reference/functions/add_metadata/.
	 * WP defines 'post', 'user', 'comment', and 'term'.
	 *
	 * @var string
	 */
	protected $meta_type = 'contact';


	/**
	 * Gets a list of props and meta keys that need updated based on change state
	 * or if they are present in the database or not.
	 *
	 * @param ResourceModel $object            The GetPaid_Data object.
	 * @param array         $meta_key_to_props A mapping of meta keys => prop names.
	 * @param string        $meta_type         The internal WP meta type (post, user, etc).
	 *
	 * @return array                        A mapping of meta keys => prop names, filtered by ones that should be updated.
	 */
	protected function get_props_to_update( $object, $meta_key_to_props, $meta_type = 'contact' ) {
		$props_to_update = array();
		$changed_props   = $object->get_changes();

		// Props should be updated if they are a part of the $changed array or don't exist yet.
		foreach ( $meta_key_to_props as $meta_key => $prop ) {
			if ( array_key_exists( $prop, $changed_props ) || ! metadata_exists( $meta_type, $object->get_id(), $meta_key ) ) {
				$props_to_update[ $meta_key ] = $prop;
			}
		}

		return $props_to_update;
	}


	/**
	 * Deletes meta based on meta ID.
	 *
	 * @since  1.1.0
	 *
	 * @param ResourceModel $object model object.
	 * @param \stdClass     $meta   (containing at least ->id).
	 */
	public function delete_meta( &$object, $meta ) {
		delete_metadata_by_mid( $this->meta_type, $meta->id );
	}

	/**
	 * Add new piece of meta.
	 *
	 * @since  1.1.0
	 *
	 * @param ResourceModel $object model object.
	 * @param \stdClass     $meta   (containing ->key and ->value).
	 *
	 * @return int meta ID
	 */
	public function add_meta( &$object, $meta ) {
		return add_metadata( $this->meta_type, $object->get_id(), $meta->key, is_string( $meta->value ) ? wp_slash( $meta->value ) : $meta->value, false );
	}

	/**
	 * Update meta.
	 *
	 * @since  1.1.0
	 *
	 * @param ResourceModel $object model object.
	 * @param \stdClass     $meta   (containing ->id, ->key and ->value).
	 */
	public function update_meta( &$object, $meta ) {
		update_metadata_by_mid( $this->meta_type, $meta->id, $meta->value, $meta->key );
	}


	/*
	|--------------------------------------------------------------------------
	| CRUD Methods
	|--------------------------------------------------------------------------
	*/
	/**
	 * Method to create a new item in the database.
	 *
	 * @param ResourceModel $item Item object.
	 *
	 * @throws \Exception
	 */
	public function insert( &$item ) {
		global $wpdb;

		$values  = array();
		$formats = array();

		$fields = $this->data_type;
		unset( $fields['id'] );

		foreach ( $fields as $key => $format ) {
			$method         = "get_$key";
			$values[ $key ] = $item->$method();
			$formats[]      = $format;
		}

		$result = $wpdb->insert( $wpdb->prefix . $this->table, wp_unslash( $values ), $formats );
		if ( false === $result ) {
			$item->error( 'db_insert_error', $wpdb->last_error );

			return false;
		}

		if ( $result ) {
			$item->set_id( $wpdb->insert_id );
			$item->apply_changes();
			$item->clear_cache();
			do_action( 'eacccounting_insert_' . $item->get_object_type(), $item, $values );

			return true;
		}

		return false;
	}

	/**
	 * Method to read a item from the database.
	 *
	 * @param ResourceModel $item Item object.
	 *
	 * @throws \Exception
	 */
	public function read( &$item ) {
		global $wpdb;
		$table = $wpdb->prefix . $this->table;

		$item->set_defaults();

		if ( ! $item->get_id() ) {
			$item->set_id( 0 );
			throw new \Exception( $wpdb->last_error );
		}

		// Maybe retrieve from the cache.
		$raw_item = wp_cache_get( $item->get_id(), $item->get_cache_group() );
		// If not found, retrieve from the db.
		if ( false === $raw_item ) {
			$raw_item = $wpdb->get_row(
				$wpdb->prepare(
					"SELECT * FROM {$table} WHERE id = %d",
					$item->get_id()
				)
			);

			// Update the cache with our data
			wp_cache_set( $item->get_id(), $raw_item, $item->get_cache_group() );
		}

		if ( ! $raw_item ) {
			$item->set_id( 0 );

			return;
		}

		foreach ( array_keys( $this->data_type ) as $key ) {
			$method = "set_$key";
			$item->$method( $raw_item->$key );
		}

		$item->set_object_read( true );
		do_action( 'eaccounting_read_' . $item->get_object_type(), $item );
	}

	/**
	 * Method to update an item in the database.
	 *
	 * @param ResourceModel $item Subscription object.
	 *
	 * @throws \Exception
	 */
	public function update( &$item ) {
		global $wpdb;
		$table   = $wpdb->prefix . $this->table;
		$changes = $item->get_changes();
		$values  = array();
		$formats = array();

		foreach ( $this->data_type as $key => $format ) {
			if ( array_key_exists( $key, $changes ) ) {
				$method         = "get_$key";
				$values[ $key ] = $item->$method();
				$formats[]      = $format;
			}
		}

		if ( empty( $values ) ) {
			return;
		}

		if ( false === $wpdb->update(
			$table,
			wp_unslash( $values ),
			array(
				'id' => $item->get_id(),
			),
			$formats,
			'%d'
		) ) {
			throw new \Exception( $wpdb->last_error );
		}

		// Apply the changes.
		$item->apply_changes();

		// Delete cache.
		$item->clear_cache();

		// Fire a hook.
		do_action( 'eaccounting_update_' . $item->get_object_type(), $changes, $item );

	}

	/**
	 * Method to delete a subscription from the database.
	 *
	 * @param ResourceModel $item
	 * @param array         $args Array of args to pass to the delete method.
	 */
	public function delete( &$item, $args = array() ) {
		global $wpdb;
		$table = $wpdb->prefix . $this->table;
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$table}
				WHERE id = %d",
				$item->get_id()
			)
		);

		// Delete cache.
		$item->clear_cache();

		// Fire a hook.
		do_action( 'eaccounting_delete_' . $item->get_object_type(), $item->get_id(), $item->get_data(), $item );

		$item->set_id( 0 );
	}
}
