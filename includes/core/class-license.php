<?php
/**
 * Handles License for the plugin.
 *
 * @package        EverAccounting
 * @class          License
 * @version        1.0.2
 */

namespace EverAccounting\Core;

defined( 'ABSPATH' ) || exit;

/**
 * Class License
 * @package EverAccounting\Core
 */
class License {
	/**
	 * Holds the update url.
	 *
	 * @since 1.1.0
	 *
	 * @var string
	 */
	private $api_url = 'https://wpeveraccounting.com/';

	/**
	 * Plugin name.
	 *
	 * @since 1.1.0
	 *
	 * @var bool|string
	 */
	public $name = '';

	/**
	 * Plugin slug.
	 *
	 * @since 1.1.0
	 *
	 * @var string
	 */
	public $slug = '';

	/**
	 * Plugin path.
	 *
	 * @since 1.1.0
	 *
	 * @var string
	 */
	public $path = '';

	/**
	 * Plugin file.
	 *
	 * @since 1.1.0
	 *
	 * @var string
	 */
	public $plugin = '';

	/**
	 * Version number.
	 *
	 * @since 1.1.0
	 *
	 * @var string
	 */
	private $version = '';

	/**
	 * Holds the short name.
	 *
	 * @since 1.1.0
	 *
	 * @var string
	 */
	private $short_name = '';

	/**
	 * Item ID.
	 *
	 * @since 1.0.0
	 * @var int
	 */
	private $item_id;

	/**
	 * License key.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	private $license = '';

	/**
	 * License status.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	private $license_status = '';

	/**
	 * @since 1.1.0
	 * @var string
	 */
	private $cache_key;


	/**
	 * License constructor.
	 *
	 * @param string $path
	 * @param null   $item_id
	 */
	public function __construct( $path, $item_id = null ) {
		$plugin_data = get_file_data(
			$path,
			array(
				'name'    => 'Plugin Name',
				'version' => 'Version',
				'author'  => 'Author',
			),
			'plugin'
		);

		$short_name = basename( $path, '.php' );
		$short_name = preg_replace( '/[^a-zA-Z0-9\s]/', '', $short_name );
		$short_name = str_replace( 'wp_ever_accounting', '', $short_name );
		$short_name = str_replace( 'ever_accounting', '', $short_name );
		$short_name = str_replace( 'eaccounting', '', $short_name );
		$short_name = str_replace( 'eaccounting_', '', $short_name );

		$this->short_name     = "eaccounting_{$short_name}";
		$this->name           = $plugin_data['name'];
		$this->version        = $plugin_data['version'];
		$this->version        = $plugin_data['version'];
		$this->path           = $path;
		$this->slug           = basename( $path );
		$this->plugin         = plugin_basename( $path );
		$this->license        = trim( eaccounting_get_option( $this->short_name . '_license_key' ) );
		$this->license_status = get_option( $this->short_name . '_license_status' );
		$this->cache_key      = 'eccounting_' . md5( serialize( $plugin_data ) );
		$this->item_id        = $item_id;

		$this->init();
	}

	/**
	 * Init Method
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	public function init() {
		// Register settings
		add_filter( 'eaccounting_settings_licenses', array( $this, 'settings' ), 1 );
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ) );
		add_filter( 'plugins_api', array( $this, 'plugins_api_filter' ), 10, 3 );
		add_action( 'in_plugin_update_message-' . plugin_basename( $this->plugin ), array( $this, 'plugin_row_license_missing' ), 10, 2 );
		add_filter( 'http_request_args', array( $this, 'http_request_args' ), 10, 2 );

		// Activate license key on settings save
		add_action( 'admin_init', array( $this, 'activate_license' ) );

		// Deactivate license key
		add_action( 'admin_init', array( $this, 'deactivate_license' ) );
	}

	/**
	 * Add license field to settings
	 *
	 * @param array $settings
	 *
	 * @return  array
	 */
	public function settings( $settings ) {
		$license_settings = array(
			array(
				'id'             => $this->short_name . '_license_key',
				'name'           => sprintf( __( '%1$s', 'wp-ever-accounting' ), $this->name ), //phpcs:ignore
				'license_status' => $this->license_status,
				'desc'           => '',
				'type'           => 'license_key',
				'size'           => 'regular',
			),
		);

		return array_merge( $settings, $license_settings );
	}


