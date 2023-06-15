<?php

namespace EverAccounting\Models;

defined( 'ABSPATH' ) || exit;

/**
 * Class Transaction.
 *
 * @since   1.0.0
 * @package EverAccounting\Models
 */
class Transaction extends Model {
	/**
	 * Table name.
	 *
	 * This is also used as table alias.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const TABLE_NAME = 'ea_transactions';

	/**
	 * Object type.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const OBJECT_TYPE = 'transaction';

	/**
	 * Cache group.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const CACHE_GROUP = 'ea_transactions';

	/**
	 * Meta type declaration for the object.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const META_TYPE = 'transaction';

	/**
	 * Core data for this object. Name value pairs (name + default value).
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $core_data = array(
		'type'           => 'payment',
		'number'         => '',
		'date'           => null,
		'amount'         => 0.00,
		'currency_code'  => 'USD',   // currency code of the transaction.
		'exchange_rate'  => 1,    // exchange rate of transaction currency to base currency.
		'reference'      => '',   // reference number or other identifier for the transaction.
		'note'           => '',   // additional notes about the transaction.
		'account_id'     => null, // ID of the account associated with the transaction.
		'document_id'    => null, // ID of the document associated with the transaction.
		'contact_id'     => null, // ID of the contact associated with the transaction.
		'category_id'    => null, // ID of the category associated with the transaction.
		'payment_method' => '',   // method of payment used for the transaction.
		'attachment_id'  => null, // ID of any attachments associated with the transaction.
		'parent_id'      => 0,    // ID of the parent transaction, if any.
		'reconciled'     => 0,    // whether the transaction has been reconciled.
		'creator_id'     => null, // ID of the user who created the transaction.
		'uuid'           => '',   // token used for payment processing, if any.
		'updated_at'     => null, // date and time the transaction was last updated.
		'created_at'     => null, // date and time the transaction was created.
	);

	/**
	 * Model constructor.
	 *
	 * @param int|object|array $data Object ID, post object, or array of data.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $data = 0 ) {
		$this->core_data['uuid']          = eac_generate_uuid();
		$this->core_data['currency_code'] = eac_get_base_currency();
		parent::__construct( $data );
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
	 * Transaction type.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 * @return mixed|null
	 */
	public function get_type( $context = 'edit' ) {
		return $this->get_prop( 'type', $context );
	}

	/**
	 * Set contact's email.
	 *
	 * @param string $value Email.
	 *
	 * @since 1.0.2
	 */
	public function set_type( $value ) {
		if ( array_key_exists( $value, eac_get_transaction_types() ) ) {
			$this->set_prop( 'type', $value );
		}
	}

	/**
	 * Transaction number.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.1.0
	 * @return mixed|null
	 */
	public function get_number( $context = 'edit' ) {
		if ( ! $this->exists() && empty( $this->data['number'] ) ) {
			$this->set_number( $this->get_next_number() );
		}

		return $this->get_prop( 'number', $context );
	}

	/**
	 * Set transaction number.
	 *
	 * @param string $value Prefix.
	 *
	 * @since 1.1.0
	 */
	public function set_number( $value ) {
		$this->set_prop( 'number', $value );
	}

	/**
	 * Paid at time.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 * @return string
	 */
	public function get_date( $context = 'edit' ) {
		return $this->get_date_prop( 'date', $context, 'Y-m-d' );
	}

	/**
	 * Set transaction date.
	 *
	 * @param string $value Transaction date.
	 *
	 * @since 1.0.2
	 */
	public function set_date( $value ) {
		$this->set_date_prop( 'date', $value );
	}

	/**
	 * Transaction Amount.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 * @return mixed|null
	 */
	public function get_amount( $context = 'edit' ) {
		return $this->get_prop( 'amount', $context );
	}

	/**
	 * Set transaction amount.
	 *
	 * @param string $value Amount.
	 *
	 * @since 1.0.2
	 */
	public function set_amount( $value ) {
		$this->set_prop( 'amount', eac_format_decimal( $value, 4 ) );
	}


	/**
	 * Currency code.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 * @return mixed|null
	 */
	public function get_currency_code( $context = 'edit' ) {
		return $this->get_prop( 'currency_code', $context );
	}

	/**
	 * Set currency code.
	 *
	 * @param string $value Currency code.
	 *
	 * @since 1.0.2
	 */
	public function set_currency_code( $value ) {
		$this->set_prop( 'currency_code', eac_clean( $value ) );
	}

	/**
	 * Currency rate.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 * @return mixed|null
	 */
	public function get_exchange_rate( $context = 'edit' ) {
		return $this->get_prop( 'exchange_rate', $context );
	}

