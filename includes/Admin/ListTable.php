<?php

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Class Table.
 *
 * @since 1.0.0
 * @package EverAccounting\Admin\ListTables
 */
abstract class ListTable extends \WP_List_Table {
	/**
	 * Current page URL.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $base_url;

	/**
	 * Constructor.
	 *
	 * @param array $args An associative array of arguments.
	 *
	 * @see WP_List_Table::__construct() for more information on default arguments.
	 * @since 1.0.0
	 */
	public function __construct( $args = array() ) {
		parent::__construct( $args );
		remove_filter( "manage_{$this->screen->id}_columns", array( $this, 'get_columns' ), 0 );
	}

	/**
	 * Return the sortable column specified for this request to order the results by, if any.
	 *
	 * @return string
	 */
	protected function get_request_orderby() {
		wp_verify_nonce( '_wpnonce' );
		$sortable_columns = $this->get_sortable_columns();
		$orderby          = isset( $_GET['orderby'] ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : '';
		if ( ! array_key_exists( $orderby, $sortable_columns ) ) {
			$orderby = '';
		}

		return $orderby;
	}

	/**
	 * Return the order specified for this request, if any.
	 *
	 * @return string
	 */
	protected function get_request_order() {
		if ( ! empty( $_GET['order'] ) && 'desc' === strtolower( sanitize_text_field( wp_unslash( $_GET['order'] ) ) ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$order = 'DESC';
		} else {
			$order = 'ASC';
		}

		return $order;
	}

	/**
	 * Return the status filter for this request, if any.
	 *
	 * @param string $fallback Default status.
	 *
	 * @since 1.2.1
	 * @return string
	 */
	protected function get_request_status( $fallback = null ) {
		wp_verify_nonce( '_wpnonce' );
		$status = ( ! empty( $_GET['status'] ) ) ? sanitize_text_field( wp_unslash( $_GET['status'] ) ) : '';

		return empty( $status ) ? $fallback : $status;
	}

	/**
	 * Return the type filter for this request, if any.
	 *
	 * @param string $fallback Default type.
	 *
	 * @since 1.2.1
	 * @return string
	 */
	protected function get_request_type( $fallback = null ) {
		wp_verify_nonce( '_wpnonce' );
		$type = ( ! empty( $_GET['type'] ) ) ? sanitize_text_field( wp_unslash( $_GET['type'] ) ) : '';

		return empty( $type ) ? $fallback : $type;
	}

	/**
	 * Return the search filter for this request, if any.
	 *
	 * @since 1.2.1
	 * @return string
	 */
	public function get_request_search() {
		wp_verify_nonce( '_wpnonce' );

		return ! empty( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
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

			$ids    = isset( $_GET['id'] ) ? map_deep( wp_unslash( $_GET['id'] ), 'intval' ) : array();
			$ids    = wp_parse_id_list( $ids );
			$method = 'bulk_' . $action;
			if ( array_key_exists( $action, $this->get_bulk_actions() ) && method_exists( $this, $method ) && ! empty( $ids ) ) {
				$this->$method( $ids );
			}
		}

		if ( isset( $_GET['_wp_http_referer'] ) ) {
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
	 * This function renders most of the columns in the list table.
	 *
	 * @param Object $item The current item.
	 * @param string $column_name The name of the column.
	 *
	 * @since 1.0.0
	 * @return string The column value.
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'status':
				$statuses = array(
					'active'   => __( 'Active', 'wp-ever-accounting' ),
					'inactive' => __( 'Inactive', 'wp-ever-accounting' ),
				);
				$status   = isset( $item->$column_name ) ? $item->$column_name : '';
				$label    = isset( $statuses[ $status ] ) ? $statuses[ $status ] : '';

				return sprintf( '<span class="eac-status is--%1$s">%2$s</span>', esc_attr( $status ), esc_html( $label ) );

			default:
				if ( is_object( $item ) && isset( $item->$column_name ) ) {
					return empty( $item->$column_name ) ? '&mdash;' : wp_kses_post( $item->$column_name );
				}
		}

		return '&mdash;';
	}

	/**
	 * Category filter
	 *
	 * @param string $type type of category.
	 *
	 * @since 1.2.1
	 * @return void
	 */
	protected function category_filter( $type ) {
		$category_id = filter_input( INPUT_GET, 'category_id', FILTER_SANITIZE_NUMBER_INT );
		$category    = empty( $category_id ) ? null : EAC()->categories->get( $category_id );
		?>
		<select class="eac_select2" name="category_id" id="filter-by-category" data-action="eac_json_search" data-type="category" data-subtype="<?php echo esc_attr( $type ); ?>" data-placeholder="<?php esc_attr_e( 'Filter by category', 'wp-ever-accounting' ); ?>">
			<?php if ( ! empty( $category ) ) : ?>
				<option value="<?php echo esc_attr( $category->id ); ?>" <?php selected( $category_id, $category->id ); ?>>
					<?php echo esc_html( $category->name ); ?>
				</option>
			<?php endif; ?>
		</select>
		<?php
	}

	/**
	 * Account filter
	 *
	 * @since 1.2.1
	 * @return void
	 */
	protected function account_filter() {
		$account_id = filter_input( INPUT_GET, 'account_id', FILTER_SANITIZE_NUMBER_INT );
		$account    = empty( $account_id ) ? null : EAC()->accounts->get( $account_id );
		?>
		<select class="eac_select2" name="account_id" id="filter-by-account" data-action="eac_json_search" data-type="account" data-placeholder="<?php esc_attr_e( 'Filter by account', 'wp-ever-accounting' ); ?>">
			<?php if ( ! empty( $account ) ) : ?>
				<option value="<?php echo esc_attr( $account->id ); ?>" <?php selected( $account_id, $account->id ); ?>>
					<?php echo esc_html( $account->name ); ?>
				</option>
			<?php endif; ?>
		</select>
		<?php
	}

	/**
	 * Currency filter
	 *
	 * @since 1.2.1
	 * @return void
	 */
	protected function currency_filter() {
		$currency_id = filter_input( INPUT_GET, 'currency_id', FILTER_SANITIZE_NUMBER_INT );
		$currency    = empty( $currency_id ) ? null : eac_get_currency( $currency_id );
		?>
		<select class="eac_select2" name="currency_id" id="filter-by-currency" data-action="eac_json_search" data-type="currency" data-placeholder="<?php esc_attr_e( 'Filter by currency', 'wp-ever-accounting' ); ?>">
			<?php if ( ! empty( $currency ) ) : ?>
				<option value="<?php echo esc_attr( $currency->id ); ?>" <?php selected( $currency_id, $currency->id ); ?>>
					<?php echo esc_html( $currency->name ); ?>
				</option>
			<?php endif; ?>
		</select>
		<?php
	}
}
