<?php

namespace EverAccounting\Admin\ListTables;

defined( 'ABSPATH' ) || exit;

// Load WP_List_Table if not loaded.
if ( ! class_exists( '\WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Class ListTable.
 *
 * @since   1.0.0
 * @package EverAccounting
 */
class ListTable extends \WP_List_Table {

	/**
	 * Current screen object.
	 *
	 * @since  1.0.2
	 * @var    \WP_Screen
	 */
	public $screen;

	/**
	 *
	 * Total number of items
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $total_count = 0;

	/**
	 * Default number of items to show per page
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $per_page = 20;

	/**
	 * Get things started
	 *
	 * @param array $args Optional. Arbitrary display and query arguments to pass through the list table. Default empty array.
	 *
	 * @see WP_List_Table::__construct()
	 * @since  1.0.2
	 */
	public function __construct( $args = array() ) {
		$this->per_page = (int) get_option( 'eac_records_per_page', 20 );
		parent::__construct( $args );
	}

	/**
	 * Gets a list of CSS classes for the WP_List_Table table tag.
	 *
	 * @since 3.1.0
	 *
	 * @return string[] Array of CSS classes for the table tag.
	 */
	// protected function get_table_classes() {
	// $mode = get_user_setting( 'posts_list_mode', 'list' );
	//
	// $mode_class = esc_attr( 'table-view-' . $mode );
	//
	// return array( 'widefat', 'striped', $mode_class, $this->_args['plural'] );
	// }

	/**
	 * Retrieve the search query string.
	 *
	 * @since 1.1.6
	 * @return string Search query.
	 */
	protected function get_search() {
		return eac_get_input_var( 's', '' );
	}

	/**
	 * Retrieve the order query string.
	 *
	 * @param string $default Default value.
	 *
	 * @since 1.1.6
	 * @return string Order query.
	 */
	protected function get_order( $default = 'DESC' ) {
		return eac_get_input_var( 'order', $default );
	}

	/**
	 * Retrieve the orderby query string.
	 *
	 * @param string $default Default value.
	 *
	 * @since 1.1.6
	 * @return string Orderby query.
	 */
	protected function get_orderby( $default = 'id' ) {
		$orderby = eac_get_input_var( 'orderby', $default );
		if ( ! array_key_exists( $orderby, $this->get_sortable_columns() ) ) {
			$orderby = $default;
		}

		return $orderby;
	}

	/**
	 * Retrieve the status query string.
	 *
	 * @since 1.1.6
	 * @return string Status query.
	 */
	protected function get_status() {
		return eac_get_input_var( 'status', '' );
	}

	/**
	 * Retrieve the per page query string.
	 *
	 * @since 1.1.6
	 * @return int Per page.
	 */
	protected function get_per_page() {
		return (int) eac_get_input_var( 'per_page', $this->per_page );
	}

	/**
	 * Retrieve the offset parameter.
	 *
	 * @since 1.1.6
	 * @return int Offset.
	 */
	protected function get_offset() {
		return (int) ( $this->get_pagenum() - 1 ) * $this->get_per_page();
	}

	/**
	 * Retrieve the page query string.
	 *
	 * @since 1.1.6
	 * @return string Page query.
	 */
	protected function get_page() {
		return eac_get_input_var( 'page' );
	}

	/**
	 * Retrieve the current page URL.
	 *
	 * @param array $args Optional. Query arguments to add to the URL. Default empty array.
	 *
	 * @since 1.1.6
	 * @return string Current page URL.
	 */
	protected function get_current_url( $args = array() ) {
		$page    = $this->get_page();
		$tab     = eac_get_input_var( 'tab' );
		$section = eac_get_input_var( 'section' );
		$args    = array_merge(
			array(
				'page'    => $page,
				'tab'     => $tab,
				'section' => $section,
			),
			$args
		);

		$args = array_filter( $args );

		// Build the base URL.
		return add_query_arg( $args, admin_url( 'admin.php' ) );
	}

	/**
	 * Get hidden columns.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_hidden_columns() {
		return array();
	}

	/**
	 * This function renders most of the columns in the list table.
	 *
	 * @param Object|array $item The current item.
	 * @param string       $column_name The name of the column.
	 *
	 * @since 1.0.2
	 * @return string The column value.
	 */
	public function column_default( $item, $column_name ) {

		if ( is_object( $item ) && method_exists( $item, "get_$column_name" ) ) {
			$getter = "get_$column_name";

			return empty( $item->$getter( 'view' ) ) ? '&mdash;' : esc_html( $item->$getter( 'view' ) );
		} elseif ( is_array( $item ) && isset( $item[ $column_name ] ) ) {
			return empty( $item[ $column_name ] ) ? '&mdash;' : esc_html( $item[ $column_name ] );
		}

		return '&mdash;';
	}

	/**
	 * Show the search field
	 *
	 * @param string $text Label for the search box.
	 * @param string $input_id ID of the search box.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	public function search_box( $text, $input_id ) {
		if ( empty( $this->get_search() ) && ! $this->has_items() ) {
			return;
		}

		$input_id = $input_id . '-search-input';
		$orderby  = $this->get_orderby();
		$order    = $this->get_order();

		if ( ! empty( $orderby ) ) {
			echo '<input type="hidden" name="orderby" value="' . esc_attr( $orderby ) . '" />';
		}
		if ( ! empty( $order ) ) {
			echo '<input type="hidden" name="order" value="' . esc_attr( $order ) . '" />';
		}
		?>
		<p class="search-box">
			<label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>"><?php echo esc_html( $text ); ?>:</label>
			<input type="search" id="<?php echo esc_attr( $input_id ); ?>" name="s" value="<?php _admin_search_query(); ?>"/>
			<?php submit_button( $text, 'button', false, false, array( 'ID' => 'search-submit' ) ); ?>
		</p>
		<?php
	}

	/**
	 * Process bulk action.
	 *
	 * @param string $doaction Action name.
	 *
	 * @since 1.1.6
	 */
	public function process_bulk_actions( $doaction ) {
		if ( ! empty( $_GET['_wp_http_referer'] ) || ! empty( $_GET['_wpnonce'] ) ) { // phpcs:ignore
			wp_safe_redirect(
				remove_query_arg(
					array(
						'_wp_http_referer',
						'_wpnonce',
					),
					wp_unslash( $_SERVER['REQUEST_URI'] ) // phpcs:ignore
				)
			);
			exit;
		}
	}

	/**
	 * Status filter.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	public function status_filter() {
		$filters = array(
			''         => __( 'All status', 'wp-ever-accounting' ),
			'active'   => __( 'Active', 'wp-ever-accounting' ),
			'inactive' => __( 'Inactive', 'wp-ever-accounting' ),
		);

		$selected = eac_get_input_var( 'status', '' );

		?>
		<select name="status" id="status-filter">
			<?php foreach ( $filters as $key => $value ) : ?>
				<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $selected, $key ); ?>><?php echo esc_html( $value ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php
	}

	/**
	 * Account filter.
	 *
	 * @since 1.1.6
	 *
	 * @return void
	 */
	public function account_filter() {
		$account_id = eac_get_input_var( 'account_id' );
		$accounts   = eac_get_accounts( array( 'include' => $account_id ) );
		?>
		<select class="eac-select__account" name="account_id" id="account-filter" data-ajax-type="account">
			<option value=""><?php esc_html_e( 'All accounts', 'wp-ever-accounting' ); ?></option>
			<?php foreach ( $accounts as $account ) : ?>
				<option value="<?php echo esc_attr( $account->get_id() ); ?>" <?php selected( $account_id, $account->get_id() ); ?>><?php echo esc_html( $account->get_name() ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php
	}

	/**
	 * Category filter.
	 *
	 * @param string $type Type of category.
	 *
	 * @since 1.1.6
	 *
	 * @return void
	 */
	public function category_filter( $type = 'item_category' ) {
		$category_id = eac_get_input_var( 'category_id' );
		$category    = eac_get_category( $category_id );
		?>
		<select class="eac-select2" name="category_id" id="category-filter" data-action="eac_json_search" data-type="<?php echo esc_attr( $type ); ?>" data-placeholder="<?php esc_attr_e( 'Filter by category', 'wp-ever-accounting' ); ?>">
			<option value=""><?php esc_html_e( 'All categories', 'wp-ever-accounting' ); ?></option>
			<?php if ( $category ) : ?>
				<option value="<?php echo esc_attr( $category->get_id() ); ?>" selected="selected"><?php echo esc_html( $category->get_formatted_name() ); ?></option>
			<?php endif; ?>
		</select>
		<?php
	}

	/**
	 * Expense category filter.
	 *
	 * @since 1.1.6
	 *
	 * @return void
	 */
	public function expense_category_filter() {
		$category_id = eac_get_input_var( 'category_id' );
		$categories  = eac_get_categories(
			array(
				'include' => $category_id,
				'type'    => 'expense',
			)
		);
		?>
		<select class="eac-select__expense-category" name="category_id" id="expense-category-filter">
			<option value=""><?php esc_html_e( 'All expense categories', 'wp-ever-accounting' ); ?></option>
			<?php foreach ( $categories as $category ) : ?>
				<option value="<?php echo esc_attr( $category->get_id() ); ?>" <?php selected( $category_id, $category->get_id() ); ?>><?php echo esc_html( $category->get_name() ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php
	}

	/**
	 * Item category filter.
	 *
	 * @since 1.1.6
	 *
	 * @return void
	 */
	public function item_category_filter() {
		$category_id = eac_get_input_var( 'category_id' );
		$categories  = eac_get_categories(
			array(
				'include' => $category_id,
				'type'    => 'item',
			)
		);
		?>
		<select class="eac-select__item-category" name="category_id" id="item-category-filter">
			<option value=""><?php esc_html_e( 'All item categories', 'wp-ever-accounting' ); ?></option>
			<?php foreach ( $categories as $category ) : ?>
				<option value="<?php echo esc_attr( $category->get_id() ); ?>" <?php selected( $category_id, $category->get_id() ); ?>><?php echo esc_html( $category->get_name() ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php
	}

	/**
	 * Customer filter.
	 *
	 * @since 1.1.6
	 *
	 * @return void
	 */
	public function customer_filter() {
		$customer_id = eac_get_input_var( 'customer_id', 'get', '', 'absint' );
		$customers   = eac_get_customers( array( 'include' => $customer_id ) );
		?>
		<select class="eac-select__customer" name="customer_id" id="customer-filter">
			<option value=""><?php esc_html_e( 'All customers', 'wp-ever-accounting' ); ?></option>
			<?php foreach ( $customers as $customer ) : ?>
				<option value="<?php echo esc_attr( $customer->get_id() ); ?>" <?php selected( $customer_id, $customer->get_id() ); ?>><?php echo esc_html( $customer->get_name() ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php
	}

	/**
	 * Vendor filter.
	 *
	 * @since 1.1.6
	 *
	 * @return void
	 */
	public function vendor_filter() {
		$vendor_id = eac_get_input_var( 'vendor_id' );
		$vendors   = eac_get_vendors( array( 'include' => $vendor_id ) );
		?>
		<select class="eac-select__vendor" name="vendor_id" id="vendor-filter" data-ajax-type="vendor" data-placeholder="<?php esc_attr_e( 'Filter by vendor', 'wp-ever-accounting' ); ?>">
			<option value=""><?php esc_html_e( 'All vendors', 'wp-ever-accounting' ); ?></option>
			<?php foreach ( $vendors as $vendor ) : ?>
				<option value="<?php echo esc_attr( $vendor->get_id() ); ?>" <?php selected( $vendor_id, $vendor->get_id() ); ?>><?php echo esc_html( $vendor->get_name() ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php
	}

	/**
	 * Payment method filter.
	 *
	 * @since 1.1.6
	 *
	 * @return void
	 */
	public function payment_method_filter() {
		$payment_method_id = eac_get_input_var( 'payment_method_id' );
		$payment_methods   = eac_get_payment_methods();
		?>
		<select class="eac-select__payment-method" name="payment_method_id" id="payment-method-filter">
			<option value=""><?php esc_html_e( 'All payment methods', 'wp-ever-accounting' ); ?></option>
			<?php foreach ( $payment_methods as $payment_method ) : ?>
				<option value="<?php echo esc_attr( $payment_method->get_id() ); ?>" <?php selected( $payment_method_id, $payment_method->get_id() ); ?>><?php echo esc_html( $payment_method->get_name() ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php
	}

	/**
	 * Date range filter.
	 *
	 * @since 1.1.6
	 *
	 * @return void
	 */
	public function date_range_filter() {
		$start_date = eac_get_input_var( 'start_date' );
		$end_date   = eac_get_input_var( 'end_date' );
		?>
		<div class="eac-date-range">
			<input type="text" class="eac-date-range__start" name="start_date" id="start-date-filter" value="<?php echo esc_attr( $start_date ); ?>" placeholder="<?php esc_attr_e( 'Start date', 'wp-ever-accounting' ); ?>"/>
			<span class="eac-date-range__separator">-</span>
			<input type="text" class="eac-date-range__end" name="end_date" id="end-date-filter" value="<?php echo esc_attr( $end_date ); ?>" placeholder="<?php esc_attr_e( 'End date', 'wp-ever-accounting' ); ?>"/>
		</div>
		<?php
	}

	/**
	 * Currency filter.
	 *
	 * @since 1.1.6
	 *
	 * @return void
	 */
	public function currency_filter() {
		$currency_code = eac_get_input_var( 'currency_code' );
		$currencies    = eac_get_currencies( array( 'code__in' => $currency_code ) );
		$currency_code = wp_parse_list( $currency_code );
		?>
		<select class="eac-select__currency" name="currency_code" id="currency-filter">
			<option value=""><?php esc_html_e( 'All currencies', 'wp-ever-accounting' ); ?></option>
			<?php foreach ( $currencies as $currency ) : ?>
				<option value="<?php echo esc_attr( $currency->get_code() ); ?>" <?php selected( in_array( $currency->get_code(), $currency_code, true ) ); ?>><?php echo esc_html( $currency->get_formatted_name() ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php
	}
}
