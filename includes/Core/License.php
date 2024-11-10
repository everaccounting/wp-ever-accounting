<?php

namespace EverAccounting\Core;

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
		$this->short_name     = sanitize_key( str_replace( '-', '_', $this->slug ) );
		$this->license_key    = get_option( $this->short_name . '_license_key', '' );
		$this->license_status = get_option( $this->short_name . '_license_status', '' );
		$this->cache_key      = $this->short_name . '_license_cache';

		if ( empty( $this->file ) || empty( $this->item_id ) ) {
			return;
		}

		add_action( 'admin_notices', array( $this, 'admin_notice' ), PHP_INT_MAX );
//		add_action( 'plugin_action_links_' . $this->basename, array( $this, 'add_license_link' ) );
//		add_action( 'after_plugin_row_' . $this->basename, array( $this, 'add_license_row' ), PHP_INT_MAX );
//		add_action( 'wp_ajax_' . $this->basename . '_license_action', array( $this, 'handle_license_action' ) );
//		add_filter( 'plugins_api', array( $this, 'plugins_api_filter' ), 10, 3 );
//		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_for_update' ) );
//		add_action( 'wp_version_check', array( $this, 'refresh_license_status' ) );
	}

	/**
	 * Display admin notice.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function admin_notice() {
		if ( ! current_user_can( 'manage_options' ) || $this->is_valid() ) {
			return;
		}
		$notice = sprintf(
		// translators: %1$s: <a> tag start, %2$s: <a> tag end, %3$s: plugin name.
			__( 'Please %1$sactivate%2$s your copy of %3$s to receive automatic updates, access to support and & other resources!', 'wp-ever-accounting' ),
			'<a href="' . admin_url( 'plugins.php' ) . '">',
			'</a>',
			'<strong>' . esc_html( $this->name ) . '</strong>'
		);
		echo '<div class="notice notice-warning is-dismissible"><p>' . wp_kses_post( $notice ) . '</p></div>';
	}
}
