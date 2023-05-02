<?php

namespace EverAccounting\Admin\Settings;

defined( 'ABSPATH' ) || exit;

/**
 * Class Currencies.
 *
 * @since   1.0.0
 * @package EverAccounting\Admin\Settings
 */
class Currencies extends \EverAccounting\Admin\SettingsTab {
	/**
	 * Currency constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->id    = 'currencies';
		$this->label = __( 'Currencies', 'ever-accounting' );

		parent::__construct();
	}
}
