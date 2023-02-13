<?php

namespace EverAccounting\Admin\Settings;

defined( 'ABSPATH' ) || exit;

/**
 * Class ExpensesSettingsPage.
 *
 * @since   1.0.0
 * @package EverAccounting\Admin\Settings
 */
class ExpensesTab extends Tab {

	/**
	 * ExpensesSettingsPage constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->id    = 'expenses';
		$this->label = __( 'Expenses', 'wp-ever-accounting' );

		parent::__construct();
	}
}
