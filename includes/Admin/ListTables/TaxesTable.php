<?php

namespace EverAccounting\Admin\ListTables;

use EverAccounting\ListTable;
use EverAccounting\Models\Tax;

defined( 'ABSPATH' ) || exit;

/**
 * Class TaxesTable.
 *
 * @since 1.0.0
 * @package EverAccounting\Admin\ListTables
 */
class TaxesTable extends ListTable {
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
					'singular' => 'tax',
					'plural'   => 'taxes',
					'screen'   => get_current_screen(),
					'args'     => array(),
				)
			)
		);

		$this->base_url = admin_url( 'admin.php?page=eac-misc&tab=taxes' );
	}

	/**
	 * Prepares the list for display.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function prepare_items() {
		$this->process_actions();
		$per_page = $this->get_items_per_page( 'eac_taxes_per_page', 20 );
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
			'status'  => $this->get_request_status(),
		);

		/**
		 * Filter the query arguments for the list table.
		 *
		 * @param array $args An associative array of arguments.
		 *
		 * @since 1.0.0
		 */
		$args = apply_filters( 'ever_accounting_taxes_table_query_args', $args );

		$this->items = eac_get_taxes( $args );
		$total       = eac_get_taxes( $args, true );
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
	 * @return void
	 * @since 1.0.0
	 */
	protected function bulk_activate( $ids ) {
		$performed = 0;
		foreach ( $ids as $id ) {
			if ( eac_insert_tax(
				array(
					'id'     => $id,
					'status' => 'active',
				)
			) ) {
				++$performed;
			}
		}
		if ( ! empty( $performed ) ) {
			// translators: %s: number of taxes activated.
			EAC()->flash->success( sprintf( __( '%s tax(es) activated successfully.', 'wp-ever-accounting' ), number_format_i18n( $performed ) ) );
		}
	}

	/**
	 * handle bulk deactivate action.
	 *
	 * @param array $ids List of item IDs.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	protected function bulk_deactivate( $ids ) {
		$performed = 0;
		foreach ( $ids as $id ) {
			if ( eac_insert_tax(
				array(
					'id'     => $id,
					'status' => 'inactive',
				)
			) ) {
				++$performed;
			}
		}
		if ( ! empty( $performed ) ) {
			// translators: %s: number of taxes deactivated.
			EAC()->flash->success( sprintf( __( '%s tax(es) deactivated successfully.', 'wp-ever-accounting' ), number_format_i18n( $performed ) ) );
		}
	}

	/**
	 * handle bulk delete action.
	 *
	 * @param array $ids List of tax IDs.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	protected function bulk_delete( $ids ) {
		$performed = 0;
		foreach ( $ids as $id ) {
			if ( eac_delete_tax( $id ) ) {
				++$performed;
			}
		}
		if ( ! empty( $performed ) ) {
			// translators: %s: number of taxes deleted.
			EAC()->flash->success( sprintf( __( '%s tax(1s) deleted successfully.', 'wp-ever-accounting' ), number_format_i18n( $performed ) ) );
		}
	}

	/**
	 * Outputs 'no users' message.
	 *
	 * @since 1.0.0
	 */
	public function no_items() {
		esc_html_e( 'No taxes found.', 'wp-ever-accounting' );
	}

	/**
	 * Returns an associative array listing all the views that can be used
	 * with this table.
	 *
	 * @return string[] An array of HTML links keyed by their view.
	 * @since 1.0.0
	 *
	 * @global string $role
	 */
	protected function get_views() {
		$current      = $this->get_request_status( 'all' );
		$status_links = array();
		$statuses     = array(
			'all'      => __( 'All', 'wp-ever-accounting' ),
			'active'   => __( 'Active', 'wp-ever-accounting' ),
			'inactive' => __( 'Inactive', 'wp-ever-accounting' ),
		);

		foreach ( $statuses as $status => $label ) {
			$link  = 'all' === $status ? $this->base_url : add_query_arg( 'status', $status, $this->base_url );
			$args  = 'all' === $status ? array() : array( 'status' => $status );
			$count = Tax::count( $args );
			$label = sprintf( '%s <span class="count">(%s)</span>', esc_html( $label ), number_format_i18n( $count ) );

			$status_links[ $status ] = array(
				'url'     => $link,
				'label'   => $label,
				'current' => $current === $status,
			);
		}
		return $this->get_views_links( $status_links );
	}

	/**
	 * Retrieves an associative array of bulk actions available on this table.
	 *
	 * @return array Array of bulk action labels keyed by their action.
	 * @since 1.0.0
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
	 * @return void
	 * @since 1.0.0
	 */
	protected function extra_tablenav( $which ) {
	}

	/**
	 * Gets a list of columns for the list table.
	 *
	 * @return string[] Array of column titles keyed by their column name.
	 * @since 1.0.0
	 */
	public function get_columns() {
		return array(
			'cb'          => '<input type="checkbox" />',
			'name'        => __( 'Name', 'wp-ever-accounting' ),
			'rate'        => __( 'Rate', 'wp-ever-accounting' ),
			'is_compound' => __( 'Compound', 'wp-ever-accounting' ),
			'status'      => __( 'Status', 'wp-ever-accounting' ),
		);
	}

	/**
	 * Gets a list of sortable columns for the list table.
	 *
	 * @return array Array of sortable columns.
	 * @since 1.0.0
	 */
	protected function get_sortable_columns() {
		return array(
			'name'        => array( 'name', false ),
			'rate'        => array( 'rate', false ),
			'is_compound' => array( 'is_compound', false ),
			'status'      => array( 'status', true ),
		);
	}

	/**
	 * Define primary column.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_primary_column_name() {
		return 'name';
	}

	/**
	 * Renders the checkbox column.
	 *
	 * @param Tax $item The current object.
	 *
	 * @return string Displays a checkbox.
	 * @since  1.0.0
	 */
	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="id[]" value="%d"/>', esc_attr( $item->id ) );
	}

	/**
	 * Renders the name column.
	 *
	 * @param Tax $item The current object.
	 *
	 * @return string Displays the name.
	 * @since  1.0.0
	 */
	public function column_name( $item ) {
		return sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( 'edit', $item->id, $this->base_url ) ), wp_kses_post( $item->name ) );
	}

	/**
	 * Renders the is_compound column.
	 *
	 * @param Tax $item The current object.
	 *
	 * @since  1.0.0
	 * @return string Displays the compound.
	 */
	public function column_is_compound( $item ) {
		return $item->is_compound ? __( 'Yes', 'wp-ever-accounting' ) : __( 'No', 'wp-ever-accounting' );
	}

	/**
	 * Generates and displays row actions links.
	 *
	 * @param Tax    $item The comment object.
	 * @param string $column_name Current column name.
	 * @param string $primary Primary column name.
	 *
	 * @return string Row actions output.
	 * @since 1.0.0
	 */
	protected function handle_row_actions( $item, $column_name, $primary ) {
		if ( $primary !== $column_name ) {
			return null;
		}
		$actions = array(
			'id'   => sprintf( '#%d', esc_attr( $item->id ) ),
			'edit' => sprintf(
				'<a href="%s">%s</a>',
				esc_url( add_query_arg( 'edit', $item->id, $this->base_url ) ),
				__( 'Edit', 'wp-ever-accounting' )
			),
		);

		if ( 'active' === $item->status ) {
			$actions['deactivate'] = sprintf(
				'<a href="%s">%s</a>',
				esc_url(
					wp_nonce_url(
						add_query_arg(
							array(
								'action' => 'deactivate',
								'id'     => $item->id,
							),
							$this->base_url
						),
						'bulk-' . $this->_args['plural']
					)
				),
				__( 'Deactivate', 'wp-ever-accounting' )
			);
		} else {
			$actions['activate'] = sprintf(
				'<a href="%s">%s</a>',
				esc_url(
					wp_nonce_url(
						add_query_arg(
							array(
								'action' => 'activate',
								'id'     => $item->id,
							),
							$this->base_url
						),
						'bulk-' . $this->_args['plural']
					)
				),
				__( 'Activate', 'wp-ever-accounting' )
			);
		}

		$actions['delete'] = sprintf(
			'<a href="%s" class="del">%s</a>',
			esc_url(
				wp_nonce_url(
					add_query_arg(
						array(
							'action' => 'delete',
							'id'     => $item->id,
						),
						$this->base_url
					),
					'bulk-' . $this->_args['plural']
				)
			),
			__( 'Delete', 'wp-ever-accounting' )
		);

		return $this->row_actions( $actions );
	}
}
