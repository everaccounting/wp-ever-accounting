<?php
/**
 * Revenues list table
 *
 * Admin revenues list table, show all the incoming transactions.
 *
 *
 * @since       1.0.2
 * @subpackage  EverAccounting\Admin\ListTables
 * @package     EverAccounting
 */

namespace EverAccounting\Admin\ListTables;

use \EverAccounting\Abstracts\List_Table;
use EverAccounting\Query_Transaction;
use EverAccounting\Transaction;

defined( 'ABSPATH' ) || exit();

/**
 * Class List_Table_Revenues
 *
 * @since 1.0.2
 */
class List_Table_Revenues extends List_Table {
	/**
	 * Type of the table should be use plural name.
	 *
	 * This will be used for filtering methods.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	protected $list_table_type = 'revenues';

	/**
	 * Default number of items to show per page
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $per_page = 20;

	/**
	 * Total number of item found
	 *
	 * @since 1.0.2
	 * @var int
	 */
	public $total_count;

	/**
	 * Get things started
	 *
	 * @since  1.0.2
	 *
	 * @see    WP_List_Table::__construct()
	 *
	 * @param array $args Optional. Arbitrary display and query arguments to pass through the list table. Default empty array.
	 *
	 */
	public function __construct( $args = array() ) {
		$args = (array) wp_parse_args( $args, array(
			'singular' => 'revenues',
			'plural'   => 'revenues',
		) );

		parent::__construct( $args );
	}

	/**
	 * Check if there is contents in the database.
	 *
	 * @since 1.0.2
	 * @return bool
	 */
	public function is_empty() {
		return parent::is_empty(); // TODO: Change the autogenerated stub
	}

	/**
	 * Render blank state.
	 *
	 * @since 1.0.2
	 * @return void
	 */
	protected function render_blank_state() {
		return parent::render_blank_state(); // TODO: Change the autogenerated stub
	}

	/**
	 * Define which columns to show on this screen.
	 *
	 * @since 1.0.2
	 * @return array
	 */
	public function define_columns() {
		return array(
			'cb'          => '<input type="checkbox" />',
			'date'        => __( 'Date', 'wp-ever-accounting' ),
			'amount'      => __( 'Amount', 'wp-ever-accounting' ),
			'account_id'  => __( 'Account Name', 'wp-ever-accounting' ),
			'category_id' => __( 'Category', 'wp-ever-accounting' ),
			'reference'   => __( 'Reference', 'wp-ever-accounting' ),
			'actions'     => __( 'Actions', 'wp-ever-accounting' ),
		);
	}

	/**
	 * Define sortable columns.
	 *
	 * @since 1.0.2
	 * @return array
	 */
	protected function define_sortable_columns() {
		return array(
			'date'        => array( 'date', false ),
			'amount'      => array( 'amount', false ),
			'account_id'  => array( 'account_id', false ),
			'category_id' => array( 'category_id', false ),
			'reference'   => array( 'reference', false ),
		);
	}

	/**
	 * Define bulk actions
	 *
	 * @since 1.0.2
	 * @return array
	 */
	public function define_bulk_actions() {
		return array(
			'delete'     => __( 'Delete', 'wp-ever-accounting' ),
		);
	}

	/**
	 * Define primary column.
	 *
	 * @since 1.0.2
	 * @return string
	 */
	public function get_primary_column() {
		return 'date';
	}


	/**
	 * Renders the checkbox column in the revenues list table.
	 *
	 *
	 * @since  1.0.2
	 *
	 * @param Transaction $revenue The current object.
	 *
	 * @return string Displays a checkbox.
	 */
	function column_cb( $revenue ) {
		return sprintf( '<input type="checkbox" name="revenue_id[]" value="%d"/>', $revenue->get_id() );
	}

