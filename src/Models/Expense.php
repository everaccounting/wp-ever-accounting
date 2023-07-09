<?php

namespace EverAccounting\Models;

defined( 'ABSPATH' ) || exit;

/**
 * Class Expense.
 *
 * @since   1.1.6
 * @package EverAccounting\Models
 */
class Expense extends Transaction {

	/**
	 * Object type.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $object_type = 'expense';

	/**
	 * Constructor.
	 *
	 * @param int|object|array $data Object ID, post object, or array of data.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $data = 0 ) {
		$this->core_data['type']           = $this->object_type;
		$this->core_data['date']           = wp_date( 'Y-m-d' );
		$this->core_data['account_id']     = get_option( 'eac_default_expenses_account_id', 0 );
		$this->core_data['category_id']    = get_option( 'eac_default_expenses_category_id', 0 );
		$this->core_data['payment_method'] = get_option( 'eac_expenses_payment_method', 'cash' );
		parent::__construct( $data );

		// after reading check if the contact is a customer.
		if ( $this->exists() && $this->object_type !== $this->get_type() ) {
			$this->set_id( 0 );
			$this->set_defaults();
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Getters and Setters
	|--------------------------------------------------------------------------
	|
	| Methods for getting and setting data.
	|
	*/

	/**
	 * Return the vendor id.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 */
	public function get_vendor_id( $context = 'edit' ) {
		return $this->get_prop( 'contact_id', $context );
	}

	/**
	 * set the vendor id.
	 *
	 * @param int $vendor_id .
	 *
	 * @since  1.1.0
	 */
	public function set_vendor_id( $vendor_id ) {
		$this->set_prop( 'contact_id', absint( $vendor_id ) );
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	|
	| Methods which create, read, update and delete discounts from the database.
	|
	*/
	/**
	 * Saves an object in the database.
	 *
	 * @return true|\WP_Error True on success, WP_Error on failure.
	 * @since 1.0.0
	 */
	public function save() {
		// Required fields payment_date,account_id,category_id, payment_method.

		// Check required fields.
		if ( empty( $this->get_date() ) ) {
			return new \WP_Error( 'missing_required', __( 'Expense date is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_account_id() ) ) {
			return new \WP_Error( 'missing_required', __( 'Account is required.', 'wp-ever-accounting' ) );
		}

		return parent::save();
	}

	/**
	 * Prepare where query.
	 *
	 * @param array $clauses Query clauses.
	 * @param array $args Array of args to pass to the query method.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	protected function prepare_where_query( $clauses, $args = array() ) {
		global $wpdb;
		$transfer_table    = (new Transfer())->get_table_name();
		$clauses['join']  .= " LEFT JOIN  {$wpdb->prefix}{$transfer_table} AS {$transfer_table} ON {$this->table_name}.id = {$transfer_table}.expense_id"; // phpcs:ignore
		$clauses['where'] .= " AND {$transfer_table}.expense_id IS NULL"; // phpcs:ignore
		$clauses['where'] .= $wpdb->prepare( " AND {$this->table_name}.type = %s", $this->object_type ); // phpcs:ignore

		return parent::prepare_where_query( $clauses, $args );
	}

	/*
	|--------------------------------------------------------------------------
	| Helper methods.
	|--------------------------------------------------------------------------
	| Utility methods which don't directly relate to this object but may be
	| used by this object.
	*/
}
