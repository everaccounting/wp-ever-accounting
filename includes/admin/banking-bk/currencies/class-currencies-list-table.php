<?php
/**
 * Currencies Admin List Table
 *
 * @package     EverAccounting
 * @subpackage  Admin/Banking
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.2
 */
defined( 'ABSPATH' ) || exit();


class Currencies_List_Table extends EAccounting_List_Table {
	/**
	 * Default number of items to show per page
	 *
	 * @var string
	 * @since 1.0
	 */
	public $per_page = 20;

	/**
	 * Total number of item found
	 *
	 * @var int
	 * @since 1.0
	 */
	public $total_count;

	/**
	 * Number of active items found
	 *
	 * @var string
	 * @since 1.0
	 */
	public $active_count;

	/**
	 *  Number of inactive items found
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
			'singular' => 'currency',
			'plural'   => 'currencies',
		) );

		parent::__construct( $args );

		$this->get_items_counts();
	}

	/**
	 * Render blank state. Extend to add content.
	 */
	protected function render_blank_state() {
		echo '<div class="ea-blankstate">';
		echo '<h2 class="ea-blankstate-message">' . esc_html__( 'Add income, expenses in any currency and let the system convert them in your main currency.', 'wp-ever-accounting' ) . '</h2>';
		echo '<a class="ea-blankstate-cta button-primary button" href="' . esc_url( eaccounting_admin_url( array(
				'tab'    => 'currencies',
				'action' => 'add'
			) ) ) . '">' . esc_html__( 'Create currency', 'wp-ever-accounting' ) . '</a>';
		echo '<a class="ea-blankstate-cta button" target="_blank" href="#?utm_source=blankslate&utm_medium=currency&utm_content=currencydoc&utm_campaign=eaccountingplugin">' . esc_html__( 'Learn more about currency', 'wp-ever-accounting' ) . '</a>';
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
		$base           = eaccounting_admin_url( [ 'tab' => 'currencies' ] );
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
			'cb'      => '<input type="checkbox" />',
			'name'    => __( 'Name', 'wp-ever-accounting' ),
			'code'    => __( 'Code', 'wp-ever-accounting' ),
			'rate'    => __( 'Rate', 'wp-ever-accounting' ),
			'enabled' => __( 'Enabled', 'wp-ever-accounting' ),
			'actions' => __( 'Actions', 'wp-ever-accounting' ),
		);

