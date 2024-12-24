<?php

namespace EverAccounting\Admin\ListTables;

use EverAccounting\Models\Transfer;

defined( 'ABSPATH' ) || exit;

/**
 * Class TransfersTable.
 *
 * @since 1.0.0
 * @package EverAccounting\Admin\ListTables
 */
class Transfers extends ListTable {
	/**
	 * Constructor.
	 *
	 * @param array $args An associative array of arguments.
	 *
	 * @see WP_List_Table::__construct() for more information on default arguments.
	 * @since 1.0.0
	 */
	public function __construct( $args = array() ) {
		parent::__construct(
			wp_parse_args(
				$args,
				array(
					'singular' => 'transfer',
					'plural'   => 'transfers',
					'screen'   => get_current_screen(),
					'args'     => array(),
				)
			)
		);

		$this->base_url = admin_url( 'admin.php?page=eac-banking&tab=transfers' );
	}

	/**
	 * Prepares the list for display.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function prepare_items() {
		$this->process_actions();
		$this->_column_headers = array( $this->get_columns(), get_hidden_columns( $this->screen ), $this->get_sortable_columns() );
		$per_page              = $this->get_items_per_page( 'eac_transfers_per_page', 20 );
		$paged                 = $this->get_pagenum();
		$search                = $this->get_request_search();
		$order_by              = $this->get_request_orderby();
		$order                 = $this->get_request_order();
		$date                  = filter_input( INPUT_GET, 'm', FILTER_VALIDATE_INT );
		$args                  = array(
			'limit'   => $per_page,
			'page'    => $paged,
			'search'  => $search,
			'orderby' => $order_by,
			'order'   => $order,
		);

		if ( ! empty( $date ) ) {
			$month                          = (int) substr( $date, 4, 2 );
			$year                           = (int) substr( $date, 0, 4 );
			$start                          = wp_date( 'Y-m-d H:i:s', mktime( 0, 0, 0, $month, 1, $year ) );
			$end                            = wp_date( 'Y-m-d H:i:s', mktime( 23, 59, 59, $month + 1, 0, $year ) );
			$args['transfer_date__between'] = array( $start, $end );
		}

		/**
		 * Filter the query arguments for the list table.
		 *
		 * @param array $args An associative array of arguments.
		 *
		 * @since 1.0.0
		 */
		$args = apply_filters( 'eac_transfers_table_query_args', $args );

