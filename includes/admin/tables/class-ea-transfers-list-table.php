<?php
defined( 'ABSPATH' ) || exit();


class EAccounting_Transfers_List_Table extends EAccounting_List_Table {
	public function __construct() {
		parent::__construct( array(
			'singular' => 'transfer',
			'plural'   => 'transfers',
			'ajax'     => false,
		) );
		$this->base_url = admin_url( 'admin.php?page=eaccounting-transfers' );
		$this->process_bulk_action();
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
		$actions = array(
			'delete' => __( 'Delete', 'wp-ever-accounting' ),
		);

		return $actions;
	}

	/**
	 * Retrieve the table columns
	 *
	 * @return array $columns Array of all the list table columns
	 * @since 1.0.0
	 */
	public function get_columns() {
		$columns = array(
			'cb'             => '<input type="checkbox" />',
			'date'           => __( 'Date', 'wp-ever-accounting' ),
			'amount'         => __( 'Amount', 'wp-ever-accounting' ),
			'from_bank'      => __( 'From Bank', 'wp-ever-accounting' ),
			'to_bank'        => __( 'To Bank', 'wp-ever-accounting' ),
			'payment_method' => __( 'Payment Method', 'wp-ever-accounting' ),
			'reference'      => __( 'Reference', 'wp-ever-accounting' ),
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
			'date'           => array( 'paid_at', false ),
			'amount'         => array( 'amount', false ),
			'from_bank'      => array( 'from_bank_id', false ),
			'to_bank'        => array( 'to_bank_id', false ),
			'payment_method' => array( 'payment_method', false ),
			'reference'      => array( 'reference', false ),
		);
	}

	/**
	 * @param $item
	 *
	 * @return string
	 * @since 1.0.0
	 */
	function column_date( $item ) {
		$transfer_url        = add_query_arg( array( 'transfer' => $item->id ), $this->base_url );
		$edit_url            = wp_nonce_url( add_query_arg( [ 'eaccounting-action' => 'edit_transfer' ], $transfer_url ), 'eaccounting_transfers_nonce' );
		$delete_url          = wp_nonce_url( add_query_arg( [ 'eaccounting-action' => 'delete_transfer' ], $transfer_url ), 'eaccounting_transfers_nonce' );
		$transferred_at      = date( 'Y-m-d', strtotime( $item->transferred_at ) );
		$row_actions['edit'] = sprintf( '<a href="%1$s">%2$s</a>', $edit_url, __( 'Edit', 'wp-ever-accounting' ) );

		$row_actions['delete'] = sprintf( '<a href="%1$s">%2$s</a>', $delete_url, __( 'Delete', 'wp-ever-accounting' ) );

		$row_actions = apply_filters( 'eaccounting_transfers_row_actions', $row_actions, $item );

		return sprintf( '<strong><a href="%1$s">%2$s</a></strong>', $edit_url, $transferred_at ) . $this->row_actions( $row_actions );
	}

	/**
	 * @param object $item
	 * @param string $column_name
	 *
	 * @return string|void
	 * @since 1.0.0
	 */
	function column_default( $item, $column_name ) {

		switch ( $column_name ) {
			case 'from_bank':
				$from_account = new EAccounting_Account( $item->from_account_id );

				return $from_account->get_name( 'view' );
				break;
			case 'to_bank':
				$to_account = new EAccounting_Account( $item->to_account_id );

				return $to_account->get_name( 'view' );
				break;
			case 'amount':
				return eaccounting_price( $item->amount );
				break;
			case 'payment_method':
				$methods = eaccounting_get_payment_methods();

				return array_key_exists( $item->payment_method, $methods ) ? $methods[ $item->payment_method ] : '&mdash;';
				break;
			case 'reference':
				return empty( $item->reference ) ? '&mdash;' : wp_unslash( $item->reference );
				break;
			default:
				return '&mdash;';
				break;
		}
	}

	/**
	 * Process the bulk actions
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function process_bulk_action() {

		if ( empty( $_REQUEST['_wpnonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'bulk-transfers' ) ) {
			return;
		}

		$ids = isset( $_GET['transfer'] ) ? $_GET['transfer'] : false;

		if ( ! is_array( $ids ) ) {
			$ids = array( $ids );
		}
		$ids = array_map( 'intval', $ids );

		foreach ( $ids as $id ) {
			if ( 'delete' === $this->current_action() ) {
				eaccounting_delete_transfer( $id );
			}
		}
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
		$status  = isset( $_GET['status'] ) ? sanitize_key( $_GET['status'] ) : '';
		$search  = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : null;

		$args = array(
			'per_page' => $per_page,
			'page'     => isset( $_GET['paged'] ) ? intval( $_GET['paged'] ) : 1,
			'orderby'  => $orderby,
			'order'    => $order,
			'status'   => $status,
			'search'   => $search
		);

		if ( array_key_exists( $orderby, $this->get_sortable_columns() ) && 'name' != $orderby ) {
			$args['orderby'] = $orderby;
		}


		$this->total_count = eaccounting_get_transfers( $args, true );

		$results = eaccounting_get_transfers( $args );

		return $results;
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

		$this->process_bulk_action();

		$items = $this->get_results();

		$total_items = $this->total_count;

		$this->items = $items;

		$this->set_pagination_args( array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page ),
			)
		);
	}

}
