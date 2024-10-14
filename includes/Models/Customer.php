<?php

namespace EverAccounting\Models;

defined( 'ABSPATH' ) || exit;

/**
 * Class Customer
 *
 * @since   1.1.0
 * @package EAccounting\Models
 */
class Customer extends Contact {
	/**
	 * The type of the object. Used for actions and filters.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $object_type = 'customer';

	/**
	 * Create a new model instance.
	 *
	 * @param string|array|object $attributes The model attributes.
	 */
	public function __construct( $attributes = array() ) {
		$this->attributes['type'] = $this->get_object_type();
		$this->query_vars['type'] = $this->get_object_type();
		parent::__construct( $attributes );
	}

	/*
	|--------------------------------------------------------------------------
	| Accessors, Mutators and Relationship Methods
	|--------------------------------------------------------------------------
	| This section contains methods for getting and setting attributes (accessors
	| and mutators) as well as defining relationships between models.
	|--------------------------------------------------------------------------
	*/

	/*
	|--------------------------------------------------------------------------
	| Helper Methods
	|--------------------------------------------------------------------------
	| This section contains utility methods that are not directly related to this
	| object but can be used to support its functionality.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Update total paid amount.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function update_amount_paid() {
		global $wpdb;
		$amount = (float) $wpdb->get_var( $wpdb->prepare(
			"SELECT SUM( amount / exchange_rate)
			 FROM {$wpdb->prefix}ea_transactions
			 WHERE contact_id = %d
			 AND type = 'payment'
			 AND status = 'completed'",
			$this->id
		) );
		$this->set_meta( 'total_paid', $amount );
		return ! is_wp_error( $this->save() ) ? $amount : false;
	}

	/**
	 * Get edit URL.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_edit_url() {
		return admin_url( 'admin.php?page=eac-sales&tab=customers&action=edit&id=' . $this->id );
	}

	/**
	 * Get view URL.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_view_url() {
		return admin_url( 'admin.php?page=eac-sales&tab=customers&action=view&id=' . $this->id );
	}
}
