<?php
/**
 * Revenue list table
 *
 * Admin revenues list table, show all the incoming transactions.
 *
 * @since       1.0.2
 * @subpackage  EverAccounting\Admin\ListTables
 * @package     EverAccounting
 */

use EverAccounting\Models\Revenue;

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( '\EAccounting_List_Table' ) ) {
	require_once dirname( __FILE__ ) . '/class-ea-admin-list-table.php';
}

/**
 * Class EAccounting_Revenue_List_Table
 * @since 1.1.0
 */
class EAccounting_Revenue_List_Table extends EAccounting_List_Table {
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
				'singular' => 'revenue',
				'plural'   => 'revenues',
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
		global $wpdb;
		return ! (int) $wpdb->get_var( "SELECT COUNT(id) from {$wpdb->prefix}ea_transactions where type='income'" );
	}

	/**
	 * Render blank state.
	 *
	 * @return void
	 * @since 1.0.2
	 */
	protected function render_blank_state() {
		?>
		<div class="ea-empty-table">
			<p class="ea-empty-table__message">
				<?php echo esc_html__( 'Revenue is a direct and independent record of income or deposit where tax is inapplicable. You can create and manage your business incomes in any currency and can also affix account, category, and customer to each revenue.', 'wp-ever-accounting' ); ?>
			</p>
			<a href="<?php echo esc_url( eaccounting_admin_url(array('page'=>'ea-sales','tab'=>'revenues','action'=>'edit')));?>" class="button-primary ea-empty-table__cta"><?php _e( 'Add Revenue', 'wp-ever-accounting' ); ?></a>
			<a href="" class="button-secondary ea-empty-table__cta" target="_blank"><?php _e( 'Learn More', 'wp-ever-accounting' ); ?></a>
		</div>
		<?php
	}

	/**
	 * Define which columns to show on this screen.
	 *
	 * @return array
	 * @since 1.0.2
	 */
	public function define_columns() {
		return array(
			'cb'          => '<input type="checkbox" />',
			'date'        => __( 'Date', 'wp-ever-accounting' ),
			'amount'      => __( 'Amount', 'wp-ever-accounting' ),
			'account_id'  => __( 'Account', 'wp-ever-accounting' ),
			'category_id' => __( 'Category', 'wp-ever-accounting' ),
			'contact_id'  => __( 'Customer', 'wp-ever-accounting' ),
			'reference'   => __( 'Reference', 'wp-ever-accounting' ),
			'actions'     => __( 'Actions', 'wp-ever-accounting' ),
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
			'date'        => array( 'payment_date', false ),
			'amount'      => array( 'amount', false ),
			'account_id'  => array( 'account_id', false ),
			'category_id' => array( 'category_id', false ),
			'contact_id'  => array( 'contact_id', false ),
			'reference'   => array( 'reference', false ),
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
			'delete' => __( 'Delete', 'wp-ever-accounting' ),
		);
	}

	/**
	 * Define primary column.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_primary_column_name() {
		return 'date';
	}

	/**
	 * Renders the checkbox column in the revenues list table.
	 *
	 * @param Revenue $revenue The current object.
	 *
	 * @return string Displays a checkbox.
	 * @since  1.0.2
	 *
	 */
	function column_cb( $revenue ) {
		return sprintf( '<input type="checkbox" name="revenue_id[]" value="%d"/>', $revenue->get_id() );
	}

	/**
	 * This function renders most of the columns in the list table.
	 *
	 * @param string $column_name The name of the column
	 *
	 * @param Revenue $revenue
	 *
	 * @return string The column value.
	 * @since 1.0.2
	 *
	 */
	function column_default( $revenue, $column_name ) {
		$revenue_id = $revenue->get_id();
		switch ( $column_name ) {
			case 'date':
				$url   = eaccounting_admin_url(
					array(
						'tab'        => 'revenues',
						'action'     => 'edit',
						'revenue_id' => $revenue_id,
					)
				);
				$value = sprintf( '<a href="%1$s">%2$s</a>', esc_url( $url ), esc_html( eaccounting_date( $revenue->get_payment_date() ) ) );
				break;
			case 'amount':
				$value = $revenue->format_amount( $revenue->get_amount() );
				break;
			case 'account_id':
				$account = eaccounting_get_account( $revenue->get_account_id( 'edit' ) );
				$value = $account ? sprintf( '<a href="%1$s" target="_blank">%2$s</a>', esc_url( eaccounting_admin_url( array( 'page' => 'ea-banking', 'tab' => 'accounts', 'action' => 'view', 'account_id' => $revenue->get_account_id( 'edit' ) ) ) ), $account->get_name() ) : '&mdash;';// phpcs:enable
				break;
			case 'category_id':
				$category = eaccounting_get_category( $revenue->get_category_id( 'edit' ) );
				$value   = $category ? $category->get_name() : '&mdash;';
				break;
			case 'contact_id':
				$contact = eaccounting_get_customer( $revenue->get_contact_id( 'edit' ) );
				$value = $contact ? sprintf( '<a href="%1$s" target="_blank">%2$s</a>', esc_url( eaccounting_admin_url( array( 'page' => 'ea-sales', 'tab' => 'customers', 'action' => 'view', 'customer_id' => $revenue->get_contact_id( 'edit' ) ) ) ), $contact->get_name() ) : '&mdash;';// phpcs:enable
				break;
			case 'actions':
				$edit_url = eaccounting_admin_url(
					array(
						'tab'        => 'revenues',
						'action'     => 'edit',
						'revenue_id' => $revenue_id,
					)
				);
				$del_url  = eaccounting_admin_url(
					array(
						'tab'        => 'revenues',
						'action'     => 'delete',
						'revenue_id' => $revenue_id,
						'_wpnonce'      => wp_create_nonce('revenue-nonce'),
					)
				);
				$actions  = array(
					'edit'   => sprintf( '<a href="%s" class="dashicons dashicons-edit"></a>', esc_url( $edit_url ) ),
					'delete' => sprintf( '<a href="%s" class="dashicons dashicons-trash del"></a>', esc_url( $del_url ) ),
				);
				$value    = $this->row_actions( $actions );
				break;
			default:
				return parent::column_default( $revenue, $column_name );
		}

		return apply_filters( 'eaccounting_revenue_list_table_' . $column_name, $value, $revenue );
	}

	/**
	 * Renders the message to be displayed when there are no items.
	 *
	 * @return void
	 * @since  1.0.2
	 */
	function no_items() {
		_e( 'There is no revenues found.', 'wp-ever-accounting' );
	}

	/**
	 * Extra controls to be displayed between bulk actions and pagination.
	 *
	 * @param string $which
	 *
	 * @since 1.0.2
	 *
	 */
	protected function extra_tablenav_depricated( $which ) {
		if ( 'top' === $which ) {
			$account_id  = isset( $_GET['account_id'] ) ? absint( $_GET['account_id'] ) : '';
			$category_id = isset( $_GET['category_id'] ) ? absint( $_GET['category_id'] ) : '';
			$customer_id = isset( $_GET['customer_id'] ) ? absint( $_GET['customer_id'] ) : '';
			$start_date  = isset( $_GET['start_date'] ) ? eaccounting_clean( $_GET['start_date'] ) : '';
			$end_date    = isset( $_GET['end_date'] ) ? eaccounting_clean( $_GET['end_date'] ) : '';
			echo '<div class="alignleft actions ea-table-filter">';

			eaccounting_input_date_range(
				array(
					'start_date' => $start_date,
					'end_date'   => $end_date,
				)
			);

			eaccounting_account_dropdown(
				array(
					'name'    => 'account_id',
					'value'   => $account_id,
					'default' => '',
					'attr'    => array(
						'data-allow-clear' => true,
					),
					'creatable'	=> false,
				)
			);

			eaccounting_category_dropdown(
				array(
					'name'    => 'category_id',
					'value'   => $category_id,
					'default' => '',
					'type'    => 'income',
					'attr'    => array(
						'data-allow-clear' => true,
					),
				)
			);
			eaccounting_contact_dropdown(
				array(
					'name'        => 'customer_id',
					'value'       => $customer_id,
					'default'     => '',
					'placeholder' => __( 'Select Customer', 'wp-ever-accounting' ),
					'type'        => 'customer',
				)
			);

			submit_button( __( 'Filter', 'wp-ever-accounting' ), 'action', false, false );
			echo "\n";

			echo '</div>';
		}
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
					eaccounting_delete_revenue( $id );
					break;
				default:
					do_action( 'eaccounting_revenues_do_bulk_action_' . $this->current_action(), $id );
			}
		}

		if ( isset( $_GET['_wpnonce'] ) ) {
			wp_safe_redirect(
				remove_query_arg(
					array(
						'revenue_id',
						'action',
						'_wpnonce',
						'_wp_http_referer',
						'action2',
						'paged',
					)
				)
			);
			exit();
		}
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

		$page = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;

		$search  = isset( $_GET['s'] ) ? $_GET['s'] : '';
		$order   = isset( $_GET['order'] ) ? $_GET['order'] : 'DESC';
		$orderby = isset( $_GET['orderby'] ) ? $_GET['orderby'] : 'id';

		$start_date  = ! empty( $_GET['start_date'] ) ? eaccounting_clean( $_GET['start_date'] ) : '';
		$end_date    = ! empty( $_GET['end_date'] ) ? eaccounting_clean( $_GET['end_date'] ) : '';
		$category_id = ! empty( $_GET['category_id'] ) ? absint( $_GET['category_id'] ) : '';
		$account_id  = ! empty( $_GET['account_id'] ) ? absint( $_GET['account_id'] ) : '';
		$customer_id = ! empty( $_GET['customer_id'] ) ? absint( $_GET['customer_id'] ) : '';

		$per_page = $this->per_page;

		$args = wp_parse_args(
			$this->query_args,
			array(
				'per_page'    => $per_page,
				'page'        => $page,
				'number'      => $per_page,
				'offset'      => $per_page * ( $page - 1 ),
				'search'      => $search,
				'orderby'     => eaccounting_clean( $orderby ),
				'order'       => eaccounting_clean( $order ),
				'category_id' => $category_id,
				'account_id'  => $account_id,
				'contact_id'  => $customer_id,
			)
		);

		if ( ! empty( $start_date ) && ! empty( $end_date ) ) {
			$args['payment_date'] = array(
				'before' => date( 'Y-m-d', strtotime( $end_date ) ),
				'after'  => date( 'Y-m-d', strtotime( $start_date ) ),
			);
		}

		$args        = apply_filters( 'eaccounting_revenue_table_query_args', $args, $this );
		$this->items = eaccounting_get_revenues( $args );

		$this->total_count = eaccounting_get_revenues( array_merge( $args, array( 'count_total' => true ) ) );

		$this->set_pagination_args(
			array(
				'total_items' => $this->total_count,
				'per_page'    => $per_page,
				'total_pages' => ceil( $this->total_count / $per_page ),
			)
		);
	}
}
