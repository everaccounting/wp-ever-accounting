<?php

namespace EverAccounting\Admin\ListTables;

use EverAccounting\Models\Category;

defined( 'ABSPATH' ) || exit;

/**
 * Class ItemsTable.
 *
 * @since 1.0.0
 * @package EverAccounting\Admin\ListTables
 */
class CategoriesTable extends \WP_List_Table {
	/**
	 * Constructor.
	 *
	 * @param array $args An associative array of arguments.
	 *
	 * @see WP_List_Table::__construct() for more information on default arguments.
	 * @since 1.0.0
	 */
	public function __construct( $args = array() ) {
		parent::__construct(
			array(
				'singular' => 'category',
				'plural'   => 'categories',
				'screen'   => get_current_screen(),
				'args'     => array(),
			)
		);
	}

	/**
	 * Prepares the list for display.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function prepare_items() {
		$this->process_actions();
		$this->_column_headers = array( $this->get_columns(), get_hidden_columns( $this->screen ), $this->get_sortable_columns() );
		$per_page              = $this->get_items_per_page( 'eac_items_categories_per_page', 20 );
		$paged                 = $this->get_pagenum();
		$search                = isset( $_REQUEST['s'] ) ? wp_unslash( trim( $_REQUEST['s'] ) ) : '';
		$order_by              = isset( $_REQUEST['orderby'] ) ? wp_unslash( trim( $_REQUEST['orderby'] ) ) : '';
		$order                 = isset( $_REQUEST['order'] ) ? wp_unslash( trim( $_REQUEST['order'] ) ) : '';
		$args                  = array(
			'limit'    => $per_page,
			'page'     => $paged,
			'search'   => $search,
			'order_by' => $order_by,
			'order'    => $order,
		);

		/**
		 * Filter the query arguments for the list table.
		 *
		 * @param array $args An associative array of arguments.
		 *
		 * @since 1.0.0
		 */
		$args = apply_filters( 'ever_accounting_category_table_query_args', $args );