	/**
	 * Renders the "Date" column in the accounts list table.
	 *
	 *
	 * @since  1.0.2
	 *
	 * @param Transaction $revenue The current transation object.
	 *
	 * @return string Data shown in the Name column.
	 */
	function column_date( $revenue ) {
		$date = $revenue->get_paid_at()->date_i18n();

		$value = sprintf( '<a href="%1$s">%2$s</a>',
			esc_url( eaccounting_admin_url( [ 'action' => 'edit', 'revenue_id' => $revenue->get_id() ] ) ),
			$date
		);

		return apply_filters( 'eaccounting_revenues_table_date', $value, $revenue );
	}

	/**
	 * Renders the "amount" column in the accounts list table.
	 *
	 * @since  1.0.2
	 *
	 * @param Transaction $revenue The current account object.
	 *
	 * @return string Data shown in the amount column.
	 */
	function column_amount( $revenue ) {
		return apply_filters( 'eaccounting_revenues_table_amount', $revenue->get_formatted_amount(), $revenue );
	}

	/**
	 * Renders the "account" column in the accounts list table.
	 *
	 * @since  1.0.2
	 *
	 * @param Transaction $revenue The current account object.
	 *
	 * @return string Data shown in the account column.
	 */
	function column_account_id( $revenue ) {
		$account = eaccounting_get_account( $revenue->get_account_id( 'edit' ) );
		$name    = $account ? $account->get_name() : __( '(Deleted Account)', 'wp-ever-account' );

		return apply_filters( 'eaccounting_revenues_table_account', esc_html( $name ), $revenue );
	}

	/**
	 * Renders the "Category" column in the accounts list table.
	 *
	 * @since  1.0.2
	 *
	 * @param Transaction $revenue The current account object.
	 *
	 * @return string Data shown in the Category column.
	 */
	function column_category_id( $revenue ) {
		$account = eaccounting_get_category( $revenue->get_category_id( 'edit' ) );
		$name    = $account ? $account->get_name() : __( '(Deleted Category)', 'wp-ever-account' );

		return apply_filters( 'eaccounting_revenues_table_category', esc_html( $name ), $revenue );
	}

	/**
	 * Renders the "Reference" column in the revenues list table.
	 *
	 * @since  1.0.2
	 *
	 * @param Transaction $revenue The current account object.
	 *
	 * @return string Data shown in the Reference column.
	 */
	function column_reference( $revenue ) {
		$reference = empty( $revenue->get_reference() ) ? '&mdash;' : $revenue->get_reference();

		return apply_filters( 'eaccounting_revenues_table_reference', esc_html( $reference ), $revenue );
	}


	/**
	 * Renders the message to be displayed when there are no items.
	 *
	 * @since  1.0.2
	 * @return void
	 */
	function no_items() {
		_e( 'No revenues found.', 'wp-ever-accounting' );
	}

	/**
	 * Extra controls to be displayed between bulk actions and pagination.
	 *
	 * @since 1.0.2
	 *
	 * @param string $which
	 */
	protected function extra_tablenav( $which ) {
		if ( 'top' == $which ) {
			$account_id  = isset( $_GET['account_id'] ) ? absint( $_GET['account_id'] ) : '';
			$category_id = isset( $_GET['category_id'] ) ? absint( $_GET['category_id'] ) : '';
			$customer_id = isset( $_GET['customer_id'] ) ? absint( $_GET['customer_id'] ) : '';
			$start_date  = isset( $_GET['start_date'] ) ? eaccounting_clean( $_GET['start_date'] ) : '';
			$end_date    = isset( $_GET['end_date'] ) ? eaccounting_clean( $_GET['end_date'] ) : '';
			echo '<div class="alignleft actions ea-table-filter">';

			eaccounting_input_date_range( array(
				'start_date' => $start_date,
				'end_date'   => $end_date,
			) );

			eaccounting_account_dropdown( [
				'name'    => 'account_id',
				'value'   => $account_id,
				'default' => '',
				'attr'    => array(
					'data-allow-clear' => true
				)
			] );

			eaccounting_category_dropdown( [
				'name'    => 'category_id',
				'value'   => $category_id,
				'default' => '',
				'type'    => 'income',
				'attr'    => array(
					'data-allow-clear' => true
				)
			] );
			eaccounting_contact_dropdown( [
				'name'        => 'customer_id',
				'value'       => $customer_id,
				'default'     => '',
				'placeholder' => __( 'Select Customer', 'wp-ever-accounting' ),
				'type'        => 'customer',
				'attr'        => array(
					'data-allow-clear' => true
				)
			] );

			submit_button( __( 'Filter', 'wp-ever-accounting' ), 'action', false, false );
			echo "\n";

			echo '</div>';
		}
	}


