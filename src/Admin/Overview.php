<?php

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Class Tools.
 *
 * @since   1.0.0
 * @package EverAccounting\Admin
 */
class Overview extends \EverAccounting\Singleton {

	/**
	 * Tools constructor.
	 *
	 * @since 1.0.0
	 */
	protected function __construct() {}

	/**
	 * Output the tools page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output() {
		include dirname( __FILE__ ) . '/views/admin-overview.php';
	}
}
