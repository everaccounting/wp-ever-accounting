<?php

namespace EverAccounting\Admin\ListTables;

use EverAccounting\Models\Customer;

defined( 'ABSPATH' ) || exit;

/**
 * Class CustomersTable.
 *
 * @since 1.0.0
 * @package EverAccounting\Admin\ListTables
 */
class CustomersTable extends ListTable {
	/**
	 * Constructor.
	 *
	 * @param array $args An associative array of arguments.
	 *
	 * @see WP_List_Table::__construct() for more information on default arguments.
	 * @since 1.0.0
	 */
	public function __construct( $args = array() ) {
		parent::__construct(
			wp_parse_args(
				$args,
				array(
					'singular' => 'customer',
					'plural'   => 'customers',
					'screen'   => get_current_screen(),
					'args'     => array(),
				)
			)
		);
		$this->base_url = admin_url( 'admin.php?page=eac-sales&tab=customers' );
	}

	/**
	 * Prepares the list for display.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function prepare_items() {
		$this->process_actions();
		$per_page = $this->get_items_per_page( 'eac_customers_per_page', 20 );
		$paged    = $this->get_pagenum();
		$search   = $this->get_request_search();
		$order_by = $this->get_request_orderby();
		$order    = $this->get_request_order();
		$args     = array(
			'limit'    => $per_page,
			'page'     => $paged,
			'search'   => $search,
			'order_by' => $order_by,
			'order'    => $order,
			'status'   => $this->get_request_status(),
		);
		/**
		 * Filter the query arguments for the list table.
		 *
		 * @param array $args An associative array of arguments.
		 *
		 * @since 1.0.0
		 */
		$args = apply_filters( 'ever_accounting_customers_table_query_args', $args );

		$this->items = Customer::query( $args );
		$total       = Customer::count( $args );