		$this->items = Transfer::results( $args );
		$total       = Transfer::count( $args );
		$this->set_pagination_args(
			array(
				'total_items' => $total,
				'per_page'    => $per_page,
			)
		);
	}

	/**
	 * handle bulk delete action.
	 *
	 * @param array $ids List of item IDs.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	protected function bulk_delete( $ids ) {
		$performed = 0;
		foreach ( $ids as $id ) {
			if ( EAC()->transfers->delete( $id ) ) {
				++$performed;
			}
		}
		if ( ! empty( $performed ) ) {
			// translators: %s: number of items deleted.
			EAC()->flash->success( sprintf( __( '%s transfers(s) deleted successfully.', 'wp-ever-accounting' ), number_format_i18n( $performed ) ) );
		}
	}

	/**
	 * Outputs 'no items' message.
	 *
	 * @since 1.0.0
	 */
	public function no_items() {
		esc_html_e( 'No transfers found.', 'wp-ever-accounting' );
	}

	/**
	 * Outputs the controls to allow user roles to be changed in bulk.
	 *
	 * @param string $which Whether invoked above ("top") or below the table ("bottom").
	 *
	 * @since 1.0.0
	 * @return void
	 */
	protected function extra_tablenav( $which ) {
		global $wpdb;
		static $has_items;
		if ( ! isset( $has_items ) ) {
			$has_items = $this->has_items();
		}
		echo '<div class="alignleft actions">';
		if ( 'top' === $which ) {
			$months = $wpdb->get_results(
				"SELECT DISTINCT YEAR( transfer_date ) AS year, MONTH( transfer_date ) AS month
					FROM {$wpdb->prefix}ea_transfers
					WHERE transfer_date IS NOT NULL
					ORDER BY transfer_date DESC",
			);

			$this->date_filter( $months );
			submit_button( __( 'Filter', 'wp-ever-accounting' ), '', 'filter_action', false );
		}
		echo '</div>';
	}

	/**
	 * Retrieves an associative array of bulk actions available on this table.
	 *
	 * @since 1.0.0
	 * @return array Array of bulk action labels keyed by their action.
	 */
	protected function get_bulk_actions() {
		$actions = array(
			'delete' => __( 'Delete', 'wp-ever-accounting' ),
		);

		return $actions;
	}

	/**
	 * Gets a list of columns for the list table.
	 *
	 * @since 1.0.0
	 * @return string[] Array of column titles keyed by their column name.
	 */
	public function get_columns() {
		return array(
			'cb'              => '<input type="checkbox" />',
			'transfer_date'   => __( 'Date', 'wp-ever-accounting' ),
			'from_account_id' => __( 'From Account', 'wp-ever-accounting' ),
			'to_account_id'   => __( 'To Account', 'wp-ever-accounting' ),
			'reference'       => __( 'Reference', 'wp-ever-accounting' ),
			'amount'          => __( 'Amount', 'wp-ever-accounting' ),
		);
	}

	/**
	 * Gets a list of sortable columns for the list table.
	 *
	 * @since 1.0.0
	 * @return array Array of sortable columns.
	 */
	protected function get_sortable_columns() {
		return array(
			'transfer_date'   => array( 'transfer_date', false ),
			'amount'          => array( 'amount', false ),
			'from_account_id' => array( 'from_account_id', false ),
			'to_account_id'   => array( 'to_account_id', false ),
			'reference'       => array( 'reference', false ),
		);
	}

	/**
	 * Define primary column.
	 *
	 * @since 1.0.2
	 * @return string
	 */
	public function get_primary_column_name() {
		return 'transfer_date';
	}

	/**
	 * Renders the checkbox column.
	 *
	 * @param Transfer $item The current object.
	 *
	 * @since  1.0.0
	 * @return string Displays a checkbox.
	 */
	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="id[]" value="%d"/>', esc_attr( $item->id ) );
	}

	/**
	 * Renders the date column.
	 *
	 * @param Transfer $item The current object.
	 *
	 * @since  1.0.0
	 * @return string Displays the date.
	 */
	public function column_transfer_date( $item ) {
		return sprintf(
			'<a class="row-title" href="%s">%s</a>',
			esc_url( $item->get_edit_url() ),
			esc_html( $item->transfer_date ? wp_date( 'Y-m-d', strtotime( $item->transfer_date ) ) : '&mdash;' )
		);
	}

	/**
	 * Renders from account column.
	 *
	 * @param Transfer $item The current object.
	 *
	 * @since  1.0.0
	 * @return string Displays from account.
	 */
	public function column_from_account_id( $item ) {
		if ( $item->expense && $item->expense->account ) {
			return sprintf(
				'<a href="%s">%s</a>',
				esc_url( $item->expense->account->get_view_url() ),
				esc_html( $item->expense->account->name )
			);
		}

		return '&mdash;';
	}

	/**
	 * Renders to account column.
	 *
	 * @param Transfer $item The current object.
	 *
	 * @since  1.0.0
	 * @return string Displays to account.
	 */
	public function column_to_account_id( $item ) {
		if ( $item->payment && $item->payment->account ) {
			return sprintf(
				'<a href="%s">%s</a>',
				esc_url( $item->payment->account->get_view_url() ),
				esc_html( $item->payment->account->name )
			);
		}

		return '&mdash;';
	}

	/**
	 * Renders the reference column.
	 *
	 * @param Transfer $item The current object.
	 *
	 * @since  1.0.0
	 * @return string Displays the reference.
	 */
	public function column_reference( $item ) {
		return $item->reference ? esc_html( $item->reference ) : '&mdash;';
	}

	/**
	 * Renders the amount column.
	 *
	 * @param Transfer $item The current object.
	 *
	 * @since  1.0.0
	 * @return string Displays the amount.
	 */
	public function column_amount( $item ) {
		return esc_html( $item->formatted_amount );
	}

	/**
	 * Generates and displays row actions links.
	 *
	 * @param Transfer $item The comment object.
	 * @param string   $column_name Current column name.
	 * @param string   $primary Primary column name.
	 *
	 * @since 1.0.0
	 * @return string Row actions output.
	 */
	protected function handle_row_actions( $item, $column_name, $primary ) {
		if ( $primary !== $column_name ) {
			return null;
		}
		$actions = array(
			'id'     => sprintf( '#%d', esc_attr( $item->id ) ),
			'edit'   => sprintf(
				'<a href="%s">%s</a>',
				esc_url( $item->get_edit_url() ),
				__( 'Edit', 'wp-ever-accounting' )
			),
			'delete' => sprintf(
				'<a href="%s" class="del del_confirm">%s</a>',
				esc_url(
					wp_nonce_url(
						add_query_arg(
							array(
								'action' => 'delete',
								'id'     => $item->id,
							),
							$this->base_url
						),
						'bulk-' . $this->_args['plural']
					)
				),
				__( 'Delete', 'wp-ever-accounting' )
			),
		);

		if ( ! current_user_can( 'eac_delete_transfer' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown -- Reason: This is a custom capability.
			unset( $actions['delete'] );
		}

		if ( ! current_user_can( 'eac_edit_transfer' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown -- Reason: This is a custom capability.
			unset( $actions['edit'] );
		}

		return $this->row_actions( $actions );
	}
}
