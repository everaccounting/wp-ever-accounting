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

class License {
	/**
	 * Plugin name.
	 *
	 * @since 1.1.0
	 *
	 * @var bool|string
	 */
	public $plugin_name = false;

	/**
	 * Plugin slug.
	 *
	 * @since 1.1.0
	 *
	 * @var bool|string
	 */
	public $plugin_slug = false;

	/**
	 * Plugin id.
	 *
	 * @since 1.1.0
	 *
	 * @var bool|string
	 */
	public $plugin_id = false;

	/**
	 * Plugin path.
	 *
	 * @since 1.1.0
	 *
	 * @var bool|string
	 */
	public $plugin_file = false;

	/**
	 * License key for the plugin.
	 *
	 * @since 1.1.0
	 *
	 * @var bool|string
	 */
	public $license_key = false;

	/**
	 * Holds the update data returned from the API.
	 *
	 * @since 1.1.0
	 *
	 * @var bool|object
	 */
	public $update = false;

	/**
	 * Holds the update url.
	 *
	 * @since 1.1.0
	 *
	 * @var string
	 */
	private $api_url = 'https://wpeveraccounting.com/';

	/**
	 * Holds the update data returned from the API.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	private $api_data = array();

	/**
	 * Holds the short name.
	 *
	 * @since 1.1.0
	 *
	 * @var string
	 */
	private $short_name = '';

	/**
	 * Version #.
	 *
	 * @since 1.1.0
	 *
	 * @var string
	 */
	private $version = '';

	/**
	 * WP Override.
	 *
	 * @since 1.1.0
	 *
	 * @var bool
	 */
	private $wp_overide = false;

	/**
	 * Beta.
	 *
	 * @since 1.1.0
	 *
	 * @var bool
	 */
	private $beta = false;

	/**
	 * Cache Key.
	 *
	 * @since 1.1.0
	 *
	 * @var string
	 */
	private $cache_key = '';

	/**
	 * Holds the plugin info details for the update.
	 *
	 * @since 1.1.0
	 *
	 * @var bool
	 */
	public $info = false;


	/**
	 * License constructor.
	 *
	 * @param array $config
	 */
	public function __construct( array $config ) {
		// If the user cannot update plugins, stop processing here.
		if ( ! current_user_can( 'update_plugins' ) ) {
			return;
		}

		$config = wp_parse_args(
			$config,
			array(
				'plugin_name' => '',
				'plugin_slug' => '',
				'plugin_id'   => '',
				'plugin_file' => '',
				'version'     => '1.1.0',
				'option_key'  => '',
			)
		);

		$this->plugin_name = $config['plugin_name'];
		$this->plugin_slug = $config['plugin_slug'];
		$this->plugin_file = $config['plugin_file'];
		$this->version     = $config['version'];
		$this->short_name  = 'eaccounting_' . preg_replace( '/[^a-zA-Z0-9_\s]/', '', str_replace( ' ', '_', strtolower( $config['plugin_name'] ) ) );
		if ( empty( $config['option_key'] ) ) {
			$config['option_key'] = str_replace( 'eaccounting_', '', $this->short_name ) . '_license_key';
		}
		$this->license_key = eaccounting_get_option( $config['option_key'] );
		$this->cache_key   = 'eccounting_' . md5( serialize( $config['plugin_file'] . $config['key'] ) );
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
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ) );
	}

	/**
	 * Check for Updates at the defined API endpoint and modify the update array.
	 *
	 * This function dives into the update API just when WordPress creates its update array,
	 * then adds a custom API call and injects the custom plugin data retrieved from the API.
	 * It is reassembled from parts of the native WordPress plugin update code.
	 * See wp-includes/update.php line 121 for the original wp_update_plugins() function.
	 *
	 * @uses api_request()
	 *
	 * @param array   $_transient_data Update array build by WordPress.
	 * @return array Modified update array with custom plugin data.
	 */
	public function check_update( $_transient_data ) {
		global $pagenow;

		if ( ! is_object( $_transient_data ) ) {
			$_transient_data = new \stdClass;
		}

		if ( 'plugins.php' === $pagenow && is_multisite() ) {
			return $_transient_data;
		}
		$basename = basename( $this->plugin_file, '.php' );
		$name     = $basename . '/' . $basename . '.php';
		if ( ! empty( $_transient_data->response ) && ! empty( $_transient_data->response[ $name ] ) && false === $this->wp_overide ) {
			return $_transient_data;
		}

	}

}