		return apply_filters( 'eaccounting_currencies_table_columns', $columns, $this );
	}

	/**
	 * Define which columns are hidden.
	 *
	 * @return array
	 * @since 1.0.2
	 */
	public function get_hidden_columns() {
		return array();
	}

	/**
	 * Retrieve the table's sortable columns
	 *
	 * @return array Array of all the sortable columns
	 * @since 1.0
	 */
	public function get_sortable_columns() {
		$columns = array(
			'name'    => array( 'name', false ),
			'code'    => array( 'code', false ),
			'rate'    => array( 'rate', false ),
			'enabled' => array( 'enabled', false ),
		);

		return apply_filters( 'eaccounting_currencies_table_sortable_columns', $columns, $this );
	}


	/**
	 * This function renders most of the columns in the list table.
	 *
	 *
	 * @param EAccounting_Currency $currency The current affiliate object.
	 * @param string $column_name The name of the column
	 *
	 * @return string The column value.
	 * @since 1.0.2
	 *
	 */
	function column_default( $currency, $column_name ) {
		switch ( $column_name ) {

			default:
				$value = isset( $currency->$column_name ) ? $currency->$column_name : '';
				break;
		}

		return apply_filters( 'eaccounting_currencies_table_' . $column_name, $value, $currency );
	}

	/**
	 * Renders the checkbox column in the accounts list table.
	 *
	 *
	 * @param EAccounting_Currency $currency The current account object.
	 *
	 * @return string Displays a checkbox.
	 * @since  1.0.2
	 *
	 */
	function column_cb( $currency ) {
		return sprintf( '<input type="checkbox" name="currency_id[]" value="%d"/>', $currency->get_id() );
	}

	/**
	 * Renders the "Name" column in the list table.
	 *
	 *
	 * @param EAccounting_Currency $currency The current object.
	 *
	 * @return string Data shown in the Name column.
	 * @since  1.0.2
	 *
	 */
	function column_name( $currency ) {
		$name = $currency->get_name();

		$value = sprintf( '<a href="%1$s">%2$s</a>',
			esc_url( eaccounting_admin_url( [
				'action'      => 'edit',
				'currency_id' => $currency->get_id(),
				'tab'         => 'currencies'
			] ) ),
			$name
		);

		return apply_filters( 'eaccounting_currency_table_name', $value, $currency );
	}

	/**
	 * Renders the "code" column in the list table.
	 *
	 * @param EAccounting_Currency $currency The current object.
	 *
	 * @return string Data shown in the "code" column.
	 * @since  1.0.2
	 *
	 */
	function column_code( $currency ) {
		return apply_filters( 'eaccounting_currency_table_balance', $currency->get_code(), $currency );
	}

	/**
	 * Renders the "rate" column in the list table.
	 *
	 * @param EAccounting_Currency $currency The current object.
	 *
	 * @return string Data shown in the "rate" column.
	 * @since  1.0.2
	 *
	 */
	function column_rate( $currency ) {
		return apply_filters( 'eaccounting_currency_table_rate', $currency->get_rate(), $currency );
	}

	/**
	 * Renders the "enabled" column in the list table.
	 *
	 * @param EAccounting_Currency $currency The current object.
	 *
	 * @return string Data shown in the "enabled" column.
	 * @since  1.0.2
	 *
	 */
	function column_enabled( $currency ) {
		ob_start();
		eaccounting_toggle( array(
			'name'  => 'enabled',
			'id'    => 'enabled_' . $currency->get_id(),
			'value' => $currency->get_enabled( 'edit' ),
			'naked' => true,
			'class' => 'ea_item_status_update',
			'attr'  => array(
				'data-objectid' => $currency->get_id(),
				'data-nonce'    => wp_create_nonce( 'ea_status_update' ),
				'data-objecttype'    => 'currency'
			)
		) );
		$output = ob_get_contents();
		ob_get_clean();

		return apply_filters( 'eaccounting_currency_table_enabled', $output, $currency );
	}

	/**
	 * @param $currency
	 *
	 * @return string
	 * @since 1.0.
	 *
	 */
	function column_actions( $currency ) {
		$base_uri    = eaccounting_admin_url( array( 'currency_id' => $currency->get_id(), 'tab' => 'currencies' ) );
		$row_actions = array();

		$row_actions['edit']   = array(
			'label' => __( 'Edit', 'wp-ever-accounting' ),
			'base_uri' => $base_uri,
			array( 'base_uri' => $base_uri )
		);
//		$row_actions['delete'] = array(
//			'label' => __( 'Delete', 'wp-ever-accounting' ),
//			array( 'base_uri' => $base_uri, 'nonce' => 'account-nonce' )
//		);

		$row_actions = apply_filters( 'eaccounting_currency_row_actions', $row_actions, $currency );

		return $this->row_actions( $row_actions );
	}

	/**
	 * Renders the message to be displayed when there are no currencies.
	 *
	 * @since  1.0.2
	 */
	function no_items() {
		_e( 'No currencies found.', 'wp-ever-accounting' );
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
		return apply_filters( 'eaccounting_currencies_bulk_actions', $actions );
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
		if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'bulk-currencies' ) && ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'currency-nonce' ) ) {
			return;
		}

		$ids = isset( $_GET['currency_id'] ) ? $_GET['currency_id'] : false;

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
				case 'activate':
					eaccounting_insert_currency( array(
						'id'      => $id,
						'enabled' => '1'
					) );
					break;
				case 'deactivate':
					eaccounting_insert_currency( array(
						'id'      => $id,
						'enabled' => '0'
					) );
					break;
				case 'delete':
					eaccounting_delete_currency( $id );
					break;
				default:
					do_action( 'eaccounting_accounts_do_bulk_action_' . $this->current_action(), $id );
			}
		}

		if ( $action ) {
			wp_safe_redirect( remove_query_arg( [
				'currency_id',
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
	public function get_items_counts() {

		$search = isset( $_GET['s'] ) ? $_GET['s'] : '';

		$this->active_count = eaccounting()->currencies->get_currencies( array_merge( $this->query_args, array(
			'status' => 'active',
			'search' => $search
		) ) )->count();

		$this->inactive_count = eaccounting()->currencies->get_currencies( array_merge( $this->query_args, array(
			'status' => 'inactive',
			'search' => $search
		) ) )->count();

		$this->total_count = $this->active_count + $this->inactive_count;
	}

	/**
	 * Retrieve all the data for the table.
	 *
	 * @return array $currencies Array of all the data for the accounts
	 * @since 1.0.2
	 */
	public function get_table_data() {
		$page    = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
		$status  = isset( $_GET['status'] ) ? $_GET['status'] : '';
		$search  = isset( $_GET['s'] ) ? $_GET['s'] : '';
		$order   = isset( $_GET['order'] ) ? $_GET['order'] : 'DESC';
		$orderby = isset( $_GET['orderby'] ) ? $_GET['orderby'] : 'id';

		$per_page = $this->get_items_per_page( 'eaccounting_edit_currencies_per_page', $this->per_page );

		$args = wp_parse_args( $this->query_args, array(
			'number'  => $per_page,
			'offset'  => $per_page * ( $page - 1 ),
			'status'  => $status,
			'search'  => $search,
			'orderby' => eaccounting_clean( $orderby ),
			'order'   => eaccounting_clean( $order )
		) );

		$args = apply_filters( 'eaccounting_currencies_table_get_currencies', $args, $this );

		$currencies = eaccounting()->currencies->get_currencies( $args )->get( OBJECT, 'eaccounting_get_currency' );

		$this->current_count = eaccounting()->currencies->get_currencies( $args )->count();

		return $currencies;
	}

	/**
	 * Setup the final data for the table
	 *
	 * @return void
	 * @since 1.0.2
	 */
	public function prepare_items() {
		$per_page = $this->get_items_per_page( 'eaccounting_edit_currencies_per_page', $this->per_page );

		$columns  = $this->get_columns();
		$hidden   = $this->get_hidden_columns();
		$sortable = $this->get_sortable_columns();

		$this->process_bulk_action();

		$total_items = $this->current_count;

		$this->items = $this->get_table_data();

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

		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil( $total_items / $per_page )
		) );

		$this->_column_headers = array( $columns, $hidden, $sortable );
	}
}