	/**
	 * Set currency rate.
	 *
	 * @param string $value Currency rate.
	 *
	 * @since 1.0.2
	 */
	public function set_exchange_rate( $value ) {
		$this->set_prop( 'exchange_rate', eac_format_decimal( $value, 8 ) );
	}

	/**
	 * Transaction reference.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 * @return mixed|null
	 */
	public function get_reference( $context = 'edit' ) {
		return $this->get_prop( 'reference', $context );
	}

	/**
	 * Set reference.
	 *
	 * @param string $value Reference.
	 *
	 * @since 1.0.2
	 */
	public function set_reference( $value ) {
		$this->set_prop( 'reference', eac_clean( $value ) );
	}

	/**
	 * Description.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 * @return mixed|null
	 */
	public function get_note( $context = 'edit' ) {
		return $this->get_prop( 'note', $context );
	}

	/**
	 * Set note.
	 *
	 * @param string $value Description.
	 *
	 * @since 1.0.2
	 */
	public function set_note( $value ) {
		$this->set_prop( 'note', eac_clean( $value ) );
	}

	/**
	 * Transaction from account id.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 * @return mixed|null
	 */
	public function get_account_id( $context = 'edit' ) {
		return $this->get_prop( 'account_id', $context );
	}

	/**
	 * Set account id.
	 *
	 * @param int $value Account id.
	 *
	 * @since 1.0.2
	 */
	public function set_account_id( $value ) {
		$this->set_prop( 'account_id', absint( $value ) );
	}


	/**
	 * Get document id.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 * @return mixed|null
	 */
	public function get_document_id( $context = 'edit' ) {
		return $this->get_prop( 'document_id', $context );
	}

	/**
	 * Set invoice id.
	 *
	 * @param int $value Invoice id.
	 *
	 * @since 1.0.2
	 */
	public function set_document_id( $value ) {
		$this->set_prop( 'document_id', absint( $value ) );
	}


	/**
	 * Contact id.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 * @return mixed|null
	 */
	public function get_contact_id( $context = 'edit' ) {
		return $this->get_prop( 'contact_id', $context );
	}

	/**
	 * Set contact id.
	 *
	 * @param int $value Contact id.
	 *
	 * @since 1.0.2
	 */
	public function set_contact_id( $value ) {
		$this->set_prop( 'contact_id', absint( $value ) );
	}


	/**
	 * Contact id.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 * @return mixed|null
	 */
	public function get_customer_id( $context = 'edit' ) {
		return $this->get_contact_id( $context );
	}

	/**
	 * Category ID.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 * @return mixed|null
	 */
	public function get_category_id( $context = 'edit' ) {
		return $this->get_prop( 'category_id', $context );
	}

	/**
	 * Set category id.
	 *
	 * @param int $value Category id.
	 *
	 * @since 1.0.2
	 */
	public function set_category_id( $value ) {
		$this->set_prop( 'category_id', absint( $value ) );
	}

	/**
	 * Transaction payment methods.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 * @return mixed|null
	 */
	public function get_payment_method( $context = 'edit' ) {
		return $this->get_prop( 'payment_method', $context );
	}

	/**
	 * Set payment method.
	 *
	 * @param string $value Payment method.
	 *
	 * @since 1.0.2
	 */
	public function set_payment_method( $value ) {
		if ( array_key_exists( $value, eac_get_payment_methods() ) ) {
			$this->set_prop( 'payment_method', $value );
		}
	}

	/**
	 * Get attachment url.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 * @return mixed|null
	 */
	public function get_attachment_id( $context = 'edit' ) {
		return $this->get_prop( 'attachment_id', $context );
	}

	/**
	 * Set attachment.
	 *
	 * @param string $value Attachment ID.
	 *
	 * @since 1.0.2
	 */
	public function set_attachment_id( $value ) {
		$this->set_prop( 'attachment_id', intval( $value ) );
	}

	/**
	 * Get associated parent payment id.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 * @return mixed|null
	 */
	public function get_parent_id( $context = 'edit' ) {
		return $this->get_prop( 'parent_id', $context );
	}

	/**
	 * Set parent id.
	 *
	 * @param string $value Parent id.
	 *
	 * @since 1.0.2
	 */
	public function set_parent_id( $value ) {
		$this->set_prop( 'parent_id', absint( $value ) );
	}

	/**
	 * Get if reconciled
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 * @return bool
	 */
	public function get_reconciled( $context = 'edit' ) {
		return (bool) $this->get_prop( 'reconciled', $context );
	}


	/**
	 * Set if reconciled.
	 *
	 * @param string $value yes or no.
	 *
	 * @since 1.0.2
	 */
	public function set_reconciled( $value ) {
		$this->set_prop( 'reconciled', $this->string_to_int( $value ) );
	}

