<?php
defined( 'ABSPATH' ) || exit();

// Load WP_List_Table if not loaded
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class EAccounting_TransactionS_List_Table extends WP_List_Table {
	/**
	 * Number of results to show per page
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public $per_page = 20;

	/**
	 *
	 * Total number of discounts
	 * @var string
	 * @since 1.0.0
	 */
	public $total_count;

	/**
	 * Active number of account
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public $active_count;

	/**
	 * Inactive number of account
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public $inactive_count;

	/**
	 * Base URL
	 * @var string
	 */
	public $base_url;

	/**
	 * EAccounting_TransactionS_List_Table constructor.
	 */
	public function __construct() {
		parent::__construct( array(
			'singular' => 'transaction',
			'plural'   => 'transaction',
			'ajax'     => false,
		) );
	}

	/**
	 * Setup the final data for the table
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function prepare_items() {
		$per_page = $this->per_page;

		$columns = $this->get_columns();

		$hidden = array();

		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$items = $this->get_results();

//		$data = array_map( function ( $item ) {
//			return new EAccounting_Payment( $item );
//		}, $items );

		$total_items = $this->total_count;

		$this->items = $items;

		$this->set_pagination_args( array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page ),
			)
		);
	}


	/**
	 * Retrieve the view types
	 *
	 * @return array $views All the views available
	 * @since 1.0.0
	 */
	public function get_views() {
		return array();
	}

	/**
	 * Retrieve the bulk actions
	 *
	 * @return array $actions Array of the bulk actions
	 * @since 1.0.0
	 */
	public function get_bulk_actions() {
		return array();
	}

	/**
	 * Retrieve the table columns
	 *
	 * @return array $columns Array of all the list table columns
	 * @since 1.0.0
	 */
	public function get_columns() {
		$columns = array(
			'date'      => __( 'Date', 'wp-ever-accounting' ),
			'amount'    => __( 'Amount', 'wp-ever-accounting' ),
			'account'   => __( 'Account Name', 'wp-ever-accounting' ),
			'type'      => __( 'Type', 'wp-ever-accounting' ),
			'category'  => __( 'Category', 'wp-ever-accounting' ),
			'reference' => __( 'Reference', 'wp-ever-accounting' ),
		);

		return $columns;
	}

	/**
	 * Retrieve the table's sortable columns
	 *
	 * @return array Array of all the sortable columns
	 * @since 1.0.0
	 */
	public function get_sortable_columns() {
		return array(
			'date'     => array( 'paid_at', false ),
			'amount'   => array( 'amount', false ),
			'account'  => array( 'account_id', false ),
			'type'     => array( 'type', false ),
			'category' => array( 'category_id', false ),
		);
	}

	/**
	 * Render the Name Column
	 *
	 * @param EAccounting_Revenue $item Contains all the data of the discount code
	 *
	 * @return string Data shown in the Name column
	 * @since 1.0.0
	 */
	function column_date( $item ) {
		$edit_url = '';
		switch ( $item->type ) {
			case 'income':
				$edit_url = add_query_arg( [ 'revenue'            => $item->id,
				                             'eaccounting-action' => 'edit_revenue'
				], admin_url( 'admin.php?page=eaccounting-revenues' ) );
				break;
			case 'expense':
				$edit_url = add_query_arg( [ 'payment'            => $item->id,
				                             'eaccounting-action' => 'edit_payment'
				], admin_url( 'admin.php?page=eaccounting-payments' ) );
				break;
		}
		$paid_at = date( 'Y-m-d', strtotime( $item->paid_at ) );

		return sprintf( '<strong><a href="%1$s">%2$s</a></strong>', $edit_url, $paid_at );
	}

	/**
	 * @param EAccounting_Revenue $item
	 *
	 * @return string;
	 * @since 1.0.0
	 */
	function column_amount( $item ) {
		return $item->amount ? eaccounting_price( $item->amount ) : '&mdash;';
	}

	/**
	 * @param EAccounting_Revenue $item
	 *
	 * @return string;
	 * @since 1.0.0
	 */
	function column_category( $item ) {
		if ( $item->category_id && $category = eaccounting_get_category( $item->category_id ) ) {
			return $category->name;
		}

		return '&mdash;';
	}

	/**
	 * @param EAccounting_Revenue $item
	 *
	 * @return string;
	 * @since 1.0.0
	 */
	function column_account( $item ) {
		if ( $item->account_id && $account = eaccounting_get_account( $item->account_id ) ) {
			return $account->name;
		}

		return '&mdash;';
	}

	/**
	 * @param EAccounting_Revenue $item
	 *
	 * @return string;
	 * @since 1.0.0
	 */
	function column_reference( $item ) {
		return $item->reference ? $item->reference : '&mdash;';
	}

	/**
	 * @return string
	 * @since 1.0.0
	 */
	function column_type( $item ) {
		return ucfirst( $item->type );
	}

	/**
	 * Retrieve all the data for all the discount codes
	 *
	 * @return array $get_results Array of all the data for the discount codes
	 * @since 1.0.0
	 */
	public function get_results() {
		$per_page = $this->per_page;

		$orderby = isset( $_GET['orderby'] ) ? sanitize_key( $_GET['orderby'] ) : 'created_at';
		$order   = isset( $_GET['order'] ) ? sanitize_key( $_GET['order'] ) : 'DESC';
		$search  = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : null;

		$args = array(
			'per_page' => $per_page,
			'page'     => isset( $_GET['paged'] ) ? intval( $_GET['paged'] ) : 1,
			'orderby'  => $orderby,
			'order'    => $order,
			'search'   => $search
		);

		if ( array_key_exists( $orderby, $this->get_sortable_columns() ) && 'name' != $orderby ) {
			$args['orderby'] = $orderby;
		}

		$this->total_count = eaccounting_get_transactions( $args, true );

		$results = eaccounting_get_transactions( $args );

		return $results;
	}

}
