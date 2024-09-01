<?php

namespace EverAccounting\Admin\Misc;

use EverAccounting\Models\Category;

defined( 'ABSPATH' ) || exit;


/**
 * Categories class.
 *
 * @since 3.0.0
 * @package EverAccounting\Admin
 */
class Categories {

	/**
	 * Categories constructor.
	 */
	public function __construct() {
		add_filter( 'eac_misc_page_tabs', array( __CLASS__, 'register_tabs' ) );
		add_action( 'load_eac_misc_page_categories_home', array( __CLASS__, 'setup_table' ) );
		add_filter( 'set-screen-option', array( __CLASS__, 'set_screen_option' ), 10, 3 );
		add_action( 'eac_misc_page_categories_home', array( __CLASS__, 'render_table' ) );
		add_action( 'eac_misc_page_categories_add', array( __CLASS__, 'render_add' ) );
		add_action( 'eac_misc_page_categories_edit', array( __CLASS__, 'render_edit' ) );
		add_action( 'admin_post_eac_edit_category', array( __CLASS__, 'handle_edit' ) );
	}

	/**
	 * Register tab.
	 *
	 * @param array $tabs Tabs.
	 *
	 * @since 3.0.0
	 * @return array
	 */
	public static function register_tabs( $tabs ) {
		$tabs['categories'] = __( 'Categories', 'wp-ever-accounting' );

		return $tabs;
	}

	/**
	 * setup categories list.
	 *
	 * @since 3.0.0
	 */
	public static function setup_table() {
		global $list_table;
		$screen     = get_current_screen();
		$list_table = new Tables\CategoriesTable();
		$list_table->prepare_items();
//		$screen->add_option( 'per_page', array(
//			'label'   => __( 'Number of items per page:', 'wp-ever-accounting' ),
//			'default' => 20,
//			'option'  => 'eac_categories_per_page',
//		) );
	}

	/**
	 * Set screen option.
	 *
	 * @param mixed  $status Status.
	 * @param string $option Option.
	 * @param mixed  $value Value.
	 *
	 * @since 3.0.0
	 * @return mixed
	 */
	public static function set_screen_option( $status, $option, $value ) {
		if ( 'eac_categories_per_page' === $option ) {
			return $value;
		}

		return $status;
	}

	/**
	 * Render categories table.
	 *
	 * @since 3.0.0
	 */
	public static function render_table() {
		global $list_table;
		include __DIR__ . '/views/categories/table.php';
	}

	/**
	 * Render add category form.
	 *
	 * @since 3.0.0
	 */
	public static function render_add() {
		$category = new Category();
		include __DIR__ . '/views/categories/add.php';
	}

	/**
	 * Render edit category form.
	 *
	 * @since 3.0.0
	 */
	public static function render_edit() {
		$id       = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );
		$category = Category::find( $id );
		if ( ! $category ) {
			esc_html_e( 'The specified category does not exist.', 'wp-ever-accounting' );

			return;
		}

		include __DIR__ . '/views/categories/edit.php';
	}

	/**
	 * Edit category.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	public static function handle_edit() {
		check_admin_referer( 'eac_edit_category' );
		$referer  = wp_get_referer();
		$id       = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0;
		$name     = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$type     = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
		$desc     = isset( $_POST['description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['description'] ) ) : '';
		$status   = isset( $_POST['status'] ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : 'active';
		$category = eac_insert_category(
			array(
				'id'          => $id,
				'name'        => $name,
				'type'        => $type,
				'description' => $desc,
				'status'      => $status,
			)
		);

		if ( is_wp_error( $category ) ) {
			EAC()->flash->error( $category->get_error_message() );
		} else {
			EAC()->flash->success( __( 'Category saved successfully.', 'wp-ever-accounting' ) );
			$referer = add_query_arg( 'edit', $category->id, $referer );
			$referer = remove_query_arg( array( 'add' ), $referer );
		}

		wp_safe_redirect( $referer );
		exit;
	}

}