	/**
	 * Get the creator id.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return int
	 */
	public function get_creator_id( $context = 'edit' ) {
		return $this->get_prop( 'creator_id', $context );
	}

	/**
	 * Set the creator id.
	 *
	 * @param int $creator_id creator id.
	 */
	public function set_creator_id( $creator_id ) {
		$this->set_prop( 'creator_id', absint( $creator_id ) );
	}

	/**
	 * Get the unique_hash.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_uuid( $context = 'edit' ) {
		return $this->get_prop( 'uuid', $context );
	}

	/**
	 * Set the uuid.
	 *
	 * @param string $uuid uuid.
	 */
	public function set_uuid( $uuid ) {
		$this->set_prop( 'uuid', $uuid );
	}

	/**
	 * Get the date updated.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_updated_at( $context = 'edit' ) {
		return $this->get_prop( 'updated_at', $context );
	}

	/**
	 * Set the date updated.
	 *
	 * @param string $updated_at date updated.
	 */
	public function set_updated_at( $updated_at ) {
		$this->set_date_prop( 'updated_at', $updated_at );
	}

	/**
	 * Get the date created.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_created_at( $context = 'edit' ) {
		return $this->get_prop( 'created_at', $context );
	}

	/**
	 * Set the date created.
	 *
	 * @param string $created_at date created.
	 */
	public function set_created_at( $created_at ) {
		$this->set_date_prop( 'created_at', $created_at );
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
	 * @since 1.0.0
	 * @return true|\WP_Error True on success, WP_Error on failure.
	 */
	public function save() {
		// We must have an account id.
		if ( empty( $this->get_account_id() ) ) {
			return new \WP_Error( 'missing_required', __( 'Account is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_uuid() ) ) {
			$this->set_uuid( eac_generate_uuid() );
		}

		// Creator ID.
		if ( empty( $this->get_creator_id() ) && ! $this->exists() && is_user_logged_in() ) {
			$this->set_creator_id( get_current_user_id() );
		}

		// If It's update, set the updated date.
		if ( $this->exists() ) {
			$this->set_updated_at( current_time( 'mysql' ) );
		}

		// If date created is not set, set it to now.
		if ( empty( $this->get_created_at() ) ) {
			$this->set_created_at( current_time( 'mysql' ) );
		}

		// if number is not set, set it to the next available number from database based on type.
		if ( empty( $this->get_number() ) ) {
			$this->set_number( $this->get_next_number() );
		}

		// todo if document id is set, the amount should be the same as the document amount.

		return parent::save();
	}

	/**
	 * Prepare order by query.
	 *
	 * @param array $clauses Query clauses.
	 * @param array $args Array of args to pass to the query method.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	protected function prepare_order_by_query( $clauses, $args = array() ) {
		if ( ! empty( $args['orderby'] ) && 'amount' === $args['orderby'] ) {
			$order = strtoupper( $args['order'] );
			// divide by currency rate to get the amount in the account currency and then cast to decimal and order by.
			$clauses['orderby'] = 'CAST( ' . $this->table_alias . '.amount / ' . $this->table_alias . '.exchange_rate AS DECIMAL( 20, 4 ) ) ' . $order . ' ';
		}

		return parent::prepare_order_by_query( $clauses, $args );
	}

	/*
	|--------------------------------------------------------------------------
	| Helper methods.
	|--------------------------------------------------------------------------
	| Utility methods which don't directly relate to this object but may be
	| used by this object.
	*/


	/**
	 * Get max voucher number.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_max_number() {
		global $wpdb;

		return (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT MAX(REGEXP_REPLACE(number, '[^0-9]', '')) FROM {$this->table} WHERE type = %s",
				$this->get_type()
			)
		);
	}

	/**
	 * Get voucher number prefix.
	 *
	 * @since 1.0.2
	 * @return string
	 */
	public function get_number_prefix() {
		return strtoupper( substr( $this->get_type(), 0, 3 ) ) . '-';
	}

	/**
	 * Get voucher number digits.
	 *
	 * @since 1.0.0
	 * @return int
	 */
	public function get_number_digits() {
		return 6;
	}

	/**
	 * Set next transaction number.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_next_number() {
		$number = $this->get_max_number();
		$prefix = $this->get_number_prefix();
		$number = absint( $number ) + 1;

		// Pad the number with zeros.
		$number = str_pad( $number, $this->get_number_digits(), '0', STR_PAD_LEFT );

		return implode( '', [ $prefix, $number ] );
	}

	/**
	 * Get formatted amount.
	 *
	 * @since 1.0.2
	 * @return string
	 */
	public function get_formatted_amount() {
		return eac_format_money( $this->get_amount(), $this->get_currency_code() );
	}
}
