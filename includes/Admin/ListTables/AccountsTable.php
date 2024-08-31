<?php

namespace EverAccounting\Admin\ListTables;

use EverAccounting\ListTable;
use EverAccounting\Models\Account;

defined( 'ABSPATH' ) || exit;

/**
 * Class AccountsTable.
 *
 * @since 1.0.0
 * @package EverAccounting\Admin\ListTables
 */
class AccountsTable extends ListTable {
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
					'singular' => 'account',
					'plural'   => 'accounts',
					'screen'   => get_current_screen(),
					'args'     => array(),
				)
			)
		);

		$this->base_url = admin_url( 'admin.php?page=eac-banking&tab=accounts' );
	}

	/**
	 * Prepares the list for display.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function prepare_items() {
		$this->process_actions();
		$per_page = $this->get_items_per_page( 'eac_accounts_per_page', 20 );
		$paged    = $this->get_pagenum();
		$search   = $this->get_request_search();
		$order_by = $this->get_request_orderby();
		$order    = $this->get_request_order();
		$args     = array(
			'limit'   => $per_page,
			'page'    => $paged,
			'search'  => $search,
			'orderby' => $order_by,
			'order'   => $order,
			'status'  => $this->get_request_status(),
		);

		/**
		 * Filter the query arguments for the list table.
		 *
		 * @param array $args An associative array of arguments.
		 *
		 * @since 1.0.0
		 */
		$args = apply_filters( 'ever_accounting_accounts_table_query_args', $args );

		$args['no_found_rows'] = false;
		$this->items           = Account::results( $args );
		$total                 = Account::count( $args );

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
			if ( eac_insert_account(
				array(
					'id'     => $id,
					'status' => 'active',
				)
			) ) {
				++$performed;
			}
		}
		if ( ! empty( $performed ) ) {
			// translators: %s: number of accounts.
			EAC()->flash->success( sprintf( __( '%s account(s) activated successfully.', 'wp-ever-accounting' ), number_format_i18n( $performed ) ) );
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
			if ( eac_insert_account(
				array(
					'id'     => $id,
					'status' => 'inactive',
				)
			) ) {
				++$performed;
			}
		}
		if ( ! empty( $performed ) ) {
			// translators: %s: number of accounts.
			EAC()->flash->success( sprintf( __( '%s account(s) deactivated successfully.', 'wp-ever-accounting' ), number_format_i18n( $performed ) ) );
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
			if ( eac_delete_account( $id ) ) {
				++$performed;
			}
		}
		if ( ! empty( $performed ) ) {
			// translators: %s: number of accounts.
			EAC()->flash->success( sprintf( __( '%s account(s) deleted successfully.', 'wp-ever-accounting' ), number_format_i18n( $performed ) ) );
		}
	}

	/**
	 * Outputs 'no users' message.
	 *
	 * @since 1.0.0
	 */
	public function no_items() {
		esc_html_e( 'No accounts found.', 'wp-ever-accounting' );
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
		$statuses     = array(
			'all'      => __( 'All', 'wp-ever-accounting' ),
			'active'   => __( 'Active', 'wp-ever-accounting' ),
			'inactive' => __( 'Inactive', 'wp-ever-accounting' ),
		);

		foreach ( $statuses as $status => $label ) {
			$link  = 'all' === $status ? $this->base_url : add_query_arg( 'status', $status, $this->base_url );
			$args  = 'all' === $status ? array() : array( 'status' => $status );
			$count = Account::count( $args );
			$label = sprintf( '%s <span class="count">(%s)</span>', esc_html( $label ), number_format_i18n( $count ) );

			$status_links[ $status ] = array(
				'url'     => $link,
				'label'   => $label,
				'current' => $current === $status,
			);
		}
		return $this->get_views_links( $status_links );
	}

	/**
	 * Retrieves an associative array of bulk actions available on this table.
	 *
	 * @since 1.0.0
	 *
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
		static $has_items;
		if ( ! isset( $has_items ) ) {
			$has_items = $this->has_items();
		}

		echo '<div class="alignleft actions">';
		if ( 'top' === $which ) {
			$this->currency_filter( 'active' );
			submit_button( __( 'Filter', 'wp-ever-accounting' ), '', 'filter_action', false );
		}
		echo '</div>';
	}

	/**
	 * Gets a list of columns for the list table.
	 *
	 * @since 1.0.0
	 *
	 * @return string[] Array of column titles keyed by their column name.
	 */
	public function get_columns() {
		return array(
			'cb'        => '<input type="checkbox" />',
			'name'      => __( 'Name', 'wp-ever-accounting' ),
			'balance'   => __( 'Balance', 'wp-ever-accounting' ),
			'number'    => __( 'Number', 'wp-ever-accounting' ),
			'bank_name' => __( 'Bank Name', 'wp-ever-accounting' ),
			'status'    => __( 'Status', 'wp-ever-accounting' ),
		);
	}

	/**
	 * Gets a list of sortable columns for the list table.
	 *
	 * @since 1.0.0
	 *
	 * @return array Array of sortable columns.
	 */
	protected function get_sortable_columns() {
		return array(
			'name'      => array( 'name', false ),
			'number'    => array( 'number', false ),
			'bank_name' => array( 'bank_name', false ),
			'balance'   => array( 'balance', false ),
			'status'    => array( 'status', false ),
		);
	}

	/**
	 * Define primary column.
	 *
	 * @since 1.0.2
	 * @return string
	 */
	public function get_primary_column_name() {
		return 'name';
	}

	/**
	 * Renders the checkbox column.
	 *
	 * @param Account $item The current object.
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
	 * @param Account $item The current object.
	 *
	 * @since  1.0.0
	 * @return string Displays the name.
	 */
	public function column_name( $item ) {
		return sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( 'edit', $item->id, $this->base_url ) ), wp_kses_post( $item->name ) );
	}

	/**
	 * Renders the balance column.
	 *
	 * @param Account $item The current object.
	 *
	 * @since  1.0.0
	 * @return string Displays the balance.
	 */
	public function column_balance( $item ) {
		return esc_html( $item->formatted_balance );
	}

	/**
	 * Generates and displays row actions links.
	 *
	 * @param Account $item The comment object.
	 * @param string  $column_name Current column name.
	 * @param string  $primary Primary column name.
	 *
	 * @since 1.0.0
	 * @return string Row actions output.
	 */
	protected function handle_row_actions( $item, $column_name, $primary ) {
		if ( $primary !== $column_name ) {
			return null;
		}
		$actions = array(
			'id' => sprintf( '#%d', esc_attr( $item->id ) ),
			'edit' => sprintf(
				'<a href="%s">%s</a>',
				esc_url( add_query_arg( 'edit', $item->id, $this->base_url ) ),
				__( 'Edit', 'wp-ever-accounting' )
			),
		);

		$actions['delete'] = sprintf(
			'<a href="%s" class="del">%s</a>',
			esc_url(
				wp_nonce_url(
					add_query_arg(
						array(
							'action' => 'delete',
							'id'     => $item->id,
						),
						$this->base_url
					),
					'bulk-' . $this->_args['plural']
				)
			),
			__( 'Delete', 'wp-ever-accounting' )
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
