<?php
namespace EverAccounting\Abstracts;

/**
 * Class Registry
 * @since 1.0.2
 * @package EverAccounting\Abstracts
 */
abstract class Registry extends \ArrayObject {

	/**
	 * Array of registry items.
	 *
	 * @since 1.0.2
	 * @var   array
	 */
	private $items = array();

	/**
	 * Initialize the registry.
	 *
	 * Each sub-class will need to do various initialization operations in this method.
	 *
	 * @since 1.0.2
	 */
	abstract public function init();

	/**
	 * Adds an item to the registry.
	 *
	 * @since 1.0.2
	 *
	 * @param int    $item_id   Item ID.
	 * @param array  $attributes {
	 *     Item attributes.
	 *
	 *     @type string $class Item handler class.
	 *     @type string $file  Item handler class file.
	 * }
	 * @return true Always true.
	 */
	public function add_item( $item_id, $attributes ) {
		foreach ( $attributes as $attribute => $value ) {
			$this->items[ $item_id ][ $attribute ] = $value;
		}

		return true;
	}

	/**
	 * Removes an item from the registry by ID.
	 *
	 * @since 1.0.2
	 *
	 * @param string $item_id Item ID.
	 */
	public function remove_item( $item_id ) {
		unset( $this->items[ $item_id ] );
	}

	/**
	 * Retrieves an item and its associated attributes.
	 *
	 * @since 1.0.2
	 *
	 * @param string $item_id Item ID.
	 * @return array|false Array of attributes for the item if registered, otherwise false.
	 */
	public function get( $item_id ) {
		if ( isset( $this->items[ $item_id ] ) ) {
			return $this->items[ $item_id ];
		}

		return false;
	}

	/**
	 * Retrieves registered items.
	 *
	 * @since 1.0.2
	 *
	 * @return array The list of registered items.
	 */
	public function get_items() {
		return $this->items;
	}

	/**
	 * Only intended for use by tests.
	 *
	 * @since 1.0.2
	 */
	public function _reset_items() {
		$this->items = array();
	}

	/**
	 * Determines whether an item exists.
	 *
	 * @since 1.0.2
	 *
	 * @param string $offset Item ID.
	 * @return bool True if the item exists, false on failure.
	 */
	public function offsetExists( $offset ) {
		if ( false !== $this->get( $offset ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Retrieves an item by its ID.
	 *
	 * Defined only for compatibility with ArrayAccess, use get() directly.
	 *
	 * @since 1.0.2
	 *
	 * @param string $offset Item ID.
	 * @return mixed The registered item, if it exists.
	 */
	public function offsetGet( $offset ) {
		return $this->get( $offset );
	}

	/**
	 * Adds/overwrites an item in the registry.
	 *
	 * Defined only for compatibility with ArrayAccess, use add_item() directly.
	 *
	 * @since 1.0.2
	 *
	 * @param string $offset Item ID.
	 * @param mixed  $value  Item attributes.
	 */
	public function offsetSet( $offset, $value ) {
		$this->add_item( $offset, $value );
	}

	/**
	 * Removes an item from the registry.
	 *
	 * Defined only for compatibility with ArrayAccess, use remove_item() directly.
	 *
	 * @since 1.0.2
	 *
	 * @param string $offset Item ID.
	 */
	public function offsetUnset( $offset ) {
		$this->remove_item( $offset );
	}
}
