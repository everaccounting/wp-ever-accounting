<?php

namespace EverAccounting;

defined( 'ABSPATH' ) || exit;

/**
 * Class Scripts.
 *
 * @since   1.1.6
 * @package EverAccounting
 */
class Scripts extends Singleton {

	/**
	 * Scripts constructor.
	 *
	 * @since 1.1.6
	 */
	protected function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'register_styles' ), - 1 );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_styles' ), - 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ), - 1 );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ), - 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_asset_data' ) );
	}

	/**
	 * Register styles.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	public function register_styles() {
		$styles = apply_filters( 'ever_accounting_styles', array(
			'eac-components' => array(
				'src' => 'packages/components/style.css',
			),
			'eac-admin'      => array(
				'src' => 'client/admin/style.css',
			),
			'eac-public'     => array(
				'src' => 'client/public/style.css',
			),
			'eac-wizard'     => array(
				'src' => 'client/wizard/style.css',
			),
		) );

		foreach ( $styles as $handle => $style ) {
			$style = wp_parse_args( $style, array(
				'src'  => '',
				'deps' => array(),
			) );
			if ( ! preg_match( '/^(http|https):\/\//', $style['src'] ) ) {
				$url  = path_join( EAC_ASSETS_URL, $style['src'] );
				$path = path_join( EAC_ASSETS_DIR, $style['src'] );
			} else {
				$url  = $style['src'];
				$path = str_replace( plugin_dir_url( __DIR__ ), plugin_dir_path( __DIR__ ), $style['src'] );
			}
			$php_file = str_replace( '.css', '.asset.php', $path );
			$asset    = $php_file && file_exists( $php_file ) ? require $php_file : [ 'version' => EAC_VERSION ];
			$ver      = $asset['version'];

			wp_register_style( $handle, $url, $style['deps'], $ver );

			//set rtl support.
			wp_style_add_data( $handle, 'rtl', 'replace' );
		}
	}

	/**
	 * Register scripts.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	public function register_scripts() {
		$scripts = apply_filters( 'ever_accounting_scripts', array(
			'eac-components' => array(
				'src' => 'packages/components/index.js',
			),
			'eac-data'       => array(
				'src' => 'packages/data/index.js',
			),
			'eac-admin'      => array(
				'src'  => 'client/admin/index.js',
				'deps' => array( 'eac-components', 'eac-data' ),
			),
		) );

		foreach ( $scripts as $handle => $script ) {
			$script = wp_parse_args( $script, array(
				'src'       => '',
				'deps'      => array(),
				'in_footer' => true,
			) );
			if ( ! preg_match( '/^(http|https):\/\//', $script['src'] ) ) {
				$url  = path_join( EAC_ASSETS_URL, $script['src'] );
				$path = path_join( EAC_ASSETS_DIR, $script['src'] );
			} else {
				$url  = $script['src'];
				$path = str_replace( plugin_dir_url( __DIR__ ), plugin_dir_path( __DIR__ ), $script['src'] );
			}
			$php_file = str_replace( '.js', '.asset.php', $path );
			$asset    = $php_file && file_exists( $php_file ) ? require $php_file : [
				'dependencies' => [],
				'version'      => EAC_VERSION,
			];

			$deps = array_merge( $asset['dependencies'], $script['deps'] );
			$ver  = $asset['version'];

			wp_register_script( $handle, $url, $deps, $ver, $script['in_footer'] );

			//set script translation.
			wp_set_script_translations( $handle, 'wp-ever-accounting' );
		}
	}

	/**
	 * Enqueue admin scripts.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	public function enqueue_admin_scripts() {
		if ( ! isset( $_GET['page'] ) || $_GET['page'] !== 'accounting' ) {
			return;
		}

		wp_enqueue_script( 'eac-admin' );
		wp_enqueue_style( 'eac-admin' );
	}


	/**
	 * Callback for enqueuing asset data via the WP api.
	 *
	 * Note: while this is hooked into print/admin_print_scripts, it still only
	 * happens if the script attached to `wc-settings` handle is enqueued. This
	 * is done to allow for any potentially expensive data generation to only
	 * happen for routes that need it.
	 */
	public function enqueue_asset_data() {
		if ( wp_script_is( 'eac-admin', 'enqueued' ) ) {
			$data   = rawurlencode( wp_json_encode( $this->get_asset_data() ) );
			$script = "var eacAssetData = JSON.parse( decodeURIComponent( '" . esc_js( $data ) . "' ) );";
			wp_add_inline_script(
				'eac-admin',
				$script,
				'before'
			);
		}
	}

	/**
	 * Get asset data.
	 *
	 * @since 1.1.6
	 * @return array
	 */
	public function get_asset_data() {
		$data = array(
			'adminUrl'           => admin_url(),
			'countries'          => array(),
			'currentUserId'      => get_current_user_id(),
			'currentUserIsAdmin' => current_user_can( 'manage_options' ),
			'homeUrl'            => esc_url( home_url( '/' ) ),
			'siteTitle'          => get_bloginfo( 'name' ),
			'assetUrl'           => plugins_url( 'assets/', EAC_PLUGIN_FILE ),
			'version'            => defined( 'EAC_VERSION' ) ? EAC_VERSION : '',
			'wpLoginUrl'         => wp_login_url(),
			'wpVersion'          => get_bloginfo( 'version' ),
			'adminSlug'          => 'accounting',
			'ajaxUrl'            => admin_url( 'admin-ajax.php' ),
			'restUrl'            => rest_url(),
			'restNonce'          => wp_create_nonce( 'wp_rest' ),
			'currencies'         => array(),
		);

		return apply_filters( 'ever_accounting_asset_data', $data );
	}
}
