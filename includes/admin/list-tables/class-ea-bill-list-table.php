<?php
/**
 * Bills Admin List Table
 *
 * @since       1.1.0
 * @subpackage  EverAccounting\Admin\ListTables
 * @package     EverAccounting
 */

use EverAccounting\Models\Bill;

defined( 'ABSPATH' ) || exit();

if ( ! class_exists( '\EAccounting_List_Table' ) ) {
	require_once dirname( __FILE__ ) . '/class-ea-admin-list-table.php';
}

/**
 * Class EAccounting_Bill_List_Table
 * @since 1.1.0
 */
class EAccounting_Bill_List_Table extends EAccounting_List_Table {
	/**
	 * Default number of items to show per page
	 *
	 * @since 1.1.0
	 * @var string
	 */
	public $per_page = 20;

	/**
	 * Total number of item found
	 *
	 * @since 1.1.0
	 * @var int
	 */
	public $total_count;

	/**
	 * Get things started
	 *
	 * @param array $args Optional. Arbitrary display and query arguments to pass through the list table. Default empty array.
	 *
	 * @since  1.1.0
	 *
	 * @see WP_List_Table::__construct()
	 */
	public function __construct( $args = array() ) {
		$args = (array) wp_parse_args(
			$args,
			array(
				'singular' => 'bill',
				'plural'   => 'bills',
			)
		);

		parent::__construct( $args );
	}

	/**
	 * Check if there is contents in the database.
	 *
	 * @return bool
	 * @since 1.1.0
	 */
	public function is_empty() {
		global $wpdb;

		return ! (int) $wpdb->get_var( "SELECT COUNT(id) from {$wpdb->prefix}ea_documents where type='bill'" );
	}

	/**
	 * Render blank state.
	 *
	 * @return void
	 * @since 1.1.0
	 */
	protected function render_blank_state() {
		?>
		<div class="ea-empty-table">
			<p class="ea-empty-table__message">
				<?php echo  esc_html__( 'A bill functions as a commercial document that itemizes and records purchases. Bill specifies a transaction between a buyer and a seller and possesses all the necessary information required. In the document, Taxes can be included or excluded.', 'wp-ever-accounting' ); ?>
			</p>
			<a href="
			<?php
			echo esc_url(
				eaccounting_admin_url(
					array(
						'page'   => 'ea-expenses',
						'tab'    => 'bills',
						'action' => 'edit',
					)
				)
			);
			?>
			" class="button-primary ea-empty-table__cta"><?php _e( 'Add Bills', 'wp-ever-accounting' ); ?></a>
			<a href="" class="button-secondary ea-empty-table__cta" target="_blank"><?php _e( 'Learn More', 'wp-ever-accounting' ); ?></a>
		</div>
		<?php
	}

	/**
	 * Define which columns to show on this screen.
	 *
	 * @return array
	 * @since 1.1.0
	 */
	public function define_columns() {
		return array(
			'cb'          => '<input type="checkbox" />',
			'bill_number' => __( 'Number', 'wp-ever-accounting' ),
			'total'       => __( 'Total', 'wp-ever-accounting' ),
			'name'        => __( 'Vendor', 'wp-ever-accounting' ),
			'issue_date'  => __( 'Bill Date', 'wp-ever-accounting' ),
			'due_date'    => __( 'Due Date', 'wp-ever-accounting' ),
			'status'      => __( 'Status', 'wp-ever-accounting' ),
			'actions'     => __( 'Actions', 'wp-ever-accounting' ),
		);
	}

	/**
	 * Define sortable columns.
	 *
	 * @return array
	 * @since 1.1.0
	 */
	protected function define_sortable_columns() {
		return array(
			'bill_number' => array( 'bill_number', false ),
			'name'        => array( 'name', false ),
			'total'       => array( 'total', false ),
			'issue_date'  => array( 'issue_date', false ),
			'due_date'    => array( 'due_date', false ),
			'status'      => array( 'status', false ),
		);
	}

	/**
	 * Define bulk actions
	 *
	 * @return array
	 * @since 1.1.0
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
	 * @since 1.1.0
	 */
	public function get_primary_column_name() {
		return 'bill_number';
	}

	/**
	 * Renders the checkbox column in the accounts list table.
	 *
	 * @param Bill $bill The current account object.
	 *
	 * @return string Displays a checkbox.
	 * @since  1.1.0
	 *
	 */
	function column_cb( $bill ) {
		return sprintf( '<input type="checkbox" name="bill_id[]" value="%d"/>', $bill->get_id() );
	}

