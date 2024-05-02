<?php

namespace EverAccounting\Admin\ListTables;

use EverAccounting\Models\Bill;

defined( 'ABSPATH' ) || exit;

/**
 * Class BillsTable.
 *
 * @since 1.0.0
 * @package EverAccounting\Admin\ListTables
 */
class BillsTable extends ListTable {
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
	}

	/**
	 * Prepares the list for display.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function prepare_items() {
		$this->process_bulk_action();
		$this->_column_headers = array(
			$this->get_columns(),
			get_hidden_columns( $this->screen ),
			$this->get_sortable_columns()
		);
		$per_page              = $this->get_items_per_page( 'eac_expenses_bills_per_page', 20 );
		$paged                 = $this->get_pagenum();
		$search                = $this->get_request_search();
		$order_by              = $this->get_request_orderby();
		$order                 = $this->get_request_order();
		$args                  = array(
			'limit'    => $per_page,
			'page'     => $paged,
			'search'   => $search,
			'orderby' => $order_by,
			'order'    => $order,
			'status'   => $this->get_request_status(),
		);
		/**
		 * Filter the query arguments for the list table.
		 *
		 * @param array $args An associative array of arguments.
		 *
		 * @since 1.0.0
		 */
		$args = apply_filters( 'ever_accounting_bills_table_query_args', $args );

		// TODO: Need to create bill query methods.
		//$this->items = eac_get_bills( $args );
		$this->items = array();
		//$total       = eac_get_bills( $args, true );
		$total = 0;

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
		$performed = [];
		foreach ( $ids as $id ) {
			// TODO: Need tp create the bill delete method.
//			if ( eac_delete_bill( $id ) ) {
//				$performed[] = $id;
//			}
		}
		if ( ! empty( $performed ) ) {
			EAC()->flash()->success( sprintf( _n( 'Bill deleted.', '%s bills deleted.', count( $performed ), 'wp-ever-accounting' ), count( $performed ) ) );
		}
	}

	/**
	 * Outputs 'no users' message.
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
	 * @return string[] An array of HTML links keyed by their view.
	 * @since 1.0.0
	 *
	 * @global string $role
	 */
	protected function get_views() {
	}

	/**
	 * Retrieves an associative array of bulk actions available on this table.
	 *
	 * @return array Array of bulk action labels keyed by their action.
	 * @since 1.0.0
	 *
	 */
	protected function get_bulk_actions() {
		$actions = array(
			'cancel'   => __( 'Cancel', 'wp-ever-accounting' ),
			'paid'     => __( 'paid', 'wp-ever-accounting' ),
			'received' => __( 'Received', 'wp-ever-accounting' ),
			'delete'   => __( 'Delete', 'wp-ever-accounting' ),
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
		static $has_items;
		if ( ! isset( $has_items ) ) {
			$has_items = $this->has_items();
		}

		if ( 'top' === $which ) {
			ob_start();
			$this->category_filter( 'item' );
			$output = ob_get_clean();
			if ( ! empty( $output ) && $this->has_items() ) {
				echo $output;
				submit_button( __( 'Filter', 'wp-ever-accounting' ), 'alignleft', 'filter_action', false );
			}
		}
	}

	/**
	 * Gets a list of columns for the list table.
	 *
	 * @return string[] Array of column titles keyed by their column name.
	 * @since 1.0.0
	 *
	 */
	public function get_columns() {
		return array(
			'cb'        => '<input type="checkbox" />',
			'number'    => __( 'Number', 'wp-ever-accounting' ),
			'total'     => __( 'Total', 'wp-ever-accounting' ),
			'vendor'    => __( 'Vendor', 'wp-ever-accounting' ),
			'bill_date' => __( 'Bill Date', 'wp-ever-accounting' ),
			'due_date'  => __( 'Due Date', 'wp-ever-accounting' ),
			'status'    => __( 'Status', 'wp-ever-accounting' ),
		);
	}

	/**
	 * Gets a list of sortable columns for the list table.
	 *
	 * @return array Array of sortable columns.
	 * @since 1.0.0
	 *
	 */
	protected function get_sortable_columns() {
		return array(
			'number'    => array( 'number', false ),
			'total'     => array( 'total', false ),
			'vendor'    => array( 'vendor', false ),
			'bill_date' => array( 'bill_date', false ),
			'due_date'  => array( 'due_date', false ),
			'status'    => array( 'status', false ),
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
	 * @param Item $item The current object.
	 *
	 * @return string Displays a checkbox.
	 * @since  1.0.0
	 */
	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="id[]" value="%d"/>', esc_attr( $item->id ) );
	}

	/**
	 * Renders the date column.
	 *
	 * @param Bill $bill The current object.
	 *
	 * @return string Displays the date.
	 * @since  1.0.0
	 */
	public function column_number( $bill ) {
		$urls    = array(
			'edit'    => admin_url( 'admin.php?page=eac-expenses&edit=' . $bill->id ),
			'delete'  => wp_nonce_url( admin_url( 'admin.php?page=eac-expenses&tab=bills&action=delete&id=' . $bill->id ), 'bulk-' . $this->_args['plural'] ),
			'enable'  => wp_nonce_url( admin_url( 'admin.php?page=eac-expenses&tab=bills&action=enable&id=' . $bill->id ), 'bulk-' . $this->_args['plural'] ),
			'disable' => wp_nonce_url( admin_url( 'admin.php?page=eac-expenses&tab=bills&action=disable&id=' . $bill->id ), 'bulk-' . $this->_args['plural'] ),
		);
		$actions = array(
			'ID'     => sprintf( 'ID: %d', $bill->id ),
			'delete' => sprintf( '<a class="eac_confirm_delete" href="%s">%s</a>', esc_url( $urls['delete'] ), __( 'Delete', 'wp-ever-accounting' ) ),
		);
		if ( $bill->enabled ) {
			$actions['disable'] = sprintf( '<a href="%s">%s</a>', esc_url( $urls['disable'] ), __( 'Disable', 'wp-ever-accounting' ) );
		} else {
			$actions['enable'] = sprintf( '<a href="%s">%s</a>', esc_url( $urls['enable'] ), __( 'Enable', 'wp-ever-accounting' ) );
		}

		return sprintf( '<a href="%1$s">%2$s</a>%3$s', admin_url( 'admin.php?page=eac-expenses&tab=bills&edit=' . $bill->id ), wp_kses_post( $bill->name ), $this->row_actions( $actions ) );
	}

	/**
	 * Renders the actions column.
	 *
	 * @param Bill $bill The current object.
	 *
	 * @return string Displays the actions.
	 * @since  1.0.0
	 */
	public function column_actions( $bill ) {
		$urls = array(
			'edit'    => admin_url( 'admin.php?page=eac-items&edit=' . $bill->id ),
			'delete'  => wp_nonce_url( admin_url( 'admin.php?page=eac-expenses&tab=bills&action=delete&id=' . $bill->id ), 'bulk-' . $this->_args['plural'] ),
			'enable'  => wp_nonce_url( admin_url( 'admin.php?page=eac-expenses&tab=bills&action=enable&id=' . $bill->id ), 'bulk-' . $this->_args['plural'] ),
			'disable' => wp_nonce_url( admin_url( 'admin.php?page=eac-expenses&tab=bills&action=disable&id=' . $bill->id ), 'bulk-' . $this->_args['plural'] ),
		);

		$actions = array(
			//'edit'   => sprintf( '<a href="%s">%s</a>', esc_url( $urls['edit'] ), __( 'Edit', 'wp-ever-accounting' ) ),
			'delete' => sprintf( '<a class="eac_confirm_delete" href="%s">%s</a>', esc_url( $urls['delete'] ), __( 'Delete', 'wp-ever-accounting' ) ),
		);
		if ( $bill->enabled ) {
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
	 * @param string $column_name The name of the column.
	 *
	 * @return string The column value.
	 * @since 1.0.0
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'column_name':
				return empty( $item->column_name ) ? '&mdash;' : $item->column_name;
		}

		return parent::column_default( $item, $column_name );
	}
}
