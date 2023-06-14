<?php

namespace EverAccounting\Admin\ListTables;

use EverAccounting\Models\Invoice;

defined( 'ABSPATH' ) || exit;

/**
 * Class Invoices
 *
 * @since 1.0.2
 * @package EverInvoiceing\Admin\ListTables
 */
class Invoices extends ListTable {
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
				'singular' => 'invoice',
				'plural'   => 'invoices',
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
			'orderby'     => $this->get_orderby( 'issue_date' ),
			'category_id' => eac_get_input_var( 'category_id' ),
			'contact_id'  => eac_get_input_var( 'customer_id' ),
		);

		$this->items       = eac_get_invoices( $args );
		$this->total_count = eac_get_invoices( $args, true );

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
		esc_html_e( 'No invoices found.', 'wp-ever-accounting' );
	}


	/**
	 * Adds the order and item filters to the licenses list.
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
			$id  = eac_get_input_var( 'invoice_id' );
			$ids = eac_get_input_var( 'invoice_ids', array() );
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
						eac_delete_invoice( $id );
						break;
				}
			}

			// Based on the action add notice.
			switch ( $doaction ) {
				case 'delete':
					$notice = __( 'Invoice(s) deleted successfully.', 'wp-ever-accounting' );
					break;
			}
			eac_add_notice( $notice, 'success' );

			wp_safe_redirect( admin_url( 'admin.php?page=eac-sales&tab=invoices' ) );
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
			'date'      => __( 'Date', 'wp-ever-accounting' ),
			'amount'    => __( 'Amount', 'wp-ever-accounting' ),
			'category'  => __( 'Category', 'wp-ever-accounting' ),
			'customer'  => __( 'Customer', 'wp-ever-accounting' ),
			'reference' => __( 'Reference', 'wp-ever-accounting' ),
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
			'date'      => array( 'invoice_date', true ),
			'amount'    => array( 'amount', false ),
			'category'  => array( 'category_id', false ),
			'customer'  => array( 'contact_id', false ),
			'reference' => array( 'reference', false ),
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
	 * @param Invoice $item The current account object.
	 *
	 * @since  1.0.2
	 * @return string Displays a checkbox.
	 */
	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="invoice_ids[]" value="%d"/>', esc_attr( $item->get_id() ) );
	}

	/**
	 * Renders the name column in the accounts list table.
	 *
	 * @param Invoice $item The current account object.
	 *
	 * @since  1.0.2
	 * @return string Displays a checkbox.
	 */
	public function column_date( $item ) {
		$args       = array( 'invoice_id' => $item->get_id() );
		$edit_url   = $this->get_current_url( array_merge( $args, array( 'action' => 'edit' ) ) );
		$delete_url = $this->get_current_url( array_merge( $args, array( 'action' => 'delete' ) ) );
		$actions    = array(
			'id'     => sprintf( '<strong>#%d</strong>', esc_attr( $item->get_id() ) ),
			'edit'   => sprintf( '<a href="%s">%s</a>', esc_url( $edit_url ), __( 'Edit', 'wp-ever-accounting' ) ),
			'delete' => sprintf( '<a href="%s" class="del">%s</a>', esc_url( wp_nonce_url( $delete_url, 'bulk-accounts' ) ), __( 'Delete', 'wp-ever-accounting' ) ),
		);
		$date       = $item->get_issued_at();
		return sprintf( '<a href="%s">%s</a> %s', esc_url( $edit_url ), esc_html( $date ), $this->row_actions( $actions ) );
	}

	/**
	 * This function renders most of the columns in the list table.
	 *
	 * @param Invoice $item The current account object.
	 * @param string  $column_name The name of the column.
	 *
	 * @since 1.0.2
	 * @return string The column value.
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'amount':
				$value = $item->get_formatted_total();
				$value = ! empty( $value ) ? $value : '&mdash;';
				break;
			case 'category123':
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
			case 'customer':
				$contact_id = $item->get_contact_id();
				$contact    = eac_get_customer( $contact_id );
				$link       = add_query_arg(
					array(
						'customer_id' => $contact_id,
						'filter'      => 'yes',
					)
				);
				$value      = $contact ? sprintf( '<a href="%s">%s</a>', esc_url( $link ), esc_html( $contact->get_name() ) ) : '&mdash;';
				break;
			case 'status':
				$value = esc_html( $item->get_status( 'view' ) );
				$value = $value ? sprintf( '<span class="eac-status-label %s">%s</span>', esc_attr( $item->get_status() ), $value ) : '&mdash;';
				break;
			default:
				$value = parent::column_default( $item, $column_name );
				break;
		}

		return $value;
	}
}
