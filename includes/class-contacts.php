<?php
/**
 * Contacts class.
 *
 * Handle contact insert, update, delete & retrieve from database.
 *
 * @version   1.1.3
 * @package   Ever_Accounting
 */

namespace Ever_Accounting;

use Ever_Accounting\Helpers\Formatting;

defined( 'ABSPATH' ) || exit;

class Contacts {

	/**
	 * Contacts construct.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __construct() {
		// customers
		add_action( 'eaccounting_delete_revenue', array( __CLASS__, 'update_customer_total_paid' ), 10, 2 );
		add_action( 'eaccounting_insert_revenue', array( __CLASS__, 'update_customer_total_paid' ), 10, 2 );
		add_action( 'eaccounting_update_revenue', array( __CLASS__, 'update_customer_total_paid' ), 10, 2 );
		add_action( 'eaccounting_insert_invoice', array( __CLASS__, 'update_customer_total_paid' ), 10, 2 );
		add_action( 'eaccounting_update_invoice', array( __CLASS__, 'update_customer_total_paid' ), 10, 2 );
		add_action( 'eaccounting_delete_invoice', array( __CLASS__, 'update_customer_total_paid' ), 10, 2 );
		add_action( 'eaccounting_delete_customer', array( __CLASS__, 'delete_customer_reference' ) );

		// vendors
		add_action( 'eaccounting_delete_payment', array( __CLASS__, 'update_vendor_total_paid' ), 10, 2 );
		add_action( 'eaccounting_insert_payment', array( __CLASS__, 'update_vendor_total_paid' ), 10, 2 );
		add_action( 'eaccounting_update_payment', array( __CLASS__, 'update_vendor_total_paid' ), 10, 2 );
		add_action( 'eaccounting_insert_bill', array( __CLASS__, 'update_vendor_total_paid' ), 10, 2 );
		add_action( 'eaccounting_update_bill', array( __CLASS__, 'update_vendor_total_paid' ), 10, 2 );
		add_action( 'eaccounting_delete_bill', array( __CLASS__, 'update_vendor_total_paid' ), 10, 2 );
		add_action( 'eaccounting_delete_vendor', array( __CLASS__, 'delete_vendor_reference' ) );
	}

	/**
	 * Update customer total paid
	 *
	 * @param int $transaction_id
	 * @param \Ever_Accounting\Abstracts\Transaction $transaction
	 *
	 * @since 1.1.0
	 */
	public static function update_customer_total_paid( $transaction_id, $transaction ) {
		$customer = self::get_customer( $transaction->get_customer_id() );
		if ( $customer ) {
			self::insert_customer(
				array(
					'id'         => $customer->get_id(),
					'total_paid' => $customer->get_calculated_total_paid(),
					'total_due'  => $customer->get_calculated_total_due(),
				)
			);
		}
	}

	/**
	 * When a customer is deleted check if
	 * customer is associated with any document and transactions.
	 *
	 * @param int $customer_id
	 *
	 * @since 1.1.0
	 */
	public function delete_customer_reference( $customer_id ) {
		global $wpdb;
		$wpdb->update( "{$wpdb->prefix}ea_documents", array( 'contact_id' => null ), array( 'contact_id' => $customer_id ) );
		$wpdb->update( "{$wpdb->prefix}ea_transactions", array( 'contact_id' => null ), array( 'contact_id' => $customer_id ) );
	}

	/**
	 * Update vendor total paid
	 *
	 * @param int $transaction_id
	 * @param \Ever_Accounting\Abstracts\Transaction $transaction
	 *
	 * @since 1.1.0
	 */
	public static function update_vendor_total_paid( $transaction_id, $transaction ) {
		$vendor = self::get_vendor( $transaction->get_vendor_id() );
		if ( $vendor ) {
			self::insert_vendor(
				array(
					'id'         => $vendor->get_id(),
					'total_paid' => $vendor->get_calculated_total_paid(),
					'total_due'  => $vendor->get_calculated_total_due(),
				)
			);
		}
	}

	/**
	 * When a vendor is deleted check if
	 * customer is associated with any document and transactions.
	 *
	 * @param int $vendor_id
	 *
	 * @since 1.1.0
	 */
	public static function delete_vendor_reference( $vendor_id ) {
		global $wpdb;
		$wpdb->update( "{$wpdb->prefix}ea_documents", array( 'contact_id' => null ), array( 'contact_id' => $vendor_id ) );
		$wpdb->update( "{$wpdb->prefix}ea_transactions", array( 'contact_id' => null ), array( 'contact_id' => $vendor_id ) );
	}

