<?php
defined( 'ABSPATH' ) || exit();

// Load WP_List_Table if not loaded
if ( ! class_exists( 'EAccounting_Admin_List_Table' ) ) {
	require_once dirname( __FILE__ ) . '/abstract-class-ea-admin-list-table.php';
}

class EAccounting_Currencies_List_Table extends EAccounting_Admin_List_Table {

	/**
	 * @since 1.0.2
	 * EAccounting_Currencies_List_Table constructor.
	 */
	public function __construct() {
		parent::__construct( 'currency', 'currencies', admin_url( 'admin.php?page=eaccounting-misc&tab=currencies' ) );
	}

	/**
	 * Retrieve the table columns
	 *
	 * @return array $columns Array of all the list table columns
	 * @since 1.0.0
	 */
	public function get_columns() {
		$columns = array(
			'cb'       => '<input type="checkbox" />',
			'currency' => __( 'Currency', 'wp-ever-accounting' ),
			'rate'     => __( 'Rate', 'wp-ever-accounting' ),
			'status'   => __( 'Status', 'wp-ever-accounting' ),
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
			'currency' => array( 'currency', false ),
			'rate'     => array( 'rate', false ),
			'status'   => array( 'status', false ),
		);
	}


	/**
	 * Get Name of the default primary column.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_primary_column_name() {
		return 'currency';
	}

	/**
	 * Render the primary column
	 *
	 * @param array $item Contains all the data of the discount code
	 *
	 * @return string Data shown in the Name column
	 * @since 1.0.0
	 */
	function column_currency( $item ) {
		$main_url       = add_query_arg( array( 'currency' => $item->id ), $this->base_url );
		$edit_url       = wp_nonce_url( add_query_arg( [ 'eaccounting-action' => 'edit_currency' ], $main_url ), 'eaccounting_currencies_nonce' );
		$activate_url   = wp_nonce_url( add_query_arg( [ 'eaccounting-action' => 'activate_currency' ], $main_url ), 'eaccounting_currencies_nonce' );
		$deactivate_url = wp_nonce_url( add_query_arg( [ 'eaccounting-action' => 'deactivate_currency' ], $main_url ), 'eaccounting_currencies_nonce' );
		$delete_url     = wp_nonce_url( add_query_arg( [ 'eaccounting-action' => 'delete_currency' ], $main_url ), 'eaccounting_currencies_nonce' );

		$row_actions['edit'] = sprintf( '<a href="%1$s">%2$s</a>', $edit_url, __( 'Edit', 'wp-ever-accounting' ) );

		if ( strtolower( $item->status ) == 'active' ) {
			$row_actions['deactivate'] = sprintf( '<a href="%1$s">%2$s</a>', $deactivate_url, __( 'Deactivate', 'wp-ever-accounting' ) );
		} elseif ( strtolower( $item->status ) == 'inactive' ) {
			$row_actions['activate'] = sprintf( '<a href="%1$s">%2$s</a>', $activate_url, __( 'Activate', 'wp-ever-accounting' ) );
		}
		$row_actions['delete'] = sprintf( '<a href="%1$s">%2$s</a>', $delete_url, __( 'Delete', 'wp-ever-accounting' ) );

		$row_actions = apply_filters( 'eaccounting_currencies_row_actions', $row_actions, $item );

		return sprintf( '<strong><a href="%1$s">%2$s</a></strong>', $edit_url, stripslashes( $item->currency ) ) . $this->row_actions( $row_actions );
	}

	/**
	 * Prepares the list of items for displaying.
	 *
	 * @since 1.0.0
	 *
	 * @uses $this->_column_headers
	 * @uses $this->items
	 * @uses $this->get_columns()
	 * @uses $this->get_sortable_columns()
	 * @uses $this->get_pagenum()
	 * @uses $this->set_pagination_args()
	 */
	public function prepare_items() {
		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$per_page = $this->per_page;
		$orderby  = isset( $_GET['orderby'] ) ? $_GET['orderby'] : 'created_at';
		$order    = isset( $_GET['order'] ) ? $_GET['order'] : 'DESC';
		$status   = isset( $_GET['status'] ) ? $_GET['status'] : '';
		$search   = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : null;

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

		$this->active_count   = eaccounting_get_currencies( array_merge( $args, array( 'status' => 'active' ) ), true );
		$this->inactive_count = eaccounting_get_currencies( array_merge( $args, array( 'status' => 'inactive' ) ), true );
		$this->total_count    = eaccounting_get_currencies( array_merge( $args, array( 'status' => '' ) ), true );

		$this->items = eaccounting_get_currencies( $args );

		$this->set_pagination_args( array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page ),
			)
		);
	}

}
