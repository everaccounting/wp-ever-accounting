<?php

namespace EverAccounting\Admin;

use EverAccounting\Models\Item;

/**
 * Menus class.
 *
 * @since 1.0.0
 * @package EverAccounting\Admin\Controllers
 */
class Menus {

	/**
	 * Main menu slug.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const PARENT_SLUG = 'eaccounting';

	/**
	 * Current tab.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $current_tab;

	/**
	 * List tables.
	 *
	 * @var \WP_List_Table
	 */
	private $list_table;

	/**
	 * Menus constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'current_screen', array( $this, 'setup_screen' ), - 1 );
		add_filter( 'set-screen-option', array( $this, 'screen_option' ), 10, 3 );
		add_action( 'current_screen', array( $this, 'setup_list_table' ) );
		add_action( 'ever_accounting_admin_items', array( $this, 'render_items_tab' ) );
		add_action( 'ever_accounting_admin_items_categories', array( $this, 'render_items_categories_tab' ) );
	}

	/**
	 * Add admin menus.
	 *
	 * @since 1.0.0
	 */
	public function admin_menu() {
		$menus = Utilities::get_menus();
		usort(
			$menus,
			function ( $a, $b ) {
				$a = isset( $a['position'] ) ? $a['position'] : PHP_INT_MAX;
				$b = isset( $b['position'] ) ? $b['position'] : PHP_INT_MAX;

				return $a - $b;
			}
		);
		foreach ( $menus as $menu ) {
			$menu = wp_parse_args(
				$menu,
				array(
					'page_title' => '',
					'menu_title' => '',
					'capability' => 'manage_options',
					'menu_slug'  => '',
					'callback'   => null,
					'position'   => '10',
					'page_hook'  => null,
					'tabs'       => array(),
					'load_hook'  => null,
				)
			);
			if ( ! is_callable( $menu['callback'] ) && ! empty( $menu['page_hook'] ) ) {
				$menu['callback'] = function () use ( $menu ) {
					$page_hook = $menu['page_hook'];
					$tabs      = $menu['tabs'];
					include_once __DIR__ . '/views/admin-page.php';
				};
			}
			$load = add_submenu_page(
				self::PARENT_SLUG,
				$menu['page_title'],
				$menu['menu_title'],
				$menu['capability'],
				$menu['menu_slug'],
				$menu['callback'],
				$menu['position']
			);
			if ( ! empty( $menu['load_hook'] ) && is_callable( $menu['load_hook'] ) ) {
				add_action( 'load-' . $load, $menu['load_hook'] );
			}
		}
	}

	/**
	 * Setup screen.
	 *
	 * @since 1.0.0
	 */
	public function setup_screen() {
		$screen = get_current_screen();
		// If we are not on our plugin's screen, exit.
		if ( empty( $screen ) || ! in_array( $screen->id, Utilities::get_screen_ids(), true ) ) {
			return;
		}
		$tab               = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : '';
		$this->current_tab = $tab;
	}

	/**
	 * Set screen option.
	 *
	 * @param mixed  $status Screen option value. Default false.
	 * @param string $option Option name.
	 * @param mixed  $value New option value.
	 *
	 * @since 1.0.0
	 * @return mixed
	 */
	public function screen_option( $status, $option, $value ) {
		$options = apply_filters( 'ever_accounting_screen_options',
			array(
				'eac_items_per_page',
				'eac_items_categories_per_page'
			)
		);
		if ( in_array( $option, $options, true ) ) {
			return $value;
		}

		return $status;
	}

	/**
	 * Current screen.
	 *
	 * @since 1.0.0
	 */
	public function setup_list_table() {
		$screen = get_current_screen();
		if ( Utilities::is_add_screen() || Utilities::is_edit_screen() || ! in_array( $screen->id, Utilities::get_screen_ids(), true ) ) {
			return;
		}
		$args = array(
			'label'   => __( 'Per page', 'wp-ever-accounting' ),
			'default' => 20,
		);
		$page = sanitize_key( implode( '-', array_filter( array( $screen->id, $this->current_tab ) ) ) );
		$page = preg_replace( '/^.*?eac-/', 'eac-', $page );
		switch ( $page ) {
			case 'eac-items':
			case 'eac-items-items':
				$this->list_table = new ListTables\ItemsTable();
				$this->list_table->prepare_items();
				$args['option'] = 'eac_items_per_page';
				add_screen_option( 'per_page', $args );
				break;
			case 'eac-items-categories':
				$this->list_table = new ListTables\CategoriesTable();
				$this->list_table->prepare_items();
				$args['option'] = 'eac_items_categories_per_page';
				add_screen_option( 'per_page', $args );
				break;
		}
	}

	/**
	 * Items Tab.
	 *
	 * @since 1.0.0
	 */
	public function render_items_tab() {
		$edit = Utilities::is_edit_screen();
		$item = new Item( $edit );
		if ( ! empty( $edit ) && ! $item->exists() ) {
			wp_safe_redirect( remove_query_arg( 'edit' ) );
			exit();
		}
		if ( Utilities::is_add_screen() ) {
			include __DIR__ . '/views/items/items/add.php';
		} elseif ( $edit ) {
			include __DIR__ . '/views/items/items/edit.php';
		} else {
			include __DIR__ . '/views/items/items/items.php';
		}
	}

	/**
	 * Items Categories Tab.
	 *
	 * @since 1.0.0
	 */
	public function render_items_categories_tab() {
		$edit = Utilities::is_edit_screen();
		$item = new Item( $edit );
		if ( ! empty( $edit ) && ! $item->exists() ) {
			wp_safe_redirect( remove_query_arg( 'edit' ) );
			exit();
		}
		if ( Utilities::is_add_screen() ) {
			include __DIR__ . '/views/items/categories/add.php';
		} elseif ( $edit ) {
			include __DIR__ . '/views/items/categories/edit.php';
		} else {
			include __DIR__ . '/views/items/categories/categories.php';
		}
	}
}
