<?php
/**
 * Load assets.
 *
 * @package     EverAccounting
 * @subpackage  Admin
 * @version     1.0.2
 */

namespace EverAccounting\Admin;

use EverAccounting\Abstracts\Assets;

defined( 'ABSPATH' ) || exit();

class Admin_Assets extends Assets {

	/**
	 * Hook in tabs.
	 *
	 * @version 1.0.2
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
	}

	/**
	 * Enqueue styles.
	 *
	 * @version 1.0.2
	 */
	public function admin_styles() {
		$version   = eaccounting()->get_version();
		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';
		// Register admin styles.
		wp_register_style( 'ea-admin-styles', eaccounting()->plugin_url() . '/assets/css/admin.css', array(), $version );
		wp_register_style( 'ea-release-styles', eaccounting()->plugin_url() . '/assets/css/release.css', array(), $version );
		wp_register_style( 'jquery-ui-style', eaccounting()->plugin_url() . '/assets/css/jquery-ui/jquery-ui.min.css', array(), $version );

		// Add RTL support for admin styles.
		wp_style_add_data( 'ea-admin-styles', 'rtl', 'replace' );

		// Admin styles for Accounting pages only.
		if ( in_array( $screen_id, eaccounting_get_screen_ids(), true ) ) {
			wp_enqueue_style( 'ea-admin-styles' );
			wp_enqueue_style( 'jquery-ui-style' );
		}
	}


	/**
	 * Enqueue scripts.
	 *
	 * @version 1.0.2
	 */
	public function admin_scripts() {
		$screen                = get_current_screen();
		$screen_id             = $screen ? $screen->id : '';
		$eaccounting_screen_id = sanitize_title( __( 'Accounting', 'wp-ever-accounting' ) );

		// 3rd parties
		self::register_script('jquery-blockui', null, ['jquery']);
		self::register_script( 'chartjs', null);
		self::register_script( 'chartjs-labels', null);
		self::register_script( 'jquery-select2', null, ['jquery'] );
		self::register_script( 'jquery-inputmask', null, ['jquery'] );
		self::register_script( 'print-this', null, ['jquery'] );

		self::register_script( 'ea-store', 'store.js' );
//		self::register_script( 'ea-data', 'data.js' );
		self::register_script( 'ea-utils', 'utils.js', ['jquery-blockui'] );
		self::register_script( 'ea-admin', null, ['ea-store'] );
		wp_enqueue_script('ea-utils');
		wp_enqueue_script('ea-admin');
	}

}

return new Admin_Assets();
