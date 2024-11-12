<?php

namespace EverAccounting\Addons;

defined( 'ABSPATH' ) || exit;

/**
 * Updater class.
 *
 * @since 1.0.0
 * @package EverAccounting
 */
class Updater {

	/**
	 * Plugin instance.
	 *
	 * @since 1.0.0
	 * @var Addon
	 */
	protected $addon;

	/**
	 * Updater constructor.
	 *
	 * @param Addon $addon The addon.
	 *
	 * @throws \InvalidArgumentException If the addon is not an instance of Addon.
	 * @since 1.0.0
	 */
	public function __construct( $addon ) {
		// if the addon is not an extended class of Addon, throw an exception.
		if ( ! is_subclass_of( $addon, Addon::class ) ) {
			throw new \InvalidArgumentException( 'The addon must be an instance of Addon.' );
		}

		$this->addon = $addon;
	}
}
