<?php

namespace EverAccounting\Admin\ListTables;

use EverAccounting\Models\Transfer;

defined( 'ABSPATH' ) || exit;

/**
 * Class Transfers
 *
 * @since 1.0.2
 * @package EverAccounting\Admin\ListTables
 */
class Transfers extends ListTable {
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
				'singular' => 'transfer',
				'plural'   => 'transfers',
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
			'limit'      => $this->get_per_page(),
			'offset'     => $this->get_offset(),
			'status'     => $this->get_status(),
			'search'     => $this->get_search(),
			'order'      => $this->get_order( 'desc' ),
			'orderby'    => $this->get_orderby( 'created_at' ),
			'account_id' => eac_get_input_var( 'account_id' ),
		);

		$this->items       = eac_get_transfers( $args );
		$this->total_count = eac_get_transfers( $args, true );

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
		esc_html_e( 'No transfers found.', 'wp-ever-accounting' );
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
		$filter = eac_get_input_var( 'filter' );
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
		if ( ! empty( $doaction ) && check_admin_referer( 'bulk-' . $this->_args['plural'] ) ) {
			$id  = eac_get_input_var( 'transfer_id' );
			$ids = eac_get_input_var( 'transfer_ids' );
			if ( ! empty( $id ) ) {
				$ids      = wp_parse_id_list( $id );
				$doaction = ( - 1 !== $_REQUEST['action'] ) ? $_REQUEST['action'] : $_REQUEST['action2']; // phpcs:ignore
			} elseif ( ! empty( $ids ) ) {
				$ids = array_map( 'absint', $ids );
			} elseif ( wp_get_referer() ) {
				wp_safe_redirect( wp_get_referer() );
				exit;
			}

			switch ( $doaction ) {
				case 'delete':
					$changed = 0;

					foreach ( $ids as $id ) {
						if ( eac_delete_transfer( $id ) ) {
							$changed ++;
						}
					}

					eac_add_notice( sprintf( __( '%s transfer(s) deleted successfully.', 'wp-ever-accounting' ), number_format_i18n( $changed ) ), 'success' );

					break;

			}

			wp_safe_redirect( admin_url( 'admin.php?page=eac-banking&tab=transfers' ) );
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
			'cb'           => '<input type="checkbox" />',
			'date'         => __( 'Date', 'wp-ever-accounting' ),
			'amount'       => __( 'Amount', 'wp-ever-accounting' ),
			'from_account' => __( 'From Account', 'wp-ever-accounting' ),
			'to_account'   => __( 'To Account', 'wp-ever-accounting' ),
			'reference'    => __( 'Reference', 'wp-ever-accounting' ),
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
			'amount'       => array( 'amount', false ),
			'date'         => array( 'payment_date', true ),
			'reference'    => array( 'reference', false ),
			'from_account' => array( 'from_account_id', false ),
			'to_account'   => array( 'to_account_id', false ),
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
			'delete' => __( 'Delete', 'wp-ever-accounting' ),
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
	 * Renders the checkbox column in the accounts list table.
	 *
	 * @param Transfer $item The current account object.
	 *
	 * @since  1.0.2
	 * @return string Displays a checkbox.
	 */
	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="transfer_ids[]" value="%d"/>', esc_attr( $item->get_id() ) );
	}

	/**
	 * Renders the name column in the accounts list table.
	 *
	 * @param Transfer $item The current account object.
	 *
	 * @since  1.0.2
	 * @return string Displays a checkbox.
	 */
	public function column_date( $item ) {
		$args       = array( 'transfer_id' => $item->get_id() );
		$edit_url   = $this->get_current_url( array_merge( $args, array( 'action' => 'edit' ) ) );
		$delete_url = $this->get_current_url( array_merge( $args, array( 'action' => 'delete' ) ) );
		$actions    = array(
			'id'     => sprintf( '<strong>#%d</strong>', esc_attr( $item->get_id() ) ),
			'edit'   => sprintf( '<a href="%s">%s</a>', esc_url( $edit_url ), __( 'Edit', 'wp-ever-accounting' ) ),
			'delete' => sprintf( '<a href="%s" class="del">%s</a>', esc_url( wp_nonce_url( $delete_url, 'bulk-transfers' ) ), __( 'Delete', 'wp-ever-accounting' ) ),
		);
		return sprintf( '<a href="%s">%s</a> %s', esc_url( $edit_url ), esc_html( $item->get_date() ), $this->row_actions( $actions ) );
	}

	/**
	 * This function renders most of the columns in the list table.
	 *
	 * @param Transfer $item The current account object.
	 * @param string   $column_name The name of the column.
	 *
	 * @since 1.0.2
	 * @return string The column value.
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'amount':
				$value = $item->get_formatted_amount();
				break;
			case 'from_account':
				$account_id = $item->get_from_account_id();
				$account    = eac_get_account( $account_id );
				$link       = add_query_arg(
					array(
						'from_account_id' => $account_id,
						'filter'          => 'yes',
					)
				);
				$value      = $account ? sprintf( '<a href="%s">%s</a>', esc_url( $link ), esc_html( $account->get_name() ) ) : '&mdash;';
				break;
			case 'to_account':
				$account_id = $item->get_to_account_id();
				$account    = eac_get_account( $account_id );
				$link       = add_query_arg(
					array(
						'to_account_id' => $account_id,
						'filter'        => 'yes',
					)
				);
				$value      = $account ? sprintf( '<a href="%s">%s</a>', esc_url( $link ), esc_html( $account->get_name() ) ) : '&mdash;';
				break;
			default:
				$value = parent::column_default( $item, $column_name );
				break;
		}

		return $value;
	}
}
