<?php

namespace EverAccounting\Admin\ListTables;

use EverAccounting\Models\Currency;

defined( 'ABSPATH' ) || exit;

/**
 * Class CurrenciesTable.
 *
 * @since 1.0.0
 * @package EverAccounting\Admin\ListTables
 */
class CurrenciesTable extends ListTable {
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
					'singular' => 'currency',
					'plural'   => 'currencies',
					'screen'   => get_current_screen(),
					'args'     => array(),
				)
			)
		);

		$this->base_url = admin_url( 'admin.php?page=eac-misc&tab=currencies' );
	}

	/**
	 * Prepares the list for display.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function prepare_items() {
		$this->process_actions();
		$per_page = $this->get_items_per_page( 'eac_currencies_per_page', 20 );
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
		$args = apply_filters( 'ever_accounting_currencies_table_query_args', $args );

		$this->items = eac_get_currencies( $args );
		$total       = eac_get_currencies( $args, true );
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
			if ( eac_insert_currency(
				array(
					'id'     => $id,
					'status' => 'active',
				)
			) ) {
				++$performed;
			}
		}
		if ( ! empty( $performed ) ) {
			// translators: %s: number of currencies.
			EAC()->flash->success( sprintf( __( '%s currency(s) activated successfully.', 'wp-ever-accounting' ), number_format_i18n( $performed ) ) );
			$note = __( '<strong>ALERT:</strong> Please update the exchange rate for the activated currencies before using the currency in transactions.', 'wp-ever-accounting' );
			EAC()->flash->info( $note );
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
			if ( eac_insert_currency(
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
			EAC()->flash->success( sprintf( __( '%s currency(s) deactivated successfully.', 'wp-ever-accounting' ), number_format_i18n( $performed ) ) );
		}
	}

	/**
	 * Outputs 'no users' message.
	 *
	 * @since 1.0.0
	 */
	public function no_items() {
		esc_html_e( 'No currencies found.', 'wp-ever-accounting' );
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
	 * @global string $role
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
			$count = eac_get_currencies( $args, true );
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
	 *
	 * @return array Array of bulk action labels keyed by their action.
	 */
	protected function get_bulk_actions() {
		$actions = array(
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
			'cb'            => '<input type="checkbox" />',
			'name'          => __( 'Name', 'wp-ever-accounting' ),
			'code'          => __( 'Code', 'wp-ever-accounting' ),
			'symbol'        => __( 'Symbol', 'wp-ever-accounting' ),
			'exchange_rate' => __( 'Exchange Rate', 'wp-ever-accounting' ),
			'status'        => __( 'Status', 'wp-ever-accounting' ),
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
			'name'          => array( 'name', true ),
			'code'          => array( 'code', true ),
			'symbol'        => array( 'symbol', true ),
			'exchange_rate' => array( 'exchange_rate', true ),
			'status'        => array( 'status', true ),
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
	 * @param Currency $item The current object.
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
	 * @param Currency $item The current object.
	 *
	 * @since  1.0.0
	 * @return string Displays the name.
	 */
	public function column_name( $item ) {
		return sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( 'edit', $item->id, $this->base_url ) ), wp_kses_post( $item->name ) );
	}

	/**
	 * Generates and displays row actions links.
	 *
	 * @param Currency $item The comment object.
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