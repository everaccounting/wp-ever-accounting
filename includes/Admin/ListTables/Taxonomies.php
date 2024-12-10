<?php

namespace EverAccounting\Admin\ListTables;

use EverAccounting\Models\Category;

defined( 'ABSPATH' ) || exit;

/**
 * Class CategoriesTable.
 *
 * @since 1.0.0
 * @package EverAccounting\Admin\ListTables
 */
class Taxonomies extends ListTable {
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
					'singular' => 'taxonomy',
					'plural'   => 'taxonomies',
					'screen'   => get_current_screen(),
					'args'     => array(),
				)
			)
		);

		$this->base_url = admin_url( 'admin.php?page=eac-settings&tab=taxonomies' );
	}

	/**
	 * Prepares the list for display.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function prepare_items() {
		$this->process_actions();
		$per_page = $this->get_items_per_page( 'eac_taxonomies_per_page' );
		$paged    = $this->get_pagenum();
		$search   = $this->get_request_search();
		$order_by = $this->get_request_orderby();
		$order    = $this->get_request_order();
		$args     = array(
			'limit'   => $per_page,
			'page'    => $paged,
			'search'  => $search,
			'orderby' => $order_by,
			'order'   => $order,
			'type'    => $this->get_request_type(),
		);

		/**
		 * Filter the query arguments for the list table.
		 *
		 * @param array $args An associative array of arguments.
		 *
		 * @since 1.0.0
		 */
		$args = apply_filters( 'eac_taxonomies_table_query_args', $args );

		$this->items = Category::results( $args );
		$total       = Category::count( $args );
		$this->set_pagination_args(
			array(
				'total_items' => $total,
				'per_page'    => $per_page,
			)
		);
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
			if ( EAC()->categories->delete( $id ) ) {
				++$performed;
			}
		}
		if ( ! empty( $performed ) ) {
			// translators: %s: number of categories.
			EAC()->flash->success( sprintf( __( '%s category(s) deleted successfully.', 'wp-ever-accounting' ), number_format_i18n( $performed ) ) );
		}
	}

	/**
	 * Outputs 'no categories' message.
	 *
	 * @since 1.0.0
	 */
	public function no_items() {
		esc_html_e( 'No categories found.', 'wp-ever-accounting' );
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
			'delete' => __( 'Delete', 'wp-ever-accounting' ),
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
		return sprintf(
			'<a class="row-title" href="%s">%s</a>',
			esc_url(
				add_query_arg(
					array(
						'section' => $item->type,
						'action'  => 'edit',
						'id'      => $item->id,
					),
					$this->base_url
				)
			),
			wp_kses_post( $item->name )
		);
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
			'id'     => sprintf( '#%d', esc_attr( $item->id ) ),
			'edit'   => sprintf(
				'<a href="%s">%s</a>',
				esc_url(
					add_query_arg(
						array(
							'section' => $item->type,
							'action'  => 'edit',
							'id'      => $item->id,
						),
						$this->base_url
					)
				),
				__( 'Edit', 'wp-ever-accounting' )
			),
			'delete' => sprintf(
				'<a href="%s" class="del">%s</a>',
				esc_url(
					wp_nonce_url(
						add_query_arg(
							array(
								'section' => $item->type,
								'action'  => 'delete',
								'id'      => $item->id,
							),
							$this->base_url
						),
						'bulk-' . $this->_args['plural']
					)
				),
				__( 'Delete', 'wp-ever-accounting' )
			),
		);

		return $this->row_actions( $actions );
	}
}
