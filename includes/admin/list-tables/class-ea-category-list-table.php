<?php
/**
 * Categories Admin List Table.
 *
 * @since       1.0.2
 * @subpackage  EverAccounting\Admin\ListTables
 * @package     EverAccounting
 */

use EverAccounting\Models\Category;

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( '\EAccounting_List_Table' ) ) {
	require_once dirname( __FILE__ ) . '/class-ea-admin-list-table.php';
}

/**
 * Class EAccounting_Category_List_Table
 * @since 1.1.0
 */
class EAccounting_Category_List_Table extends EAccounting_List_Table {
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
	 * Number of active items found
	 *
	 * @since 1.0
	 * @var string
	 */
	public $active_count;

	/**
	 *  Number of inactive items found
	 *
	 * @since 1.0
	 * @var string
	 */
	public $inactive_count;

	/**
	 * Get things started
	 *
	 * @param array $args Optional. Arbitrary display and query arguments to pass through the list table. Default empty array.
	 *
	 * @see    WP_List_Table::__construct()
	 *
	 * @since  1.0.2
	 *
	 */
	public function __construct( $args = array() ) {
		$args = (array) wp_parse_args(
			$args,
			array(
				'singular' => 'category',
				'plural'   => 'categories',
			)
		);

		parent::__construct( $args );
	}

	/**
	 * Check if there is contents in the database.
	 *
	 * @return bool
	 * @since 1.0.2
	 */
	public function is_empty() {
		return parent::is_empty(); // TODO: Change the autogenerated stub
	}

	/**
	 * Render blank state.
	 *
	 * @return void
	 * @since 1.0.2
	 */
	protected function render_blank_state() {
		return parent::render_blank_state(); // TODO: Change the autogenerated stub
	}

	/**
	 * Define which columns to show on this screen.
	 *
	 * @return array
	 * @since 1.0.2
	 */
	public function define_columns() {
		return array(
			'cb'      => '<input type="checkbox" />',
			'name'    => __( 'Name', 'wp-ever-accounting' ),
			'type'    => __( 'Type', 'wp-ever-accounting' ),
			'color'   => __( 'Color', 'wp-ever-accounting' ),
			'enabled' => __( 'Enabled', 'wp-ever-accounting' ),
			'actions' => __( 'Actions', 'wp-ever-accounting' ),
		);
	}

	/**
	 * Define sortable columns.
	 *
	 * @return array
	 * @since 1.0.2
	 */
	protected function define_sortable_columns() {
		return array(
			'name'    => array( 'name', false ),
			'type'    => array( 'type', false ),
			'color'   => array( 'color', false ),
			'enabled' => array( 'enabled', false ),
		);
	}

	/**
	 * Define bulk actions
	 *
	 * @return array
	 * @since 1.0.2
	 */
	public function define_bulk_actions() {
		return array(
			'enable'  => __( 'Enable', 'wp-ever-accounting' ),
			'disable' => __( 'Disable', 'wp-ever-accounting' ),
			'delete'  => __( 'Delete', 'wp-ever-accounting' ),
		);
	}


	/**
	 * Define primary column.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_primary_column() {
		return 'name';
	}


	/**
	 * Renders the checkbox column in the categories list table.
	 *
	 * @param Category $category The current object.
	 *
	 * @return string Displays a checkbox.
	 * @since  1.0.2
	 *
	 */
	function column_cb( $category ) {
		return sprintf( '<input type="checkbox" name="category_id[]" value="%d"/>', $category->get_id() );
	}