		$this->set_pagination_args(
			array(
				'total_items' => $total,
				'per_page'    => $per_page,
			)
		);
	}

	/**
	 * handle bulk activate action.
	 *
	 * @param array $ids List of item IDs.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	protected function bulk_activate( $ids ) {
		$performed = 0;
		foreach ( $ids as $id ) {
			if ( eac_insert_customer(
				array(
					'id'     => $id,
					'status' => 'active',
				)
			) ) {
				++$performed;
			}
		}
		if ( ! empty( $performed ) ) {
			// translators: %s: number of customers.
			EAC()->flash->success( sprintf( __( '%s customer(s) activated successfully.', 'wp-ever-accounting' ), number_format_i18n( $performed ) ) );
		}
	}

	/**
	 * handle bulk delete action.
	 *
	 * @param array $ids List of item IDs.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	protected function bulk_delete( $ids ) {
		$performed = 0;
		foreach ( $ids as $id ) {
			if ( eac_delete_customer( $id ) ) {
				++$performed;
			}
		}
		if ( ! empty( $performed ) ) {
			// translators: %s: number of customers.
			EAC()->flash->success( sprintf( __( '%s customer(s) deleted successfully.', 'wp-ever-accounting' ), number_format_i18n( $performed ) ) );
		}
	}

	/**
	 * handle bulk deactivate action.
	 *
	 * @param array $ids List of item IDs.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	protected function bulk_deactivate( $ids ) {
		$performed = 0;
		foreach ( $ids as $id ) {
			if ( eac_insert_customer(
				array(
					'id'     => $id,
					'status' => 'inactive',
				)
			) ) {
				++$performed;
			}
		}
		if ( ! empty( $performed ) ) {
			// translators: %s: number of currencies.
			EAC()->flash->success( sprintf( __( '%s customer(s) deactivated successfully.', 'wp-ever-accounting' ), number_format_i18n( $performed ) ) );
		}
	}

	/**
	 * Outputs 'results' message.
	 *
	 * @since 1.0.0
	 */
	public function no_items() {
		esc_html_e( 'No customers found.', 'wp-ever-accounting' );
	}

	/**
	 * Returns an associative array listing all the views that can be used
	 * with this table.
	 *
	 * Provides a list of roles and user count for that role for easy
	 * filtering of the user table.
	 *
	 * @since 1.0.0
	 *
	 * @return string[] An array of HTML links keyed by their view.
	 */
	protected function get_views() {
		$current      = $this->get_request_status( 'all' );
		$status_links = array();
		$views        = array(
			// translators: %s: number of currencies.
			'all'      => _nx_noop( 'All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', 'list_table', 'wp-ever-accounting' ),
			// translators: %s: number of currencies.
			'active'   => _nx_noop( 'Active <span class="count">(%s)</span>', 'Active <span class="count">(%s)</span>', 'list_table', 'wp-ever-accounting' ),
			// translators: %s: number of currencies.
			'inactive' => _nx_noop( 'Inactive <span class="count">(%s)</span>', 'Inactive <span class="count">(%s)</span>', 'list_table', 'wp-ever-accounting' ),
		);
		foreach ( $views as $view => $label ) {
			$link  = 'all' === $view ? $this->base_url : add_query_arg( 'status', $view, $this->base_url );
			$args  = 'all' === $view ? array() : array( 'status' => $view );
			$count = Customer::count( $args );
			$label = sprintf( translate_nooped_plural( $label, $count, 'wp-ever-accounting' ), number_format_i18n( $count ) );

			$status_links[ $view ] = array(
				'url'     => $link,
				'label'   => $label,
				'current' => $current === $view,
			);
		}

		return $this->get_views_links( $status_links );
	}

	/**
	 * Retrieves an associative array of bulk actions available on this table.
	 *
	 * @since 1.0.0
	 * @return array Array of bulk action labels keyed by their action.
	 */
	protected function get_bulk_actions() {
		$actions = array(
			'delete'     => __( 'Delete', 'wp-ever-accounting' ),
			'activate'   => __( 'Activate', 'wp-ever-accounting' ),
			'deactivate' => __( 'Deactivate', 'wp-ever-accounting' ),
		);

		return $actions;
	}

	/**
	 * Outputs the controls to allow user roles to be changed in bulk.
	 *
	 * @param string $which Whether invoked above ("top") or below the table ("bottom").
	 *
	 * @since 1.0.0
	 * @return void
	 */
	protected function extra_tablenav( $which ) {
		// TODO: Need to include customersTable filters 'Select Month', 'Select Account', 'Select Category', 'Select Customer'.
		static $has_items;
		if ( ! isset( $has_items ) ) {
			$has_items = $this->has_items();
		}
	}

	/**
	 * Gets a list of columns for the list table.
	 *
	 * @since 1.0.0
	 * @return string[] Array of column titles keyed by their column name.
	 */
	public function get_columns() {
		return array(
			'cb'      => '<input type="checkbox" />',
			'name'    => __( 'Name', 'wp-ever-accounting' ),
			'email'   => __( 'Email', 'wp-ever-accounting' ),
			'phone'   => __( 'Phone', 'wp-ever-accounting' ),
			'country' => __( 'Country', 'wp-ever-accounting' ),
			'status'  => __( 'Status', 'wp-ever-accounting' ),
		);
	}

	/**
	 * Gets a list of sortable columns for the list table.
	 *
	 * @since 1.0.0
	 * @return array Array of sortable columns.
	 */
	protected function get_sortable_columns() {
		return array(
			'name'    => array( 'name', false ),
			'email'   => array( 'email', false ),
			'phone'   => array( 'phone', false ),
			'country' => array( 'country', false ),
			'status'  => array( 'status', false ),
		);
	}

	/**
	 * Define primary column.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_primary_column_name() {
		return 'name';
	}

	/**
	 * Renders the checkbox column.
	 *
	 * @param Customer $item The current object.
	 *
	 * @since  1.0.0
	 * @return string Displays a checkbox.
	 */
	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="id[]" value="%d"/>', esc_attr( $item->id ) );
	}

	/**
	 * Renders the name column.
	 *
	 * @param Customer $item The current object.
	 *
	 * @since  1.0.0
	 * @return string Displays the name.
	 */
	public function column_name( $item ) {
		return sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( 'edit', $item->id, $this->base_url ) ), wp_kses_post( $item->name ) );
	}

	/**
	 * Renders the address column.
	 *
	 * @param Customer $item The current object.
	 *
	 * @since 1.0.0
	 * @return string Displays the address.
	 */
	public function column_address( $item ) {
		$data = array(
			'company'   => $item->company,
			'address_1' => $item->address_1,
			'address_2' => $item->address_2,
			'city'      => $item->city,
			'state'     => $item->state,
			'postcode'  => $item->postcode,
			'country'   => $item->country,
		);

		return eac_get_formatted_address( $data );
	}

	/**
	 * Renders the country column.
	 *
	 * @param Customer $item The current object.
	 *
	 * @since 1.0.0
	 * @return string Displays the country.
	 */
	public function column_country( $item ) {
		return $item->country_name;
	}

	/**
	 * Generates and displays row actions links.
	 *
	 * @param Customer $item The customer object.
	 * @param string   $column_name Current column name.
	 * @param string   $primary Primary column name.
	 *
	 * @since 1.0.0
	 * @return string Row actions output.
	 */
	protected function handle_row_actions( $item, $column_name, $primary ) {
		if ( $primary !== $column_name ) {
			return null;
		}
		$actions = array(
			'id'   => sprintf( '#%d', esc_attr( $item->id ) ),
			'view' => sprintf(
				'<a href="%s">%s</a>',
				esc_url( add_query_arg( 'view', $item->id, $this->base_url ) ),
				__( 'View', 'wp-ever-accounting' )
			),
			'edit' => sprintf(
				'<a href="%s">%s</a>',
				esc_url( add_query_arg( 'edit', $item->id, $this->base_url ) ),
				__( 'Edit', 'wp-ever-accounting' )
			),
		);

		if ( 'active' === $item->status ) {
			$actions['deactivate'] = sprintf(
				'<a href="%s">%s</a>',
				esc_url(
					wp_nonce_url(
						add_query_arg(
							array(
								'action' => 'deactivate',
								'id'     => $item->id,
							),
							$this->base_url
						),
						'bulk-' . $this->_args['plural']
					)
				),
				__( 'Deactivate', 'wp-ever-accounting' )
			);
		} else {
			$actions['activate'] = sprintf(
				'<a href="%s">%s</a>',
				esc_url(
					wp_nonce_url(
						add_query_arg(
							array(
								'action' => 'activate',
								'id'     => $item->id,
							),
							$this->base_url
						),
						'bulk-' . $this->_args['plural']
					)
				),
				__( 'Activate', 'wp-ever-accounting' )
			);
		}

		return $this->row_actions( $actions );
	}
}