	/**
	 * Get contact types.
	 *
	 * @since 1.1.3
	 * @return array
	 */
	public static function get_types() {
		return apply_filters(
			'eaccounting_contact_types',
			array(
				'customer' => __( 'Customer', 'wp-ever-accounting' ),
				'vendor'   => __( 'Vendor', 'wp-ever-accounting' ),
			)
		);
	}

	/**
	 * Get contact
	 *
	 * @param int $id Contact ID
	 * @param string $output The required return type. One of OBJECT, ARRAY_A, or ARRAY_N. Default OBJECT.
	 *
	 * @since 1.0.0
	 */
	public static function get( $id, $output = OBJECT ) {
		if ( empty( $id ) ) {
			return null;
		}

		if ( $id instanceof Contact ) {
			$contact = $id;
		} else {
			$contact = new Contact( $id );
		}

		if ( ! $contact->exists() ) {
			return null;
		}

		if ( ARRAY_A === $output ) {
			return $contact->get_data();
		}

		if ( ARRAY_N === $output ) {
			return array_values( $contact->get_data() );
		}

		return $contact;
	}

	/**
	 * Get the contact type label of a specific type
	 *
	 * @param string $type Type
	 *
	 * @since 1.1.0
	 * @retrun string
	*/
	public static function get_type( $type ) {
		$types = self::get_types();

		return array_key_exists( $type, $types ) ? $types[ $type ] : null;
	}

	/**
	 * Get customer.
	 *
	 * @param $customer
	 *
	 * @since 1.1.0
	 *
	 * @return \Ever_Accounting\Contact|null
	 */
	public static function get_customer( $customer ) {
		if ( empty( $customer ) ) {
			return null;
		}
		try {
			$result = new Contact( $customer );

			return $result->exists() ? $result : null;
		} catch ( \Exception $e ) {
			return null;
		}
	}

	/**
	 * Get customer by email
	 *
	 * @param string $email Email
	 *
	 * @since 1.1.0
	 * @return \Ever_Accounting\Contact
	*/
	public static function get_customer_by_email( $email ) {
		global $wpdb;
		$email = sanitize_email( $email );
		if ( empty( $email ) ) {
			return null;
		}

		$cache_key = "customer-email-$email";
		$customer = wp_cache_get( $cache_key, Contact::get_cache_group() );

		if ( false === $customer ) {
			$customer = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ea_contacts where `email`=%s AND `type`='customer'", Formatting::clean( $email ) ) );
			wp_cache_set( $cache_key, $customer, Contact::get_cache_group() );
		}
		if( $customer ) {
			wp_cache_set( $cache_key, $customer, Contact::get_cache_group() );
			return self::get_customer( $customer );
		}

		return null;
	}

	/**
	 * Insert customer
	 *
	 * @param array|object $data Customer Data
	 *
	 * @since 1.1.0
	 * @return object|\WP_Error
	 */
	public static function insert_customer( $data ) {
		if ( $data instanceof Customer ) {
			$data = $data->get_data();
		} elseif ( is_object( $data ) ) {
			$data = get_object_vars( $data );
		}

		if ( empty( $data ) || ! is_array( $data ) ) {
			return new \WP_Error( 'invalid_data', __( 'Customer could not be saved.', 'wp-ever-accounting' ) );
		}

		$data     = wp_parse_args( $data, array( 'id' => null ) );
		$customer = new Customer( (int) $data['id'] );
		$customer->set_props( $data );
		$is_error = $customer->save();
		if ( is_wp_error( $is_error ) ) {
			return $is_error;
		}

		return $customer;
	}

	/**
	 * Delete customer
	 *
	 * @param int $id Customer ID
	 *
	 * @since 1.1.0
	 * @return object|bool
	 */
	public static function delete_customer( $id ) {
		if ( $id instanceof Customer ) {
			$id = $id->get_id();
		}

		if ( empty( $id ) ) {
			return false;
		}

		$customer = new Customer( (int) $id );
		if ( ! $customer->exists() ) {
			return false;
		}

		return $customer->delete();
	}

	/**
	 * Get all customers
	 *
	 * @since 1.0.0
	 * @return array|int
	*/
	public static function query_customers ( $args = array(), $count = false ) {
		return self::query( array_merge( $args, array( 'type' => 'customer' ) ), $count );
	}

