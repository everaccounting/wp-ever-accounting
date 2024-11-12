<?php

namespace EverAccounting;

use EverAccounting\Addons\Addon;

defined( 'ABSPATH' ) || exit;

/**
 * Extensions class.
 *
 * @since 1.0.0
 * @package EverAccounting
 */
class Addons extends \ArrayObject {

	/**
	 * Adds an addon to the collection.
	 *
	 * @since 1.0.0
	 * @param Addon $addon The addon to add.
	 */
	public function add( Addon $addon ) {
		if ( ! $this->offsetExists( $addon->get_slug() ) ) {
			$this->offsetSet( $addon->get_slug(), $addon );
		}
	}

	/**
	 * Gets an addon by its slug.
	 *
	 * @since 1.0.0
	 * @param string $slug The addon slug.
	 * @return Addon|null The addon or null if not found.
	 */
	public function get( $slug ) {
		return $this->offsetGet( $slug );
	}

	/**
	 * Gets all addons.
	 *
	 * @since 1.0.0
	 * @return Addon[] The addons.
	 */
	public function all() {
		return $this->getArrayCopy();
	}
}
