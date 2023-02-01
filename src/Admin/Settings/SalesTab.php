<?php

namespace EverAccounting\Admin\Settings;

defined( 'ABSPATH' ) || exit;

/**
 * Class SalesSettingsPage.
 *
 * @since   1.0.0
 * @package EverAccounting\Admin\Settings
 */
class SalesTab extends Tab {

	/**
	 * SalesSettingsPage constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->id    = 'sales';
		$this->label = __( 'Sales', 'wp-ever-accounting' );

		parent::__construct();
	}
}
