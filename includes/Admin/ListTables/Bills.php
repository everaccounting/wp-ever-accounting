<?php

namespace EverAccounting\Admin\ListTables;

use EverAccounting\Models\Bill;

defined( 'ABSPATH' ) || exit;

/**
 * Class Bills.
 *
 * @since 1.0.0
 * @package EverAccounting\Admin\ListTables
 */
class Bills extends ListTable {
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
					'singular' => 'bill',
					'plural'   => 'bills',
					'screen'   => get_current_screen(),
					'args'     => array(),
				)
			)
		);

		$this->base_url = admin_url( 'admin.php?page=eac-purchases&tab=bills' );
	}

	/**
	 * Prepares the list for display.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function prepare_items() {
		$this->process_actions();
		$per_page = $this->get_items_per_page( 'eac_bills_per_page', 20 );
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
		$args = apply_filters( 'eac_bills_table_query_args', $args );

		$args['no_found_rows'] = false;
		$this->items           = Bill::results( $args );
		$total                 = Bill::count( $args );

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
			if ( EAC()->bills->delete( $id ) ) {
				++$performed;
			}
		}
		if ( ! empty( $performed ) ) {
			// translators: %s: number of items deleted.
			EAC()->flash->success( sprintf( __( '%s bill(s) deleted successfully.', 'wp-ever-accounting' ), number_format_i18n( $performed ) ) );
		}
	}

	/**
	 * Outputs 'no items' message.
	 *
	 * @since 1.0.0
	 */
	public function no_items() {
		esc_html_e( 'No bills found.', 'wp-ever-accounting' );
	}

	/**
	 * Returns an associative array listing all the views that can be used
	 * with this table.
	 *
	 * Provides a list of roles and user count for that role for easy
	 * filtering of the user table.
	 *
	 * @since 1.0.0
	 * @return string[] An array of HTML links keyed by their view.
	 */
	protected function get_views() {
		$current      = $this->get_request_status( 'all' );
		$status_links = array();
		$statuses     = EAC()->bills->get_statuses();

		foreach ( $statuses as $status => $label ) {
			$link  = 'all' === $status ? $this->base_url : add_query_arg( 'status', $status, $this->base_url );
			$args  = 'all' === $status ? array() : array( 'status' => $status );
			$count = Bill::count( $args );
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
	 * @since 1.0.0
	 * @return array Array of bulk action labels keyed by their action.
	 */
	protected function get_bulk_actions() {
		$actions = array(
			'cancel'  => __( 'Cancel', 'wp-ever-accounting' ),
			'paid'    => __( 'Paid', 'wp-ever-accounting' ),
			'pending' => __( 'Pending', 'wp-ever-accounting' ),
			'delete'  => __( 'Delete', 'wp-ever-accounting' ),
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
		// TODO: Need to include invoicesTable filters 'Select Month', 'Select Account', 'Select Category', 'Select Customer'.
		static $has_items;
		if ( ! isset( $has_items ) ) {
			$has_items = $this->has_items();
		}

		echo '<div class="alignleft actions">';

		if ( 'top' === $which ) {
			submit_button( __( 'Filter', 'wp-ever-accounting' ), '', 'filter_action', false );
		}

		echo '</div>';
	}

	/**
	 * Gets a list of columns for the list table.
	 *
	 * @since 1.0.0
	 * @return string[] Array of column titles keyed by their column name.
	 */
	public function get_columns() {
		return array(
			'cb'         => '<input type="checkbox" />',
			'number'     => __( 'Bill #', 'wp-ever-accounting' ),
			'reference'  => __( 'Order #', 'wp-ever-accounting' ),
			'issue_date' => __( 'Issue Date', 'wp-ever-accounting' ),
			'due_date'   => __( 'Due Date', 'wp-ever-accounting' ),
			'vendor'     => __( 'Vendor', 'wp-ever-accounting' ),
			'status'     => __( 'Status', 'wp-ever-accounting' ),
			'total'      => __( 'Total', 'wp-ever-accounting' ),
		);
	}

	/**
	 * Gets a list of sortable columns for the list table.
	 *
	 * @since 1.0.0
	 * @return array Array of sortable columns.
	 */
	protected function get_sortable_columns() {
		return array(
			'number'     => array( 'number', false ),
			'reference'  => array( 'reference', false ),
			'issue_date' => array( 'issue_date', false ),
			'due_date'   => array( 'due_date', false ),
			'vendor'     => array( 'vendor', false ),
			'status'     => array( 'status', false ),
			'total'      => array( 'total', false ),
		);
	}

	/**
	 * Define primary column.
	 *
	 * @since 1.0.2
	 * @return string
	 */
	public function get_primary_column_name() {
		return 'number';
	}

	/**
	 * Renders the checkbox column.
	 *
	 * @param Bill $item The current object.
	 *
	 * @since  1.0.0
	 * @return string Displays a checkbox.
	 */
	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="id[]" value="%d"/>', esc_attr( $item->id ) );
	}

	/**
	 * Renders the number column.
	 *
	 * @param Bill $item The current object.
	 *
	 * @since  1.0.0
	 * @return string Displays the name.
	 */
	public function column_number( $item ) {
		return sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( 'edit', $item->id, $this->base_url ) ), wp_kses_post( $item->number ) );
	}

	/**
	 * Renders the reference column.
	 *
	 * @param Bill $item The current object.
	 *
	 * @since  1.0.0
	 * @return string Displays the reference.
	 */
	public function column_reference( $item ) {
		return $item->reference ? esc_html( $item->reference ) : '&mdash;';
	}

	/**
	 * Renders the date column.
	 *
	 * @param Bill $bill The current object.
	 *
	 * @since  1.0.0
	 * @return string Displays the date.
	 */
	public function column_issue_date( $item ) {
		return $item->issue_date ? esc_html( wp_date( 'd M Y', strtotime( $item->issue_date ) ) ) : '&mdash;';
	}

	/**
	 * Renders the due date column.
	 *
	 * @param Bill $item The current object.
	 *
	 * @since  1.0.0
	 * @return string Displays the due date.
	 */
	public function column_due_date( $item ) {
		return $item->due_date ? esc_html( wp_date( 'd M Y', strtotime( $item->due_date ) ) ) : '&mdash;';
	}

	/**
	 * Renders the price column.
	 *
	 * @param Bill $item The current object.
	 *
	 * @since  1.0.0
	 * @return string Displays the price.
	 */
	public function column_total( $item ) {
		return esc_html( $item->formatted_total );
	}


	/**
	 * Renders the vendor column.
	 *
	 * @param Bill $item The current object.
	 *
	 * @since  1.0.0
	 * @return string Displays the vendor.
	 */
	public function column_vendor( $item ) {
		if ( $item->vendor ) {
			return sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( 'vendor_id', $item->vendor->id, $this->base_url ) ), wp_kses_post( $item->vendor->name ) );
		}

		return '&mdash;';
	}

	/**
	 * Renders the status column.
	 *
	 * @param Bill $item The current object.
	 *
	 * @since 1.0.0
	 * @return string Displays the status.
	 */
	public function column_status( $item ) {
		$statuses = EAC()->invoices->get_statuses();
		$status   = isset( $item->status ) ? $item->status : '';
		$label    = isset( $statuses[ $status ] ) ? $statuses[ $status ] : '';

		return sprintf( '<span class="eac-status is--%1$s">%2$s</span>', esc_attr( $status ), esc_html( $label ) );
	}

	/**
	 * Generates and displays row actions links.
	 *
	 * @param Bill   $item The object.
	 * @param string $column_name Current column name.
	 * @param string $primary Primary column name.
	 *
	 * @since 1.0.0
	 * @return string Row actions output.
	 */
	protected function handle_row_actions( $item, $column_name, $primary ) {
		if ( $primary !== $column_name ) {
			return null;
		}
		$actions = array(
			'view' => sprintf(
				'<a href="%s">%s</a>',
				esc_url(
					add_query_arg(
						array(
							'action' => 'view',
							'id'     => $item->id,
						),
						$this->base_url
					)
				),
				__( 'View', 'wp-ever-accounting' )
			),
			'edit' => sprintf(
				'<a href="%s">%s</a>',
				esc_url(
					add_query_arg(
						array(
							'action' => 'edit',
							'id'     => $item->id,
						),
						$this->base_url
					)
				),
				__( 'Edit', 'wp-ever-accounting' )
			),
		);

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
