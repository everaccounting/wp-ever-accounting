<?php

namespace EverAccounting\Admin\ListTables;

use EverAccounting\Models\Transaction;

defined( 'ABSPATH' ) || exit;

/**
 * Class Transactions
 *
 * @since 1.0.2
 * @package EverAccounting\Admin\ListTables
 */
class Transactions extends ListTable {
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
				'singular' => 'transaction',
				'plural'   => 'transactions',
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
			'limit'       => $this->get_per_page(),
			'offset'      => $this->get_offset(),
			'status'      => $this->get_status(),
			'search'      => $this->get_search(),
			'order'       => $this->get_order( 'DESC' ),
			'orderby'     => $this->get_orderby( 'payment_date' ),
			'account_id'  => eac_get_input_var( 'account_id' ),
			'category_id' => eac_get_input_var( 'category_id' ),
			'contact_id'  => eac_get_input_var( 'contact_id' ),
		);

		$this->items       = eac_get_transactions( $args );
		$this->total_count = eac_get_transactions( $args, true );

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
		esc_html_e( 'No transactions found.', 'wp-ever-accounting' );
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
			$id  = eac_get_input_var( 'transaction_id' );
			$ids = eac_get_input_var( 'transaction_ids' );
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
					$notice = __( 'Transaction(s) deleted successfully.', 'wp-ever-accounting' );
					break;
			}
			eac_add_notice( $notice, 'success' );

			wp_safe_redirect( admin_url( 'admin.php?page=ea-banking&tab=transactions' ) );
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
			'cb'       => '<input type="checkbox" />',
			'date'     => __( 'Date', 'wp-ever-accounting' ),
			'category' => __( 'Category', 'wp-ever-accounting' ),
			'account'  => __( 'Account', 'wp-ever-accounting' ),
			'contact'  => __( 'Contact', 'wp-ever-accounting' ),
			'amount'   => __( 'Amount', 'wp-ever-accounting' ),
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
			'date'      => array( 'payment_date', false ),
			'contact'   => array( 'contact_id', false ),
			'amount'    => array( 'amount', false ),
			'type'      => array( 'type', false ),
			'account'   => array( 'account_id', false ),
			'category'  => array( 'category_id', false ),
			'reference' => array( 'reference', false ),
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
	 * Renders the checkbox column in the transactions list table.
	 *
	 * @param Transaction $item The current account object.
	 *
	 * @since  1.0.2
	 * @return string Displays a checkbox.
	 */
	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="transaction_ids[]" value="%d"/>', esc_attr( $item->get_id() ) );
	}

	/**
	 * Renders the name column in the transactions list table.
	 *
	 * @param Transaction $item The current account object.
	 *
	 * @since  1.0.2
	 * @return string Displays a checkbox.
	 */
	public function column_date( $item ) {
		$type       = $item->get_type();
		$page       = 'payment' === $type ? 'eac-sales' : 'eac-purchases';
		$tab        = 'payment' === $type ? 'payments' : 'expenses';
		$args       = array(
			$type . '_id' => $item->get_id(),
			'page'        => $page,
			'tab'         => $tab,
		);
		$edit_url   = $this->get_current_url( array_merge( $args, array( 'action' => 'edit' ) ) );
		$delete_url = $this->get_current_url(
			array_merge(
				$args,
				array(
					'action' => 'delete',
					'page'   => 'ea-banking',
					'tab'    => 'transactions',
				)
			)
		);
		$actions    = array(
			'id'     => sprintf( '<strong>#%d</strong>', esc_attr( $item->get_id() ) ),
			'edit'   => sprintf( '<a href="%s">%s</a>', esc_url( $edit_url ), __( 'Edit', 'wp-ever-accounting' ) ),
			'delete' => sprintf( '<a href="%s" class="del">%s</a>', esc_url( wp_nonce_url( $delete_url, 'bulk-transactions' ) ), __( 'Delete', 'wp-ever-accounting' ) ),
		);

		return sprintf( '<a href="%s">%s</a> %s', esc_url( $edit_url ), esc_html( $item->get_payment_date() ), $this->row_actions( $actions ) );
	}

	/**
	 * This function renders most of the columns in the list table.
	 *
	 * @param Transaction $item The current account object.
	 * @param string      $column_name The name of the column.
	 *
	 * @since 1.0.2
	 * @return string The column value.
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'date':
				$value = $item->get_payment_date();
				break;
			case 'amount':
				// Based on the type show minus or plus.
				$type = $item->get_type();
				if ( 'payment' === $type ) {
					$value = $item->get_formatted_amount();
				} else {
					$value = '-&nbsp;' . $item->get_formatted_amount();
				}
				break;
			case 'account':
				$account_id = $item->get_account_id();
				$account    = eac_get_account( $account_id );
				$link       = add_query_arg(
					array(
						'account_id' => $account_id,
						'filter'     => 'yes',
					)
				);
				$value      = $account ? sprintf( '<a href="%s">%s</a>', esc_url( $link ), esc_html( $account->get_name() ) ) : '&mdash;';
				break;
			case 'category':
				$category_id = $item->get_category_id();
				$category    = eac_get_category( $category_id );
				$link        = add_query_arg(
					array(
						'category_id' => $category_id,
						'filter'      => 'yes',
					)
				);
				$value       = $category ? sprintf( '<a href="%s">%s</a>', esc_url( $link ), esc_html( $category->get_name() ) ) : '&mdash;';
				break;
			case 'contact':
				$contact_id = $item->get_contact_id();
				$contact    = eac_get_customer( $contact_id );
				$link       = add_query_arg(
					array(
						'contact_id' => $contact_id,
						'filter'     => 'yes',
					)
				);
				$value      = $contact ? sprintf( '<a href="%s">%s</a>', esc_url( $link ), esc_html( $contact->get_name() ) ) : '&mdash;';
				break;
			default:
				$value = parent::column_default( $item, $column_name );
				break;
		}

		return $value;
	}
}