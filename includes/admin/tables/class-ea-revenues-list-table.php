<?php
defined( 'ABSPATH' ) || exit();

// Load WP_List_Table if not loaded
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class EAccounting_Revenues_List_Table extends WP_List_Table {
	/**
	 * Number of results to show per page
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public $per_page = 20;

	/**
	 *
	 * Total number of discounts
	 * @var string
	 * @since 1.0.0
	 */
	public $total_count;

	/**
	 * Active number of account
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public $active_count;

	/**
	 * Inactive number of account
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public $inactive_count;

	/**
	 * Base URL
	 * @var string
	 */
	public $base_url;

	/**
	 * EAccounting_Revenues_List_Table constructor.
	 */
	public function __construct() {
		parent::__construct( array(
			'singular' => 'revenue',
			'plural'   => 'revenues',
			'ajax'     => false,
		) );
		$this->base_url = admin_url( 'admin.php?page=eaccounting-revenues' );
		$this->process_bulk_action();
	}

	/**
	 * Setup the final data for the table
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function prepare_items() {
		$per_page = $this->per_page;

		$columns = $this->get_columns();

		$hidden = array();

		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$this->process_bulk_action();

		$items = $this->get_results();

		$data = array_map( function ( $item ) {
			return new EAccounting_Revenue( $item );
		}, $items );

		$status = isset( $_GET['status'] ) ? sanitize_key( $_GET['status'] ) : 'any';

		switch ( $status ) {
			case 'active':
				$total_items = $this->active_count;
				break;
			case 'inactive':
				$total_items = $this->inactive_count;
				break;
			case 'any':
			default:
				$total_items = $this->total_count;
				break;
		}

		$this->items = $data;

		$this->set_pagination_args( array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page ),
			)
		);
	}

	/**
	 * Retrieve the view types
	 *
	 * @return array $views All the views available
	 * @since 1.0.0
	 */
	public function get_views() {
		return array();
	}

	/**
	 * Retrieve the bulk actions
	 *
	 * @return array $actions Array of the bulk actions
	 * @since 1.0.0
	 */
	public function get_bulk_actions() {
		$actions = array(
			'delete' => __( 'Delete', 'wp-ever-accounting' ),
		);

		return $actions;
	}

	/**
	 * Retrieve the table columns
	 *
	 * @return array $columns Array of all the list table columns
	 * @since 1.0.0
	 */
	public function get_columns() {
		$columns = array(
			'cb'        => '<input type="checkbox" />',
			'date'      => __( 'Date', 'wp-ever-accounting' ),
			'amount'    => __( 'Amount', 'wp-ever-accounting' ),
			'customer'  => __( 'Customer', 'wp-ever-accounting' ),
			'category'  => __( 'Category', 'wp-ever-accounting' ),
			'account'   => __( 'Account', 'wp-ever-accounting' ),
			'reference' => __( 'Reference', 'wp-ever-accounting' ),
		);

		return $columns;
	}

	/**
	 * Retrieve the table's sortable columns
	 *
	 * @return array Array of all the sortable columns
	 * @since 1.0.0
	 */
	public function get_sortable_columns() {
		return array(
			'date'     => array( 'paid_at', false ),
			'amount'   => array( 'amount', false ),
			'customer' => array( 'contact_id', false ),
			'category' => array( 'category_id', false ),
			'account'  => array( 'account_id', false ),
		);
	}

	/**
	 * @param object $item
	 * @param string $column_name
	 *
	 * @return string;
	 * @since 1.0.0
	 */
	function column_default( $item, $column_name ) {
		return $item->$column_name;
	}

	/**
	 * Render the checkbox column
	 *
	 * @param array $item Contains all the data for the checkbox column
	 *
	 * @return string Displays a checkbox
	 * @since 1.0.0
	 */
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			/*$1%s*/ $this->_args['singular'],
			/*$2%s*/ $item->id
		);
	}

	/**
	 * Render the Name Column
	 *
	 * @param EAccounting_Revenue $item Contains all the data of the discount code
	 *
	 * @return string Data shown in the Name column
	 * @since 1.0.0
	 */
	function column_date( $item ) {
		$revenue_url = add_query_arg( array( 'revenue' => $item->get_id() ), $this->base_url );
		$edit_url    = wp_nonce_url( add_query_arg( [ 'eaccounting-action' => 'edit_revenue' ], $revenue_url ), 'eaccounting_revenues_nonce' );
		$delete_url  = wp_nonce_url( add_query_arg( [ 'eaccounting-action' => 'delete_revenue' ], $revenue_url ), 'eaccounting_revenues_nonce' );

		$row_actions['edit'] = sprintf( '<a href="%1$s">%2$s</a>', $edit_url, __( 'Edit', 'wp-ever-accounting' ) );

		$row_actions['delete'] = sprintf( '<a href="%1$s">%2$s</a>', $delete_url, __( 'Delete', 'wp-ever-accounting' ) );

		$row_actions = apply_filters( 'eaccounting_revenues_row_actions', $row_actions, $item );

		return sprintf( '<strong><a href="%1$s">%2$s</a></strong>', $edit_url, stripslashes( $item->get_paid_at() ) ) . $this->row_actions( $row_actions );
	}

	/**
	 * @param EAccounting_Revenue $item
	 *
	 * @return string;
	 * @since 1.0.0
	 */
	function column_amount( $item ) {
		return $item->get_amount( 'view' );
	}

	/**
	 * @param EAccounting_Revenue $item
	 *
	 * @return string;
	 * @since 1.0.0
	 */
	function column_customer( $item ) {
		$customer = $item->get_contact( 'view' );

		return $customer ? $customer->get_name() : '&mdash;';
	}

	/**
	 * @param EAccounting_Revenue $item
	 *
	 * @return string;
	 * @since 1.0.0
	 */
	function column_category( $item ) {
		$category = $item->get_category( 'view' );

		return $category ? $category : '&mdash;';
	}

	/**
	 * @param EAccounting_Revenue $item
	 *
	 * @return string;
	 * @since 1.0.0
	 */
	function column_reference( $item ) {
		return $item->get_reference() ? $item->get_reference() : '&mdash;';
	}

	/**
	 * @param EAccounting_Revenue $item
	 *
	 * @return string;
	 * @since 1.0.0
	 */
	function column_account( $item ) {
		return $item->get_account( 'view' ) ? $item->get_account( 'view' ) : '&mdash;';
	}

	/**
	 * @return string
	 * @since 1.0.0
	 */
	function column_unpaid() {
		return '&mdash;';
	}

	/**
	 * since 1.0.0
	 *
	 * @param string $which
	 */
	function extra_tablenav( $which ) {
		if ( $which == "top" ) {
			$customers     = eaccounting_get_contacts();
			$s_customer_id = isset( $_GET['customer_id'] ) ? absint( $_GET['customer_id'] ) : '';
			?>
			<div class="alignleft actions bulkactions">
				<select name="customer_id">
					<?php echo sprintf( '<option value="%s" %s>%s</option>', '', selected( '', $s_customer_id, false ), __( 'Customer', 'wp-ever-accounting' ) ); ?>
					<?php foreach ( $customers as $customer ) {
						$name = sprintf( '%s %s', $customer->first_name, $customer->last_name );
						echo sprintf( '<option value="%s" %s>%s</option>', $customer->id, selected( $customer->id, $s_customer_id, false ), $name );
					} ?>
				</select>
				<button type="submit" class="button-secondary"><?php _e( 'Filter', 'wp-ever-accounting' ); ?></button>
			</div>
			<?php
		}
		if ( $which == "bottom" ) {
			//The code that goes after the table is there

		}
	}

	/**
	 * Process the bulk actions
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function process_bulk_action() {

		if ( empty( $_REQUEST['_wpnonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'bulk-revenues' ) ) {
			return;
		}

		$ids = isset( $_GET['revenue'] ) ? $_GET['revenue'] : false;

		if ( ! is_array( $ids ) ) {
			$ids = array( $ids );
		}
		$ids = array_map( 'intval', $ids );

		foreach ( $ids as $id ) {
			if ( 'delete' === $this->current_action() ) {
				eaccounting_delete_revenue( $id );
			}
		}
	}

	/**
	 * Retrieve all the data for all the discount codes
	 *
	 * @return array $get_results Array of all the data for the discount codes
	 * @since 1.0.0
	 */
	public function get_results() {
		$per_page = $this->per_page;

		$orderby    = isset( $_GET['orderby'] ) ? sanitize_key( $_GET['orderby'] ) : 'created_at';
		$order      = isset( $_GET['order'] ) ? sanitize_key( $_GET['order'] ) : 'DESC';
		$status     = isset( $_GET['status'] ) ? sanitize_key( $_GET['status'] ) : '';
		$search     = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : null;
		$contact_id = isset( $_GET['customer_id'] ) ? absint( $_GET['customer_id'] ) : '';
		$args       = array(
			'per_page'   => $per_page,
			'page'       => isset( $_GET['paged'] ) ? intval( $_GET['paged'] ) : 1,
			'orderby'    => $orderby,
			'contact_id' => $contact_id,
			'order'      => $order,
			'status'     => $status,
			'search'     => $search
		);

		if ( array_key_exists( $orderby, $this->get_sortable_columns() ) && 'name' != $orderby ) {
			$args['orderby'] = $orderby;
		}

		$this->active_count   = eaccounting_get_revenues( array_merge( $args, array( 'status' => 'active' ) ), true );
		$this->inactive_count = eaccounting_get_revenues( array_merge( $args, array( 'status' => 'inactive' ) ), true );
		$this->total_count    = eaccounting_get_revenues( array_merge( $args, array( 'status' => '' ) ), true );

		$results = eaccounting_get_revenues( $args );

		return $results;
	}




}
