<?php

namespace EverAccounting\Admin\ListTables;

use EverAccounting\Models\Invoice;

defined( 'ABSPATH' ) || exit;

/**
 * Class InvoicesTable.
 *
 * @since 1.0.0
 * @package EverAccounting\Admin\ListTables
 */
class InvoicesTable extends ListTable {
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
					'singular' => 'invoice',
					'plural'   => 'invoices',
					'screen'   => get_current_screen(),
					'args'     => array(),
				)
			)
		);

		$this->base_url = admin_url( 'admin.php?page=eac-sales&tab=invoices' );
	}

	/**
	 * Prepares the list for display.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function prepare_items() {
		$this->process_actions();
		$per_page = $this->get_items_per_page( 'eac_invoices_per_page', 20 );
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
		$args = apply_filters( 'ever_accounting_invoices_table_query_args', $args );

		$args['no_found_rows'] = false;
		$this->items           = Invoice::results( $args );
		$total                 = Invoice::count( $args );

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
	 * @return void
	 * @since 1.0.0
	 */
	protected function bulk_delete( $ids ) {
		$performed = 0;
		foreach ( $ids as $id ) {
			if ( eac_delete_invoice( $id ) ) {
				++$performed;
			}
		}
		if ( ! empty( $performed ) ) {
			// translators: %s: number of items deleted.
			EAC()->flash->success( sprintf( __( '%s invoice(s) deleted successfully.', 'wp-ever-accounting' ), number_format_i18n( $performed ) ) );
		}
	}

	/**
	 * Outputs 'no users' message.
	 *
	 * @since 1.0.0
	 */
	public function no_items() {
		esc_html_e( 'No invoices found.', 'wp-ever-accounting' );
	}

	/**
	 * Returns an associative array listing all the views that can be used
	 * with this table.
	 *
	 * Provides a list of roles and user count for that role for easy
	 * filtering of the user table.
	 *
	 * @return string[] An array of HTML links keyed by their view.
	 * @since 1.0.0
	 */
	protected function get_views() {
		$current      = $this->get_request_status( 'all' );
		$status_links = array();
		$statuses     = eac_get_invoice_statuses();

		foreach ( $statuses as $status => $label ) {
			$link  = 'all' === $status ? $this->base_url : add_query_arg( 'status', $status, $this->base_url );
			$args  = 'all' === $status ? array() : array( 'status' => $status );
			$count = Invoice::count( $args );
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
	 * @return void
	 * @since 1.0.0
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
	 * @return string[] Array of column titles keyed by their column name.
	 * @since 1.0.0
	 */
	public function get_columns() {
		return array(
			'cb'         => '<input type="checkbox" />',
			'number'     => __( 'Number', 'wp-ever-accounting' ),
			'total'      => __( 'Total', 'wp-ever-accounting' ),
			'customer'   => __( 'Customer', 'wp-ever-accounting' ),
			'issue_date' => __( 'Issue Date', 'wp-ever-accounting' ),
			'due_date'   => __( 'Due Date', 'wp-ever-accounting' ),
			'status'     => __( 'Status', 'wp-ever-accounting' ),
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
			'number'     => array( 'number', false ),
			'total'      => array( 'total', false ),
			'customer'   => array( 'customer', false ),
			'issue_date' => array( 'issue_date', false ),
			'due_date'   => array( 'due_date', false ),
			'status'     => array( 'status', false ),
		);
	}

	/**
	 * Define primary column.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_primary_column_name() {
		return 'number';
	}

	/**
	 * Renders the checkbox column.
	 *
	 * @param Invoice $item The current object.
	 *
	 * @return string Displays a checkbox.
	 * @since  1.0.0
	 */
	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="id[]" value="%d"/>', esc_attr( $item->id ) );
	}

	/**
	 * Renders the number column.
	 *
	 * @param Invoice $item The current object.
	 *
	 * @since  1.0.0
	 * @return string Displays the name.
	 */
	public function column_number( $item ) {
		return sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( 'edit', $item->id, $this->base_url ) ), wp_kses_post( $item->number ) );
	}

	/**
	 * Renders the price column.
	 *
	 * @param Invoice $item The current object.
	 *
	 * @since  1.0.0
	 * @return string Displays the price.
	 */
	public function column_total( $item ) {
		return esc_html( $item->formatted_total );
	}


	/**
	 * Renders the customer column.
	 *
	 * @param Invoice $item The current object.
	 *
	 * @since  1.0.0
	 * @return string Displays the customer.
	 */
	public function column_customer( $item ) {
		if ( $item->customer ) {
			return sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( 'customer_id', $item->customer->id, $this->base_url ) ), wp_kses_post( $item->customer->name ) );
		}

		return '&mdash;';
	}

	/**
	 * Renders the status column.
	 *
	 * @param Invoice $item The current object.
	 *
	 * @since 1.0.0
	 * @return string Displays the status.
	 */
	public function column_status( $item ) {
		$statuses = eac_get_invoice_statuses();
		$status   = isset( $item->status ) ? $item->status : '';
		$label    = isset( $statuses[ $status ] ) ? $statuses[ $status ] : '';

		return sprintf( '<span class="eac-status is--%1$s">%2$s</span>', esc_attr( $status ), esc_html( $label ) );
	}

	/**
	 * Generates and displays row actions links.
	 *
	 * @param Invoice $item The object.
	 * @param string  $column_name Current column name.
	 * @param string  $primary Primary column name.
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
				esc_url( add_query_arg( 'view', $item->id, $this->base_url ) ),
				__( 'View', 'wp-ever-accounting' )
			),
			'edit' => sprintf(
				'<a href="%s">%s</a>',
				esc_url( add_query_arg( 'edit', $item->id, $this->base_url ) ),
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
