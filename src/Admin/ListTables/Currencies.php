<?php

namespace EverAccounting\Admin\ListTables;

defined( 'ABSPATH' ) || exit;

/**
 * Class Currencies
 *
 * @package EverAccounting\Admin\ListTables
 */
class Currencies extends ListTable {
	/**
	 * Get things started
	 *
	 * @param array $args Optional. Arbitrary display and query arguments to pass through the list table. Default empty array.
	 *
	 * @see WP_List_Table::__construct()
	 * @since  1.0.2
	 */
	public function __construct( $args = array() ) {
		$args         = (array) wp_parse_args(
			$args,
			array(
				'singular' => 'currency',
				'plural'   => 'currencies',
			)
		);
		$this->screen = get_current_screen();
		parent::__construct( $args );
	}

	/**
	 * Retrieve all the data for the table.
	 *
	 * @since 1.0.2
	 * @return void
	 */
	public function prepare_items() {
		$columns               = $this->get_columns();
		$sortable              = $this->get_sortable_columns();
		$hidden                = $this->get_hidden_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		$search                = $this->get_search();
		$order_by              = $this->get_orderby( 'status' );
		$order                 = $this->get_order( 'asc' );
		$base_currency         = eac_get_base_currency();

		$currencies = eac_get_currencies();
		if ( isset( $currencies[ $base_currency ] ) ) {
			unset( $currencies[ $base_currency ] );
		}
		// if search string is present, filter the currencies by the search string.
		if ( ! empty( $search ) ) {
			$currencies = array_filter(
				$currencies,
				function ( $currency ) use ( $search ) {
					return false !== stripos( $currency['name'], $search ) || false !== stripos( $currency['code'], $search );
				}
			);
		}

		// if order by is present, sort the currencies by the order by.
		if ( ! empty( $order_by ) ) {
			usort(
				$currencies,
				function ( $a, $b ) use ( $order_by, $order ) {
					if ( 'asc' === $order ) {
						if ( $a[ $order_by ] === $b[ $order_by ] ) {
							return 0;
						}

						return ( $a[ $order_by ] < $b[ $order_by ] ) ? - 1 : 1;
					}

					if ( $a[ $order_by ] === $b[ $order_by ] ) {
						return 0;
					}

					return ( $a[ $order_by ] > $b[ $order_by ] ) ? - 1 : 1;
				}
			);
		}

		$this->items       = $currencies;
		$this->total_count = count( $currencies );

		$this->set_pagination_args(
			array(
				'total_items' => $this->total_count,
				'per_page'    => $this->total_count,
				'total_pages' => 1,
			)
		);
	}

	/**
	 * No items found text.
	 *
	 * @since 1.0.2
	 * @return void
	 */
	public function no_items() {
		esc_html_e( 'No currencies found. Note: Base currency is not listed here.', 'wp-ever-accounting' );
	}

	/**
	 * Adds the order and product filters to the licenses list.
	 *
	 * @param string $which The location of the extra table nav markup: 'top' or 'bottom'.
	 *
	 * @since 1.0.2
	 */
	protected function extra_tablenav( $which ) {
		if ( 'top' !== $which ) {
			return;
		}
	}

	/**
	 * Process bulk action.
	 *
	 * @param string $doaction Action name.
	 *
	 * @since 1.0.2
	 */
	public function process_bulk_action( $doaction ) {
		if ( ! empty( $doaction ) ) {
			$currency   = eac_get_input_var( 'currency' );
			$currencies = eac_get_input_var( 'currencies' );

			if ( ! empty( $id ) ) {
				$currencies = wp_parse_list( $currency );
				$doaction = ( - 1 !== $_REQUEST['action'] ) ? $_REQUEST['action'] : $_REQUEST['action2']; // phpcs:ignore
			} elseif ( ! empty( $ids ) ) {
				$currencies = array_map( 'sanitize_text_field', $currencies );
			} elseif ( wp_get_referer() ) {
				wp_safe_redirect( wp_get_referer() );
				exit;
			}

			var_dump($currencies);

			foreach ( $currencies as $currency ) {
				switch ( $doaction ) {
					case 'activate':
						eac_update_currency(
							array(
								'code'   => $currency,
								'status' => 'active',
							)
						);
						break;
					case 'deactivate':
						eac_update_currency(
							array(
								'code'   => $currency,
								'status' => 'inactive',
							)
						);
						break;
				}
			}

			// Based on the action add notice.
			switch ( $doaction ) {
				case 'enable':
					$notice = __( 'Currency(s) enabled successfully.', 'wp-ever-accounting' );
					eac_add_notice( $notice, 'success' );
					break;
				case 'disable':
					$notice = __( 'Currency(s) disabled successfully.', 'wp-ever-accounting' );
					eac_add_notice( $notice, 'success' );
					break;
			}

//			wp_safe_redirect( admin_url( 'admin.php?page=eac-settings&tab=currencies' ) );
//			exit();
		}
	}


