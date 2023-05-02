<?php

namespace EverAccounting\Admin\Settings;

defined( 'ABSPATH' ) || exit;

/**
 * Class Categories.
 *
 * @since   1.0.0
 * @package EverAccounting\Admin\Settings
 */
class Categories extends \EverAccounting\Admin\SettingsTab {
	/**
	 * Currency constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->id    = 'categories';
		$this->label = __( 'Categories', 'ever-accounting' );

		parent::__construct();
	}
}
