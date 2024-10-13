<?php

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Class Dashboard
 *
 * @since 1.0.0
 * @package EverAccounting\Admin
 */
class Dashboard {

	/**
	 * Dashboard constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'eac_dashboard_page_content', array( __CLASS__, 'page_content' ) );
	}

	/**
	 * Page content.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function render_page() {
		include __DIR__ . '/views/dashboard.php';
	}
}
