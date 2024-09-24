<?php

namespace EverAccounting\Admin\Items;

use EverAccounting\Models\Item;

defined( 'ABSPATH' ) || exit;

/**
 * Class Items
 *
 * @since 3.0.0
 * @package EverAccounting\Admin\Items
 */
class Items {

	/**
	 * Items constructor.
	 */
	public function __construct() {
		add_filter( 'eac_items_page_tabs', array( __CLASS__, 'register_tabs' ) );
		add_filter( 'set-screen-option', array( __CLASS__, 'set_screen_option' ), 10, 3 );
		add_action( 'load_eac_items_page_items', array( __CLASS__, 'setup_table' ) );
		add_action( 'eac_items_page_items', array( __CLASS__, 'render_table' ) );
		add_action( 'eac_items_page_items_add', array( __CLASS__, 'render_add' ) );
		add_action( 'eac_items_page_items_edit', array( __CLASS__, 'render_edit' ) );
		add_action( 'admin_post_eac_edit_item', array( __CLASS__, 'handle_edit' ) );
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
		$tabs['items'] = __( 'Items', 'wp-ever-accounting' );

		return $tabs;
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
		if ( 'eac_items_per_page' === $option ) {
			return $value;
		}

		return $status;
	}

	/**
	 * setup expenses list.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public static function setup_table() {
		global $list_table;
		$screen     = get_current_screen();
		$list_table = new Tables\ItemsTable();
		$list_table->prepare_items();
		$screen->add_option(
			'per_page',
			array(
				'label'   => __( 'Number of items per page:', 'wp-ever-accounting' ),
				'default' => 20,
				'option'  => 'eac_items_per_page',
			)
		);
	}

	/**
	 * Render table.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public static function render_table() {
		global $list_table;
		include __DIR__ . '/views/item-list.php';
	}

	/**
	 * Render add form.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public static function render_add() {
		$item = new Item();
		include __DIR__ . '/views/item-add.php';
	}

	/**
	 * Render edit form.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public static function render_edit() {
		$id   = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );
		$item = Item::find( $id );
		if ( ! $item ) {
			esc_html_e( 'The specified item does not exist.', 'wp-ever-accounting' );

			return;
		}
		include __DIR__ . '/views/item-edit.php';
	}

	/**
	 * Handle edit.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	public static function handle_edit() {
		check_admin_referer( 'eac_edit_item' );
		$referer     = wp_get_referer();
		$id          = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0;
		$name        = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$type        = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
		$price       = isset( $_POST['price'] ) ? floatval( wp_unslash( $_POST['price'] ) ) : 0;
		$cost        = isset( $_POST['cost'] ) ? floatval( wp_unslash( $_POST['cost'] ) ) : 0;
		$category_id = isset( $_POST['category_id'] ) ? absint( wp_unslash( $_POST['category_id'] ) ) : 0;
		$unit        = isset( $_POST['unit'] ) ? sanitize_text_field( wp_unslash( $_POST['unit'] ) ) : '';
		$tax_ids     = isset( $_POST['tax_ids'] ) ? array_map( 'absint', wp_unslash( $_POST['tax_ids'] ) ) : array();
		$desc        = isset( $_POST['description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['description'] ) ) : '';
		$status      = isset( $_POST['status'] ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : 'active';
		$item        = EAC()->items->insert(
			array(
				'id'          => $id,
				'name'        => $name,
				'type'        => $type,
				'price'       => $price,
				'cost'        => $cost,
				'category_id' => $category_id,
				'unit'        => $unit,
				'tax_ids'     => implode( ',', array_unique( array_filter( $tax_ids ) ) ),
				'description' => $desc,
				'status'      => $status,
			)
		);

		if ( is_wp_error( $item ) ) {
			EAC()->flash->error( $item->get_error_message() );
		} else {
			EAC()->flash->success( __( 'Item saved successfully.', 'wp-ever-accounting' ) );
			$referer = add_query_arg( ['action' => 'edit', 'id' => $item->id ], $referer );
			$referer = remove_query_arg( array( 'add' ), $referer );
		}

		wp_safe_redirect( $referer );
		exit();
	}
}
