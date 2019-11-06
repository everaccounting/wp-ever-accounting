<?php
defined( 'ABSPATH' ) || exit();


// Load WP_List_Table if not loaded
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class EAccounting_Taxes_Table extends WP_List_Table {
	/**
	 * Number of results to show per page
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public $per_page = 30;

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
	 * Get things started
	 *
	 * @since 1.0.0
	 * @uses EAccounting_Accounts_Table::get_counts()
	 * @see  WP_List_Table::__construct()
	 */
	public function __construct() {
		global $status, $page;

		parent::__construct( array(
			'singular' => 'tax',
			'plural'   => 'taxes',
			'ajax'     => false,
		) );
	}

	/**
	 * Show the search field
	 *
	 * @param string $text Label for the search box
	 * @param string $input_id ID of the search box
	 *
	 * @return void
	 * @since 1.0.0
	 *
	 */
	public function search_box( $text, $input_id ) {
		if ( empty( $_REQUEST['s'] ) && ! $this->has_items() ) {
			return;
		}

		$input_id = $input_id . '-search-input';

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			echo '<input type="hidden" name="orderby" value="' . esc_attr( $_REQUEST['orderby'] ) . '" />';
		}
		if ( ! empty( $_REQUEST['order'] ) ) {
			echo '<input type="hidden" name="order" value="' . esc_attr( $_REQUEST['order'] ) . '" />';
		}
		?>
		<p class="search-box">
			<label class="screen-reader-text" for="<?php echo $input_id ?>"><?php echo $text; ?>:</label>
			<input type="search" id="<?php echo $input_id ?>" name="s" value="<?php _admin_search_query(); ?>"/>
			<?php submit_button( $text, 'button', false, false, array( 'ID' => 'search-submit' ) ); ?>
		</p>
		<?php
	}


	/**
	 * Retrieve the view types
	 *
	 * @return array $views All the views available
	 * @since 1.0.0
	 */
	public function get_views() {
		$base = admin_url( 'admin.php?page=eaccounting-taxes' );

		$current        = isset( $_GET['status'] ) ? $_GET['status'] : '';
		$total_count    = '&nbsp;<span class="count">(' . $this->total_count . ')</span>';
		$active_count   = '&nbsp;<span class="count">(' . $this->active_count . ')</span>';
		$inactive_count = '&nbsp;<span class="count">(' . $this->inactive_count . ')</span>';

		$views = array(
			'all'      => sprintf( '<a href="%s"%s>%s</a>', remove_query_arg( 'status', $base ), $current === 'all' || $current == '' ? ' class="current"' : '', __( 'All', 'wp-ever-accounting' ) . $total_count ),
			'active'   => sprintf( '<a href="%s"%s>%s</a>', add_query_arg( 'status', 'active', $base ), $current === 'active' ? ' class="current"' : '', __( 'Active', 'wp-ever-accounting' ) . $active_count ),
			'inactive' => sprintf( '<a href="%s"%s>%s</a>', add_query_arg( 'status', 'inactive', $base ), $current === 'inactive' ? ' class="current"' : '', __( 'Inactive', 'wp-ever-accounting' ) . $inactive_count ),
		);

		return $views;
	}

	/**
	 * Retrieve the table columns
	 *
	 * @return array $columns Array of all the list table columns
	 * @since 1.0.0
	 */
	public function get_columns() {
		$columns = array(
			'cb'     => '<input type="checkbox" />',
			'name'   => __( 'Name', 'wp-ever-accounting' ),
			'rate'   => __( 'Rate(%)', 'wp-ever-accounting' ),
			'type'   => __( 'Type', 'wp-ever-accounting' ),
			'status' => __( 'Status', 'wp-ever-accounting' ),
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
			'name'   => array( 'name', false ),
			'rate'   => array( 'rate', false ),
			'type'   => array( 'type', false ),
			'status' => array( 'status', false ),
		);
	}

	/**
	 * Gets the name of the primary column.
	 *
	 * @return string Name of the primary column.
	 * @since 2.5
	 * @access protected
	 *
	 */
	protected function get_primary_column_name() {
		return 'name';
	}

	/**
	 * This function renders most of the columns in the list table.
	 *
	 * @param array $item Contains all the data of the discount code
	 * @param string $column_name The name of the column
	 *
	 * @return string Column Name
	 * @since 1.0.0
	 *
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
			/*$1%s*/ 'discount',
			/*$2%s*/ $item->id
		);
	}


	/**
	 * Render the Name Column
	 *
	 * @param array $item Contains all the data of the discount code
	 *
	 * @return string Data shown in the Name column
	 * @since 1.0.0
	 */
	function column_name( $item ) {
		$base                = admin_url( 'admin.php?page=eaccounting-taxes' );
		$row_actions['edit'] = '<a href="' . add_query_arg( array(
				'eaccounting_action' => 'edit_tax',
				'tax'            => $item->id
			), $base ) . '">' . __( 'Edit', 'wp-ever-accounting' ) . '</a>';

		if ( strtolower( $item->status ) == '1' ) {
			$row_actions['deactivate'] = '<a href="' . esc_url( wp_nonce_url( add_query_arg( array(
					'eaccounting_action' => 'deactivate_tax',
					'tax'            => $item->id
				), $base ), 'eaccounting_taxes_nonce' ) ) . '">' . __( 'Deactivate', 'wp-ever-accounting' ) . '</a>';
		} elseif ( strtolower( $item->status ) == '0' ) {
			$row_actions['activate'] = '<a href="' . esc_url( wp_nonce_url( add_query_arg( array(
					'eaccounting_action' => 'activate_tax',
					'tax'            => $item->id
				), $base ), 'eaccounting_taxes_nonce' ) ) . '">' . __( 'Activate', 'wp-ever-accounting' ) . '</a>';
		}

		$row_actions['delete'] = '<a href="' . esc_url( wp_nonce_url( add_query_arg( array(
				'eaccounting_action' => 'delete_tax',
				'tax'            => $item->id
			), $base ), 'eaccounting_taxes_nonce' ) ) . '">' . __( 'Delete', 'wp-ever-accounting' ) . '</a>';

		$row_actions = apply_filters( 'eaccounting_row_actions', $row_actions, $item );

		return sprintf( '<strong>%s</strong>', stripslashes( $item->name ) ) . $this->row_actions( $row_actions );
	}

	/**
	 * Render the current balance column
	 *
	 * @param array $item Contains all the data for the checkbox column
	 *
	 * @return string Displays current balance
	 * @since 1.0.0
	 */
	function column_current_balance( $item ) {
		return '';
	}

	/**
	 * Render the current balance column
	 *
	 * @param array $item Contains all the data for the checkbox column
	 *
	 * @return string Displays current balance
	 * @since 1.0.0
	 */
	function column_opening_balance( $item ) {
		return eaccounting_amount( $item->opening_balance );
	}

	/**
	 * Message to be displayed when there are no items
	 *
	 * @since 1.7.2
	 */
	function no_items() {
		_e( 'No accounts found.', 'wp-ever-accounting' );
	}

	/**
	 * Retrieve the bulk actions
	 *
	 * @return array $actions Array of the bulk actions
	 * @since 1.0.0
	 */
	public function get_bulk_actions() {
		$actions = array(
			'activate'   => __( 'Activate', 'wp-ever-accounting' ),
			'deactivate' => __( 'Deactivate', 'wp-ever-accounting' ),
			'delete'     => __( 'Delete', 'wp-ever-accounting' ),
		);

		return $actions;
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

		if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'bulk-accounts' ) ) {
			return;
		}

		$ids = isset( $_GET['tax'] ) ? $_GET['tax'] : false;

		if ( ! is_array( $ids ) ) {
			$ids = array( $ids );
		}


		foreach ( $ids as $id ) {
			if ( 'delete' === $this->current_action() ) {
				eaccounting_delete_tax( $id );
			}
			if ( 'activate' === $this->current_action() ) {
				eaccounting_insert_tax( [ 'id' => $id, 'status' => '1' ] );
			}
			if ( 'deactivate' === $this->current_action() ) {
				eaccounting_insert_tax( [ 'id' => $id, 'status' => '0' ] );
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
		$results  = array();
		$per_page = $this->per_page;

		$orderby  = isset( $_GET['orderby'] ) ? $_GET['orderby'] : 'ID';
		$order    = isset( $_GET['order'] ) ? $_GET['order'] : 'DESC';
		$status   = isset( $_GET['status'] ) ? $_GET['status'] : '';
		$meta_key = isset( $_GET['meta_key'] ) ? $_GET['meta_key'] : null;
		$search   = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : null;
		$status = ($status=='active')?1:0;
		$args = array(
			'per_page' => $per_page,
			'paged'    => isset( $_GET['paged'] ) ? $_GET['paged'] : 1,
			'orderby'  => $orderby,
			'order'    => $order,
			'status'   => $status,
			'meta_key' => $meta_key,
			'search'   => $search
		);

		if ( array_key_exists( $orderby, $this->get_sortable_columns() ) && 'name' != $orderby ) {
			$args['orderby'] = $orderby;
		}

		$this->active_count   = eaccounting_get_taxes( array_merge( $args, array( 'status' => '1' ) ), true );
		$this->inactive_count = eaccounting_get_taxes( array_merge( $args, array( 'status' => '0' ) ), true );
		$this->total_count    = eaccounting_get_taxes( array_merge( $args, array( 'status' => '' ) ), true );

		$results = eaccounting_get_taxes( $args );

		return $results;
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

		$data = $this->get_results();

		$status = isset( $_GET['status'] ) ? $_GET['status'] : 'any';

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
}