	/**
	 * Define which columns to show on this screen.
	 *
	 * @since 1.0.2
	 * @return array
	 */
	public function get_columns() {
		return array(
			'cb'     => '<input type="checkbox" />',
			'name'   => __( 'Name', 'wp-ever-accounting' ),
			'code'   => __( 'Code', 'wp-ever-accounting' ),
			'symbol' => __( 'Symbol', 'wp-ever-accounting' ),
			'rate'   => __( 'Rate', 'wp-ever-accounting' ),
			'status' => __( 'Status', 'wp-ever-accounting' ),
		);
	}

	/**
	 * Define sortable columns.
	 *
	 * @since 1.0.2
	 * @return array
	 */
	protected function get_sortable_columns() {
		return array(
			'name'   => array( 'name', true ),
			'code'   => array( 'code', true ),
			'symbol' => array( 'symbol', true ),
			'rate'   => array( 'rate', true ),
			'status' => array( 'status', true ),
		);
	}

	/**
	 * Get bulk actions
	 *
	 * since 1.0.0
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		return array(
			'enable'  => __( 'Enable', 'wp-ever-accounting' ),
			'disable' => __( 'Disable', 'wp-ever-accounting' ),
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
	 * Renders the checkbox column in the customers list table.
	 *
	 * @param array $item The current account object.
	 *
	 * @since  1.0.2
	 * @return string Displays a checkbox.
	 */
	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="currencies[]" value="%s"/>', esc_attr( $item['code'] ) );
	}

	/**
	 * Renders the name column in the accounts list table.
	 *
	 * @param array $item The current account object.
	 *
	 * @since  1.0.2
	 * @return string Displays a checkbox.
	 */
	public function column_name( $item ) {
		$args        = array( 'currency' => $item['code'] );
		$edit_url    = $this->get_current_url( array_merge( $args, array( 'action' => 'edit' ) ) );
		$enable_url  = $this->get_current_url( array_merge( $args, array( 'action' => 'enable' ) ) );
		$disable_url = $this->get_current_url( array_merge( $args, array( 'action' => 'disable' ) ) );
		$actions     = array(
			'edit'    => sprintf( '<a href="%s">%s</a>', esc_url( $edit_url ), __( 'Edit', 'wp-ever-accounting' ) ),
			'enable'  => sprintf( '<a href="%s">%s</a>', esc_url( $enable_url ), __( 'Enable', 'wp-ever-accounting' ) ),
			'disable' => sprintf( '<a href="%s">%s</a>', esc_url( $disable_url ), __( 'Disable', 'wp-ever-accounting' ) ),
		);
		if ( 'active' === $item['status'] ) {
			unset( $actions['enable'] );
		} else {
			unset( $actions['disable'] );
		}

		return sprintf( '<a href="%s">%s</a> %s', esc_url( $edit_url ), esc_html( $item['name'] ), $this->row_actions( $actions ) );
	}

	/**
	 * This function renders most of the columns in the list table.
	 *
	 * @param array  $item The current account object.
	 * @param string $column_name The name of the column.
	 *
	 * @since 1.0.2
	 * @return string The column value.
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'rate':
				$value = empty( $item['rate'] ) ? '&mdash;' : esc_html( eac_convert_money( $item['rate'], eac_get_base_currency(), $item['rate'], null, true ) );
				break;
			case 'status':
				$value = esc_html( $item['status'] );
				$value = $value ? sprintf( '<span class="eac-status-label %s">%s</span>', esc_attr( $item['status'] ), $value ) : '&mdash;';
				break;
			default:
				$value = parent::column_default( $item, $column_name );
				break;
		}

		return $value;
	}
}
