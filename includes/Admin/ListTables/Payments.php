<?php

namespace EverAccounting\Admin\ListTables;

use EverAccounting\Models\Payment;

defined( 'ABSPATH' ) || exit;

/**
 * Class Payments.
 *
 * @since 1.0.0
 * @package EverAccounting
 */
class Payments extends ListTable {
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
					'singular' => 'payment',
					'plural'   => 'payments',
					'screen'   => get_current_screen(),
					'args'     => array(),
				)
			)
		);
		add_filter( 'set-screen-option', array( __CLASS__, 'set_screen_option' ), 10, 3 );
		$this->base_url = admin_url( 'admin.php?page=eac-sales&tab=payments' );
	}

	/**
	 * Set screen option.
	 *
	 * @param mixed  $status Status.
	 * @param string $option Option.
	 * @param mixed  $value Value.
	 *
	 * @since 3.0.0
	 * @return mixed
	 */
	public static function set_screen_option( $status, $option, $value ) {
		if ( 'eac_payments_per_page' === $option ) {
			return $value;
		}

		return $status;
	}

	/**
	 * Prepares the list for display.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function prepare_items() {
		$this->process_actions();
		$per_page    = $this->get_items_per_page( 'eac_payments_per_page', 20 );
		$paged       = $this->get_pagenum();
		$search      = $this->get_request_search();
		$order_by    = $this->get_request_orderby();
		$order       = $this->get_request_order();
		$account_id  = filter_input( INPUT_GET, 'account_id', FILTER_VALIDATE_INT );
		$category_id = filter_input( INPUT_GET, 'category_id', FILTER_VALIDATE_INT );
		$contact_id  = filter_input( INPUT_GET, 'customer_id', FILTER_VALIDATE_INT );
		$args        = array(
			'limit'       => $per_page,
			'page'        => $paged,
			'search'      => $search,
			'orderby'     => $order_by,
			'order'       => $order,
			'status'      => $this->get_request_status(),
			'account_id'  => $account_id,
			'category_id' => $category_id,
			'contact_id'  => $contact_id,
		);
		/**
		 * Filter the query arguments for the list table.
		 *
		 * @param array $args An associative array of arguments.
		 *
		 * @since 1.0.0
		 */
		$args = apply_filters( 'eac_payments_table_query_args', $args );

		$args['no_found_rows'] = false;
		$this->items           = Payment::results( $args );
		$total                 = Payment::count( $args );

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
			if ( EAC()->payments->delete( $id ) ) {
				++$performed;
			}
		}
		if ( ! empty( $performed ) ) {
			// translators: %s: number of items deleted.
			EAC()->flash->success( sprintf( __( '%s payment(s) deleted successfully.', 'wp-ever-accounting' ), number_format_i18n( $performed ) ) );
		}
	}

	/**
	 * Outputs 'no results' message.
	 *
	 * @since 1.0.0
	 */
	public function no_items() {
		esc_html_e( 'No payments found.', 'wp-ever-accounting' );
	}

	/**
	 * Returns an associative array listing all the views that can be used
	 * with this table.
	 *
	 * @since 1.0.0
	 *
	 * @return string[] An array of HTML links keyed by their view.
	 */
	protected function get_views() {
		$current      = $this->get_request_status( 'all' );
		$status_links = array();
		$statuses     = EAC()->payments->get_statuses();
		$statuses     = array_merge( array( 'all' => __( 'All', 'wp-ever-accounting' ) ), $statuses );

		foreach ( $statuses as $status => $label ) {
			$link  = 'all' === $status ? $this->base_url : add_query_arg( 'status', $status, $this->base_url );
			$args  = 'all' === $status ? array() : array( 'status' => $status );
			$count = Payment::count( $args );
			$label = sprintf( '%s <span class="count">(%s)</span>', esc_html( $label ), number_format_i18n( $count ) );

			$status_links[ $status ] = array(
				'url'     => $link,
				'label'   => $label,
				'current' => $current === $status,
			);
		}

		return $this->get_views_links( $status_links );
	}

	/**
	 * Retrieves an associative array of bulk actions available on this table.
	 *
	 * @since 1.0.0
	 *
	 * @return array Array of bulk action labels keyed by their action.
	 */
	protected function get_bulk_actions() {
		$actions = array(
			'mark_paid'      => __( 'Mark as Paid', 'wp-ever-accounting' ),
			'mark_pending'   => __( 'Mark as Pending', 'wp-ever-accounting' ),
			'mark_refunded'  => __( 'Mark as Refunded', 'wp-ever-accounting' ),
			'mark_cancelled' => __( 'Mark as Cancelled', 'wp-ever-accounting' ),
			'delete'         => __( 'Delete', 'wp-ever-accounting' ),
		);

		return $actions;
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
		static $has_items;
		if ( ! isset( $has_items ) ) {
			$has_items = $this->has_items();
		}
		echo '<div class="alignleft actions">';
		if ( 'top' === $which ) {
			$this->date_filter();
			$this->year_filter();
			$this->account_filter( 'active' );
			$this->category_filter( 'payment' );
			submit_button( __( 'Filter', 'wp-ever-accounting' ), '', 'filter_action', false );
		}
		echo '</div>';
	}

	/**
	 * Gets a list of columns for the list table.
	 *
	 * @since 1.0.0
	 *
	 * @return string[] Array of column titles keyed by their column name.
	 */
	public function get_columns() {
		return array(
			'cb'       => '<input type="checkbox" />',
			'number'   => __( 'Payment #', 'wp-ever-accounting' ),
			'date'     => __( 'Date', 'wp-ever-accounting' ),
			'account'  => __( 'Account', 'wp-ever-accounting' ),
			'customer' => __( 'Customer', 'wp-ever-accounting' ),
			'category' => __( 'Category', 'wp-ever-accounting' ),
			'status'   => __( 'Status', 'wp-ever-accounting' ),
			'amount'   => __( 'Amount', 'wp-ever-accounting' ),
		);
	}

	/**
	 * Gets a list of sortable columns for the list table.
	 *
	 * @since 1.0.0
	 *
	 * @return array Array of sortable columns.
	 */
	protected function get_sortable_columns() {
		return array(
			'date'     => array( 'date', false ),
			'number'   => array( 'number', false ),
			'account'  => array( 'account', false ),
			'category' => array( 'category', false ),
			'customer' => array( 'customer', false ),
			'status'   => array( 'status', false ),
			'amount'   => array( 'amount', false ),
		);
	}

	/**
	 * Define primary column.
	 *
	 * @since 1.0.2
	 * @return string
	 */
	public function get_primary_column_name() {
		return 'number';
	}

	/**
	 * Renders the checkbox column.
	 *
	 * @param Payment $item The current object.
	 *
	 * @since  1.0.0
	 * @return string Displays a checkbox.
	 */
	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="id[]" value="%d"/>', esc_attr( $item->id ) );
	}

	/**
	 * Renders the name column.
	 *
	 * @param Payment $item The current object.
	 *
	 * @since  1.0.0
	 * @return string Displays the name.
	 */
	public function column_date( $item ) {
		return sprintf(
			'<a href="%s">%s</a>',
			esc_url(
				add_query_arg(
					array(
						'date' => $item->date,
					)
				)
			),
			wp_kses_post( $item->date )
		);
	}

	/**
	 * Renders the number column.
	 *
	 * @param Payment $item The current object.
	 *
	 * @since  1.0.0
	 * @return string Displays the number.
	 */
	public function column_number( $item ) {
		return sprintf(
			'<a href="%s">%s</a>',
			esc_url(
				add_query_arg(
					array(
						'action' => 'view',
						'id'     => $item->id,
					),
					$this->base_url
				)
			),
			wp_kses_post( $item->number )
		);
	}

	/**
	 * Renders the amount column.
	 *
	 * @param Payment $item The current object.
	 *
	 * @since  1.0.0
	 * @return string Displays the amount.
	 */
	public function column_amount( $item ) {
		return esc_html( $item->formatted_amount );
	}

	/**
	 * Renders the account column.
	 *
	 * @param Payment $item The current object.
	 *
	 * @since  1.0.0
	 * @return string Displays the account.
	 */
	public function column_account( $item ) {
		$account  = $item->account ? sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( 'account_id', $item->account->id, $this->base_url ) ), wp_kses_post( $item->account->name ) ) : '&mdash;';
		$metadata = $item->account && $item->account->type ? ucfirst( $item->account->type ) : '';

		return sprintf( '%s%s', $account, $this->column_metadata( $metadata ) );
	}

	/**
	 * Renders the category column.
	 *
	 * @param Payment $item The current object.
	 *
	 * @since  1.0.0
	 * @return string Displays the category.
	 */
	public function column_category( $item ) {
		$category = $item->category ? sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( 'category_id', $item->category->id, $this->base_url ) ), wp_kses_post( $item->category->name ) ) : '&mdash;';
		$metadata = '';

		return sprintf( '%s%s', $category, $this->column_metadata( $metadata ) );
	}

	/**
	 * Renders the customer column.
	 *
	 * @param Payment $item The current object.
	 *
	 * @since  1.0.0
	 * @return string Displays the customer.
	 */
	public function column_customer( $item ) {
		$customer = $item->customer ? sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( 'customer_id', $item->customer->id, $this->base_url ) ), wp_kses_post( $item->customer->name ) ) : '&mdash;';
		$metadata = $item->customer && $item->customer->company ? $item->customer->company : '';

		return sprintf( '%s%s', $customer, $this->column_metadata( $metadata ) );
	}

	/**
	 * Renders the status column.
	 *
	 * @param Payment $item The current object.
	 *
	 * @since 1.0.0
	 * @return string Displays the status.
	 */
	public function column_status( $item ) {
		$statuses = EAC()->payments->get_statuses();
		$status   = isset( $item->status ) ? $item->status : '';
		$label    = isset( $statuses[ $status ] ) ? $statuses[ $status ] : '';

		return sprintf( '<span class="eac-status is--%1$s">%2$s</span>', esc_attr( $status ), esc_html( $label ) );
	}

	/**
	 * Generates and displays row actions links.
	 *
	 * @param Payment $item The comment object.
	 * @param string  $column_name Current column name.
	 * @param string  $primary Primary column name.
	 *
	 * @since 1.0.0
	 * @return string Row actions output.
	 */
	protected function handle_row_actions( $item, $column_name, $primary ) {
		if ( $primary !== $column_name ) {
			return null;
		}
		$actions = array(
			'edit'   => sprintf(
				'<a href="%s">%s</a>',
				esc_url(
					add_query_arg(
						array(
							'action' => 'edit',
							'id'     => $item->id,
						),
						$this->base_url
					)
				),
				__( 'Edit', 'wp-ever-accounting' )
			),
			'delete' => sprintf(
				'<a href="%s" class="del">%s</a>',
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

		return $this->row_actions( $actions );
	}
}