	/**
	 * Check for Updates at the defined API endpoint and modify the update array.
	 *
	 * This function dives into the update API just when WordPress creates its update array,
	 * then adds a custom API call and injects the custom plugin data retrieved from the API.
	 * It is reassembled from parts of the native WordPress plugin update code.
	 * See wp-includes/update.php line 121 for the original wp_update_plugins() function.
	 *
	 * @param array $_transient_data Update array build by WordPress.
	 *
	 * @return array Modified update array with custom plugin data.
	 * @uses api_request()
	 *
	 */
	public function check_update( $_transient_data = null ) {
		global $pagenow;
		if ( ! is_object( $_transient_data ) ) {
			$_transient_data = new \stdClass;
		}

		if ( 'plugins.php' === $pagenow && is_multisite() ) {
			return $_transient_data;
		}

		if ( trailingslashit( home_url() ) === $this->api_url ) {
			return $_transient_data;
		}

		if ( ! empty( $_transient_data->response ) && ! empty( $_transient_data->response[ $this->plugin ] ) ) {
			return $_transient_data;
		}

		$version_info = $this->get_plugin_info();
		if ( false !== $version_info && is_object( $version_info ) && isset( $version_info->new_version ) ) {

			if ( version_compare( $this->version, $version_info->new_version, '<' ) ) {
				$_transient_data->response[ $this->plugin ] = $version_info;
			} else {
				// Populating the no_update information is required to support auto-updates in WordPress 5.5.
				$_transient_data->no_update[ $this->plugin ] = $version_info;
			}
		}
		$_transient_data->last_checked             = time();
		$_transient_data->checked[ $this->plugin ] = $this->version;

		return $_transient_data;
	}

