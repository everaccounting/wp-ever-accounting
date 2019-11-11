<?php
defined( 'ABSPATH' ) || exit();


class EAccounting_Payment_Methods_List_Table extends EAccounting_List_Table {
	/**
	 * EAccounting_Payment_Methods_List_Table constructor.
	 */
	public function __construct() {
		parent::__construct( array(
			'singular' => 'payment_method',
			'plural'   => 'payment_methods',
			'ajax'     => false,
		) );
		$this->base_url = admin_url( 'admin.php?page=eaccounting-misc&tab=payment_methods' );
		$this->process_bulk_action();
	}

	/**
	 * Retrieve the table columns
	 *
	 * @return array $columns Array of all the list table columns
	 * @since 1.0.0
	 */
	public function get_columns() {
		$columns = array(
			'cb'     => '<input type="checkbox" />',
			'name'   => __( 'Name', 'wp-ever-accounting' ),
			'code'   => __( 'Code', 'wp-ever-accounting' ),
			'order'  => __( 'Order', 'wp-ever-accounting' ),
			'status' => __( 'Status', 'wp-ever-accounting' ),
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
			'name'   => array( 'name', false ),
			'code'   => array( 'code', false ),
			'order'  => array( 'order', false ),
			'status' => array( 'status', false ),
		);
	}

	/**
	 * Render the Name Column
	 *
	 * @param array $item Contains all the data of the discount code
	 *
	 * @return string Data shown in the Name column
	 * @since 1.0.0
	 */
	function column_name( $item ) {
		$payment_method_url    = add_query_arg( array( 'payment_method' => $item->id ), $this->base_url );
		$edit_url       = wp_nonce_url( add_query_arg( [ 'eaccounting-action' => 'edit_payment_method' ], $payment_method_url ), 'eaccounting_payment_methods_nonce' );
		$activate_url   = wp_nonce_url( add_query_arg( [ 'eaccounting-action' => 'activate_payment_method' ], $payment_method_url ), 'eaccounting_payment_methods_nonce' );
		$deactivate_url = wp_nonce_url( add_query_arg( [ 'eaccounting-action' => 'deactivate_payment_method' ], $payment_method_url ), 'eaccounting_payment_methods_nonce' );
		$delete_url     = wp_nonce_url( add_query_arg( [ 'eaccounting-action' => 'delete_payment_method' ], $payment_method_url ), 'eaccounting_payment_methods_nonce' );

		$row_actions['edit'] = sprintf( '<a href="%1$s">%2$s</a>', $edit_url, __( 'Edit', 'wp-eaccounting' ) );

		if ( strtolower( $item->status ) == 'active' ) {
			$row_actions['deactivate'] = sprintf( '<a href="%1$s">%2$s</a>', $deactivate_url, __( 'Deactivate', 'wp-eaccounting' ) );
		} elseif ( strtolower( $item->status ) == 'inactive' ) {
			$row_actions['activate'] = sprintf( '<a href="%1$s">%2$s</a>', $activate_url, __( 'Activate', 'wp-eaccounting' ) );
		}
		$row_actions['delete'] = sprintf( '<a href="%1$s">%2$s</a>', $delete_url, __( 'Delete', 'wp-eaccounting' ) );

		$row_actions = apply_filters( 'eaccounting_payment_methods_row_actions', $row_actions, $item );

		return sprintf( '<strong><a href="%1$s">%2$s</a></strong>', $edit_url, stripslashes( $item->name ) ) . $this->row_actions( $row_actions );
	}

	/**
	 * @since 1.0.0
	 * @param object $item
	 * @param string $column_name
	 * @return string;
	 */
	function column_default( $item, $column_name ) {
		return $item->$column_name;
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

		if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'bulk-payment_methods' ) ) {
			return;
		}

		$ids = isset( $_GET['payment_method'] ) ? $_GET['payment_method'] : false;

		if ( ! is_array( $ids ) ) {
			$ids = array( $ids );
		}


		foreach ( $ids as $id ) {
			if ( 'delete' === $this->current_action() ) {
				eaccounting_delete_payment_method( $id );
			}
			if ( 'activate' === $this->current_action() ) {
				eaccounting_insert_payment_method( [ 'id' => $id, 'status' => '1' ] );
			}
			if ( 'deactivate' === $this->current_action() ) {
				eaccounting_insert_payment_method( [ 'id' => $id, 'status' => '0' ] );
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

		$orderby = isset( $_GET['orderby'] ) ? $_GET['orderby'] : 'created_at';
		$order   = isset( $_GET['order'] ) ? $_GET['order'] : 'DESC';
		$status  = isset( $_GET['status'] ) ? $_GET['status'] : '';
		$search  = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : null;

		$args = array(
			'per_page' => $per_page,
			'page'     => isset( $_GET['paged'] ) ? $_GET['paged'] : 1,
			'orderby'  => $orderby,
			'order'    => $order,
			'status'   => $status,
			'search'   => $search
		);

		if ( array_key_exists( $orderby, $this->get_sortable_columns() ) && 'name' != $orderby ) {
			$args['orderby'] = $orderby;
		}

		$this->active_count   = eaccounting_get_payment_methods( array_merge( $args, array( 'status' => 'active' ) ), true );
		$this->inactive_count = eaccounting_get_payment_methods( array_merge( $args, array( 'status' => 'inactive' ) ), true );
		$this->total_count    = eaccounting_get_payment_methods( array_merge( $args, array( 'status' => '' ) ), true );

		$results = eaccounting_get_payment_methods( $args );

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

		$data = $this->get_results();

		$status = isset( $_GET['status'] ) ? $_GET['status'] : 'any';

		switch ( $status ) {
			case 'active':
				$total_items = $this->active_count;
				break;
			case 'inactive':
				$total_items = $this->inactive_count;
				break;
			case 'any':
			default:
				$total_items = $this->total_count;
				break;
		}

		$this->items = $data;

		$this->set_pagination_args( array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page ),
			)
		);
	}

}
