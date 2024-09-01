<?php

namespace EverAccounting\Admin\Misc;

defined( 'ABSPATH' ) || exit;


/**
 * Currencies class.
 *
 * @since 3.0.0
 * @package EverAccounting\Admin
 */
class Currencies {

	/**
	 * Currencies constructor.
	 */
	public function __construct() {
		add_filter( 'eac_misc_page_tabs', array( __CLASS__, 'register_tabs' ) );
	}

	/**
	 * Register tab.
	 *
	 * @param array $tabs Tabs.
	 *
	 * @since 3.0.0
	 * @return array
	 */
	public static function register_tabs( $tabs ) {
		$tabs['currencies'] = __( 'Currencies', 'wp-ever-accounting' );

		return $tabs;
	}
}