	/**
	 * This function renders most of the columns in the list table.
	 *
	 * @param string $column_name The name of the column
	 *
	 * @param Bill $bill
	 *
	 * @return string The column value.
	 * @since 1.1.0
	 *
	 */
	function column_default( $bill, $column_name ) {
		$bill_id = $bill->get_id();
		switch ( $column_name ) {
			case 'bill_number':
				$bill_number = $bill->get_bill_number();

				$value = sprintf(
					'<a href="%1$s">%2$s</a>',
					esc_url(
						eaccounting_admin_url(
							array(
								'action'  => 'view',
								'tab'     => 'bills',
								'bill_id' => $bill_id,
							)
						)
					),
					$bill_number
				);
				break;
			case 'total':
				$value = eaccounting_price( $bill->get_total(), $bill->get_currency_code() );
				break;
			case 'name':
				$value = sprintf( '<a href="%1$s" target="_blank">%2$s</a>', esc_url( eaccounting_admin_url( array( 'page' => 'ea-expenses', 'tab' => 'vendors', 'action' => 'view', 'vendor_id' => $bill->get_contact_id() ) ) ), $bill->get_name() );// phpcs:enable
				break;
			case 'issue_date':
				$value = eaccounting_format_datetime( $bill->get_issue_date(), 'Y-m-d' );
				break;
			case 'due_date':
				$value = eaccounting_format_datetime( $bill->get_due_date(), 'Y-m-d' );
				break;
			case 'status':
				$value = sprintf( '<span class="bill-status %s">%s</span>', $bill->get_status(), $bill->get_status_nicename() );
				break;
			case 'actions':
				$edit_url = eaccounting_admin_url(
					array(
						'tab'     => 'bills',
						'action'  => 'edit',
						'bill_id' => $bill_id,
					)
				);
				$del_url  = eaccounting_admin_url(
					array(
						'tab'      => 'bills',
						'action'   => 'delete',
						'_wpnonce' => wp_create_nonce( 'bill-nonce' ),
						'bill_id'  => $bill_id,
					)
				);
				$actions  = array(
					'edit'   => sprintf( '<a href="%s" class="dashicons dashicons-edit"></a>', esc_url( $edit_url ) ),
					'delete' => sprintf( '<a href="%s" class="dashicons dashicons-trash del"></a>', esc_url( $del_url ) ),
				);
				$value    = $this->row_actions( $actions );
				break;
			default:
				return parent::column_default( $bill, $column_name );
		}

		return apply_filters( 'eaccounting_bill_list_table_' . $column_name, $value, $bill );
	}

	/**
	 * Renders the message to be displayed when there are no items.
	 *
	 * @return void
	 * @since  1.1.0
	 */
	function no_items() {
		_e( 'There is no bills found.', 'wp-ever-accounting' );
	}

	/**
	 * Process the bulk actions
	 *
	 * @return void
	 * @since 1.1.0
	 */
	public function process_bulk_action() {
		if ( empty( $_REQUEST['_wpnonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'bulk-bills' ) && ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'bill-nonce' ) ) {
			return;
		}

		$ids = isset( $_GET['bill_id'] ) ? $_GET['bill_id'] : false;

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
				case 'delete':
					eaccounting_delete_bill( $id );
					break;
				default:
					do_action( 'eaccounting_bills_do_bulk_action_' . $this->current_action(), $id );
			}
		}

		if ( isset( $_GET['_wpnonce'] ) ) {
			wp_safe_redirect(
				remove_query_arg(
					array(
						'bill_id',
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
	 * @since 1.1.0
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
				'status'   => $status,
				'search'   => $search,
				'orderby'  => eaccounting_clean( $orderby ),
				'order'    => eaccounting_clean( $order ),
			)
		);

		$args              = apply_filters( 'eaccounting_bill_table_query_args', $args, $this );
		$this->items       = eaccounting_get_bills( $args );
		$this->total_count = eaccounting_get_bills( array_merge( $args, array( 'count_total' => true ) ) );
		$this->set_pagination_args(
			array(
				'total_items' => $this->total_count,
				'per_page'    => $per_page,
				'total_pages' => ceil( $this->total_count / $per_page ),
			)
		);
	}
}