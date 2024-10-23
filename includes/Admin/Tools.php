<?php

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Class Tools
 *
 * @package EverAccounting\Admin
 * @since 1.0.0
 */
class Tools {

	/**
	 * Tools constructor.
	 */
	public function __construct() {
		add_filter( 'eac_tools_page_tabs', array( __CLASS__, 'register_tabs' ), -1 );
		add_action( 'eac_tools_page_import_content', array( __CLASS__, 'import_tab' ) );
	}

	/**
	 * Register tabs.
	 *
	 * @param array $tabs Tabs.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function register_tabs( $tabs ) {
		$tabs['import'] = __( 'Import', 'ever-accounting' );
		$tabs['export'] = __( 'Export', 'ever-accounting' );

		return $tabs;
	}

	/**
	 * Import tab.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function import_tab() {
		?>
		<div class="eac-poststuff">
			<div class="column-1">
				<div id="eac-bill"></div>'
			</div>
			<div class="column-2"></div>
		</div>
		<?php
	}
}
