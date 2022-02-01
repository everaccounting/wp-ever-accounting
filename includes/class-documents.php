<?php
/**
 * Documents class.
 *
 * Handle document insert, update, delete & retrieve from database.
 *
 * @version   1.1.3
 * @package   EverAccounting
 */

namespace EverAccounting;

defined( 'ABSPATH' ) || exit;

class Documents {
	/**
	 * Documents construct.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __construct() {
		// invoice
		add_action( 'eaccounting_delete_revenue', array( __CLASS__, 'update_invoice_data' ), 10, 2 );
		add_action( 'eaccounting_update_revenue', array( __CLASS__, 'update_invoice_data' ), 10, 2 );
		add_action( 'eaccounting_daily_scheduled_events', array( __CLASS__, 'update_invoice_status' ) );

		// bill
		add_action( 'eaccounting_delete_payment', array( $this, 'update_bill_data' ), 10, 2 );
		add_action( 'eaccounting_update_payment', array( $this, 'update_bill_data' ), 10, 2 );
		add_action( 'eaccounting_daily_scheduled_events', array( $this, 'update_bill_status' ) );
	}

	/*
	|--------------------------------------------------------------------------
	| Invoice
	|--------------------------------------------------------------------------
	|
	| Handle side effect of inserting, update, deleting invoice
	*/
	public static function update_invoice_data( $payment_id, $revenue ) {
		try {
			if ( ! empty( $revenue->get_document_id() ) && $invoice = self::get_invoice( $revenue->get_document_id() ) ) { //phpcs:ignore
				$invoice->save();
			}
		} catch ( \Exception  $e ) {

		}
	}

