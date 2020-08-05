<?php
/**
 * Accounts Admin List Table
 *
 * @package     EverAccounting
 * @subpackage  Admin/Banking
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
require_once( EACCOUNTING_ABSPATH . '/includes/abstracts/abstract-ea-admin-list-table.php' );

class EAccounting_Accounts_Table extends EAccounting_Admin_List_Table {
	/**
	 * Default number of items to show per page
	 *
	 * @var string
	 * @since 1.0
	 */
	public $per_page = 30;

	/**
	 * Total number of accounts found
	 *
	 * @var int
	 * @since 1.0
	 */
	public $total_count;

	/**
	 * Number of active affiliates found
	 *
	 * @var string
	 * @since 1.0
	 */
	public $active_count;

	/**
	 *  Number of inactive affiliates found
	 *
	 * @var string
	 * @since 1.0
	 */
	public $inactive_count;

	/**
	 * Get things started
	 *
	 * @param array $args Optional. Arbitrary display and query arguments to pass through the list table. Default empty array.
	 *
	 * @see WP_List_Table::__construct()
	 *
	 * @since  1.0
	 *
	 */
	public function __construct( $args = array() ) {
		$args = (array) wp_parse_args( $args, array(
			'singular' => 'account',
			'plural'   => 'accounts',
		) );

		parent::__construct( $args );

		$this->get_accounts_counts();
	}

	/**
	 * Render blank state. Extend to add content.
	 */
	protected function render_blank_state() {
		echo '<div class="ea-blankstate">';
		echo '<h2 class="ea-blankstate-message">' . esc_html__( 'Accounts is where you keep your money, it could be cash or bank account having different currencies.', 'wp-ever-accounting' ) . '</h2>';
		echo '<a class="ea-blankstate-cta button-primary button" href="' . esc_url( admin_url( 'post-new.php?post_type=shop_coupon' ) ) . '">' . esc_html__( 'Create your first account', 'wp-ever-accounting' ) . '</a>';
		echo '<a class="ea-blankstate-cta button" target="_blank" href="#?utm_source=blankslate&utm_medium=product&utm_content=couponsdoc&utm_campaign=woocommerceplugin">' . esc_html__( 'Learn more about accounts', 'wp-ever-accounting' ) . '</a>';
		echo '</div>';
	}

	/**
	 * Retrieve the view types
	 *
	 * @access public
	 * @return array $views All the views available
	 * @since 1.0
	 */
	public function get_views() {
		$base           = eaccounting_admin_url();
		$current        = isset( $_GET['status'] ) ? $_GET['status'] : '';
		$total_count    = '&nbsp;<span class="count">(' . $this->total_count . ')</span>';
		$active_count   = '&nbsp;<span class="count">(' . $this->active_count . ')</span>';
		$inactive_count = '&nbsp;<span class="count">(' . $this->inactive_count . ')</span>';

		$views = array(
			'all'      => sprintf( '<a href="%s"%s>%s</a>', esc_url( remove_query_arg( 'status', $base ) ), $current === 'all' || $current == '' ? ' class="current"' : '', __( 'All', 'wp-ever-accounting' ) . $total_count ),
			'active'   => sprintf( '<a href="%s"%s>%s</a>', esc_url( add_query_arg( 'status', 'active', $base ) ), $current === 'active' ? ' class="current"' : '', __( 'Active', 'wp-ever-accounting' ) . $active_count ),
			'inactive' => sprintf( '<a href="%s"%s>%s</a>', esc_url( add_query_arg( 'status', 'inactive', $base ) ), $current === 'inactive' ? ' class="current"' : '', __( 'Inactive', 'wp-ever-accounting' ) . $inactive_count ),
		);

		return $views;
	}

	/**
	 * Retrieve the table columns
	 *
	 * @return array $columns Array of all the list table columns
	 * @since 1.0
	 */
	public function get_columns() {
		$columns = array(
			'cb'        => '<input type="checkbox" />',
			'name'      => __( 'Name', 'wp-ever-accounting' ),
			'balance'   => __( 'Balance', 'wp-ever-accounting' ),
			'number'    => __( 'Number', 'wp-ever-accounting' ),
			'bank_name' => __( 'Bank Name', 'wp-ever-accounting' ),
			'enabled'   => __( 'Enabled', 'wp-ever-accounting' ),
			'actions'   => __( 'Actions', 'wp-ever-accounting' ),
		);

		return $columns; //apply_filters( 'eaccounting_accounts_table_columns', $this->prepare_columns( $columns ), $columns, $this );
	}

	/**
	 * Retrieve the table's sortable columns
	 *
	 * @return array Array of all the sortable columns
	 * @since 1.0
	 */
	public function get_sortable_columns() {
		$columns = array(
			'name'      => array( 'name', false ),
			'number'    => array( 'number', false ),
			'bank_name' => array( 'bank_name', false ),
			'balance'   => array( 'balance', false ),
			'enabled'   => array( 'enabled', false ),
		);

		return apply_filters( 'eaccounting_accounts_table_sortable_columns', $columns, $this );
	}

	/**
	 * This function renders most of the columns in the list table.
	 *
	 *
	 * @param \AffWP\Affiliate $affiliate The current affiliate object.
	 * @param string $column_name The name of the column
	 *
	 * @return string The column value.
	 * @since 1.0.2
	 *
	 */
	function column_default( $affiliate, $column_name ) {
		switch ( $column_name ) {

			default:
				$value = isset( $affiliate->$column_name ) ? $affiliate->$column_name : '';
				break;
		}

		return apply_filters( 'eaccounting_accounts_table_' . $column_name, $value, $affiliate );
	}

	/**
	 * Renders the checkbox column in the accounts list table.
	 *
	 *
	 * @param EAccounting_Account $account The current account object.
	 *
	 * @return string Displays a checkbox.
	 * @since  1.0.2
	 *
	 */
	function column_cb( $account ) {
		return sprintf( '<input type="checkbox" name="account_id[]" value="%d"/>', $account->get_id() );
	}

	/**
	 * Renders the "Name" column in the accounts list table.
	 *
	 *
	 * @param EAccounting_Account $account The current account object.
	 *
	 * @return string Data shown in the Name column.
	 * @since  1.0.2
	 *
	 */
	function column_name( $account ) {
		$name = $account->get_name();

		$value = sprintf( '<a href="%1$s">%2$s</a>',
			esc_url( eaccounting_admin_url( [ 'action' => 'edit', 'account_id' => $account->get_id() ] ) ),
			$name
		);

		return apply_filters( 'eaccounting_account_table_name', $value, $account );
	}

	/**
	 * Renders the "Balance" column in the accounts list table.
	 *
	 * @param EAccounting_Account $account The current account object.
	 *
	 * @return string Data shown in the Balance column.
	 * @since  1.0.2
	 *
	 */
	function column_balance( $account ) {
		return apply_filters( 'eaccounting_account_table_balance', $account->get_balance( true ), $account );
	}

	/**
	 * Renders the "Number" column in the accounts list table.
	 *
	 * @param EAccounting_Account $account The current account object.
	 *
	 * @return string Data shown in the Number column.
	 * @since  1.0.2
	 *
	 */
	function column_number( $account ) {
		return apply_filters( 'eaccounting_account_table_number', $account->get_number(), $account );
	}

	/**
	 * Renders the "Bank Name" column in the accounts list table.
	 *
	 * @param EAccounting_Account $account The current account object.
	 *
	 * @return string Data shown in the Bank Name column.
	 * @since  1.0.2
	 *
	 */
	function column_bank_name( $account ) {
		return apply_filters( 'eaccounting_account_table_bank_name', $account->get_bank_name(), $account );
	}

	/**
	 * @since 1.0.
	 * @return string
	 */
	function column_enabled() {
		return sprintf('<label class="ea-toggle"><input type="checkbox" name="enabled[1]" checked="checked"><span data-label-off="No" data-label-on="Yes" class="ea-toggle-slider"></span></label>');
	}

	/**
	 * @param $account
	 * @since 1.0.
	 *
	 * @return string
	 */
	function column_actions( $account ) {
		$base_uri    = eaccounting_admin_url( array( 'account_id' => $account->get_id(), 'tab' => 'accounts' ) );
		$row_actions = array();

		$row_actions['edit'] = array(
			'label' => __( 'Edit', 'wp-ever-accounting' ),
			array( 'action' => 'edit' ),
			array( 'base_uri' => $base_uri )
		);
		$row_actions['delete'] = array(
			'label' => __( 'Delete', 'wp-ever-accounting' ),
			array( 'base_uri' => $base_uri,  'nonce' => 'account-nonce' )
		);

		$row_actions = apply_filters( 'eaccounting_account_row_actions', $row_actions, $account );

		return $this->row_actions( $row_actions );
	}

	/**
	 * Renders the message to be displayed when there are no accounts.
	 *
	 * @since  1.0.2
	 */
	function no_items() {
		_e( 'No accounts found.', 'wp-ever-accounting' );
	}

	/**
	 * Retrieve the bulk actions
	 *
	 * @return array $actions Array of the bulk actions
	 * @since 1.0
	 */
	public function get_bulk_actions() {
		$actions = array(
			'activate'   => __( 'Activate', 'wp-ever-accounting' ),
			'deactivate' => __( 'Deactivate', 'wp-ever-accounting' ),
			'delete'     => __( 'Delete', 'wp-ever-accounting' )
		);

		/**
		 * Filters the bulk actions to return in the accounts list table.
		 *
		 * @param array $actions Bulk actions.
		 *
		 * @since 1.0.2
		 *
		 */
		return apply_filters( 'eaccounting_accounts_bulk_actions', $actions );
	}

	/**
	 * Process the bulk actions
	 *
	 * @return void
	 * @since 1.0
	 */
	public function process_bulk_action() {

		if ( empty( $_REQUEST['_wpnonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'bulk-accounts' ) && ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'account-nonce' ) ) {
			return;
		}

		$ids = isset( $_GET['account_id'] ) ? $_GET['account_id'] : false;

		if ( ! is_array( $ids ) ) {
			$ids = array( $ids );
		}

		$ids = array_map( 'absint', $ids );

		if ( empty( $ids ) ) {
			return;
		}

		$action = $this->current_action();

		foreach ( $ids as $id ) {
			$account = eaccounting_get_account( $id );
			switch ( $action ) {
				case 'activate':
					$account->set_enabled( 1 );
					$account->save();
					break;
				case 'deactivate':
					$account->set_enabled( 0 );
					$account->save();
					break;
				case 'delete':
					$account->delete();
					break;
				default:
					do_action( 'eaccounting_accounts_do_bulk_action_' . $this->current_action(), $account );
			}
		}

		if ( ! empty( $account ) ) {
			wp_safe_redirect( remove_query_arg( [
				'account_id',
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
	 * Retrieve the accounts counts
	 *
	 * @return void
	 * @since 1.0
	 */
	public function get_accounts_counts() {

		$search = isset( $_GET['s'] ) ? $_GET['s'] : '';

		$this->active_count = eaccounting()->accounts->get_accounts( array_merge( $this->query_args, array(
			'status' => 'active',
			'search' => $search
		) ) )->count();

		$this->inactive_count = eaccounting()->accounts->get_accounts( array_merge( $this->query_args, array(
			'status' => 'inactive',
			'search' => $search
		) ) )->count();

		$this->total_count = $this->active_count + $this->inactive_count;
	}

	/**
	 * Retrieve all the data for all the accounts
	 *
	 * @return array $accounts Array of all the data for the accounts
	 * @since 1.0.2
	 */
	public function accounts_data() {
		$page    = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
		$status  = isset( $_GET['status'] ) ? $_GET['status'] : '';
		$search  = isset( $_GET['s'] ) ? $_GET['s'] : '';
		$order   = isset( $_GET['order'] ) ? $_GET['order'] : 'DESC';
		$orderby = isset( $_GET['orderby'] ) ? $_GET['orderby'] : 'id';

		$per_page = $this->get_items_per_page( 'eaccounting_edit_accounts_per_page', $this->per_page );

		$args = wp_parse_args( $this->query_args, array(
			'number'  => $per_page,
			'offset'  => $per_page * ( $page - 1 ),
			'status'  => $status,
			'search'  => $search,
			'orderby' => eaccounting_clean( $orderby ),
			'order'   => eaccounting_clean( $order )
		) );

		$args = apply_filters( 'eaccounting_account_table_get_accounts', $args, $this );

		$accounts = eaccounting()->accounts->get_accounts( $args )->get( OBJECT, 'eaccounting_get_account' );

		$this->current_count = eaccounting()->accounts->get_accounts( $args )->count();

		return $accounts;
	}

	/**
	 * Setup the final data for the table
	 *
	 * @return void
	 * @since 1.0.2
	 */
	public function prepare_items() {
		$per_page = $this->get_items_per_page( 'eaccounting_edit_accounts_per_page', $this->per_page );

		$columns = $this->get_columns();

		$hidden = array();

		$sortable = $this->get_sortable_columns();

		$this->get_column_info();

		$this->process_bulk_action();

		$data = $this->accounts_data();

		$current_page = $this->get_pagenum();

		$total_items = $this->current_count;

		$status = isset( $_GET['status'] ) ? $_GET['status'] : 'any';

		switch ( $status ) {
			case 'active':
				$total_items = $this->active_count;
				break;
			case 'inactive':
				$total_items = $this->inactive_count;
				break;
			case 'any':
				$total_items = $this->current_count;
				break;
		}

		$this->items = $data;

		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil( $total_items / $per_page )
		) );
	}

}
