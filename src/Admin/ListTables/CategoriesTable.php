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
class CategoriesTable extends ListTable {
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
			wp_parse_args(
				$args,
				array(
					'singular' => 'category',
					'plural'   => 'categories',
					'screen'   => get_current_screen(),
					'args'     => array(),
				)
			)
		);

		$this->base_url = admin_url( 'admin.php?page=eac-misc&tab=categories' );
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
	 * handle bulk activate action.
	 *
	 * @param array $ids List of item IDs.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	protected function bulk_activate( $ids ) {
		$performed = 0;
		foreach ( $ids as $id ) {
			if ( eac_insert_category( array( 'id' => $id, 'status' => 'active' ) ) ) {
				$performed ++;
			}
		}
		if ( ! empty( $performed ) ) {
			EAC()->flash->success( sprintf( __( '%s category(s) activated successfully.', 'wp-ever-accounting' ), number_format_i18n( $performed ) ) );
		}
	}

	/**
	 * handle bulk deactivate action.
	 *
	 * @param array $ids List of item IDs.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	protected function bulk_deactivate( $ids ) {
		$performed = 0;
		foreach ( $ids as $id ) {
			if ( eac_insert_category( array( 'id' => $id, 'status' => 'inactive' ) ) ) {
				$performed ++;
			}
		}
		if ( ! empty( $performed ) ) {
			EAC()->flash->success( sprintf( __( '%s category(s) deactivated successfully.', 'wp-ever-accounting' ), number_format_i18n( $performed ) ) );
		}

	}

	/**
	 * handle bulk delete action.
	 *
	 * @param array $ids List of item IDs.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	protected function bulk_delete( $ids ) {
		$performed = 0;
		foreach ( $ids as $id ) {
			if ( eac_delete_category( $id ) ) {
				$performed ++;
			}
		}
		if ( ! empty( $performed ) ) {
			EAC()->flash->success( sprintf( __( '%s category(s) deleted successfully.', 'wp-ever-accounting' ), number_format_i18n( $performed ) ) );
		}
	}

	/**
	 * Outputs 'no users' message.
	 *
	 * @since 1.0.0
	 */
	public function no_items() {
		esc_html_e( 'No categories found.', 'wp-ever-accounting' );
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
		$current      = $this->get_request_status( 'all' );
		$status_links = array();
		$views        = array(
			'all'      => _nx_noop( 'All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', 'wp-ever-accounting' ),
			'active'   => _nx_noop( 'Active <span class="count">(%s)</span>', 'Active <span class="count">(%s)</span>', 'wp-ever-accounting' ),
			'inactive' => _nx_noop( 'Inactive <span class="count">(%s)</span>', 'Inactive <span class="count">(%s)</span>', 'wp-ever-accounting' ),
		);
		foreach ( $views as $view => $label ) {
			$link  = $view === 'all' ? $this->base_url : add_query_arg( 'status', $view, $this->base_url );
			$args  = 'all' === $view ? array() : array( 'status' => $view );
			$count = eac_get_categories( $args, true );
			$label = sprintf( _n( $label[0], $label[1], $count, 'wp-ever-accounting' ), number_format_i18n( $count ) );

			$status_links[ $view ] = array(
				'url'     => $link,
				'label'   => $label,
				'current' => $current === $view,
			);
		}

		return $this->get_views_links( $status_links );
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
			'delete'     => __( 'Delete', 'wp-ever-accounting' ),
			'activate'   => __( 'Activate', 'wp-ever-accounting' ),
			'deactivate' => __( 'Deactivate', 'wp-ever-accounting' ),
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
			'cb'          => '<input type="checkbox" />',
			'name'        => __( 'Name', 'wp-ever-accounting' ),
			'description' => __( 'Description', 'wp-ever-accounting' ),
			'type'        => __( 'Type', 'wp-ever-accounting' ),
			'status'      => __( 'Status', 'wp-ever-accounting' ),
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
			'name'        => array( 'name', false ),
			'description' => array( 'description', false ),
			'type'        => array( 'type', false ),
			'status'      => array( 'status', false ),
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
	 * @param Category $item The current object.
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
	 * @param Category $item The current object.
	 *
	 * @since  1.0.0
	 * @return string Displays the name.
	 */
	public function column_name( $item ) {
		return sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( 'edit', $item->id, $this->base_url ) ), wp_kses_post( $item->name ) );
	}

	/**
	 * Renders the type column.
	 *
	 * @param Category $item The current object.
	 *
	 * @since  1.0.0
	 * @return string Displays the type.
	 */
	public function column_type( $item ) {
		$types = eac_get_category_types();

		return isset( $types[ $item->type ] ) ? $types[ $item->type ] : '';
	}

	/**
	 * Generates and displays row actions links.
	 *
	 * @param Category $item The comment object.
	 * @param string   $column_name Current column name.
	 * @param string   $primary Primary column name.
	 *
	 * @since 1.0.0
	 * @return string Row actions output.
	 */
	protected function handle_row_actions( $item, $column_name, $primary ) {
		if ( $primary !== $column_name ) {
			return null;
		}
		$actions = array(
			'id' => sprintf( '#%d', esc_attr( $item->id ) ),
		);

		if ( $item->status === 'active' ) {
			$actions['deactivate'] = sprintf(
				'<a href="%s">%s</a>',
				esc_url( wp_nonce_url( add_query_arg( array( 'action' => 'deactivate', 'id' => $item->id ), $this->base_url ), 'bulk-' . $this->_args['plural'] ) ),
				__( 'Deactivate', 'wp-ever-accounting' )
			);
		} else {
			$actions['activate'] = sprintf(
				'<a href="%s">%s</a>',
				esc_url( wp_nonce_url( add_query_arg( array( 'action' => 'activate', 'id' => $item->id ), $this->base_url ), 'bulk-' . $this->_args['plural'] ) ),
				__( 'Activate', 'wp-ever-accounting' )
			);
		}

		$actions['delete'] = sprintf(
			'<a href="%s" class="del">%s</a>',
			esc_url( wp_nonce_url( add_query_arg( array( 'action' => 'delete', 'id' => $item->id ), $this->base_url ), 'bulk-' . $this->_args['plural'] ) ),
			__( 'Delete', 'wp-ever-accounting' )
		);

		return $this->row_actions( $actions );
	}
}