	/**
	 * This function renders most of the columns in the list table.
	 *
	 * @param string $column_name The name of the column
	 *
	 * @param Category $category
	 *
	 * @return string The column value.
	 * @since 1.0.2
	 *
	 */
	function column_default( $category, $column_name ) {
		$category_id = $category->get_id();

		switch ( $column_name ) {
			case 'name':
				$name = $category->get_name();

				$value = sprintf(
					'<a href="%1$s">%2$s</a>',
					esc_url(
						eaccounting_admin_url(
							array(
								'action'      => 'edit',
								'tab'         => 'categories',
								'category_id' => $category->get_id(),
							)
						)
					),
					$name
				);
				break;
			case 'type':
				$type  = $category->get_type();
				$types = eaccounting_get_category_types();
				$value = array_key_exists( $type, $types ) ? $types[ $type ] : ucfirst( $type );
				break;
			case 'color':
				$value = sprintf( '<span class="dashicons dashicons-marker" style="color:%s;">&nbsp;</span>', $category->get_color() );
				break;
			case 'enabled':
				ob_start();
				eaccounting_toggle(
					array(
						'name'  => 'enabled',
						'id'    => 'enabled_' . $category->get_id(),
						'value' => $category->get_enabled( 'edit' ),
						'naked' => true,
						'attr'  => array(
							'data-id'    => $category->get_id(),
							'data-nonce' => wp_create_nonce( 'ea_edit_category' ),
						),
					)
				);
				$value = ob_get_contents();
				ob_get_clean();
				break;
			case 'actions':
				$edit_url = eaccounting_admin_url(
					array(
						'tab'         => 'categories',
						'action'      => 'edit',
						'category_id' => $category_id,
					)
				);
				$del_url  = eaccounting_admin_url(
					array(
						'tab'         => 'categories',
						'action'      => 'delete',
						'category_id' => $category_id,
					)
				);
				$actions  = array(
					'edit'   => sprintf( '<a href="%s" class="dashicons dashicons-edit"></a>', esc_url( $edit_url ) ),
					'delete' => sprintf( '<a href="%s" class="dashicons dashicons-trash"></a>', esc_url( $del_url ) ),
				);
				$value    = $this->row_actions( $actions );
				break;
			default:
				return parent::column_default( $category, $column_name );
		}

		return apply_filters( 'eaccounting_category_list_table_' . $column_name, $value, $category );
	}

	/**
	 * Renders the message to be displayed when there are no items.
	 *
	 * @return void
	 * @since  1.0.2
	 */
	function no_items() {
		_e( 'There is no categories found.', 'wp-ever-accounting' );
	}

	/**
	 * Process the bulk actions
	 *
	 * @return void
	 * @since 1.0.2
	 */
	public function process_bulk_action() {
		if ( empty( $_REQUEST['_wpnonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'bulk-categories' ) && ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'category-nonce' ) ) {
			return;
		}

		$ids = isset( $_GET['category_id'] ) ? $_GET['category_id'] : false;

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
				case 'enable':
					eaccounting_insert_category(
						array(
							'id'      => $id,
							'enabled' => '1',
						)
					);
					break;
				case 'disable':
					eaccounting_insert_category(
						array(
							'id'      => $id,
							'enabled' => '0',
						)
					);
					break;
				case 'delete':
					eaccounting_delete_category( $id );
					break;
				default:
					do_action( 'eaccounting_categories_do_bulk_action_' . $this->current_action(), $id );
			}
		}

		if ( isset( $_GET['_wpnonce'] ) ) {
			wp_safe_redirect(
				remove_query_arg(
					array(
						'category_id',
						'action',
						'_wpnonce',
						'_wp_http_referer',
						'action2',
						'doaction',
						'paged',
					)
				)
			);
			exit();
		}
	}

	/**
	 * Retrieve the view types
	 *
	 * @access public
	 * @return array $views All the views available
	 * @since  1.0.2
	 */
	public function get_views() {
		$base           = eaccounting_admin_url( array( 'tab' => 'categories' ) );
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
	 * Retrieve all the data for the table.
	 * Setup the final data for the table
	 *
	 * @return void
	 * @since 1.0.2
	 */
	public function prepare_items() {
		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$this->process_bulk_action();

		$page    = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
		$status  = isset( $_GET['status'] ) ? $_GET['status'] : '';
		$search  = isset( $_GET['s'] ) ? $_GET['s'] : '';
		$order   = isset( $_GET['order'] ) ? $_GET['order'] : 'DESC';
		$orderby = isset( $_GET['orderby'] ) ? $_GET['orderby'] : 'id';

		$per_page = $this->per_page;

		$args = wp_parse_args(
			$this->query_args,
			array(
				'number'   => $per_page,
				'offset'   => $per_page * ( $page - 1 ),
				'per_page' => $per_page,
				'page'     => $page,
				'search'   => $search,
				'status'   => $status,
				'orderby'  => eaccounting_clean( $orderby ),
				'order'    => eaccounting_clean( $order ),
			)
		);

		$args = apply_filters( 'eaccounting_category_table_query_args', $args, $this );

		$this->items = eaccounting_get_categories( $args );

		$this->active_count = eaccounting_get_categories(
			array_merge(
				$args,
				array(
					'count_total' => true,
					'status'      => 'active',
				)
			)
		);

		$this->inactive_count = eaccounting_get_categories(
			array_merge(
				$args,
				array(
					'count_total' => true,
					'status'      => 'inactive',
				)
			)
		);

		$this->total_count = $this->active_count + $this->inactive_count;

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

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page ),
			)
		);
	}
}