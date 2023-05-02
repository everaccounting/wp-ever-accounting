<?php

namespace EverAccounting\Admin\ListTables;

use EverAccounting\Models\Account;

defined( 'ABSPATH' ) || exit;

/**
 * Class Accounts
 *
 * @since 1.0.2
 * @package EverAccounting\Admin\ListTables
 */
class Accounts extends ListTable {
	/**
	 * Get things started
	 *
	 * @param array $args Optional. Arbitrary display and query arguments to pass through the list table. Default empty array.
	 *
	 * @see WP_List_Table::__construct()
	 * @since  1.0.2
	 */
	public function __construct( $args = array() ) {
		$args         = (array) wp_parse_args(
			$args,
			array(
				'singular' => 'account',
				'plural'   => 'accounts',
			)
		);
		$this->screen = get_current_screen();
		parent::__construct( $args );
	}

	/**
	 * Retrieve all the data for the table.
	 *
	 * @since 1.0.2
	 * @return void
	 */
	public function prepare_items() {
		$columns               = $this->get_columns();
		$sortable              = $this->get_sortable_columns();
		$hidden                = $this->get_hidden_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$args = array(
			'limit'   => $this->get_per_page(),
			'offset'  => $this->get_offset(),
			'status'  => $this->get_status(),
			'search'  => $this->get_search(),
			'order'   => $this->get_order( 'ASC' ),
			'orderby' => $this->get_orderby( 'status' ),
		);

		$this->items       = eac_get_accounts( $args );
		$this->total_count = eac_get_accounts( $args, true );

		$this->set_pagination_args(
			array(
				'total_items' => $this->total_count,
				'per_page'    => $this->get_per_page(),
				'total_pages' => ceil( $this->total_count / $this->get_per_page() ),
			)
		);
	}

	/**
	 * No items found text.
	 *
	 * @since 1.0.2
	 * @return void
	 */
	public function no_items() {
		esc_html_e( 'No accounts found.', 'wp-ever-accounting' );
	}

	/**
	 * Adds the order and product filters to the licenses list.
	 *
	 * @param string $which The location of the extra table nav markup: 'top' or 'bottom'.
	 *
	 * @since 1.0.2
	 */
	protected function extra_tablenav( $which ) {
		if ( 'top' !== $which ) {
			return;
		}

		echo '<div class="alignleft actions">';
		$this->status_filter();
		submit_button( __( 'Filter', 'wp-ever-accounting' ), '', 'filter', false );
		echo '</div>';

		$filter = eac_filter_input( INPUT_GET, 'filter' );
		if ( ! empty( $filter ) || ! empty( $this->get_search() ) ) {
			echo sprintf(
				'<a href="%s" class="button">%s</a>',
				esc_url( $this->get_current_url() ),
				esc_html__( 'Reset', 'wp-ever-accounting' )
			);
		}
	}

	/**
	 * Process bulk action.
	 *
	 * @param string $doaction Action name.
	 *
	 * @since 1.0.2
	 */
	public function process_bulk_action( $doaction ) {
		if ( ! empty( $doaction ) ) {
			$id  = eac_get_request_var( 'account_id', 'get', 0 );
			$ids = eac_get_request_var( 'account_ids', 'get', array() );
			if ( ! empty( $id ) ) {
				$ids      = wp_parse_id_list( $id );
				$doaction = ( - 1 !== $_REQUEST['action'] ) ? $_REQUEST['action'] : $_REQUEST['action2']; // phpcs:ignore
			} elseif ( ! empty( $ids ) ) {
				$ids = array_map( 'absint', $ids );
			} elseif ( wp_get_referer() ) {
				wp_safe_redirect( wp_get_referer() );
				exit;
			}

			foreach ( $ids as $id ) { // Check the permissions on each.
				switch ( $doaction ) {
					case 'delete':
						eac_delete_account( $id );
						break;
					case 'enable':
						eac_insert_account(
							array(
								'id'     => $id,
								'status' => 'active',
							)
						);
						break;
					case 'disable':
						eac_insert_account(
							array(
								'id'     => $id,
								'status' => 'inactive',
							)
						);
						break;
				}
			}

			// Based on the action add notice.
			switch ( $doaction ) {
				case 'delete':
					$notice = __( 'Account(s) deleted successfully.', 'wp-ever-accounting' );
					break;
				case 'enable':
					$notice = __( 'Account(s) enabled successfully.', 'wp-ever-accounting' );
					break;
				case 'disable':
					$notice = __( 'Account(s) disabled successfully.', 'wp-ever-accounting' );
					break;
			}
			eac_add_notice( $notice, 'success' );

			wp_safe_redirect( wp_get_referer() );
			exit();
		}

		parent::process_bulk_actions( $doaction );
	}