	/**
	 * Get vendor.
	 *
	 * @param $vendor
	 *
	 * @since 1.1.0
	 *
	 * @return \Ever_Accounting\Contact|null
	 */
	public static function get_vendor( $vendor ) {
		if ( empty( $vendor ) ) {
			return null;
		}
		try {
			$result = new Contact( $vendor );

			return $result->exists() ? $result : null;
		} catch ( \Exception $e ) {
			return null;
		}
	}

	/**
	 * Get vendor by email
	 *
	 * @param string $email Email
	 *
	 * @since 1.1.0
	 * @return \Ever_Accounting\Contact
	 */
	public static function get_vendor_by_email( $email ) {
		global $wpdb;
		$email = sanitize_email( $email );
		if ( empty( $email ) ) {
			return null;
		}
		$cache_key = "vendor-email-$email";
		$vendor = wp_cache_get( $cache_key, Contact::get_cache_group() );
		if ( false === $vendor ) {
			$vendor = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ea_contacts where `email`=%s AND `type`='vendor'", Formatting::clean( $email ) ) );
			wp_cache_set( $cache_key, $vendor, Contact::get_cache_group() );
		}
		if( $vendor ) {
			wp_cache_set( $cache_key, $vendor, Contact::get_cache_group() );

			return self::get_vendor( $vendor );
		}

		return null;
	}

	/**
	 * Insert vendor
	 *
	 * @param array|object $data Vendor Data
	 *
	 * @since 1.1.0
	 * @return object|\WP_Error
	 */
	public static function insert_vendor( $data ) {
		if ( $data instanceof Vendor ) {
			$data = $data->get_data();
		} elseif ( is_object( $data ) ) {
			$data = get_object_vars( $data );
		}

		if ( empty( $data ) || ! is_array( $data ) ) {
			return new \WP_Error( 'invalid_data', __( 'Vendor could not be saved.', 'wp-ever-accounting' ) );
		}

		$data     = wp_parse_args( $data, array( 'id' => null ) );
		$vendor = new Vendor( (int) $data['id'] );
		$vendor->set_props( $data );
		$is_error = $vendor->save();
		if ( is_wp_error( $is_error ) ) {
			return $is_error;
		}

		return $vendor;
	}

	/**
	 * Delete vendor
	 *
	 * @param int $id Vendor ID
	 *
	 * @since 1.0.0
	 * @return object|bool
	 */
	public static function delete_vendor( $id ) {
		if ( $id instanceof Vendor ) {
			$id = $id->get_id();
		}

		if ( empty( $id ) ) {
			return false;
		}

		$vendor = new Vendor( (int) $id );
		if ( ! $vendor->exists() ) {
			return false;
		}

		return $vendor->delete();
	}

	/**
	 * Get all vendors
	 *
	 * @since 1.0.0
	 * @return array|int
	 */
	public static function query_vendors ( $args = array(), $count = false ) {
		return self::query( array_merge( $args, array( 'type' => 'vendor' ) ), $count );
	}

	/**
	 * Get all contacts
	 *
	 * @param array $args Search arguments
	 *
	 * @since 1.0.0
	 * @return int|object
	 */
	public static function query( $args = array(), $count = false ) {
		global $wpdb;
		$results      = null;
		$total        = 0;
		$cache_group  = Contact::get_cache_group();
		$table        = $wpdb->prefix . Contact::get_table_name();
		$meta_table   = $wpdb->prefix . Contact::get_meta_type();
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
			'orderby'    => 'date_created',
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
			$allowed_fields = array( 'name', 'email', 'phone', 'street', 'country' );
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

		// Parse status params
		if ( ! empty( $args['status'] ) && ! in_array( $args['status'], array( 'all', 'any'), true ) ) {
			$status = Formatting::string_to_bool( $args['status'] );
			$status = Formatting::bool_to_number( $status );
			$where .= " AND $table.`enabled` = ('$status')";
		}

		// Parse creator id params
		if ( ! empty( $args['creator_id'] ) ) {
			$creator_id = implode( ',', wp_parse_id_list( $args['creator_id'] ) );
			$where      .=  " AND $table.`creator_id` IN ($creator_id)";
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
					$item = new Contact();
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
						case 'customer':
							$contact = self::get_customer( $item );
							break;
						case 'vendor':
							$contact = self::get_vendor( $item );
							break;
						default:
							$contact = apply_filters( 'ever_accounting_get_contact_callback_' . $item->type, null, $item );
					}

					return $contact;
				},
				$results
			);
		}

		return $count ? $total : $results;
	}
}