		$this->items = eac_get_categories( $args );
		$total       = eac_get_categories( $args, true );
		$this->set_pagination_args(
			array(
				'total_items' => $total,
				'per_page'    => $per_page,
			)
		);
	}

	/**
	 * Process bulk action.
	 *
	 * @since 1.0.0
	 */
	public function process_actions() {
		$action = $this->current_action();
		if ( empty( $action ) ) {
			return;
		}
		wp_die( 1 );

//		$action = $this->current_action();
//		if ( ! empty( $action ) && check_admin_referer( 'bulk-' . $this->_args['plural'] ) ) {
//			if ( wp_unslash( isset( $_REQUEST['id'] ) ) ) {
//				$ids = wp_parse_id_list( wp_unslash( $_REQUEST['id'] ) );
//			} elseif ( isset( $_REQUEST['ids'] ) ) {
//				$ids = array_map( 'absint', $_REQUEST['ids'] );
//			} elseif ( wp_get_referer() ) {
//				wp_safe_redirect( wp_get_referer() );
//				exit;
//			}
//
//			switch ( $action ) {
//				case 'delete':
//					foreach ( $ids as $id ) {
//						eac_delete_item( $id );
//					}
//					EAC()->flash()->success( __( 'Items deleted.', 'wp-ever-accounting' ) );
//					break;
//				case 'enable':
//					foreach ( $ids as $id ) {
//						eac_insert_item( $id, array( 'enabled' => 1 ) );
//					}
//					EAC()->flash()->success( __( 'Items enabled.', 'wp-ever-accounting' ) );
//					break;
//				case 'disable':
//					foreach ( $ids as $id ) {
//						eac_insert_item( $id, array( 'enabled' => 0 ) );
//					}
//					EAC()->flash()->success( __( 'Items disabled.', 'wp-ever-accounting' ) );
//					break;
//			}
//
//			if ( isset( $_REQUEST['_wp_http_referer'] ) && ! empty( $_SERVER['REQUEST_URI'] ) ) {
//				wp_safe_redirect(
//					remove_query_arg(
//						array( '_wp_http_referer', '_wpnonce', 'action', 'action2' ),
//						esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) )
//					)
//				);
//				exit;
//			}
//		}
//
//		if ( isset( $_REQUEST['_wp_http_referer'] ) && ! empty( $_SERVER['REQUEST_URI'] ) ) {
//			wp_safe_redirect(
//				remove_query_arg(
//					array( '_wp_http_referer', '_wpnonce', 'action', 'action2' ),
//					esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) )
//				)
//			);
//			exit;
//		}
	}

	/**
	 * Outputs 'no users' message.
	 *
	 * @since 1.0.0
	 */
	public function no_items() {
		esc_html_e( 'No items found.', 'wp-ever-accounting' );
	}

	/**
	 * Returns an associative array listing all the views that can be used
	 * with this table.
	 *
	 * Provides a list of roles and user count for that role for easy
	 * filtering of the user table.
	 *
	 * @since 1.0.0
	 *
	 * @return string[] An array of HTML links keyed by their view.
	 * @global string $role
	 */
	protected function get_views() {
		return array();
	}

	/**
	 * Retrieves an associative array of bulk actions available on this table.
	 *
	 * @since 1.0.0
	 *
	 * @return array Array of bulk action labels keyed by their action.
	 */
	protected function get_bulk_actions() {
		$actions = array(
			'delete'  => __( 'Delete', 'wp-ever-accounting' ),
			'enable'  => __( 'Enable', 'wp-ever-accounting' ),
			'disable' => __( 'Disable', 'wp-ever-accounting' ),
		);

		return $actions;
	}

	/**
	 * Outputs the controls to allow user roles to be changed in bulk.
	 *
	 * @param string $which Whether invoked above ("top") or below the table ("bottom").
	 *
	 * @since 1.0.0
	 * @return void
	 */
	protected function extra_tablenav( $which ) {
	}

	/**
	 * Gets a list of columns for the list table.
	 *
	 * @since 1.0.0
	 *
	 * @return string[] Array of column titles keyed by their column name.
	 */
	public function get_columns() {
		return array(
			'cb'      => '<input type="checkbox" />',
			'name'    => __( 'Name', 'wp-ever-accounting' ),
			'type'    => __( 'Type', 'wp-ever-accounting' ),
			'color'   => __( 'Color', 'wp-ever-accounting' ),
			'status'  => __( 'Status', 'wp-ever-accounting' ),
			'actions' => __( 'Actions', 'wp-ever-accounting' ),
		);
	}

	/**
	 * Gets a list of sortable columns for the list table.
	 *
	 * @since 1.0.0
	 *
	 * @return array Array of sortable columns.
	 */
	protected function get_sortable_columns() {
		return array(
			'name'   => array( 'name', false ),
			'type'   => array( 'type', false ),
			'color'  => array( 'color', false ),
			'status' => array( 'enabled', false ),
		);
	}

	/**
	 * Define primary column.
	 *
	 * @since 1.0.2
	 * @return string
	 */
	public function get_primary_column_name() {
		return 'name';
	}

	/**
	 * Renders the checkbox column.
	 *
	 * @param Item $item The current object.
	 *
	 * @since  1.0.0
	 * @return string Displays a checkbox.
	 */
	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="id[]" value="%d"/>', esc_attr( $item->id ) );
	}

	/**
	 * Renders the name column.
	 *
	 * @param Item $item The current object.
	 *
	 * @since  1.0.0
	 * @return string Displays the name.
	 */
	public function column_name( $item ) {
		return sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=eac-items&edit=' . $item->id ), wp_kses_post( $item->name ) );
	}

	/**
	 * Renders the actions column.
	 *
	 * @param Item $item The current object.
	 *
	 * @since  1.0.0
	 * @return string Displays the actions.
	 */
	public function column_actions( $item ) {
		$urls = array(
			'edit'    => admin_url( 'admin.php?page=eac-items&edit=' . $item->id ),
			'delete'  => wp_nonce_url( admin_url( 'admin.php?page=eac-items&action=delete&id=' . $item->id ), 'bulk-' . $this->_args['plural'] ),
			'enable'  => wp_nonce_url( admin_url( 'admin.php?page=eac-items&action=enable&id=' . $item->id ), 'bulk-' . $this->_args['plural'] ),
			'disable' => wp_nonce_url( admin_url( 'admin.php?page=eac-items&action=disable&id=' . $item->id ), 'bulk-' . $this->_args['plural'] ),
		);

		$actions = array(
			'edit'   => sprintf( '<a href="%s">%s</a>', esc_url( $urls['edit'] ), __( 'Edit', 'wp-ever-accounting' ) ),
			'delete' => sprintf( '<a href="%s">%s</a>', esc_url( $urls['delete'] ), __( 'Delete', 'wp-ever-accounting' ) ),
		);
		if ( $item->enabled ) {
			$actions['disable'] = sprintf( '<a href="%s">%s</a>', esc_url( $urls['disable'] ), __( 'Disable', 'wp-ever-accounting' ) );
		} else {
			$actions['enable'] = sprintf( '<a href="%s">%s</a>', esc_url( $urls['enable'] ), __( 'Enable', 'wp-ever-accounting' ) );
		}

		return $this->row_actions( $actions, true );
	}

	/**
	 * This function renders most of the columns in the list table.
	 *
	 * @param Category $item The current item.
	 * @param string   $column_name The name of the column.
	 *
	 * @since 1.0.0
	 * @return string The column value.
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'sale_price':
				return empty( $item->sale_price ) ? '&mdash;' : $item->formatted_sale_price;
			case 'purchase_price':
				return empty( $item->purchase_price ) ? '&mdash;' : $item->formatted_purchase_price;
			case 'category':
				return ! isset( $item->category ) ? '&mdash;' : $item->category->name;
			default:
				if ( is_object( $item ) && isset( $item->$column_name ) ) {
					return empty( $item->$column_name ) ? '&mdash;' : wp_kses_post( $item->$column_name );
				}
		}

		return '&mdash;';
	}
}