	/**
	 * Process the bulk actions
	 *
	 * @since 1.0.2
	 * @return void
	 */
	public function process_bulk_action() {
		if ( empty( $_REQUEST['_wpnonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'bulk-revenues' ) && ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'revenue-nonce' ) ) {
			return;
		}

		$ids = isset( $_GET['revenue_id'] ) ? $_GET['revenue_id'] : false;

		if ( ! is_array( $ids ) ) {
			$ids = array( $ids );
		}

		$ids = array_map( 'absint', $ids );

		if ( empty( $ids ) ) {
			return;
		}

		$action = $this->current_action();
		foreach ( $ids as $id ) {
			switch ( $action ) {
				case 'export_csv':
					break;
				case 'delete':
					eaccounting_delete_transaction( $id );
					break;
				default:
					do_action( 'eaccounting_revenues_do_bulk_action_' . $this->current_action(), $id );
			}
		}

		if ( isset( $_GET['_wpnonce'] ) ) {
			wp_safe_redirect( remove_query_arg( [
				'revenue_id',
				'action',
				'_wpnonce',
				'_wp_http_referer',
				'action2',
				'paged'
			] ) );
			exit();
		}
	}

	/**
	 * Retrieve all the data for the table.
	 * Setup the final data for the table
	 *
	 * @since 1.0.2
	 * @return void
	 */
	public function prepare_items() {
		$columns               = $this->get_columns();
		$hidden                = $this->get_hidden_columns();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$this->process_bulk_action();

		$page = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;;
		$search  = isset( $_GET['s'] ) ? $_GET['s'] : '';
		$order   = isset( $_GET['order'] ) ? $_GET['order'] : 'DESC';
		$orderby = isset( $_GET['orderby'] ) ? $_GET['orderby'] : 'id';

		$start_date  = ! empty( $_GET['start_date'] ) ? eaccounting_clean( $_GET['start_date'] ) : '';
		$end_date    = ! empty( $_GET['end_date'] ) ? eaccounting_clean( $_GET['end_date'] ) : '';
		$category_id = ! empty( $_GET['category_id'] ) ? absint( $_GET['category_id'] ) : '';
		$account_id  = ! empty( $_GET['account_id'] ) ? absint( $_GET['account_id'] ) : '';
		$customer_id = ! empty( $_GET['customer_id'] ) ? absint( $_GET['customer_id'] ) : '';

		$per_page = $this->get_per_page();

		$args = wp_parse_args( $this->query_args, array(
			'per_page'    => $per_page,
			'page'        => $page,
			'number'      => $per_page,
			'offset'      => $per_page * ( $page - 1 ),
			'search'      => $search,
			'orderby'     => eaccounting_clean( $orderby ),
			'order'       => eaccounting_clean( $order ),
			'type'        => 'income',
			'category_id' => $category_id,
			'account_id'  => $account_id,
			'contact_id'  => $customer_id,
		) );

		$args = apply_filters( 'eaccounting_revenues_table_get_revenues', $args, $this );

		$this->items = Query_Transaction::init()
		                                ->where( $args )
		                                ->notTransfer()
		                                ->whereDateBetween( 'paid_at', $start_date, $end_date )
		                                ->get( OBJECT, 'eaccounting_get_transaction' );

		$this->total_count = Query_Transaction::init()
		                                      ->where( $args )
		                                      ->notTransfer()
		                                      ->whereDateBetween( 'paid_at', $start_date, $end_date )
		                                      ->count();


		$this->set_pagination_args( array(
			'total_items' => $this->total_count,
			'per_page'    => $per_page,
			'total_pages' => ceil( $this->total_count / $per_page )
		) );
	}
}