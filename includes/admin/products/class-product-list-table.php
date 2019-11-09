<?php
defined( 'ABSPATH' ) || exit();


class EAccounting_Products_List_Table extends EAccounting_List_Table {
	/**
	 * EAccounting_Products_List_Table constructor.
	 */
	public function __construct() {
		parent::__construct( array(
			'singular' => 'product',
			'plural'   => 'products',
			'ajax'     => false,
		) );
		$this->base_url = admin_url( 'admin.php?page=eaccounting-products' );
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
			'cb'             => '<input type="checkbox" />',
			'name'           => __( 'Name', 'wp-eaccounting' ),
			'sale_price'     => __( 'Sale Price', 'wp-eaccounting' ),
			'purchase_price' => __( 'Purchase Price', 'wp-eaccounting' ),
			'quantity'       => __( 'Quantity', 'wp-eaccounting' ),
			'category'       => __( 'Category', 'wp-eaccounting' ),
			'status'         => __( 'Status', 'wp-eaccounting' ),
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
			'quantity'        => array( 'quantity', false ),
			'sale_price'      => array( 'sale_price', false ),
			'current_balance' => array( 'current_balance', false ),
			'purchase_price'  => array( 'purchase_price', false ),
			'status'          => array( 'status', false ),
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
		$product_url    = add_query_arg( array( 'product' => $item->id ), $this->base_url );
		$edit_url       = wp_nonce_url( add_query_arg( [ 'eaccounting-action' => 'edit_product' ], $product_url ), 'eaccounting_products_nonce' );
		$activate_url   = wp_nonce_url( add_query_arg( [ 'eaccounting-action' => 'activate_product' ], $product_url ), 'eaccounting_products_nonce' );
		$deactivate_url = wp_nonce_url( add_query_arg( [ 'eaccounting-action' => 'deactivate_product' ], $product_url ), 'eaccounting_products_nonce' );
		$delete_url     = wp_nonce_url( add_query_arg( [ 'eaccounting-action' => 'delete_product' ], $product_url ), 'eaccounting_products_nonce' );

		$row_actions['edit'] = sprintf( '<a href="%1$s">%2$s</a>', $edit_url, __( 'Edit', 'wp-eaccounting' ) );

		if ( strtolower( $item->status ) == 'active' ) {
			$row_actions['deactivate'] = sprintf( '<a href="%1$s">%2$s</a>', $deactivate_url, __( 'Deactivate', 'wp-eaccounting' ) );
		} elseif ( strtolower( $item->status ) == 'inactive' ) {
			$row_actions['activate'] = sprintf( '<a href="%1$s">%2$s</a>', $activate_url, __( 'Activate', 'wp-eaccounting' ) );
		}
		$row_actions['delete'] = sprintf( '<a href="%1$s">%2$s</a>', $delete_url, __( 'Delete', 'wp-eaccounting' ) );

		$row_actions = apply_filters( 'eaccounting_products_row_actions', $row_actions, $item );

		return sprintf( '<strong><a href="%1$s">%2$s</a></strong>', $edit_url, stripslashes( $item->name ) ) . $this->row_actions( $row_actions );
	}

	/**
	 * Render the current sale price column
	 *
	 * @param array $item Contains all the data for the checkbox column
	 *
	 * @return string Displays current balance
	 * @since 1.0.0
	 */
	function column_sale_price( $item ) {
		return eaccounting_price( $item->sale_price );
	}

	/**
	 * Render the current purchase price column
	 *
	 * @param array $item Contains all the data for the checkbox column
	 *
	 * @return string Displays current balance
	 * @since 1.0.0
	 */
	function column_purchase_price( $item ) {
		return eaccounting_price( $item->purchase_price );
	}

	/**
	 * Render the current quantity
	 *
	 * @param array $item Contains all the data for the checkbox column
	 *
	 * @return string Displays current balance
	 * @since 1.0.0
	 */
	function column_quantity( $item ) {
		return intval( $item->quantity );
	}

	/**
	 * Render the category
	 *
	 * @param array $item Contains all the data for the checkbox column
	 *
	 * @return string Displays current balance
	 * @since 1.0.0
	 */
	function column_category( $item ) {
		$category = ! empty( $item->category_id ) ? eaccounting_get_category( $item->category_id ) : new StdClass();

		return isset( $category->name ) ? wp_unslash( $category->name ) : '&mdash;';
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

		if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'bulk-products' ) ) {
			return;
		}

		$ids = isset( $_GET['product'] ) ? $_GET['product'] : false;

		if ( ! is_array( $ids ) ) {
			$ids = array( $ids );
		}


		foreach ( $ids as $id ) {
			if ( 'delete' === $this->current_action() ) {
				eaccounting_delete_product( $id );
			}
			if ( 'activate' === $this->current_action() ) {
				eaccounting_insert_product( [ 'id' => $id, 'status' => '1' ] );
			}
			if ( 'deactivate' === $this->current_action() ) {
				eaccounting_insert_product( [ 'id' => $id, 'status' => '0' ] );
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

		$orderby = isset( $_GET['orderby'] ) ? $_GET['orderby'] : 'ID';
		$order   = isset( $_GET['order'] ) ? $_GET['order'] : 'DESC';
		$status  = isset( $_GET['status'] ) ? $_GET['status'] : '';
		$search  = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : null;

		$args = array(
			'per_page' => $per_page,
			'paged'    => isset( $_GET['paged'] ) ? $_GET['paged'] : 1,
			'orderby'  => $orderby,
			'order'    => $order,
			'status'   => $status,
			'search'   => $search
		);

		if ( array_key_exists( $orderby, $this->get_sortable_columns() ) && 'name' != $orderby ) {
			$args['orderby'] = $orderby;
		}

		$this->active_count   = eaccounting_get_products( array_merge( $args, array( 'status' => 'active' ) ), true );
		$this->inactive_count = eaccounting_get_products( array_merge( $args, array( 'status' => 'inactive' ) ), true );
		$this->total_count    = eaccounting_get_products( array_merge( $args, array( 'status' => '' ) ), true );

		$results = eaccounting_get_products( $args );

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
