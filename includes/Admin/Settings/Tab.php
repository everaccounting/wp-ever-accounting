<?php

namespace EverAccounting\Admin\Settings;

defined( 'ABSPATH' ) || exit;

/**
 * Class Tab.
 *
 * @since   1.0.0
 * @package EverAccounting\Admin\Settings\Tabs
 */
abstract class Tab {
	/**
	 * Setting tab id.
	 *
	 * @var string
	 */
	protected $id = '';

	/**
	 * Setting tab label.
	 *
	 * @var string
	 */
	protected $label = '';

	/**
	 * Current tab sections.
	 *
	 * @var array
	 */
	protected $sections = array();

	/**
	 * Tab Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'eac_settings_page_tabs', array( $this, 'register_tab' ), 0 );
	}

	/**
	 * Get settings tab ID.
	 *
	 * @since 3.0.0
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Get settings tab label.
	 *
	 * @since 3.0.0
	 * @return string
	 */
	public function get_label() {
		return $this->label;
	}

	/**
	 * Get settings tab sections.
	 *
	 * @since 3.0.0
	 * @return array
	 */
	public function get_sections() {
		return apply_filters( 'eac_settings_' . $this->id . '_tab_sections', $this->sections );
	}

	/**
	 * Register settings tab.
	 *
	 * @param array $tabs Tabs.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function register_tab( $tabs ) {
		$tabs[ $this->id ] = $this->label;

		return $tabs;
	}
}