	/**
	 * show update nofication row -- needed for multisite subsites, because WP won't tell you otherwise!
	 *
	 * @param string $file
	 * @param array  $plugin
	 */
	public function show_update_notification( $file, $plugin ) {
		if ( ! current_user_can( 'update_plugins' ) ) {
			return;
		}
		if ( ! is_multisite() ) {
			return;
		}

		if ( $this->plugin !== $file ) {
			return;
		}
		// Remove our filter on the site transient
		remove_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ), 10 );
		$update_cache = get_site_transient( 'update_plugins' );
		$update_cache = is_object( $update_cache ) ? $update_cache : new \stdClass();

	}

	/**
	 * Updates information on the "View version x.x details" page with custom data.
	 *
	 * @param mixed  $data
	 * @param string $action
	 * @param object $args
	 *
	 * @uses api_request()
	 *
	 */
	public function plugins_api_filter( $data, $action = '', $args = null ) {
		error_log(print_r($data, true ));
		error_log(print_r($args, true ));
		if ( 'plugin_information' !== $action ) {
			return $data;
		}

		$plugin_data = $this->get_plugin_info();
		error_log(print_r($data, true ));
		if ( ! isset( $args->slug ) || ( $args->slug !== $plugin_data->slug ) ) {
			return $data;
		}

		// Convert sections into an associative array, since we're getting an object, but Core expects an array.
		if ( isset( $data->sections ) && ! is_array( $data->sections ) ) {
			$data->sections = get_object_vars( $data->sections );
		}

		// Convert banners into an associative array, since we're getting an object, but Core expects an array.
		if ( isset( $data->banners ) && ! is_array( $data->banners ) ) {
			$data->banners = get_object_vars( $data->banners );
		}

		// Convert icons into an associative array, since we're getting an object, but Core expects an array.
		if ( isset( $data->icons ) && ! is_array( $data->icons ) ) {
			$data->icons = get_object_vars( $data->icons );
		}

		// Convert contributors into an associative array, since we're getting an object, but Core expects an array.
		if ( isset( $data->contributors ) && ! is_array( $data->contributors ) ) {
			$data->contributors = get_object_vars( $data->contributors );
		}

		if ( ! isset( $data->plugin ) ) {
			$data->plugin = $this->name;
		}

		return $data;
	}

	/**
	 * If available, show the changelog for sites in a multisite install.
	 */
	public function show_changelog() {

	}

	/**
	 * Displays message inline on plugin row that the license key is missing
	 *
	 * @since   1.1.0
	 * @return  void
	 */
	public function plugin_row_license_missing( $plugin_data, $version_info ) {
		if ( ( ! is_object( $this->license_status ) || 'valid' !== $this->license_status->license ) ) {
			echo '&nbsp;<strong><a href="' . esc_url( admin_url( 'admin.php?page=ea-settings&tab=licenses' ) ) . '">' . __( 'Enter valid license key for automatic updates.', 'wp-ever-accounting' ) . '</a></strong>';
		}

	}

	/**
	 * Disables SSL verification to prevent download package failures.
	 *
	 * @since 1.7.0
	 *
	 * @param array  $args Array of request args.
	 * @param string $url  The URL to be pinged.
	 *
	 * @return array $args Amended array of request args.
	 */
	public function http_request_args( $args, $url ) {

		// If this is an SSL request and we are performing an upgrade routine, disable SSL verification.
		if ( strpos( $url, 'https://' ) !== false && strpos( $url, 'edd_action=package_download' ) ) {
			$args['sslverify'] = false;
		}

		return $args;

	}

	/**
	 * Get version info.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $force
	 *
	 * @return bool|mixed|string
	 */
	public function get_plugin_info( $force = true ) {
		$version_info = $this->get_cached_info();
		if ( $force || false === $version_info ) {
			$version_info = $this->remote_request(
				'get_version',
				array(
					'license' => $this->license,
					'version' => $this->version,
					'url'     => home_url(),
					'beta'    => false,
					'item_id' => $this->item_id,
				)
			);
			$this->set_cache_info( $version_info );
		}

		return $version_info;
	}

	/**
	 * Activate the license key
	 *
	 * @return  void
	 */
	public function activate_license() {
		if ( ! isset( $_POST['eaccounting_settings'] ) ) {
			return;
		}
		if ( ! isset( $_REQUEST[ $this->short_name . '_license_key-nonce' ] ) || ! wp_verify_nonce( $_REQUEST[ $this->short_name . '_license_key-nonce' ], $this->short_name . '_license_key-nonce' ) ) {
			return;
		}
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( empty( $_POST['eaccounting_settings'][ $this->short_name . '_license_key' ] ) ) {
			delete_option( $this->short_name . '_license_status' );

			return;
		}

		foreach ( $_POST as $key => $value ) {
			if ( false !== strpos( $key, 'license_key_deactivate' ) ) {
				// Don't activate a key when deactivating a different key
				return;
			}
		}
		$details = get_option( $this->short_name . '_license_active' );

		if ( is_object( $details ) && 'valid' === $details->license ) {
			return;
		}

		$license = sanitize_text_field( $_POST['eaccounting_settings'][ $this->short_name . '_license_key' ] );

		if ( empty( $license ) ) {
			return;
		}

		$response = $this->remote_request(
			'activate_license',
			array(
				'license' => $license,
				'item_id' => $this->item_id,
			)
		);
		if ( ! $response ) {
			return;
		}

		// Tell WordPress to look for updates
		set_site_transient( 'update_plugins', null );
		update_option( $this->short_name . '_license_status', $response );
	}


	/**
	 * Deactivate the license key
	 *
	 * @return  void
	 */
	public function deactivate_license() {
		if ( ! isset( $_POST['eaccounting_settings'] ) ) {
			return;
		}

		if ( ! isset( $_POST['eaccounting_settings'][ $this->short_name . '_license_key' ] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_REQUEST[ $this->short_name . '_license_key-nonce' ], $this->short_name . '_license_key-nonce' ) ) {

			wp_die( __( 'Nonce verification failed', 'wp-ever-accounting' ), __( 'Error', 'wp-ever-accounting' ), array( 'response' => 403 ) );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Run on deactivate button press
		if ( isset( $_POST[ $this->short_name . '_license_key_deactivate' ] ) ) {

			$response = $this->remote_request(
				'deactivate_license',
				array(
					'license' => $this->license,
					'item_id' => $this->item_id,
				)
			);
			if ( ! $response ) {
				return;
			}

			// Tell WordPress to look for updates
			delete_option( $this->short_name . '_license_status' );
		}
	}

	/**
	 * Queries the remote URL via wp_remote_post and returns a json decoded response.
	 *
	 * @since 1.1.0
	 *
	 * @param string $action The name of the $_POST action var.
	 * @param array  $body   The content to retrieve from the remote URL.
	 *
	 * @return string|bool          Json decoded response on success, false on failure.
	 */
	public function remote_request( $action = 'get_version', $body = array() ) {
		$verify_ssl = $this->verify_ssl();
		$api_params = array(
			'edd_action' => $action,
			'license'    => ! empty( $body['license'] ) ? $body['license'] : '',
			'item_name'  => isset( $body['item_name'] ) ? $body['item_name'] : false,
			'item_id'    => isset( $body['item_id'] ) ? $body['item_id'] : false,
			'version'    => isset( $body['version'] ) ? $body['version'] : false,
			'slug'       => isset( $body['slug'] ) ? $body['slug'] : '',
			'url'        => home_url(),
			'beta'       => ! empty( $body['beta'] ),
		);

		$request = wp_remote_post(
			$this->api_url,
			array(
				'timeout'   => 15,
				'sslverify' => $verify_ssl,
				'body'      => $api_params,
			)
		);

		// Bail out early if there are any errors.
		if ( 200 !== wp_remote_retrieve_response_code( $request ) || is_wp_error( $request ) ) {
			return false;
		}

		$response = json_decode( wp_remote_retrieve_body( $request ) );

		if ( $response && isset( $response->sections ) ) {
			$response->sections = maybe_unserialize( $response->sections );
		}

		if ( $response && isset( $response->banners ) ) {
			$response->banners = maybe_unserialize( $response->banners );
		}

		if ( $response && isset( $response->icons ) ) {
			$response->icons = maybe_unserialize( $response->icons );
		}

		if ( ! empty( $response->sections ) ) {
			foreach ( $response->sections as $key => $section ) {
				$response->$key = (array) $section;
			}
		}

		return $response;
	}

	/**
	 * Queries the remote URL via wp_remote_post and returns a json decoded response.
	 *
	 * @since 1.1.0
	 *
	 * @param string $cache_key Cache key.
	 *
	 * @return mixed|bool          Json decoded response on success, false on failure.
	 */
	public function get_cached_info( $cache_key = '' ) {

		if ( empty( $cache_key ) ) {
			$cache_key = $this->cache_key;
		}

		$cache = get_option( $cache_key );

		if ( empty( $cache['timeout'] ) || current_time( 'timestamp' ) > $cache['timeout'] ) { // @codingStandardsIgnoreLine
			return false; // Cache is expired.
		}

		return json_decode( $cache['value'] );

	}

	/**
	 * Queries the remote URL via wp_remote_post and returns a json decoded response.
	 *
	 * @since 1.1.0
	 *
	 * @param string $value     Value.
	 * @param string $cache_key Cache key.
	 */
	public function set_cache_info( $value = '', $cache_key = '' ) {

		if ( empty( $cache_key ) ) {
			$cache_key = $this->cache_key;
		}

		$data = array(
			'timeout' => strtotime( '+2 hours', current_time( 'timestamp' ) ), // @codingStandardsIgnoreLine
			'value'   => wp_json_encode( $value ),
		);

		update_option( $cache_key, $data, 'no' );
	}

	/**
	 * Filter for vertify SSL.
	 *
	 * @since 1.1.0
	 */
	public function verify_ssl() {
		return (bool) apply_filters( 'edd_sl_api_request_verify_ssl', true, $this );
	}
}
