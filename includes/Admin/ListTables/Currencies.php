<?php

namespace EverAccounting\Admin\ListTables;

defined( 'ABSPATH' ) || exit;

/**
 * Class Currencies.
 *
 * @since 1.0.0
 * @package EverAccounting\Admin\ListTables
 */
class Currencies extends ListTable {
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

		$this->base_url = admin_url( 'admin.php?page=eac-settings&section=currencies' );
	}

	/**
	 * Prepares the list for display.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function prepare_items() {
		$this->process_actions();
		$order_by = $this->get_request_orderby();
		$order    = $this->get_request_order();

		$currencies = eac_get_currencies();
		// sort currencies.
		uasort(
			$currencies,
			function ( $a, $b ) use ( $order_by, $order ) {
				if ( in_array( $order_by, array( 'rate', 'precision' ), true ) ) {
					$comparison = $a[ $order_by ] - $b[ $order_by ];
				} elseif ( isset( $a[ $order_by ] ) && isset( $b[ $order_by ] ) ) {
					$comparison = strcmp( $a[ $order_by ], $b[ $order_by ] );
				} else {
					$comparison = 0;
				}
				return 'asc' === $order ? $comparison : - $comparison;
			}
		);

		$this->items = $currencies;
		$total_items = count( $currencies );
		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $total_items,
			)
		);
	}

	/**
	 * Checks if the current request has a bulk action. If that is the case it will validate and will
	 * execute the bulk method handler. Regardless if the action is valid or not it will redirect to
	 * the previous page removing the current arguments that makes this request a bulk action.
	 */
	protected function process_actions() {
		$this->_column_headers = array( $this->get_columns(), get_hidden_columns( $this->screen ), $this->get_sortable_columns() );

		// Detect when a bulk action is being triggered.
		$action = $this->current_action();
		if ( ! empty( $action ) ) {

			check_admin_referer( 'bulk-' . $this->_args['plural'] );

			$ids    = isset( $_GET['code'] ) ? map_deep( wp_unslash( $_GET['code'] ), 'sanitize_key' ) : array();
			$ids    = wp_parse_list( $ids );
			$method = 'bulk_' . $action;
			if ( method_exists( $this, $method ) && ! empty( $ids ) ) {
				$this->$method( $ids );
			}
		}

		if ( isset( $_GET['_wpnonce'] ) && isset( $_SERVER['REQUEST_URI'] ) ) {
			wp_safe_redirect(
				remove_query_arg(
					array( '_wp_http_referer', '_wpnonce', 'id', 'action', 'action2' ),
					esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) )
				)
			);
			exit;
		}
	}

	/**
	 * handle bulk delete action.
	 *
	 * @param array $codes List of item codes.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	protected function bulk_delete( $codes ) {
		$performed  = 0;
		$currencies = get_option( 'eac_currencies', array() );
		foreach ( $codes as $code ) {
			$code = strtoupper( $code );
			if ( isset( $currencies[ $code ] ) ) {
				unset( $currencies[ $code ] );
				++$performed;
			}
		}
		update_option( 'eac_currencies', $currencies );
		if ( ! empty( $performed ) ) {
			// translators: %s: number of items deleted.
			EAC()->flash->success( sprintf( __( '%s currencies(s) deleted successfully.', 'wp-ever-accounting' ), number_format_i18n( $performed ) ) );
		}
	}

	/**
	 * Outputs 'no items' message.
	 *
	 * @since 1.0.0
	 */
	public function no_items() {
		esc_html_e( 'No configured currencies found.', 'wp-ever-accounting' );
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
			'name'               => __( 'Name', 'wp-ever-accounting' ),
			'rate'               => __( 'Rate', 'wp-ever-accounting' ),
			'precision'          => __( 'Precision', 'wp-ever-accounting' ),
			'decimal_separator'  => __( 'Decimal Separator', 'wp-ever-accounting' ),
			'thousand_separator' => __( 'Thousand Separator', 'wp-ever-accounting' ),
			'position'           => __( 'Position', 'wp-ever-accounting' ),
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
			'name'               => array( 'name', true ),
			'rate'               => array( 'rate', true ),
			'precision'          => array( 'precision', true ),
			'decimal_separator'  => array( 'decimal_separator', true ),
			'thousand_separator' => array( 'thousand_separator', true ),
			'position'           => array( 'position', true ),
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
	 * @param array $item The current object.
	 *
	 * @since 1.0.0
	 *
	 * @return string Displays a checkbox.
	 */
	public function column_cb( $item ) {
		$base = eac_base_currency();
		if ( $base === $item['code'] ) {
			return '&mdash;';
		}

		return sprintf( '<input type="checkbox" name="code[]" value="%s"/>', esc_attr( $item['code'] ) );
	}

	/**
	 * Renders the name column.
	 *
	 * @param array $item The current object.
	 *
	 * @since 1.0.0
	 *
	 * @return string Displays the name.
	 */
	public function column_name( $item ) {
		return empty( $item['formatted_name'] ) ? '&mdash;' : esc_html( $item['formatted_name'] );
	}

	/**
	 * Renders the rate column.
	 *
	 * @param array $item The current object.
	 *
	 * @since 1.0.0
	 *
	 * @return string Displays the rate.
	 */
	public function column_rate( $item ) {
		return number_format( $item['rate'], 4 );
	}

	/**
	 * Renders the precision column.
	 *
	 * @param array $item The current object.
	 *
	 * @since 1.0.0
	 *
	 * @return string Displays the precision.
	 */
	public function column_precision( $item ) {
		return $item['precision'];
	}

	/**
	 * Renders the decimal separator column.
	 *
	 * @param array $item The current object.
	 *
	 * @since 1.0.0
	 *
	 * @return string Displays the decimal separator.
	 */
	public function column_decimal_separator( $item ) {
		return $item['decimal_separator'];
	}

	/**
	 * Renders the thousand separator column.
	 *
	 * @param array $item The current object.
	 *
	 * @since 1.0.0
	 *
	 * @return string Displays the thousand separator.
	 */
	public function column_thousand_separator( $item ) {
		return $item['thousand_separator'];
	}

	/**
	 * Renders the position column.
	 *
	 * @param array $item The current object.
	 *
	 * @since 1.0.0
	 *
	 * @return string Displays the position.
	 */
	public function column_position( $item ) {
		return $item['position'];
	}

	/**
	 * Generates and displays row actions links.
	 *
	 * @param array  $item The object.
	 * @param string $column_name Current column name.
	 * @param string $primary Primary column name.
	 *
	 * @since 1.0.0
	 * @return string Row actions output.
	 */
	protected function handle_row_actions( $item, $column_name, $primary ) {
		if ( $primary !== $column_name || eac_base_currency() === $item['code'] ) {
			return null;
		}
		$actions = array(
			'edit'   => sprintf(
				'<a href="%s">%s</a>',
				esc_url(
					add_query_arg(
						array(
							'action'   => 'edit',
							'currency' => $item['code'],
						),
						$this->base_url
					)
				),
				__( 'Edit', 'wp-ever-accounting' )
			),
			'delete' => sprintf(
				'<a href="%s">%s</a>',
				esc_url(
					wp_nonce_url(
						add_query_arg(
							array(
								'action' => 'delete',
								'code'   => $item['code'],
							),
							$this->base_url
						),
						'bulk-currencies'
					)
				),
				__( 'Delete', 'wp-ever-accounting' )
			),
		);

		return $this->row_actions( $actions );
	}
}