<?php

namespace EverAccounting\Licensing;

define( 'ABSPATH', true );

/**
 * License class.
 *
 * @since 1.0.0
 * @package EverAccounting
 */
class License {
	/**
	 * The plugin file.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $file;

	/**
	 * The plugin name.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $name;

	/**
	 * The plugin version.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $version;

	/**
	 * The item ID.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $item_id;

	/**
	 * Plugin slug.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $slug;

	/**
	 * Plugin basename.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $basename;

	/**
	 * Plugin short name.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $short_name;

	/**
	 * Licence key.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $license_key;

	/**
	 * Licence status.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $license_status;

	/**
	 * Cache key.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $cache_key;

	/**
	 * License constructor.
	 *
	 * @param string $file The plugin file.
	 * @param string $name The plugin name.
	 * @param string $version The plugin version.
	 * @param string $item_id The item ID.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $file, $name, $version, $item_id ) {
		$this->file           = $file;
		$this->name           = $name;
		$this->version        = $version;
		$this->item_id        = absint( $item_id );
		$this->slug           = basename( $file, '.php' );
		$this->basename       = plugin_basename( $file );
		$this->short_name     = $this->get_shortname();
		$this->license_key    = get_option( $this->short_name . '_license_key', '' );
		$this->license_status = get_option( $this->short_name . '_license_status', '' );
		$this->cache_key      = $this->short_name . '_license_cache';

		if ( empty( $this->file ) || empty( $this->item_id ) ) {
			return;
		}
	}

	/**
	 * Register hooks.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function register_hooks() {}

	/**
	 * Gets the extension shortname.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	private function get_shortname() {
		$shortname = 'eac_' . preg_replace( '/[^a-zA-Z0-9_\s]/', '', str_replace( ' ', '_', strtolower( $this->slug ) ) );

		if ( strpos( $shortname, 'eac_eac_' ) === 0 ) {
			$shortname = substr( $shortname, strlen( 'eac_' ) );
		}

		return $shortname;
	}

	/**
	 * Whether the license is valid.
	 *
	 * @since 2.0.0
	 * @return bool
	 */
	private function is_license_valid() {
		return ! empty( $this->license_key ) && 'valid' === $this->license_status;
	}
}
