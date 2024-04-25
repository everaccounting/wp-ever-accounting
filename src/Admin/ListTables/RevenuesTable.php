<?php

namespace EverAccounting\Admin\ListTables;

use EverAccounting\Models\Revenue;

defined( 'ABSPATH' ) || exit;

/**
 * Class RevenuesTable.
 *
 * @since 1.0.0
 * @package EverAccounting\Admin\ListTables
 */
class RevenuesTable extends ListTable {
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
					'singular' => 'revenue',
					'plural'   => 'revenues',
					'screen'   => get_current_screen(),
					'args'     => array(),
				)
			)
		);

		$this->base_url = admin_url( 'admin.php?page=eac-sales&tab=revenues' );
	}

	/**
	 * Prepares the list for display.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function prepare_items() {
		$this->process_actions();
		$per_page    = $this->get_items_per_page( 'eac_revenues_per_page', 20 );
		$paged       = $this->get_pagenum();
		$search      = $this->get_request_search();
		$order_by    = $this->get_request_orderby();
		$order       = $this->get_request_order();
		$account_id  = isset( $_GET['account_id'] ) ? absint( wp_unslash( $_GET['account_id'] ) ) : 0;
		$category_id = isset( $_GET['category_id'] ) ? absint( wp_unslash( $_GET['category_id'] ) ) : 0;
		$contact_id  = isset( $_GET['customer_id'] ) ? absint( wp_unslash( $_GET['customer_id'] ) ) : 0;
		$args        = array(
			'limit'    => $per_page,
			'page'     => $paged,
			'search'   => $search,
			'order_by' => $order_by,
			'order'    => $order,
			'status'   => $this->get_request_status(),
			'account'  => $account_id,
			'category' => $category_id,
			'customer' => $contact_id,
		);
		/**
		 * Filter the query arguments for the list table.
		 *
		 * @param array $args An associative array of arguments.
		 *
		 * @since 1.0.0
		 */
		$args = apply_filters( 'ever_accounting_revenues_table_query_args', $args );

		$this->items = Revenue::query( $args );
		$total       = Revenue::count( $args );

		$this->set_pagination_args(
			array(
				'total_items' => $total,
				'per_page'    => $per_page,
			)
		);
	}

	/**
	 * Outputs 'no users' message.
	 *
	 * @since 1.0.0
	 */
	public function no_items() {
		esc_html_e( 'No revenues found.', 'wp-ever-accounting' );
	}

	/**
	 * Returns an associative array listing all the views that can be used
	 * with this table.
	 *
	 * @since 1.0.0
	 *
	 * @return string[] An array of HTML links keyed by their view.
	 */
	protected function get_views() {
		$current      = $this->get_request_status( 'all' );
		$status_links = array();

		// $views        = array(
		// translators: %s: number of revenues.
		// 'all'       => _nx_noop( 'All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', 'list_table', 'wp-ever-accounting' ),
		// translators: %s: number of revenues.
		// 'pending'   => _nx_noop( 'Pending <span class="count">(%s)</span>', 'Pending <span class="count">(%s)</span>', 'list_table', 'wp-ever-accounting' ),
		// translators: %s: number of revenues.
		// 'paid'      => _nx_noop( 'Paid <span class="count">(%s)</span>', 'Paid <span class="count">(%s)</span>', 'list_table', 'wp-ever-accounting' ),
		// translators: %s: number of revenues.
		// 'refunded'  => _nx_noop( 'Refunded <span class="count">(%s)</span>', 'Refunded <span class="count">(%s)</span>', 'list_table', 'wp-ever-accounting' ),
		// translators: %s: number of revenues.
		// 'cancelled' => _nx_noop( 'Cancelled <span class="count">(%s)</span>', 'Cancelled <span class="count">(%s)</span>', 'list_table', 'wp-ever-accounting' ),
		// );
		$views = array();

		//return $this->get_views_links( $status_links );
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
			'mark_paid'      => __( 'Mark as Paid', 'wp-ever-accounting' ),
			'mark_pending'   => __( 'Mark as Pending', 'wp-ever-accounting' ),
			'mark_refunded'  => __( 'Mark as Refunded', 'wp-ever-accounting' ),
			'mark_cancelled' => __( 'Mark as Cancelled', 'wp-ever-accounting' ),
			'delete'         => __( 'Delete', 'wp-ever-accounting' ),
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
		// TODO: Need to include revenuesTable filters 'Select Month', 'Select Account', 'Select Category', 'Select Customer'.
		static $has_items;
		if ( ! isset( $has_items ) ) {
			$has_items = $this->has_items();
		}
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
			'cb'        => '<input type="checkbox" />',
			'date'      => __( 'Date', 'wp-ever-accounting' ),
			'amount'    => __( 'Amount', 'wp-ever-accounting' ),
			'account'   => __( 'Account', 'wp-ever-accounting' ),
			'category'  => __( 'Category', 'wp-ever-accounting' ),
			'customer'  => __( 'Customer', 'wp-ever-accounting' ),
			'reference' => __( 'Reference', 'wp-ever-accounting' ),
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
			'date'      => array( 'date', false ),
			'amount'    => array( 'amount', false ),
			'account'   => array( 'account', false ),
			'category'  => array( 'category', false ),
			'customer'  => array( 'customer', false ),
			'reference' => array( 'reference', false ),
		);
	}

	/**
	 * Define primary column.
	 *
	 * @since 1.0.2
	 * @return string
	 */
	public function get_primary_column_name() {
		return 'date';
	}

	/**
	 * Renders the checkbox column.
	 *
	 * @param Revenue $item The current object.
	 *
	 * @since  1.0.0
	 * @return string Displays a checkbox.
	 */
	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="id[]" value="%d"/>', esc_attr( $item->id ) );
	}

	/**
	 * Renders the date column.
	 *
	 * @param Revenue $revenue The current object.
	 *
	 * @since  1.0.0
	 * @return string Displays the date.
	 */
	public function column_date( $revenue ) {
		$urls    = array(
			'edit'    => admin_url( 'admin.php?page=eac-sales&edit=' . $revenue->id ),
			'delete'  => wp_nonce_url( admin_url( 'admin.php?page=eac-sales&tab=revenues&action=delete&id=' . $revenue->id ), 'bulk-' . $this->_args['plural'] ),
			'enable'  => wp_nonce_url( admin_url( 'admin.php?page=eac-sales&tab=revenues&action=enable&id=' . $revenue->id ), 'bulk-' . $this->_args['plural'] ),
			'disable' => wp_nonce_url( admin_url( 'admin.php?page=eac-sales&tab=revenues&action=disable&id=' . $revenue->id ), 'bulk-' . $this->_args['plural'] ),
		);
		$actions = array(
			'ID'     => sprintf( 'ID: %d', $revenue->id ),
			'delete' => sprintf( '<a class="eac_confirm_delete" href="%s">%s</a>', esc_url( $urls['delete'] ), __( 'Delete', 'wp-ever-accounting' ) ),
		);
		if ( $revenue->enabled ) {
			$actions['disable'] = sprintf( '<a href="%s">%s</a>', esc_url( $urls['disable'] ), __( 'Disable', 'wp-ever-accounting' ) );
		} else {
			$actions['enable'] = sprintf( '<a href="%s">%s</a>', esc_url( $urls['enable'] ), __( 'Enable', 'wp-ever-accounting' ) );
		}

		return sprintf( '<a href="%1$s">%2$s</a>%3$s', admin_url( 'admin.php?page=eac-sales&tab=revenues&edit=' . $revenue->id ), wp_kses_post( $revenue->name ), $this->row_actions( $actions ) );
	}

	/**
	 * Renders the actions column.
	 *
	 * @param Revenue $revenue The current object.
	 *
	 * @since  1.0.0
	 * @return string Displays the actions.
	 */
	public function column_actions( $revenue ) {
		$urls = array(
			'edit'    => admin_url( 'admin.php?page=eac-items&edit=' . $revenue->id ),
			'delete'  => wp_nonce_url( admin_url( 'admin.php?page=eac-sales&tab=revenues&action=delete&id=' . $revenue->id ), 'bulk-' . $this->_args['plural'] ),
			'enable'  => wp_nonce_url( admin_url( 'admin.php?page=eac-sales&tab=revenues&action=enable&id=' . $revenue->id ), 'bulk-' . $this->_args['plural'] ),
			'disable' => wp_nonce_url( admin_url( 'admin.php?page=eac-sales&tab=revenues&action=disable&id=' . $revenue->id ), 'bulk-' . $this->_args['plural'] ),
		);

		$actions = array(
			// 'edit'   => sprintf( '<a href="%s">%s</a>', esc_url( $urls['edit'] ), __( 'Edit', 'wp-ever-accounting' ) ),
			'delete' => sprintf( '<a class="eac_confirm_delete" href="%s">%s</a>', esc_url( $urls['delete'] ), __( 'Delete', 'wp-ever-accounting' ) ),
		);
		if ( $revenue->enabled ) {
			$actions['disable'] = sprintf( '<a href="%s">%s</a>', esc_url( $urls['disable'] ), __( 'Disable', 'wp-ever-accounting' ) );
		} else {
			$actions['enable'] = sprintf( '<a href="%s">%s</a>', esc_url( $urls['enable'] ), __( 'Enable', 'wp-ever-accounting' ) );
		}

		return $this->row_actions( $actions, true );
	}

	/**
	 * This function renders most of the columns in the list table.
	 *
	 * @param Object|array $item The current item.
	 * @param string       $column_name The name of the column.
	 *
	 * @since 1.0.0
	 * @return string The column value.
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'column_name':
				return empty( $item->column_name ) ? '&mdash;' : $item->column_name;
		}

		return parent::column_default( $item, $column_name );
	}
}
