<?php
defined( 'ABSPATH' ) || exit();

// Load WP_List_Table if not loaded
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class EAccounting_Currencies_List_Table extends WP_List_Table {
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
	 * EAccounting_Currencies_List_Table constructor.
	 */
	public function __construct() {
		parent::__construct( array(
			'singular' => 'currency',
			'plural'   => 'currencies',
			'ajax'     => false,
		) );
		$this->base_url = admin_url( 'admin.php?page=eaccounting-misc&tab=currencies' );
	}

	/**
	 * Setup the final data for the table
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function prepare_items() {
		$per_page              = $this->per_page;
		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		$data                  = $this->get_results();
		$status                = isset( $_GET['status'] ) ? $_GET['status'] : 'any';

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
	 * Message to be displayed when there are no items
	 *
	 * @since 1.0.0
	 */
	function no_items() {
		echo sprintf( __( 'No %s found.', 'wp-ever-accounting' ), $this->_args['plural'] );
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
		$current        = isset( $_GET['status'] ) ? sanitize_key( $_GET['status'] ) : '';
		$total_count    = '&nbsp;<span class="count">(' . $this->total_count . ')</span>';
		$active_count   = '&nbsp;<span class="count">(' . $this->active_count . ')</span>';
		$inactive_count = '&nbsp;<span class="count">(' . $this->inactive_count . ')</span>';

		$views = array(
			'all'      => sprintf( '<a href="%s"%s>%s</a>', remove_query_arg( 'status', $this->base_url ), $current === 'all' || $current == '' ? ' class="current"' : '', __( 'All', 'wp-ever-accounting' ) . $total_count ),
			'active'   => sprintf( '<a href="%s"%s>%s</a>', add_query_arg( 'status', 'active', $this->base_url ), $current === 'active' ? ' class="current"' : '', __( 'Active', 'wp-ever-accounting' ) . $active_count ),
			'inactive' => sprintf( '<a href="%s"%s>%s</a>', add_query_arg( 'status', 'inactive', $this->base_url ), $current === 'inactive' ? ' class="current"' : '', __( 'Inactive', 'wp-ever-accounting' ) . $inactive_count ),
		);

		return $views;
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
	 * Retrieve the table columns
	 *
	 * @return array $columns Array of all the list table columns
	 * @since 1.0.0
	 */
	public function get_columns() {
		$columns = array(
			'cb'       => '<input type="checkbox" />',
			'currency' => __( 'Currency', 'wp-ever-accounting' ),
			'rate'     => __( 'Rate', 'wp-ever-accounting' ),
			'status'   => __( 'Status', 'wp-ever-accounting' ),
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
			'currency' => array( 'currency', false ),
			'rate'     => array( 'rate', false ),
			'status'   => array( 'status', false ),
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
	 * Render the Name Column
	 *
	 * @param array $item Contains all the data of the discount code
	 *
	 * @return string Data shown in the Name column
	 * @since 1.0.0
	 */
	function column_name( $item ) {
		$category_url   = add_query_arg( array( 'category' => $item->id ), $this->base_url );
		$edit_url       = wp_nonce_url( add_query_arg( [ 'eaccounting-action' => 'edit_category' ], $category_url ), 'eaccounting_currencies_nonce' );
		$activate_url   = wp_nonce_url( add_query_arg( [ 'eaccounting-action' => 'activate_category' ], $category_url ), 'eaccounting_currencies_nonce' );
		$deactivate_url = wp_nonce_url( add_query_arg( [ 'eaccounting-action' => 'deactivate_category' ], $category_url ), 'eaccounting_currencies_nonce' );
		$delete_url     = wp_nonce_url( add_query_arg( [ 'eaccounting-action' => 'delete_category' ], $category_url ), 'eaccounting_currencies_nonce' );

		$row_actions['edit'] = sprintf( '<a href="%1$s">%2$s</a>', $edit_url, __( 'Edit', 'wp-ever-accounting' ) );

		if ( strtolower( $item->status ) == 'active' ) {
			$row_actions['deactivate'] = sprintf( '<a href="%1$s">%2$s</a>', $deactivate_url, __( 'Deactivate', 'wp-ever-accounting' ) );
		} elseif ( strtolower( $item->status ) == 'inactive' ) {
			$row_actions['activate'] = sprintf( '<a href="%1$s">%2$s</a>', $activate_url, __( 'Activate', 'wp-ever-accounting' ) );
		}
		$row_actions['delete'] = sprintf( '<a href="%1$s">%2$s</a>', $delete_url, __( 'Delete', 'wp-ever-accounting' ) );

		$row_actions = apply_filters( 'eaccounting_currencies_row_actions', $row_actions, $item );

		return sprintf( '<strong><a href="%1$s">%2$s</a></strong>', $edit_url, stripslashes( $item->name ) ) . $this->row_actions( $row_actions );
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
	 * Types of category
	 *
	 * @param $item
	 *
	 * @return string
	 * @since 1.0.0
	 */
	function column_type( $item ) {
		$types = eaccounting_get_category_types();

		return array_key_exists( $item->type, $types ) ? $types[ $item->type ] : '-';
	}

	/**
	 * Shows status of the item
	 *
	 * @param $item
	 *
	 * @return string
	 * @since 1.0.0
	 */
	function column_status( $item ) {
		return sprintf( '<span class="ea-item-status %1$s">%1$s</span>', $item->status );
	}


	/**
	 * Retrieve all the data for all the discount codes
	 *
	 * @return array $get_results Array of all the data for the discount codes
	 * @since 1.0.0
	 */
	public function get_results() {
		$per_page = $this->per_page;

		$orderby = isset( $_GET['orderby'] ) ? $_GET['orderby'] : 'created_at';
		$order   = isset( $_GET['order'] ) ? $_GET['order'] : 'DESC';
		$status  = isset( $_GET['status'] ) ? $_GET['status'] : '';
		$search  = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : null;

		$args = array(
			'per_page' => $per_page,
			'page'     => isset( $_GET['paged'] ) ? $_GET['paged'] : 1,
			'orderby'  => $orderby,
			'order'    => $order,
			'status'   => $status,
			'search'   => $search
		);

		if ( array_key_exists( $orderby, $this->get_sortable_columns() ) && 'name' != $orderby ) {
			$args['orderby'] = $orderby;
		}

		$this->active_count   = eaccounting_get_currencies( array_merge( $args, array( 'status' => 'active' ) ), true );
		$this->inactive_count = eaccounting_get_currencies( array_merge( $args, array( 'status' => 'inactive' ) ), true );
		$this->total_count    = eaccounting_get_currencies( array_merge( $args, array( 'status' => '' ) ), true );

		$results = eaccounting_get_currencies( $args );

		return $results;
	}
}