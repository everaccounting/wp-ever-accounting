<?php
defined( 'ABSPATH' ) || exit();


class EAccounting_Accounts_List_Table extends EAccounting_List_Table {
	/**
	 * EAccounting_Products_List_Table constructor.
	 */
	public function __construct() {
		parent::__construct( array(
			'singular' => 'account',
			'plural'   => 'accounts',
			'ajax'     => false,
		) );
		$this->base_url = admin_url( 'admin.php?page=eaccounting-accounts' );
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
			'cb'              => '<input type="checkbox" />',
			'name'            => __( 'Name', 'wp-ever-accounting' ),
			'current_balance' => __( 'Current Balance', 'wp-ever-accounting' ),
			'number'          => __( 'Account Number', 'wp-ever-accounting' ),
			'bank_name'       => __( 'Bank Name', 'wp-ever-accounting' ),
			'opening_balance' => __( 'Opening Balance', 'wp-ever-accounting' ),
			'status'          => __( 'Status', 'wp-ever-accounting' ),
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
			'name'            => array( 'name', false ),
			'number'          => array( 'number', false ),
			'bank_name'       => array( 'bank_name', false ),
			'opening_balance' => array( 'opening_balance', false ),
			'status'          => array( 'status', false ),
		);
	}

	/**
	 * Render the Name Column
	 *
	 * @param EAccounting_Account $item Contains all the data of the discount code
	 *
	 * @return string Data shown in the Name column
	 * @since 1.0.0
	 */
	function column_name( $item ) {
		$account_url    = add_query_arg( array( 'account' => $item->get_id() ), $this->base_url );
		$edit_url       = wp_nonce_url( add_query_arg( [ 'eaccounting-action' => 'edit_account' ], $account_url ), 'eaccounting_accounts_nonce' );
		$activate_url   = wp_nonce_url( add_query_arg( [ 'eaccounting-action' => 'activate_account' ], $account_url ), 'eaccounting_accounts_nonce' );
		$deactivate_url = wp_nonce_url( add_query_arg( [ 'eaccounting-action' => 'deactivate_account' ], $account_url ), 'eaccounting_accounts_nonce' );
		$delete_url     = wp_nonce_url( add_query_arg( [ 'eaccounting-action' => 'delete_account' ], $account_url ), 'eaccounting_accounts_nonce' );

		$row_actions['edit'] = sprintf( '<a href="%1$s">%2$s</a>', $edit_url, __( 'Edit', 'wp-eaccounting' ) );

		if ( strtolower( $item->status ) == 'active' ) {
			$row_actions['deactivate'] = sprintf( '<a href="%1$s">%2$s</a>', $deactivate_url, __( 'Deactivate', 'wp-eaccounting' ) );
		} elseif ( strtolower( $item->status ) == 'inactive' ) {
			$row_actions['activate'] = sprintf( '<a href="%1$s">%2$s</a>', $activate_url, __( 'Activate', 'wp-eaccounting' ) );
		}
		$row_actions['delete'] = sprintf( '<a href="%1$s">%2$s</a>', $delete_url, __( 'Delete', 'wp-eaccounting' ) );

		$row_actions = apply_filters( 'eaccounting_accounts_row_actions', $row_actions, $item );

		return sprintf( '<strong><a href="%1$s">%2$s</a></strong>', $edit_url, stripslashes( $item->get_name() ) ) . $this->row_actions( $row_actions );
	}

	/**
	 * since 1.0.0
	 * @param $item EAccounting_Account
	 *
	 * @return string
	 */
	function column_number( $item ) {
		return !empty( $item->get_number() ) ? $item->get_number() : '&mdash;';
	}

	/**
	 * since 1.0.0
	 * @param $item EAccounting_Account
	 *
	 * @return string
	 */
	function column_bank_name( $item ) {
		return !empty( $item->get_bank_name() ) ? $item->get_bank_name() : '&mdash;';
	}

	/**
	 * @param object $item
	 * @param string $column_name
	 *
	 * @return string;
	 * @since 1.0.0
	 */
	function column_default( $item, $column_name ) {
		return ! empty( $item->$column_name ) ? $item->$column_name : '&mdash;';
	}

	/**
	 * Render the current balance column
	 *
	 * @param array $item Contains all the data for the checkbox column
	 *
	 * @return string Displays current balance
	 * @since 1.0.0
	 */
	function column_current_balance( $item ) {
		return $item->get_current_balance('view');
	}

	/**
	 * Render the current balance column
	 *
	 * @param EAccounting_Account $item Contains all the data for the checkbox column
	 *
	 * @return string Displays current balance
	 * @since 1.0.0
	 */
	function column_opening_balance( $item ) {
		return ! empty( $item->get_opening_balance() ) ? $item->get_opening_balance() : '&mdash;';
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

		if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'bulk-accounts' ) ) {
			return;
		}

		$ids = isset( $_GET['account'] ) ? $_GET['account'] : false;

		if ( ! is_array( $ids ) ) {
			$ids = array( $ids );
		}


		foreach ( $ids as $id ) {
			if ( 'delete' === $this->current_action() ) {
				eaccounting_delete_account( $id );
			}
			if ( 'activate' === $this->current_action() ) {
				eaccounting_insert_account( [ 'id' => $id, 'status' => '1' ] );
			}
			if ( 'deactivate' === $this->current_action() ) {
				eaccounting_insert_account( [ 'id' => $id, 'status' => '0' ] );
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

		$this->active_count   = eaccounting_get_accounts( array_merge( $args, array( 'status' => 'active' ) ), true );
		$this->inactive_count = eaccounting_get_accounts( array_merge( $args, array( 'status' => 'inactive' ) ), true );
		$this->total_count    = eaccounting_get_accounts( array_merge( $args, array( 'status' => '' ) ), true );

		$results = eaccounting_get_accounts( $args );

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

		$this->items = array_map( function ( $item ) {
			return new EAccounting_Account( $item );
		}, $data );

		$this->set_pagination_args( array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page ),
			)
		);
	}

}
