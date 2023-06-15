<?php

namespace EverAccounting\Admin\ListTables;

use EverAccounting\Models\Currency;

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
	 * @return void
	 * @since 1.0.2
	 */
	public function prepare_items() {
		$columns               = $this->get_columns();
		$sortable              = $this->get_sortable_columns();
		$hidden                = $this->get_hidden_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$args = array(
			'limit'   => $this->get_per_page(),
			'offset'  => $this->get_offset(),
			'status'  => $this->get_status(),
			'search'  => $this->get_search(),
			'order'   => $this->get_order( 'ASC' ),
			'orderby' => $this->get_orderby( 'status' ),
		);

		$this->items       = eac_get_currencies( $args );
		$this->total_count = eac_get_currencies( $args, true );

		$this->set_pagination_args(
			array(
				'total_items' => $this->total_count,
				'per_page'    => $this->total_count,
				'total_pages' => ceil( $this->total_count / $this->get_per_page() ),
			)
		);
	}

	/**
	 * No items found text.
	 *
	 * @return void
	 * @since 1.0.2
	 */
	public function no_items() {
		esc_html_e( 'No currencies found.', 'wp-ever-accounting' );
	}

	/**
	 * Adds the order and item filters to the licenses list.
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
		if ( ! empty( $doaction ) && check_admin_referer( 'bulk-' . $this->_args['plural'] ) ) {
			$currency_id  = eac_get_input_var( 'currency_id' );
			$currency_ids = eac_get_input_var( 'currency_ids' );

			if ( ! empty( $id ) ) {
				$currency_ids = wp_parse_list( $currency_id );
				$doaction = (-1 !== $_REQUEST['action']) ? $_REQUEST['action'] : $_REQUEST['action2']; // phpcs:ignore
			} elseif ( ! empty( $ids ) ) {
				$currency_ids = array_map( 'absint', $currency_ids );
			} elseif ( wp_get_referer() ) {
				wp_safe_redirect( wp_get_referer() );
				exit;
			}

			foreach ( $currency_ids as $currency_id ) {
				switch ( $doaction ) {
					case 'activate':
						eac_insert_currency(
							array(
								'code'   => $currency_id,
								'status' => 'active',
							)
						);
						break;
					case 'deactivate':
						eac_insert_currency(
							array(
								'code'   => $currency_id,
								'status' => 'inactive',
							)
						);
						break;
					case 'delete':
						eac_delete_currency( $currency_id );
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

			wp_safe_redirect( admin_url( 'admin.php?page=eac-settings&section=currencies' ) );
			exit();
		}
	}


	/**
	 * Define which columns to show on this screen.
	 *
	 * @return array
	 * @since 1.0.2
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
	 * @return array
	 * @since 1.0.2
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
			'activate'   => __( 'Activate', 'wp-ever-accounting' ),
			'deactivate' => __( 'Deactivate', 'wp-ever-accounting' ),
		);
	}

	/**
	 * Define primary column.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_primary_column_name() {
		 return 'name';
	}

	/**
	 * Renders the checkbox column in the customers list table.
	 *
	 * @param Currency $item The current account object.
	 *
	 * @return string Displays a checkbox.
	 * @since  1.0.2
	 */
	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="currency_id[]" value="%s" %s/>', esc_attr( $item->get_id() ), disabled( $item->get_code(), eac_get_base_currency(), false ) );
	}

	/**
	 * Renders the name column in the accounts list table.
	 *
	 * @param Currency $item The current account object.
	 *
	 * @return string Displays a checkbox.
	 * @since  1.0.2
	 */
	public function column_name( $item ) {
		$args           = array( 'currency_id' => $item->get_id() );
		$edit_url       = $this->get_current_url( array_merge( $args, array( 'action' => 'edit' ) ) );
		$activate_url   = $this->get_current_url( array_merge( $args, array( 'action' => 'activate' ) ) );
		$deactivate_url = $this->get_current_url( array_merge( $args, array( 'action' => 'deactivate' ) ) );
		$delete_url     = $this->get_current_url( array_merge( $args, array( 'action' => 'delete' ) ) );
		$actions        = array(
			'edit'       => sprintf( '<a href="%s">%s</a>', esc_url( $edit_url ), __( 'Edit', 'wp-ever-accounting' ) ),
			'activate'   => sprintf( '<a href="%s">%s</a>', esc_url( wp_nonce_url( $activate_url, 'bulk-currencies' ) ), __( 'Enable', 'wp-ever-accounting' ) ),
			'deactivate' => sprintf( '<a href="%s">%s</a>', esc_url( wp_nonce_url( $deactivate_url, 'bulk-currencies' ) ), __( 'Disable', 'wp-ever-accounting' ) ),
			'delete'     => sprintf( '<a href="%s">%s</a>', esc_url( wp_nonce_url( $delete_url, 'bulk-currencies' ) ), __( 'Delete', 'wp-ever-accounting' ) ),
		);

		if ( 'active' === $item->get_status() ) {
			unset( $actions['activate'] );
		} else {
			unset( $actions['deactivate'] );
		}

		// if not base then show enable/disable and delete link.
		if ( $item->get_code() == eac_get_base_currency() ) {
			unset( $actions['delete'] );
			unset( $actions['activate'] );
			unset( $actions['deactivate'] );
		}

		return sprintf( '<a href="%s">%s</a> %s', esc_url( $edit_url ), esc_html( $item->get_name() ), $this->row_actions( $actions ) );
	}

	/**
	 * This function renders most of the columns in the list table.
	 *
	 * @param Currency $item The current account object.
	 * @param string   $column_name The name of the column.
	 *
	 * @return string The column value.
	 * @since 1.0.2
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'rate':
				$value = esc_html( eac_sanitize_number( $item->get_rate(), 8 ) );
				break;
			case 'status':
				$value = esc_html( $item->get_status( 'view' ) );
				$value = $value ? sprintf( '<span class="eac-status-label %s">%s</span>', esc_attr( $item->get_status() ), $value ) : '&mdash;';
				break;
			default:
				$value = parent::column_default( $item, $column_name );
				break;
		}

		return $value;
	}
}
