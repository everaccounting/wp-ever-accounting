<?php

namespace EverAccounting\Admin\Settings;

use EverAccounting\Admin\Settings\Tables\CategoriesTable;

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
	public function render_content() {
		$list_table = new CategoriesTable();
		$list_table->prepare_items();
		include __DIR__ . '/views/category-list.php';
	}
}