	/**
	 * Define which columns to show on this screen.
	 *
	 * @since 1.0.2
	 * @return array
	 */
	public function get_columns() {
		return array(
			'cb'        => '<input type="checkbox" />',
			'name'      => __( 'Name', 'wp-ever-accounting' ),
			'balance'   => __( 'Balance', 'wp-ever-accounting' ),
			'number'    => __( 'Number', 'wp-ever-accounting' ),
			'bank_name' => __( 'Bank Name', 'wp-ever-accounting' ),
			'status'    => __( 'Status', 'wp-ever-accounting' ),
		);
	}

	/**
	 * Define sortable columns.
	 *
	 * @since 1.0.2
	 * @return array
	 */
	protected function get_sortable_columns() {
		return array(
			'name'      => array( 'name', false ),
			'number'    => array( 'number', false ),
			'bank_name' => array( 'bank_name', false ),
			'balance'   => array( 'balance', false ),
			'status'    => array( 'status', false ),
		);
	}

	/**
	 * Get bulk actions
	 *
	 * since 1.0.0
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		return array(
			'delete'  => __( 'Delete', 'wp-ever-accounting' ),
			'enable'  => __( 'Enable', 'wp-ever-accounting' ),
			'disable' => __( 'Disable', 'wp-ever-accounting' ),
		);
	}

	/**
	 * Define primary column.
	 *
	 * @since 1.0.2
	 * @return string
	 */
	public function get_primary_column_name() {
		return 'name';
	}

	/**
	 * Renders the checkbox column in the accounts list table.
	 *
	 * @param Account $item The current account object.
	 *
	 * @since  1.0.2
	 * @return string Displays a checkbox.
	 */
	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="account_ids[]" value="%d"/>', esc_attr( $item->get_id() ) );
	}

	/**
	 * Renders the name column in the accounts list table.
	 *
	 * @param Account $item The current account object.
	 *
	 * @since  1.0.2
	 * @return string Displays a checkbox.
	 */
	public function column_name( $item ) {
		$args        = array( 'account_id' => $item->get_id() );
		$edit_url    = $this->get_current_url( array_merge( $args, array( 'action' => 'edit' ) ) );
		$enable_url  = $this->get_current_url( array_merge( $args, array( 'action' => 'enable' ) ) );
		$disable_url = $this->get_current_url( array_merge( $args, array( 'action' => 'disable' ) ) );
		$delete_url  = $this->get_current_url( array_merge( $args, array( 'action' => 'delete' ) ) );
		$actions     = array(
			'id'      => sprintf( '<strong>#%d</strong>', esc_attr( $item->get_id() ) ),
			'edit'    => sprintf( '<a href="%s">%s</a>', esc_url( $edit_url ), __( 'Edit', 'wp-ever-accounting' ) ),
			'enable'  => sprintf( '<a href="%s">%s</a>', esc_url( wp_nonce_url( $enable_url, 'bulk-accounts' ) ), __( 'Enable', 'wp-ever-accounting' ) ),
			'disable' => sprintf( '<a href="%s">%s</a>', esc_url( wp_nonce_url( $disable_url, 'bulk-accounts' ) ), __( 'Disable', 'wp-ever-accounting' ) ),
			'delete'  => sprintf( '<a href="%s" class="del">%s</a>', esc_url( wp_nonce_url( $delete_url, 'bulk-accounts' ) ), __( 'Delete', 'wp-ever-accounting' ) ),
		);
		if ( 'active' === $item->get_status() ) {
			unset( $actions['enable'] );
		} else {
			unset( $actions['disable'] );
		}

		return sprintf( '<a href="%s">%s</a> %s', esc_url( $edit_url ), esc_html( $item->get_name() ), $this->row_actions( $actions ) );
	}

	/**
	 * This function renders most of the columns in the list table.
	 *
	 * @param Account $item The current account object.
	 * @param string  $column_name The name of the column.
	 *
	 * @since 1.0.2
	 * @return string The column value.
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'balance':
				$value = esc_html( $item->get_formatted_balance() );
				break;
			case 'status':
				$value = esc_html( $item->get_status( 'view' ) );
				$value = sprintf( '<span class="eac-status-label %s">%s</span>', esc_attr( $item->get_status() ), $value );
				break;
			default:
				$value = parent::column_default( $item, $column_name );
				break;
		}

		return $value;
	}
}
