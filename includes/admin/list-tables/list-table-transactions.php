<?php
/**
 * Transactions list table
 *
 * Admin transactions list table it shows all kind of transactions
 * related to  the company
 *
 *
 * @since       1.0.2
 * @subpackage  EverAccounting\Admin\ListTables
 * @package     EverAccounting
 */

namespace EverAccounting\Admin\ListTables;

defined( 'ABSPATH' ) || exit();

use EverAccounting\Abstracts\List_Table;
use EverAccounting\Query_Transaction;
use EverAccounting\Transaction;

/**
 * Class List_Table_Transactions
 *
 * @since 1.0.2
 */
class List_Table_Transactions extends List_Table {
	/**
	 * Type of the table should be use plural name.
	 *
	 * This will be used for filtering methods.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	protected $list_table_type = 'transactions';

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
	 * Total number of income item found
	 *
	 * @since 1.0.2
	 * @var int
	 */
	protected $income_count;

	/**
	 * Total number of expense item found
	 *
	 * @since 1.0.2
	 * @var int
	 */
	protected $expense_count;

	/**
	 * Total number of others item found
	 *
	 * @since 1.0.2
	 * @var int
	 */
	protected $others_count;

	/**
	 * Get things started
	 *
	 * @since  1.0
	 *
	 * @see    WP_List_Table::__construct()
	 *
	 * @param array $args Optional. Arbitrary display and query arguments to pass through the list table. Default empty array.
	 *
	 */
	public function __construct( $args = array() ) {
		$args = (array) wp_parse_args( $args, array(
			'singular' => 'transaction',
			'plural'   => 'transactions',
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
			'date'        => __( 'Date', 'wp-ever-accounting' ),
			'amount'      => __( 'Amount', 'wp-ever-accounting' ),
			'account_id'  => __( 'Account Name', 'wp-ever-accounting' ),
			'type'        => __( 'Type', 'wp-ever-accounting' ),
			'category_id' => __( 'Category', 'wp-ever-accounting' ),
			'reference'   => __( 'Reference', 'wp-ever-accounting' ),
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
			'type'        => array( 'type', false ),
			'category_id' => array( 'category_id', false ),
			'reference'   => array( 'reference', false ),
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
	 * Renders the "Date" column in the accounts list table.
	 *
	 *
	 * @since  1.0.2
	 *
	 * @param Transaction $transaction The current account object.
	 *
	 * @return string Data shown in the Name column.
	 */
	function column_date( $transaction ) {
		$date = $transaction->get_paid_at()->date_i18n();

		$value = sprintf( '<a href="%1$s">%2$s</a>',
			esc_url( eaccounting_admin_url( [ 'action' => 'edit', 'account_id' => $transaction->get_id() ] ) ),
			$date
		);

		return apply_filters( 'eaccounting_transaction_table_date', $value, $transaction );
	}

	/**
	 * Renders the "amount" column in the accounts list table.
	 *
	 * @since  1.0.2
	 *
	 * @param Transaction $transaction The current account object.
	 *
	 * @return string Data shown in the amount column.
	 */
	function column_amount( $transaction ) {
		return apply_filters( 'eaccounting_transaction_table_amount', $transaction->get_formatted_amount(), $transaction );
	}

	/**
	 * Renders the "account" column in the accounts list table.
	 *
	 * @since  1.0.2
	 *
	 * @param Transaction $transaction The current account object.
	 *
	 * @return string Data shown in the account column.
	 */
	function column_account_id( $transaction ) {
		$account = eaccounting_get_account( $transaction->get_account_id( 'edit' ) );
		$name    = $account ? $account->get_name() : __( '(Deleted Account)', 'wp-ever-account' );

		return apply_filters( 'eaccounting_transaction_table_account', esc_html( $name ), $transaction );
	}

	/**
	 * Renders the "type" column in the accounts list table.
	 *
	 * @since  1.0.2
	 *
	 * @param Transaction $transaction The current account object.
	 *
	 * @return string Data shown in the type column.
	 */
	function column_type( $transaction ) {
		$type  = $transaction->get_type();
		$types = eaccounting_get_transaction_types();
		$type  = array_key_exists( $type, $types ) ? $types[ $type ] : ucfirst( $type );

		return apply_filters( 'eaccounting_transaction_table_type', esc_html( $type ), $transaction );
	}

	/**
	 * Renders the "Category" column in the accounts list table.
	 *
	 * @since  1.0.2
	 *
	 * @param Transaction $transaction The current account object.
	 *
	 * @return string Data shown in the Category column.
	 */
	function column_category_id( $transaction ) {
		$account = eaccounting_get_category( $transaction->get_category_id( 'edit' ) );
		$name    = $account ? $account->get_name() : __( '(Deleted Category)', 'wp-ever-account' );

		return apply_filters( 'eaccounting_transaction_table_category', esc_html( $name ), $transaction );
	}

	/**
	 * Renders the "Reference" column in the accounts list table.
	 *
	 * @since  1.0.2
	 *
	 * @param Transaction $transaction The current account object.
	 *
	 * @return string Data shown in the Reference column.
	 */
	function column_reference( $transaction ) {
		$reference = empty( $transaction->get_reference() ) ? '&mdash;' : $transaction->get_reference();

		return apply_filters( 'eaccounting_transaction_table_reference', esc_html( $reference ), $transaction );
	}


	/**
	 * Renders the message to be displayed when there are no items.
	 *
	 * @since  1.0.2
	 * @return void
	 */
	function no_items() {
		_e( 'No transactions found.', 'wp-ever-accounting' );
	}

	/**
	 * Retrieve the view types
	 *
	 * @access public
	 * @since  1.0.2
	 * @return array $views All the views available
	 */
	public function get_views() {
		$base          = eaccounting_admin_url();
		$current       = isset( $_GET['type'] ) ? $_GET['type'] : '';
		$total_count   = '&nbsp;<span class="count">(' . $this->total_count . ')</span>';
		$income_count  = '&nbsp;<span class="count">(' . $this->income_count . ')</span>';
		$expense_count = '&nbsp;<span class="count">(' . $this->expense_count . ')</span>';
		$others_count  = '&nbsp;<span class="count">(' . $this->others_count . ')</span>';

		$views = array(
			'all'     => sprintf( '<a href="%s"%s>%s</a>', esc_url( remove_query_arg( 'type', $base ) ), $current === 'all' || $current == '' ? ' class="current"' : '', __( 'All', 'wp-ever-accounting' ) . $total_count ),
			'income'  => sprintf( '<a href="%s"%s>%s</a>', esc_url( add_query_arg( 'type', 'income', $base ) ), $current === 'income' ? ' class="current"' : '', __( 'Income', 'wp-ever-accounting' ) . $income_count ),
			'expense' => sprintf( '<a href="%s"%s>%s</a>', esc_url( add_query_arg( 'type', 'expense', $base ) ), $current === 'expense' ? ' class="current"' : '', __( 'Expense', 'wp-ever-accounting' ) . $expense_count ),
			//'other'   => sprintf( '<a href="%s"%s>%s</a>', esc_url( add_query_arg( 'type', 'other', $base ) ), $current === 'other' ? ' class="current"' : '', __( 'Other', 'wp-ever-accounting' ) . $others_count ),
		);

		return $views;
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
			$account_id = isset( $_GET['account_id'] ) ? absint( $_GET['account_id'] ) : '';
			$start_date = isset( $_GET['start_date'] ) ? eaccounting_clean( $_GET['start_date'] ) : '';
			$end_date   = isset( $_GET['end_date'] ) ? eaccounting_clean( $_GET['end_date'] ) : '';
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
		if ( isset( $_GET['_wpnonce'] ) ) {
			wp_safe_redirect( remove_query_arg( [
				'_wpnonce',
				'_wp_http_referer',
				'action',
				'action2',
				'doaction',
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
		$page       = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
		$account_id = isset( $_GET['account_id'] ) ? absint( $_GET['account_id'] ) : '';
		$type       = isset( $_GET['type'] ) ? $_GET['type'] : '';
		$search     = isset( $_GET['s'] ) ? $_GET['s'] : '';
		$order      = isset( $_GET['order'] ) ? $_GET['order'] : 'DESC';
		$orderby    = isset( $_GET['orderby'] ) ? $_GET['orderby'] : 'id';
		$start_date = ! empty( $_GET['start_date'] ) ? eaccounting_clean( $_GET['start_date'] ) : '';
		$end_date   = ! empty( $_GET['end_date'] ) ? eaccounting_clean( $_GET['end_date'] ) : '';
		$per_page   = $this->get_per_page();
		$args       = wp_parse_args( $this->query_args, array(
			'number'     => $per_page,
			'offset'     => $per_page * ( $page - 1 ),
			'search'     => $search,
			'account_id' => $account_id,
			'orderby'    => eaccounting_clean( $orderby ),
			'order'      => eaccounting_clean( $order )
		) );


		$args = apply_filters( 'eaccounting_transactions_table_get_transactions', $args, $this );

		$base_query = Query_Transaction::init()
		                               ->where( $args )
		                               ->search( $search )
		                               ->notTransfer()
		                               ->whereDateBetween( 'paid_at', $start_date, $end_date )
		                               ->order_by( $orderby, $order )
		                               ->page( $page, $per_page );
		$this->items = $base_query->copy()->where( [ 'type' => $type ] )->get( OBJECT, 'eaccounting_get_transaction' );

		$this->income_count = $base_query->copy()->where( array_merge( $this->query_args, array(
			'type'   => 'income',
			'search' => $search
		) ) )->count();

		$this->expense_count = $base_query->copy()->where( array_merge( $this->query_args, array(
			'type'   => 'expense',
			'search' => $search
		) ) )->count();


		$this->total_count = $this->income_count + $this->expense_count + $this->others_count;

		$type = isset( $_GET['type'] ) ? $_GET['type'] : 'any';

		switch ( $type ) {
			case 'income':
				$total_items = $this->income_count;
				break;
			case 'expense':
				$total_items = $this->expense_count;
				break;
			case 'other':
				$total_items = $this->others_count;
				break;
			case 'any':
			default:
				$total_items = $this->total_count;
				break;
		}

		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil( $total_items / $per_page )
		) );
	}

}