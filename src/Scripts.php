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
		add_action( 'init', array( $this, 'register_styles' ) );
		add_action( 'init', array( $this, 'register_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
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
				'src' => 'components/style.css',
			),
			'eac-admin'      => array(
				'src' => 'admin/style.css',
			),
			'eac-public'     => array(
				'src' => 'public/style.css',
			),
			'eac-wizard'     => array(
				'src' => 'wizard/style.css',
			),
		) );

		foreach ( $styles as $handle => $style ) {
			$style = wp_parse_args( $style, array(
				'src' => '',
				'deps' => array(),
			) );
			if ( ! preg_match( '/^(http|https):\/\//', $style['src'] ) ) {
				$url  = path_join( EAC_DIST_URL, $style['src'] );
				$path = path_join( EAC_DIST_DIR, $style['src'] );
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
				'src' => 'components/index.js',
			),
			'eac-data'       => array(
				'src' => 'data/index.js',
			),
			'eac-admin'      => array(
				'src'  => 'admin/index.js'
			),
		) );

		foreach ( $scripts as $handle => $script ) {
			$script = wp_parse_args( $script, array(
				'src'       => '',
				'deps'      => array(),
				'in_footer' => false,
			) );
			if ( ! preg_match( '/^(http|https):\/\//', $script['src'] ) ) {
				$url  = path_join( EAC_DIST_URL, $script['src'] );
				$path = path_join( EAC_DIST_DIR, $script['src'] );
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

		$vars = array(
			'site_url'   => site_url(),
			'admin_url'  => admin_url(),
			'asset_url'  => EAC_DIST_URL,
			'plugin_url' => EAC_PLUGIN_URL,
			'ajax_url'   => admin_url( 'admin-ajax.php' ),
			'rest_url'   => rest_url(),
			'rest_nonce' => wp_create_nonce( 'wp_rest' ),
			'admin_slug' => 'accounting',
		);

		wp_localize_script( 'eac-admin', 'eac_vars', $vars );
	}

}
