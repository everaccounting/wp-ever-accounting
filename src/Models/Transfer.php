<?php

namespace EverAccounting\Models;

class Transfer extends Model {
	/**
	 * The table associated with the model.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $table = 'ea_transfers';

	/**
	 * Table columns.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $columns = array(
		'id',
		'date',
		'from_account_id',
		'amount',
		'to_account_id',
		'income_id',
		'expense_id',
		'payment_method',
		'reference',
		'description',
		'creator_id',
		'date_created',
	);

	/**
	 * Model's data container.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $attributes = array();

	/**
	 * Model's casts data.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	protected $casts = array(
		'id'              => 'int',
		'from_account_id' => 'int',
		'to_account_id'   => 'int',
		'income_id'       => 'int',
		'expense_id'      => 'int',
		'creator_id'      => 'int',
		'amount'          => 'double',
		'date'            => 'datetime',
		'date_created'    => 'datetime',
	);

	/**
	 * Save the object to the database.
	 *
	 * @since 1.0.0
	 * @return \WP_Error|true True on success, WP_Error on failure.
	 */
	public function save() {
		if ( empty( $this->from_account_id ) ) {
			return new \WP_Error( 'missing_required', __( 'From account is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $this->to_account_id ) ) {
			return new \WP_Error( 'missing_required', __( 'To account is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $this->amount ) ) {
			return new \WP_Error( 'missing_required', __( 'Transfer amount is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $this->date ) ) {
			return new \WP_Error( 'missing_required', __( 'Transfer date is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $this->payment_method ) ) {
			return new \WP_Error( 'missing_required', __( 'Payment method is required.', 'wp-ever-accounting' ) );
		}

		return parent::save();
	}

}
