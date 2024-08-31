<?php

namespace EverAccounting\Admin\ListTables;

use EverAccounting\ListTable;
use EverAccounting\Models\Transaction;

defined( 'ABSPATH' ) || exit;

/**
 * Class TransactionsTable.
 *
 * @since 1.0.0
 * @package EverAccounting\Admin\ListTables
 */
class TransactionsTable extends ListTable {
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
					'singular' => 'transaction',
					'plural'   => 'transactions',
					'screen'   => get_current_screen(),
					'args'     => array(),
				)
			)
		);
		$this->base_url = admin_url( 'admin.php?page=eac-banking&tab=transactions' );
	}

	/**
	 * Prepares the list for display.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function prepare_items() {
		$this->process_actions();
		$per_page = $this->get_items_per_page( 'eac_banking_transactions_per_page', 20 );
		$paged    = $this->get_pagenum();
		$search   = $this->get_request_search();
		$order_by = $this->get_request_orderby();
		$order    = $this->get_request_order();
		$args     = array(
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
		$args = apply_filters( 'ever_accounting_transactions_table_query_args', $args );

		$this->items = Transaction::query( $args );
		$total       = Transaction::count( $args );

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
	protected function bulk_delete( $ids ) {}

	/**
	 * Outputs 'no users' message.
	 *
	 * @since 1.0.0
	 */
	public function no_items() {
		esc_html_e( 'No transactions found.', 'wp-ever-accounting' );
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
	}

	/**
	 * Retrieves an associative array of bulk actions available on this table.
	 *
	 * @since 1.0.0
	 * @return array Array of bulk action labels keyed by their action.
	 */
	protected function get_bulk_actions() {
		return array();
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
	 * @since 1.0.0
	 * @return string[] Array of column titles keyed by their column name.
	 */
	public function get_columns() {
		return array(
			'cb'        => '<input type="checkbox" />',
			'date'      => __( 'Date', 'wp-ever-accounting' ),
			'amount'    => __( 'Amount', 'wp-ever-accounting' ),
			'type'      => __( 'Type', 'wp-ever-accounting' ),
			'account'   => __( 'Account', 'wp-ever-accounting' ),
			'category'  => __( 'Category', 'wp-ever-accounting' ),
			'reference' => __( 'Reference', 'wp-ever-accounting' ),
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
			'date'      => array( 'date', false ),
			'amount'    => array( 'amount', false ),
			'type'      => array( 'type', false ),
			'account'   => array( 'account', false ),
			'category'  => array( 'category', false ),
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
	 * @param Item $item The current object.
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
	 * @param Transaction $transaction The current object.
	 *
	 * @since  1.0.0
	 * @return string Displays the date.
	 */
	public function column_date( $transaction ) {
		$urls    = array(
			'edit'    => admin_url( 'admin.php?page=eac-expenses&edit=' . $transaction->id ),
			'delete'  => wp_nonce_url( admin_url( 'admin.php?page=eac-expenses&tab=transactions&action=delete&id=' . $transaction->id ), 'bulk-' . $this->_args['plural'] ),
			'enable'  => wp_nonce_url( admin_url( 'admin.php?page=eac-expenses&tab=transactions&action=enable&id=' . $transaction->id ), 'bulk-' . $this->_args['plural'] ),
			'disable' => wp_nonce_url( admin_url( 'admin.php?page=eac-expenses&tab=transactions&action=disable&id=' . $transaction->id ), 'bulk-' . $this->_args['plural'] ),
		);
		$actions = array(
			'ID'     => sprintf( 'ID: %d', $transaction->id ),
			'delete' => sprintf( '<a class="eac_confirm_delete" href="%s">%s</a>', esc_url( $urls['delete'] ), __( 'Delete', 'wp-ever-accounting' ) ),
		);
		if ( $transaction->enabled ) {
			$actions['disable'] = sprintf( '<a href="%s">%s</a>', esc_url( $urls['disable'] ), __( 'Disable', 'wp-ever-accounting' ) );
		} else {
			$actions['enable'] = sprintf( '<a href="%s">%s</a>', esc_url( $urls['enable'] ), __( 'Enable', 'wp-ever-accounting' ) );
		}

		return sprintf( '<a href="%1$s">%2$s</a>%3$s', admin_url( 'admin.php?page=eac-expenses&tab=transactions&edit=' . $transaction->id ), wp_kses_post( $transaction->name ), $this->row_actions( $actions ) );
	}

	/**
	 * Renders the actions column.
	 *
	 * @param Transaction $transaction The current object.
	 *
	 * @since  1.0.0
	 * @return string Displays the actions.
	 */
	public function column_actions( $transaction ) {
		$urls = array(
			'edit'    => admin_url( 'admin.php?page=eac-items&edit=' . $transaction->id ),
			'delete'  => wp_nonce_url( admin_url( 'admin.php?page=eac-expenses&tab=transactions&action=delete&id=' . $transaction->id ), 'bulk-' . $this->_args['plural'] ),
			'enable'  => wp_nonce_url( admin_url( 'admin.php?page=eac-expenses&tab=transactions&action=enable&id=' . $transaction->id ), 'bulk-' . $this->_args['plural'] ),
			'disable' => wp_nonce_url( admin_url( 'admin.php?page=eac-expenses&tab=transactions&action=disable&id=' . $transaction->id ), 'bulk-' . $this->_args['plural'] ),
		);

		$actions = array(
			// 'edit'   => sprintf( '<a href="%s">%s</a>', esc_url( $urls['edit'] ), __( 'Edit', 'wp-ever-accounting' ) ),
			'delete' => sprintf( '<a class="eac_confirm_delete" href="%s">%s</a>', esc_url( $urls['delete'] ), __( 'Delete', 'wp-ever-accounting' ) ),
		);
		if ( $transaction->enabled ) {
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