	public static function update_invoice_status() {
		global $wpdb;
		$current_time = date_i18n( 'Y-m-d H:i:s' );
		$invoice_ids  = $wpdb->get_col( $wpdb->prepare( "select id from {$wpdb->prefix}ea_documents where due_date != '' AND %s > due_date AND `type` ='invoice' AND status not in ('paid', 'cancelled', 'draft', 'overdue')", $current_time ) );
		foreach ( $invoice_ids as $id ) {
			$invoice = self::get_invoice( $id );
			if ( $invoice ) {
				$invoice->set_status( 'overdue' );
				$invoice->save();
			}
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Bill
	|--------------------------------------------------------------------------
	|
	| Handle side effect of inserting, update, deleting Bill
	*/

	public static function update_bill_data( $payment_id, $payment ) {
		try {
			if ( ! empty( $payment->get_document_id() ) && $bill = self::get_bill( $payment->get_document_id() ) ) { //phpcs:ignore
				$bill->save();
			}
		} catch ( \Exception  $e ) {

		}
	}

	public static function update_bill_status() {
		global $wpdb;
		$current_time = date_i18n( 'Y-m-d H:i:s' );
		$bill_ids     = $wpdb->get_col( $wpdb->prepare( "select id from {$wpdb->prefix}ea_documents where due_date != '' AND %s > due_date AND `type` ='bill' AND status not in ('paid', 'cancelled', 'draft', 'overdue')", $current_time ) );
		foreach ( $bill_ids as $id ) {
			$bill = self::get_bill( $id );
			if ( $bill ) {
				$bill->set_status( 'overdue' );
				$bill->save();
			}
		}
	}

	/**
	 * Main function for returning invoice.
	 *
	 * @since 1.1.0
	 *
	 * @param $invoice
	 *
	 * @return Invoice|null
	 */
	public static function get_invoice( $invoice ) {
		if ( empty( $invoice ) ) {
			return null;
		}
		try {
			$result = new Invoice( $invoice );

			return $result->exists() ? $result : null;
		} catch ( \Exception $e ) {
			return null;
		}
	}

	/**
	 *  Create new invoice programmatically.
	 *  Returns a new invoice object on success.
	 *
	 * @since 1.1.0
	 *
	 * @param bool $wp_error
	 * @param      $args
	 *
	 * @return Invoice|false|int|\WP_Error
	 */
	public static function insert_invoice( $args, $wp_error = true ) {
		// Ensure we have data
		if ( empty( $args ) ) {
			return false;
		}
		try {
			// The id will be provided when updating an item
			$args = wp_parse_args( $args, array( 'id' => null ) );

			// Retrieve the item
			$item = new Invoice( $args['id'] );

			// Load new data
			$item->set_props( $args );

			// Save the item
			$item->save();

			return $item;
		} catch ( \Exception $e ) {
			return $wp_error ? new \WP_Error( 'insert_item', $e->getMessage(), array( 'status' => $e->getCode() ) ) : 0;
		}
	}

	/**
	 * Delete an invoice.
	 *
	 * @since 1.1.0
	 *
	 * @param $invoice_id
	 *
	 * @return bool
	 */
	public static function delete_invoice( $invoice_id ) {
		try {
			$invoice = new Invoice( $invoice_id );

			return $invoice->exists() ? $invoice->delete() : false;
		} catch ( \Exception $e ) {
			return false;
		}
	}

	/**
	 * Get all invoices
	 *
	 * @param array $args
	 *
	 * @return array|Invoice[]|int|
	 * @since 1.1.0
	 */
	public static function get_invoices( $args = array(), $count = false ) {
		$args = array_merge( $args, array( 'type' => 'invoice' ) );
		if ( isset( $args['customer_id'] ) ) {
			$args['contact_id'] = $args['customer_id'];
			unset( $args['customer_id'] );
		}
		return self::get_documents( $args, $count );
	}

	/**
	 * Main function for returning bill.
	 *
	 * @since 1.1.0
	 *
	 * @param $bill
	 *
	 * @return Bill|null
	 */
	public static function get_bill( $bill ) {
		if ( empty( $bill ) ) {
			return null;
		}
		try {
			$result = new Bill( $bill );

			return $result->exists() ? $result : null;
		} catch ( \Exception $e ) {
			return null;
		}
	}

	/**
	 *  Create new bill programmatically.
	 *  Returns a new bill object on success.
	 *
	 * @since 1.1.0
	 *
	 * @param bool $wp_error
	 * @param      $args
	 *
	 * @return Bill|false|int|\WP_Error
	 */
	public static function insert_bill( $args, $wp_error = true ) {
		// Ensure that we have data.
		if ( empty( $args ) ) {
			return false;
		}
		try {
			// The  id will be provided when updating an item.
			$args = wp_parse_args( $args, array( 'id' => null ) );

			// Retrieve the item.
			$item = new Bill( $args['id'] );

			// Load new data.
			$item->set_props( $args );

			// Save the item
			$item->save();

			return $item;
		} catch ( \Exception $e ) {
			return $wp_error ? new \WP_Error( 'insert_item', $e->getMessage(), array( 'status' => $e->getCode() ) ) : 0;
		}
	}

	/**
	 * Delete a bill.
	 *
	 * @since 1.1.0
	 *
	 * @param $bill_id
	 *
	 * @return bool
	 */
	public static function delete_bill( $bill_id ) {
		try {
			$bill = new Bill( $bill_id );

			return $bill->exists() ? $bill->delete() : false;
		} catch ( \Exception $e ) {
			return false;
		}
	}

	/**
	 * Get all bill items
	 *
	 * @param array $args
	 *
	 * @return array|Invoice[]|int|
	 * @since 1.1.0
	 */
	public static function get_bills( $args = array(), $count = false ) {
		$args = array_merge( $args, array( 'type' => 'bill' ) );
		if ( isset( $args['vendor_id'] ) ) {
			$args['contact_id'] = $args['vendor_id'];
			unset( $args['vendor_id'] );
		}
		return self::get_documents( $args, $count );
	}


	/**
	 * Get document
	 *
	 * @param int $id Document ID
	 * @param string $output The required return type. One of OBJECT, ARRAY_A, or ARRAY_N. Default OBJECT.
	 *
	 * @since 1.0.0
	 */
	public static function get_document( $id, $output = OBJECT ) {
		if ( empty( $id ) ) {
			return null;
		}

		if ( $id instanceof Document ) {
			$document = $id;
		} else {
			$document = new Document( $id );
		}

		if ( ! $document->exists() ) {
			return null;
		}

		if ( ARRAY_A === $output ) {
			return $document->get_data();
		}

		if ( ARRAY_N === $output ) {
			return array_values( $document->get_data() );
		}

		return $document;
	}

	/**
	 * Get the next available number.
	 *
	 * @param Document $document
	 * @since 1.1.0
	 * @return int
	 */
	public static function get_next_number( &$document ) {
		global $wpdb;
		$max = (int) $wpdb->get_var( $wpdb->prepare( "select max(id) from {$wpdb->prefix}ea_documents WHERE type=%s", $document->get_type() ) );
		return $max + 1;
	}

	/**
	 * Read order items of a specific type from the database for this order.
	 *
	 * @param Document $document Order object.
	 *
	 * @return array
	 */
	public static function get_items( $document ) {
		global $wpdb;
		if ( ! $document->get_id() ) {
			return array();
		}

		// Get from cache if available.
		$cache_key = 'query:document-items' . md5( $document->get_id() ) . ':' . wp_cache_get_last_changed( 'ea_document_items' );
		$items     = wp_cache_get( $cache_key, 'ea_document_items' );

		if ( false === $items ) {
			$items = $wpdb->get_results(
				$wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ea_document_items WHERE document_id = %d ORDER BY id;", $document->get_id() )
			);
			foreach ( $items as $item ) {
				wp_cache_set( 'document-item-' . $item->id, $item, 'ea_document_items' );
			}
			if ( 0 < $document->get_id() ) {
				wp_cache_set( $cache_key, $items, 'ea_document_items' );
			}
		}

		$results = array();
		foreach ( $items as $item ) {
			$results[ $item->id ] = new Document_Item( $item->id );
		}

		return $results;
	}

	/**
	 * Delete Invoice Items.
	 *
	 * @since 1.1.0
	 *
	 * @param $item
	 */
	public static function delete_items( $item ) {
		global $wpdb;
		$wpdb->delete( $wpdb->prefix . Document_Item::get_table_name(), array( 'document_id' => $item->get_id() ) );
		eaccounting_cache_set_last_changed( 'ea_document_items' );
	}

	/**
	 * Delete Invoice notes.
	 *
	 * @since 1.1.0
	 *
	 * @param $item
	 */
	public static function delete_notes( $item ) {
		global $wpdb;
		$wpdb->delete(
			$wpdb->prefix . Note::get_table_name(),
			array(
				'parent_id' => $item->get_id(),
				'type'      => $item->get_type(),
			)
		);
		eaccounting_cache_set_last_changed( 'ea_notes' );
	}

	/**
	 * @param $item
	 * @since 1.1.0 Delete all related transactions.
	 */
	public static function delete_transactions( $item ) {
		global $wpdb;
		$wpdb->delete( $wpdb->prefix . Transaction::get_table_name(), array( 'document_id' => $item->get_id() ) );
		eaccounting_cache_set_last_changed( 'ea_transactions' );
	}

	/**
	 * Get document items.
	 *
	 * @since 1.1.0
	 *
	 * @param array $args
	 *
	 * @return int|object
	 */
	public static function get_documents( $args = array(), $count = false) {
		global $wpdb;
		$results      = null;
		$total        = 0;
		$cache_group  = Document::get_cache_group();
		$table        = $wpdb->prefix . Document::get_table_name();
		$meta_table   = $wpdb->prefix . Document::get_meta_type();
		$columns      = Contact::get_columns();
		$key          = md5( serialize( $args ) );
		$last_changed = wp_cache_get_last_changed( $cache_group );
		$cache_key    = "$cache_group:$key:$last_changed";
		$cache        = wp_cache_get( $cache_key, $cache_group );
		$fields       = '';
		$join         = '';
		$where        = '';
		$groupby      = '';
		$having       = '';
		$limit        = '';

		$args = (array) wp_parse_args( $args, array(
			'type'       => '',
			'orderby'    => 'issue_date',
			'order'      => 'ASC',
			'search'     => '',
			'offset'     => '',
			'per_page'   => 20,
			'paged'      => 1,
			'meta_key'   => '',
			'meta_value' => '',
			'no_count'   => false,
			'fields'     => 'all',
			'return'     => 'objects',
			'customer_id' => ''
		) );

		if ( false !== $cache ) {
			return $count ? $cache->total : $cache->results;
		}

		// Fields setup
		if ( is_array( $args['fields'] ) ) {
			$fields .= implode( ',', $args['fields'] );
		} elseif( 'all' === $args['fields'] ) {
			$fields .= "$table.* ";
		} else {
			$fields .= "$fields.id";
		}

		if ( false === (bool) $args['no_count'] ) {
			$fields = 'SQL_CALC_FOUND_ROWS ' . $fields;
		}

		// Query from.
		$from = "FROM $table";

		// Parse arch params
		if ( ! empty ( $args['search'] ) ) {
			$allowed_fields = array( 'document_number', 'order_number', 'address' );
			$search_fields = ! empty( $args['search_field'] ) ? $args['search_field'] : $allowed_fields;
			$search_fields = array_intersect( $search_fields, $allowed_fields );
			$searches = array();
			foreach ( $search_fields as $field ) {
				$searches[] = $wpdb->prepare( $field . ' LIKE %s', '%' . $wpdb->esc_like( $args['search'] ) . '%' );
			}

			$where .= ' AND (' . implode( ' OR ', $searches ) . ')';
		}

		// Parse date params
		if ( ! empty ( $args['date'] ) ) {
			$args['date_from'] = $args['date'];
			$args['date_to']   = $args['date'];
		}

		if ( !empty( $args['date_from'])) {
			$date = get_gmt_from_date( gmdate( 'Y-m-d H:i:s', strtotime( $args['date_from'] . ' 00:00:00' ) ) );
			$where .= $wpdb->prepare( " AND DATE($table.date_created) >= %s", $date );
		}

		if ( !empty( $args['date_to'])) {
			$date  = get_gmt_from_date( gmdate( 'Y-m-d H:i:s', strtotime( $args['date_to'] . ' 23:59:59' ) ) );
			$where .= $wpdb->prepare( " AND DATE($table.date_created) <= %s", $date );
		}

		if ( ! empty( $args['date_after'] ) ) {
			$date  = get_gmt_from_date( gmdate( 'Y-m-d H:i:s', strtotime( $args['date_after'] ) ) );
			$where .= $wpdb->prepare( " AND DATE($table.date_created) > %s", $date );
		}

		if ( ! empty( $args['date_before'] ) ) {
			$date  = get_gmt_from_date( gmdate( 'Y-m-d H:i:s', strtotime( $args['date_before'] ) ) );
			$where .= $wpdb->prepare( " AND DATE($table.date_created) < %s", $date );
		}

		// Parse __in params
		$ins = array();
		foreach ( $args as $arg => $value ) {
			if ( '__in' === substr( $arg, - 4 ) ) {
				$ins[ $arg ] = wp_parse_list( $value );
			}
		}
		if ( ! empty( $ins ) ) {
			foreach ( $ins as $key => $value ) {
				if ( empty( $value ) || ! is_array( $value ) ) {
					continue;
				}

				$field = str_replace( array( 'record_', '__in' ), '', $key );
				$field = empty( $field ) ? 'id' : $field;
				$type  = is_numeric( reset( $value ) ) ? '%d' : '%s';

				if ( ! empty( $value ) ) {
					$format = '(' . implode( ',', array_fill( 0, count( $value ), $type ) ) . ')';

					$where .= $wpdb->prepare( " AND $table.$field IN {$format}", $value ); // @codingStandardsIgnoreLine prepare okay
				}
			}
		}

		// Parse not__in params.
		$not_ins = array();
		foreach ( $args as $arg => $value ) {
			if ( '__not_in' === substr( $arg, - 8 ) ) {
				$not_ins[ $arg ] = $value;
			}
		}
		if ( ! empty( $not_ins ) ) {
			foreach ( $not_ins as $key => $value ) {
				if ( empty( $value ) || ! is_array( $value ) ) {
					continue;
				}

				$field = str_replace( array( 'record_', '__not_in' ), '', $key );
				$field = empty( $field ) ? 'id' : $field;
				$type  = is_numeric( reset( $value ) ) ? '%d' : '%s';

				if ( ! empty( $value ) ) {
					$format = '(' . implode( ',', array_fill( 0, count( $value ), $type ) ) . ')';
					$where  .= $wpdb->prepare( " AND $table.$field NOT IN {$format}", $value ); // @codingStandardsIgnoreLine prepare okay
				}
			}
		}
		// Parse type params
		if ( ! empty( $args['type'] ) ) {
			$types  = implode( "','", wp_parse_list( $args['type'] ) );
			$where .= " AND $table.`type` IN ('$types')";
		}

		// Parse currency code params
		if ( ! empty( $args['currency_code'] ) ) {
			$currency_code = implode( "','", wp_parse_list( $args['currency_code'] ) );
			$where        .= " AND $table.`currency_code` IN ('$currency_code')";
		}

		// Parse category_id params
		if ( ! empty( $args['category_id'] ) ) {
			$category_in = implode( ',', wp_parse_id_list( $args['category_id'] ) );
			$where      .= " AND $table.`category_id__in` IN ($category_in)";
		}

		// Parse contact_id params
		if ( ! empty( $args['contact_id'] ) ) {
			$contact_id = implode( ',', wp_parse_id_list( $args['contact_id'] ) );
			$where     .= " AND $table.`contact_id__in` IN ($contact_id)";
		}

		// Parse parent_id params
		if ( ! empty( $args['parent_id'] ) ) {
			$parent_id = implode( ',', wp_parse_id_list( $args['parent_id'] ) );
			$where    .= " AND $table.`parent_id__in` IN ($parent_id)";
		}

		// Parse issue_date params
		if ( ! empty( $args['issue_date'] ) && is_array( $args['issue_date'] ) ) {
			$date_created_query = new \WP_Date_Query( $args['issue_date'], "{$table}.issue_date" );
			$where             .= $date_created_query->get_sql();
		}

		// Parse due_date params
		if ( ! empty( $args['due_date'] ) && is_array( $args['due_date'] ) ) {
			$date_created_query = new \WP_Date_Query( $args['due_date'], "{$table}.due_date" );
			$where             .= $date_created_query->get_sql();
		}

		// Parse payment_date params
		if ( ! empty( $args['payment_date'] ) && is_array( $args['payment_date'] ) ) {
			$date_created_query = new \WP_Date_Query( $args['payment_date'], "{$table}.payment_date" );
			$where             .= $date_created_query->get_sql();
		}

		// Parse creator id params
		if ( ! empty( $args['creator_id'] ) ) {
			$creator_id = implode( ',', wp_parse_id_list( $args['creator_id'] ) );
			$where      .=  " AND $table.`creator_id` IN ($creator_id)";
		}

		// Parse customer_id params
		if ( ! empty( $args['contact_id'] ) ) {
			$customer_id = implode( ',', wp_parse_id_list( $args['contact_id'] ) );
			$where      .= " AND $table.`contact_id` IN ($customer_id)";
		}


		//Parse pagination
		$page     = absint( $args['paged'] );
		$per_page = absint( $args['per_page'] );
		if ( $per_page >= 0 ) {
			$offset = absint( ( $page - 1 ) * $per_page );
			$limit  = " LIMIT {$offset}, {$per_page}";
		}

		//Parse order.
		$orderby = "$table.id";
		if ( in_array( $args['orderby'], $columns, true ) ) {
			$orderby = sprintf( '%s.%s', $table, $args['orderby'] );
		} elseif ( 'meta_value_num' === $args['orderby'] && ! empty( $args['meta_key'] ) ) {
			$orderby = "CAST($meta_table.meta_value AS SIGNED)";
		} elseif ( 'meta_value' === $args['orderby'] && ! empty( $args['meta_key'] ) ) {
			$orderby = "$meta_table.meta_value";
		}
		// Show the recent records first by default.
		$order = 'DESC';
		if ( 'ASC' === strtoupper( $args['order'] ) ) {
			$order = 'ASC';
		}

		$orderby = sprintf( 'ORDER BY %s %s', $orderby, $order );

		//Parse meta param.
		$meta_query = new \WP_Meta_Query();
		$meta_query->parse_query_vars( $args );
		if ( ! empty( $meta_query->queries ) ) {
			$meta_clauses = $meta_query->get_sql( str_replace( $wpdb->prefix, '', $meta_table ), $table, 'id' );
			$from         .= $meta_clauses['join'];
			$where        .= $meta_clauses['where'];

			if ( $meta_query->has_or_relation() ) {
				$fields = 'DISTINCT ' . $fields;
			}
		}

		if ( null === $results ) {
			$request = "SELECT {$fields} {$from} {$join} WHERE 1=1 {$where} {$groupby} {$having} {$orderby} {$limit}";

			if ( is_array( $args['fields'] ) || 'all' === $args['fields'] ) {
				$results = $wpdb->get_results( $request );
			} else {
				$results = $wpdb->get_col( $request );
			}

			if ( ! $args['no_count'] ) {
				$total = (int) $wpdb->get_var( "SELECT FOUND_ROWS()" );
			}

			if ( 'all' === $args['fields'] ) {
				foreach ( $results as $key => $row ) {
					wp_cache_add( $row->id, $row, $cache_group );
					$item = new Document();
					$item->set_props( $row );
					$item->set_object_read( true );
					$results[ $key ] = $item;
				}
			}

			$cache          = new \StdClass;
			$cache->results = $results;
			$cache->total   = $total;

			wp_cache_add( $cache_key, $cache, $cache_group );
		}

		if ( 'objects' === $args['return'] && true !== $args['no_count'] ) {
			$results = array_map(
				function ( $item ) {
					switch ( $item->type ) {
						case 'invoice':
							$document = self::get_invoice( $item );
							break;
						case 'bill':
							$document = self::get_bill( $item );
							break;
						default:
							$document = apply_filters( 'eaccounting_get_documetn_callback_' . $item->type, null, $item );
					}

					return $document;
				},
				$results
			);
		}

		return $count ? $total : $results;

	}

	/**
	 * Get bill statues
	 *
	 * @return mixed|void
	 * @since 1.1.0
	 */
	public static function get_bill_statuses() {
		$statuses = array(
			'draft'     => __( 'Draft', 'wp-ever-accounting' ),
			'received'  => __( 'Received', 'wp-ever-accounting' ),
			'partial'   => __( 'Partial', 'wp-ever-accounting' ),
			'paid'      => __( 'Paid', 'wp-ever-accounting' ),
			'overdue'   => __( 'Overdue', 'wp-ever-accounting' ),
			'cancelled' => __( 'Cancelled', 'wp-ever-accounting' ),
		);

		return apply_filters( 'eaccounting_bill_statuses', $statuses );
	}

	/**
	 * Get invoice statuses
	 *
	 * @return mixed|void
	 * @since 1.1.0
	 */
	public static function get_invoice_statuses() {
		$statuses = array(
			'draft'     => __( 'Draft', 'wp-ever-accounting' ),
			'pending'   => __( 'Pending', 'wp-ever-accounting' ),
			'partial'   => __( 'Partial', 'wp-ever-accounting' ),
			'paid'      => __( 'Paid', 'wp-ever-accounting' ),
			'overdue'   => __( 'Overdue', 'wp-ever-accounting' ),
			'cancelled' => __( 'Cancelled', 'wp-ever-accounting' ),
			'refunded'  => __( 'Refunded', 'wp-ever-accounting' ),
		);

		return apply_filters( 'eaccounting_invoice_statuses', $statuses );
	}
}
