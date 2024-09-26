<?php

namespace EverAccounting\Admin\Settings;

defined( 'ABSPATH' ) || exit;

/**
 * Class Categories
 *
 * @since   1.0.0
 * @package EverAccounting\Admin\Settings
 */
class Categories extends Page {

	/**
	 * Categories constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct( 'categories', __( 'Categories', 'wp-ever-accounting' ) );
	}

	/**
	 * Render page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function render_content() {}
}
