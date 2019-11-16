<?php
defined( 'ABSPATH' ) || exit();


class EAccounting_Contacts_List_Table extends EAccounting_List_Table {
	/**
	 * EAccounting_Products_List_Table constructor.
	 */
	public function __construct() {
		parent::__construct( array(
			'singular' => 'contact',
			'plural'   => 'contacts',
			'ajax'     => false,
		) );
		$this->base_url = admin_url( 'admin.php?page=eaccounting-contacts' );
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
			'name'   => __( 'Name', 'wp-eaccounting' ),
			'email'  => __( 'Email', 'wp-eaccounting' ),
			'phone'  => __( 'Phone', 'wp-eaccounting' ),
//			'unpaid' => __( 'Unpaid', 'wp-eaccounting' ),
//			'types'  => __( 'Types', 'wp-eaccounting' ),
			'status' => __( 'Status', 'wp-eaccounting' ),
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
			'name'   => array( 'first_name', false ),
			'status' => array( 'status', false ),
			'email'  => array( 'email', false ),
			'phone'  => array( 'phone', false )
		);
	}

	/**
	 * Render the Name Column
	 *
	 * @param EAccounting_Contact $item Contains all the data of the discount code
	 *
	 * @return string Data shown in the Name column
	 * @since 1.0.0
	 */
	function column_name( $item ) {
		$contact_url    = add_query_arg( array( 'contact' => $item->get_id() ), $this->base_url );
		$edit_url       = wp_nonce_url( add_query_arg( [ 'eaccounting-action' => 'edit_contact' ], $contact_url ), 'eaccounting_contacts_nonce' );
		$activate_url   = wp_nonce_url( add_query_arg( [ 'eaccounting-action' => 'activate_contact' ], $contact_url ), 'eaccounting_contacts_nonce' );
		$deactivate_url = wp_nonce_url( add_query_arg( [ 'eaccounting-action' => 'deactivate_contact' ], $contact_url ), 'eaccounting_contacts_nonce' );
		$delete_url     = wp_nonce_url( add_query_arg( [ 'eaccounting-action' => 'delete_contact' ], $contact_url ), 'eaccounting_contacts_nonce' );

		$row_actions['edit'] = sprintf( '<a href="%1$s">%2$s</a>', $edit_url, __( 'Edit', 'wp-eaccounting' ) );

		if ( strtolower( $item->get_status() ) == 'active' ) {
			$row_actions['deactivate'] = sprintf( '<a href="%1$s">%2$s</a>', $deactivate_url, __( 'Deactivate', 'wp-eaccounting' ) );
		} elseif ( strtolower( $item->get_status() ) == 'inactive' ) {
			$row_actions['activate'] = sprintf( '<a href="%1$s">%2$s</a>', $activate_url, __( 'Activate', 'wp-eaccounting' ) );
		}
		$row_actions['delete'] = sprintf( '<a href="%1$s">%2$s</a>', $delete_url, __( 'Delete', 'wp-eaccounting' ) );

		$row_actions = apply_filters( 'eaccounting_contacts_row_actions', $row_actions, $item );

		return sprintf( '<strong><a href="%1$s">%2$s</a></strong>', $edit_url, stripslashes( $item->get_name() ) ) . $this->row_actions( $row_actions );
	}

	/**
	 * @param object $item
	 * @param string $column_name
	 *
	 * @return string;
	 * @since 1.0.0
	 */
	function column_default( $item, $column_name ) {
		return $item->$column_name;
	}

	/**
	 * since 1.0.0
	 * @param $item EAccounting_Contact
	 *
	 * @return bool
	 */
	function column_email($item) {
		return empty($item->get_email())? '&mdash;': $item->get_email();
	}

	/**
	 * since 1.0.0
	 * @param $item EAccounting_Contact
	 *
	 * @return bool
	 */
	function column_phone($item) {
		return empty($item->get_phone())? '&mdash;': $item->get_phone();
	}


	/**
	 * @return string
	 * @since 1.0.0
	 */
	function column_unpaid() {
		return '&mdash;';
	}

	/**
	 * @return string
	 * @since 1.0.0
	 */
	function column_types() {
		return '&mdash;';
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

		if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'bulk-contacts' ) ) {
			return;
		}

		$ids = isset( $_GET['contact'] ) ? $_GET['contact'] : false;

		if ( ! is_array( $ids ) ) {
			$ids = array( $ids );
		}


		foreach ( $ids as $id ) {
			if ( 'delete' === $this->current_action() ) {
				eaccounting_delete_contact( $id );
			}
			if ( 'activate' === $this->current_action() ) {
				eaccounting_insert_contact( [ 'id' => $id, 'status' => '1' ] );
			}
			if ( 'deactivate' === $this->current_action() ) {
				eaccounting_insert_contact( [ 'id' => $id, 'status' => '0' ] );
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

		$this->active_count   = eaccounting_get_contacts( array_merge( $args, array( 'status' => 'active' ) ), true );
		$this->inactive_count = eaccounting_get_contacts( array_merge( $args, array( 'status' => 'inactive' ) ), true );
		$this->total_count    = eaccounting_get_contacts( array_merge( $args, array( 'status' => '' ) ), true );

		$results = eaccounting_get_contacts( $args );

		return $results;
	}

	/**
	 * since 1.0.0
	 *
	 * @param $item
	 *
	 * @return EAccounting_Contact
	 */
	protected function map_to_object( $item ) {
		return new EAccounting_Contact( $item );
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

		$this->items = array_map( array( $this, 'map_to_object' ), $data );

		$this->set_pagination_args( array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page ),
			)
		);
	}

}
