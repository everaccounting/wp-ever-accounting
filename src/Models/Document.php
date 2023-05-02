<?php

namespace EverAccounting\Models;

defined( 'ABSPATH' ) || exit;

/**
 * Class Document.
 *
 * @since   1.0.0
 * @package EverAccounting\Models
 */
abstract class Document extends Model {
	/**
	 * Table name.
	 *
	 * This is also used as table alias.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const TABLE_NAME = 'ea_documents';

	/**
	 * Object type.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const OBJECT_TYPE = 'document';

	/**
	 * Cache group.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const CACHE_GROUP = 'ea_documents';

	/**
	 * Core data for this object. Name value pairs (name + default value).
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $core_data = array(
		'document_number' => '',
		'type'            => '',
		'order_number'    => '',
		'status'          => 'draft',
		'issue_date'      => null,
		'due_date'        => null,
		'payment_date'    => null,
		'category_id'     => null,
		'contact_id'      => null,
		'address'         => array(
			'name'       => '',
			'company'    => '',
			'street'     => '',
			'city'       => '',
			'state'      => '',
			'postcode'   => '',
			'country'    => '',
			'email'      => '',
			'phone'      => '',
			'vat_number' => '',
		),
		'discount'        => 0.00,
		'discount_type'   => 'percentage',
		'subtotal'        => 0.00,
		'total_tax'       => 0.00,
		'total_discount'  => 0.00,
		'total_fees'      => 0.00,
		'total_shipping'  => 0.00,
		'total'           => 0.00,
		'tax_inclusive'   => 1,
		'note'            => '',
		'terms'           => '',
		'attachment_id'   => null,
		'currency_code'   => null,
		'currency_rate'   => 1,
		'key'             => null,
		'parent_id'       => null,
		'creator_id'      => null,
		'updated_at'      => null,
		'created_at'      => null,
	);


	/**
	 * document items will be stored here, sometimes before they persist in the DB.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $items = array();

	/**
	 * document items that need deleting are stored here.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $items_to_delete = array();


	/**
	 * Returns all data for this object.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.0.0
	 * @return array
	 */
	public function get_data( $context = 'edit' ) {
		return array(
			array_merge(
				parent::get_data(),
				array(
					'items' => $this->get_items(),
				)
			),
		);
	}

	/**
	 * Get supported statuses.
	 *
	 * @since 1.1.0
	 * @return array
	 */
	public function get_statuses() {
		return array();
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	|
	| Methods which create, read, update and delete documents from the database.
	| Written in abstract fashion so that the way documents are stored can be
	| changed more easily in the future.
	|
	| A save method is included for convenience (chooses update or create based
	| on if the order exists yet).
	|
	*/

	/**
	 * Saves an object in the database.
	 *
	 * @since 1.0.0
	 * @return true|\WP_Error True on success, WP_Error on failure.
	 */
	public function save() {
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

		parent::save();
		$this->save_items();

		return $this->exists();
	}

	/**
	 * Save all document items which are part of this order.
	 */
	protected function save_items() {

		foreach ( $this->items_to_delete as $item ) {
			if ( $item->exists() ) {
				$item->delete();
			}
		}

		$this->items_to_delete = array();

		$items = array_filter( $this->items );
		// Add/save items.
		foreach ( $items as $item ) {
			$item->set_document_id( $this->get_id() );
			$item->set_currency_code( $this->get_currency_code() );
			$item->save();
		}
	}

	/**
	 * Delete notes.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public function delete_notes() {
		if ( $this->exists() ) {
			global $wpdb;
			$wpdb->delete( $wpdb->prefix . 'ea_notes', array( 'document_id' => $this->get_id() ) );
		}
	}

	/**
	 * Delete all transactions.
	 *
	 * @since 1.1.0
	 */
	public function delete_payments() {
		if ( $this->exists() ) {
			global $wpdb;
			$wpdb->delete( $wpdb->prefix . 'ea_transactions', array( 'document_id' => $this->get_id() ) );
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Return the document number.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 *
	 * @return string
	 */
	public function get_document_number( $context = 'edit' ) {
		return $this->get_prop( 'document_number', $context );
	}

	/**
	 * Get internal type.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_type( $context = 'edit' ) {
		return $this->get_prop( 'type', $context );
	}

	/**
	 * Return the order number.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 *
	 * @return string
	 */
	public function get_order_number( $context = 'edit' ) {
		return $this->get_prop( 'order_number', $context );
	}

	/**
	 * Return the status.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 *
	 * @return string
	 */
	public function get_status( $context = 'edit' ) {
		return $this->get_prop( 'status', $context );
	}

	/**
	 * Return the issued at.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 *
	 * @return string
	 */
	public function get_issue_date( $context = 'edit' ) {
		return $this->get_date_prop( 'issue_date', $context, 'Y-m-d' );
	}

	/**
	 * Return the due at.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 *
	 * @return string
	 */
	public function get_due_date( $context = 'edit' ) {
		return $this->get_prop( 'due_date', $context );
	}

	/**
	 * Return the due at.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 *
	 * @return string
	 */
	public function get_payment_date( $context = 'edit' ) {
		return $this->get_prop( 'payment_date', $context );
	}

	/**
	 * Return the category id.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 *
	 * @return string
	 */
	public function get_category_id( $context = 'edit' ) {
		return $this->get_prop( 'category_id', $context );
	}

	/**
	 * Return the contact id.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 *
	 * @return string
	 */
	public function get_contact_id( $context = 'edit' ) {
		return $this->get_prop( 'contact_id', $context );
	}

	/**
	 * Return the address.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 *
	 * @return string
	 */
	public function get_address( $context = 'edit' ) {
		return $this->get_prop( 'address', $context );
	}

	/**
	 * Gets a prop for a getter method.
	 *
	 * @param string $prop Name of prop to get.
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since  1.1.0
	 *
	 * @return mixed
	 */
	protected function get_address_prop( $prop, $context = 'view' ) {
		$value = null;

		if ( array_key_exists( $prop, $this->data['address'] ) ) {
			$value = isset( $this->changes['address'][ $prop ] ) ? $this->changes['address'][ $prop ] : $this->data['address'][ $prop ];

			if ( 'view' === $context ) {
				$value = apply_filters( $this->get_hook_prefix() . 'address_' . $prop, $value, $this );
			}
		}

		return $value;
	}

	/**
	 * Get name.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	public function get_name( $context = 'view' ) {
		return $this->get_address_prop( 'name', $context );
	}

	/**
	 * Get company.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	public function get_company( $context = 'view' ) {
		return $this->get_address_prop( 'company', $context );
	}

	/**
	 * Get street.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	public function get_street( $context = 'view' ) {
		return $this->get_address_prop( 'street', $context );
	}

	/**
	 * Get city.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	public function get_city( $context = 'view' ) {
		return $this->get_address_prop( 'city', $context );
	}

	/**
	 * Get state.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	public function get_state( $context = 'view' ) {
		return $this->get_address_prop( 'state', $context );
	}

	/**
	 * Get postcode.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	public function get_postcode( $context = 'view' ) {
		return $this->get_address_prop( 'postcode', $context );
	}

	/**
	 * Get country.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	public function get_country( $context = 'view' ) {
		return $this->get_address_prop( 'country', $context );
	}

	/**
	 * Get email.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	public function get_email( $context = 'view' ) {
		return $this->get_address_prop( 'email', $context );
	}

	/**
	 * Get phone.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	public function get_phone( $context = 'view' ) {
		return $this->get_address_prop( 'phone', $context );
	}

	/**
	 * Get vat_number.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	public function get_vat_number( $context = 'view' ) {
		return $this->get_address_prop( 'vat_number', $context );
	}

	/**
	 * Get the invoice discount total.
	 *
	 * @param string $context View or edit context.
	 *
	 * @since 1.1.0
	 *
	 * @return float
	 */
	public function get_discount( $context = 'view' ) {
		return $this->get_prop( 'discount', $context );
	}

	/**
	 * Get the invoice discount type.
	 *
	 * @param string $context View or edit context.
	 *
	 * @since 1.1.0
	 *
	 * @return float
	 */
	public function get_discount_type( $context = 'view' ) {
		return $this->get_prop( 'discount_type', $context );
	}

	/**
	 * Get the invoice subtotal.
	 *
	 * @param string $context View or edit context.
	 *
	 * @since 1.1.0
	 *
	 * @return float
	 */
	public function get_subtotal( $context = 'view' ) {
		return (float) $this->get_prop( 'subtotal', $context );
	}

	/**
	 * Get the invoice tax total.
	 *
	 * @param string $context View or edit context.
	 *
	 * @since 1.1.0
	 *
	 * @return float
	 */
	public function get_total_tax( $context = 'view' ) {
		return (float) $this->get_prop( 'total_tax', $context );
	}

	/**
	 * Get the invoice discount total.
	 *
	 * @param string $context View or edit context.
	 *
	 * @since 1.1.0
	 *
	 * @return float
	 */
	public function get_total_discount( $context = 'view' ) {
		return (float) $this->get_prop( 'total_discount', $context );
	}

	/**
	 * Get total fees.
	 *
	 * @param string $context View or edit context.
	 *
	 * @since 1.1.0
	 *
	 * @return float
	 */
	public function get_total_fees( $context = 'view' ) {
		return (float) $this->get_prop( 'total_fees', $context );
	}

	/**
	 * Get total shipping.
	 *
	 * @param string $context View or edit context.
	 *
	 * @since 1.1.0
	 *
	 * @return float
	 */
	public function get_total_shipping( $context = 'view' ) {
		return (float) $this->get_prop( 'total_shipping', $context );
	}

	/**
	 * Get the document total.
	 *
	 * @param string $context View or edit context.
	 *
	 * @since 1.1.0
	 *
	 * @return float
	 */
	public function get_total( $context = 'view' ) {
		return (float) $this->get_prop( 'total', $context );
	}

	/**
	 * Get tax inclusive or not.
	 *
	 * @param string $context View or edit context.
	 *
	 * @since 1.1.0
	 *
	 * @return mixed|null
	 */
	public function get_tax_inclusive( $context = 'edit' ) {
		if ( ! $this->exists() ) {
			return eaccounting_prices_include_tax();
		}

		return $this->get_prop( 'tax_inclusive', $context );
	}

	/**
	 * Return the note.
	 *
	 * @param string $context View or edit context.
	 *
	 * @since  1.1.0
	 *
	 * @return string
	 */
	public function get_note( $context = 'edit' ) {
		return $this->get_prop( 'note', $context );
	}

	/**
	 * Return the terms.
	 *
	 * @param string $context View or edit context.
	 *
	 * @since  1.1.0
	 *
	 * @return string
	 */
	public function get_terms( $context = 'edit' ) {
		return $this->get_prop( 'terms', $context );
	}

	/**
	 * Return the attachment.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 *
	 * @return string
	 */
	public function get_attachment_id( $context = 'edit' ) {
		return $this->get_prop( 'attachment_id', $context );
	}

	/**
	 * Return the currency code.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 *
	 * @return string
	 */
	public function get_currency_code( $context = 'edit' ) {
		return $this->get_prop( 'currency_code', $context );
	}

	/**
	 * Return the currency rate.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 *
	 * @return string
	 */
	public function get_currency_rate( $context = 'edit' ) {
		return $this->get_prop( 'currency_rate', $context );
	}

	/**
	 * Return the invoice key.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 *
	 * @return string
	 */
	public function get_key( $context = 'edit' ) {
		return $this->get_prop( 'key', $context );
	}

	/**
	 * Return the parent id.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 *
	 * @return string
	 */
	public function get_parent_id( $context = 'edit' ) {
		return $this->get_prop( 'parent_id', $context );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/
	/**
	 * set the number.
	 *
	 * @param string $document_number .
	 *
	 * @since  1.1.0
	 */
	public function set_document_number( $document_number ) {
		$this->set_prop( 'document_number', eac_clean( $document_number ) );
	}

	/**
	 * set the number.
	 *
	 * @param string $order_number Order number.
	 *
	 * @since  1.1.0
	 */
	public function set_order_number( $order_number ) {
		$this->set_prop( 'order_number', eac_clean( $order_number ) );
	}

	/**
	 * set the number.
	 *
	 * @param string $type Type of document.
	 *
	 * @since  1.1.0
	 */
	public function set_type( $type ) {
		$this->set_prop( 'type', eac_clean( $type ) );
	}

	/**
	 * set the status.
	 *
	 * @param string $status .
	 *
	 * @since  1.1.0
	 *
	 * @return string[]
	 */
	public function set_status( $status ) {
		$old_status = $this->get_status();
		// If setting the status, ensure it's set to a valid status.
		if ( true === $this->object_read ) {
			// Only allow valid new status.
			if ( ! array_key_exists( $status, $this->get_statuses() ) ) {
				$status = 'draft';
			}

			// If the old status is set but unknown (e.g. draft) assume its pending for action usage.
			if ( $old_status && ! array_key_exists( $old_status, $this->get_statuses() ) ) {
				$old_status = 'draft';
			}
		}

		$this->set_prop( 'status', $status );

		return array(
			'from' => $old_status,
			'to'   => $status,
		);
	}

	/**
	 * Set date when the invoice was created.
	 *
	 * @param string $date Value to set.
	 *
	 * @since 1.1.0
	 */
	public function set_issue_date( $date ) {
		$this->set_date_prop( 'issue_date', $date );
	}

	/**
	 * set the due at.
	 *
	 * @param string $due_date .
	 *
	 * @since  1.1.0
	 */
	public function set_due_date( $due_date ) {
		$this->set_date_prop( 'due_date', $due_date );
	}

	/**
	 * set the completed at.
	 *
	 * @param string $payment_date .
	 *
	 * @since  1.1.0
	 */
	public function set_payment_date( $payment_date ) {
		if ( $payment_date && $this->is_paid() ) {
			$this->set_date_prop( 'payment_date', $payment_date );
		}
	}

	/**
	 * set the category id.
	 *
	 * @param int $category_id .
	 *
	 * @since  1.1.0
	 */
	public function set_category_id( $category_id ) {
		$this->set_prop( 'category_id', absint( $category_id ) );
	}

	/**
	 * set the customer_id.
	 *
	 * @param int $contact_id .
	 *
	 * @since  1.1.0
	 */
	public function set_contact_id( $contact_id ) {
		$this->set_prop( 'contact_id', absint( $contact_id ) );
	}

	/**
	 * set the address.
	 *
	 * @param int $address .
	 *
	 * @since  1.1.0
	 */
	public function set_address( $address ) {
		$this->set_prop( 'address', maybe_unserialize( $address ) );
	}

	/**
	 * Sets a prop for a setter method.
	 *
	 * @param string $prop Name of prop to set.
	 * @param mixed  $value Value of the prop.
	 *
	 * @since 1.1.0
	 */
	protected function set_address_prop( $prop, $value ) {
		if ( array_key_exists( $prop, $this->data['address'] ) ) {
			if ( true === $this->object_read ) {
				if ( $value !== $this->data['address'][ $prop ] || ( isset( $this->changes['address'] ) && array_key_exists( $prop, $this->changes['address'] ) ) ) {
					$this->changes['address'][ $prop ] = $value;
				}
			} else {
				$this->data['address'][ $prop ] = $value;
			}
		}
	}

	/**
	 * Set name.
	 *
	 * @param string $name name.
	 *
	 * @since 1.1.0
	 */
	public function set_name( $name ) {
		$this->set_address_prop( 'name', eac_clean( $name ) );
	}

	/**
	 * Set company.
	 *
	 * @param string $company company.
	 *
	 * @since 1.1.0
	 */
	public function set_company( $company ) {
		$this->set_address_prop( 'company', eac_clean( $company ) );
	}

	/**
	 * Set street.
	 *
	 * @param string $street street.
	 *
	 * @since 1.1.0
	 */
	public function set_street( $street ) {
		$this->set_address_prop( 'street', eac_clean( $street ) );
	}

	/**
	 * Set city.
	 *
	 * @param string $city city.
	 *
	 * @since 1.1.0
	 */
	public function set_city( $city ) {
		$this->set_address_prop( 'city', eac_clean( $city ) );
	}

	/**
	 * Set state.
	 *
	 * @param string $state state.
	 *
	 * @since 1.1.0
	 */
	public function set_state( $state ) {
		$this->set_address_prop( 'state', eac_clean( $state ) );
	}

	/**
	 * Set postcode.
	 *
	 * @param string $postcode postcode.
	 *
	 * @since 1.1.0
	 */
	public function set_postcode( $postcode ) {
		$this->set_address_prop( 'postcode', eac_clean( $postcode ) );
	}

	/**
	 * Set country.
	 *
	 * @param string $country country.
	 *
	 * @since 1.1.0
	 */
	public function set_country( $country ) {
		$this->set_address_prop( 'country', eac_clean( $country ) );
	}

	/**
	 * Set email.
	 *
	 * @param string $email email.
	 *
	 * @since 1.1.0
	 */
	public function set_email( $email ) {
		$this->set_address_prop( 'email', sanitize_email( $email ) );
	}

	/**
	 * Set phone.
	 *
	 * @param string $phone phone.
	 *
	 * @since 1.1.0
	 */
	public function set_phone( $phone ) {
		$this->set_address_prop( 'phone', eac_clean( $phone ) );
	}

	/**
	 * Set vat_number.
	 *
	 * @param string $vat_number vat_number.
	 *
	 * @since 1.1.0
	 */
	public function set_vat_number( $vat_number ) {
		$this->set_address_prop( 'vat_number', eac_clean( $vat_number ) );
	}

	/**
	 * set the discount.
	 *
	 * @param float $discount .
	 *
	 * @since  1.1.0
	 */
	public function set_discount( $discount ) {
		$this->set_prop( 'discount', abs( eac_format_decimal( $discount, 4 ) ) );
	}

	/**
	 * set the discount type.
	 *
	 * @param float $discount_type .
	 *
	 * @since  1.1.0
	 */
	public function set_discount_type( $discount_type ) {
		if ( in_array( $discount_type, array( 'percentage', 'fixed' ), true ) ) {
			$this->set_prop( 'discount_type', $discount_type );
		}
	}

	/**
	 * set the subtotal.
	 *
	 * @param float $subtotal .
	 *
	 * @since  1.1.0
	 */
	public function set_subtotal( $subtotal ) {
		$this->set_prop( 'subtotal', eac_format_decimal( $subtotal, 4 ) );
	}

	/**
	 * set the tax.
	 *
	 * @param float $tax .
	 *
	 * @since  1.1.0
	 */
	public function set_total_tax( $tax ) {
		$this->set_prop( 'total_tax', eac_format_decimal( $tax, 4 ) );
	}

	/**
	 * set the tax.
	 *
	 * @param float $discount .
	 *
	 * @since  1.1.0
	 */
	public function set_total_discount( $discount ) {
		$this->set_prop( 'total_discount', eac_format_decimal( $discount, 4 ) );
	}

	/**
	 * set the fees.
	 *
	 * @param float $fees .
	 *
	 * @since  1.1.0
	 */
	public function set_total_fees( $fees ) {
		$this->set_prop( 'total_fees', eac_format_decimal( $fees, 4 ) );
	}

	/**
	 * set the shipping.
	 *
	 * @param float $shipping .
	 *
	 * @since  1.1.0
	 */
	public function set_total_shipping( $shipping ) {
		$this->set_prop( 'total_shipping', eac_format_decimal( $shipping, 4 ) );
	}

	/**
	 * set the total.
	 *
	 * @param float $total .
	 *
	 * @since  1.1.0
	 */
	public function set_total( $total ) {
		$this->set_prop( 'total', eac_format_decimal( $total, 4 ) );
	}

	/**
	 * set the note.
	 *
	 * @param string $type .
	 *
	 * @since  1.1.0
	 */
	public function set_tax_inclusive( $type ) {
		$this->set_prop( 'tax_inclusive', $this->string_to_bool( $type ) );
	}

	/**
	 * set the note.
	 *
	 * @param string $note .
	 *
	 * @since  1.1.0
	 */
	public function set_note( $note ) {
		$this->set_prop( 'note', sanitize_textarea_field( $note ) );
	}

	/**
	 * set the note.
	 *
	 * @param string $terms .
	 *
	 * @since  1.1.0
	 */
	public function set_terms( $terms ) {
		$this->set_prop( 'terms', sanitize_textarea_field( $terms ) );
	}

	/**
	 * set the attachment.
	 *
	 * @param string $attachment Attachment id.
	 *
	 * @since  1.1.0
	 */
	public function set_attachment_id( $attachment ) {
		$this->set_prop( 'attachment_id', absint( $attachment ) );
	}

	/**
	 * set the currency code.
	 *
	 * @param string $currency_code .
	 *
	 * @since  1.1.0
	 */
	public function set_currency_code( $currency_code ) {
		$this->set_prop( 'currency_code', eac_clean( $currency_code ) );
		if ( $this->get_currency_code() && ( ! $this->exists() || array_key_exists( 'currency_code', $this->changes ) ) ) {
			$currency = eaccounting_get_currency( $this->get_currency_code() );
			$this->set_currency_rate( $currency->get_rate() );
		}
	}


	/**
	 * set the currency rate.
	 *
	 * @param double $currency_rate .
	 *
	 * @since  1.1.0
	 */
	public function set_currency_rate( $currency_rate ) {
		if ( ! empty( $currency_rate ) ) {
			$this->set_prop( 'currency_rate', eac_format_decimal( $currency_rate, 7 ) );
		}
	}

	/**
	 * set the parent id.
	 *
	 * @param int $parent_id .
	 *
	 * @since  1.1.0
	 */
	public function set_parent_id( $parent_id ) {
		$this->set_prop( 'parent_id', absint( $parent_id ) );
	}

	/**
	 * Set the invoice key.
	 *
	 * @param string $value New key.
	 *
	 * @since 1.1.0
	 */
	public function set_key( $value ) {
		$key = strtolower( eac_clean( $value ) );
		$this->set_prop( 'key', substr( $key, 0, 30 ) );
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
	| Non CRUD getter & Setter
	|--------------------------------------------------------------------------
	|
	*/

	/**
	 * Get invoice status nice name.
	 *
	 * @since 1.1.0
	 *
	 * @return mixed|string
	 */
	public function get_status_nicename() {
		return isset( $this->get_statuses()[ $this->get_status() ] ) ? $this->get_statuses()[ $this->get_status() ] : $this->get_status();
	}


	/**
	 * Get item ids.
	 *
	 * @since 1.1.0
	 *
	 * @return array
	 */
	public function get_item_ids() {
		$ids = array();
		foreach ( $this->get_items() as $item ) {
			$ids[] = $item->get_id();
		}

		return array_filter( $ids );
	}

	/**
	 * Get the invoice items.
	 *
	 * @since 1.1.0
	 *
	 * @return array
	 */
	public function get_taxes() {
		$taxes = array();
		if ( ! empty( $this->get_items() ) ) {
			foreach ( $this->get_items() as $item ) {
				$taxes[] = array(
					'line_id' => $item->get_item_id(),
					'rate'    => $item->get_tax_rate(),
					'amount'  => $item->get_tax(),
				);
			}
		}

		return $taxes;
	}

	/**
	 * Get formatted amount.
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	public function get_formatted_total() {
		return eac_format_price( $this->get_total(), $this->get_currency_code() );
	}


	/*
	|--------------------------------------------------------------------------
	| Document Item Handling
	|--------------------------------------------------------------------------
	|
	| document items are used for products, taxes, shipping, and fees within
	| each order.
	*/

	/**
	 * Remove all line items from the order.
	 *
	 * @deprecatd 1.1.3
	 */
	public function remove_items() {
		$this->delete_items();
	}

	/**
	 * Delete items.
	 *
	 * @since 1.1.3
	 */
	public function delete_items() {
		if ( $this->exists() ) {
			$this->repository->delete_items( $this );
			$this->items = array();
		}
	}

	/**
	 * Get the invoice items.
	 *
	 * @since 1.1.0
	 *
	 * @return Document_Item[]
	 */
	public function get_items() {
		if ( $this->exists() && empty( $this->items ) ) {
			$items      = $this->repository->get_items( $this );
			$removables = array_keys( $this->items_to_delete );
			foreach ( $items as $line_id => $item ) {
				if ( ! in_array( $item->get_id(), $removables, true ) ) {
					$this->items[ $line_id ] = $item;
				}
			}
		}

		return $this->items;
	}

	/**
	 * Get item.
	 *
	 * @param int $item_id .
	 *
	 * @since 1.1.0
	 *
	 * @return Document_Item|int
	 */
	public function get_item( $item_id ) {
		$items = $this->get_items();
		if ( empty( absint( $item_id ) ) ) {
			return false;
		}

		foreach ( $items as $item ) {
			if ( $item->get_id() === absint( $item_id ) ) {
				return $item;
			}
		}

		return false;
	}

	/**
	 * Set the document items.
	 *
	 * @param array|Document_Item[] $items items.
	 * @param bool                  $append append.
	 *
	 * @since 1.1.0
	 */
	public function set_items( $items, $append = false ) {
		// Ensure that we have an array.
		if ( ! is_array( $items ) ) {
			return;
		}
		// Remove existing items.
		$old_items = $this->get_items();
		$new_ids   = array();
		foreach ( $items as $item ) {
			$new_ids[] = $this->add_item( $item );
		}

		if ( ! $append ) {
			$new_ids         = array_values( array_filter( $new_ids ) );
			$old_item_ids    = array_keys( $old_items );
			$remove_item_ids = array_diff( $old_item_ids, $new_ids );
			foreach ( $remove_item_ids as $remove_item_id ) {
				$this->items_to_delete[] = $old_items[ $remove_item_id ];
				unset( $this->items[ $remove_item_id ] );
			}
		}
	}

	/**
	 * Adds an item to the document.
	 *
	 * @param array $args array|Document_Item item data.
	 *
	 * @return false|int
	 */
	abstract public function add_item( $args );

	/**
	 * Remove item from the order.
	 *
	 * @param int $item_id Item ID to delete.
	 *
	 * @return false|void
	 */
	public function remove_item( $item_id ) {
		if ( empty( $item_id ) ) {
			return false;
		}

		$item = $this->get_item( $item_id );

		if ( ! $item ) {
			return false;
		}

		// Unset and remove later.
		$this->items_to_delete[] = $item;
		unset( $this->items[ $item_id ] );
	}

	/*
	|--------------------------------------------------------------------------
	| Conditionals
	|--------------------------------------------------------------------------
	|
	| Checks if a condition is true or false.
	|
	*/
	/**
	 * Checks if the invoice has a given status.
	 *
	 * @param string $status Status to check.
	 *
	 * @since 1.1.0
	 *
	 * @return bool
	 */
	public function is_status( $status ) {
		return $this->get_status() === eac_clean( $status );
	}

	/**
	 * Checks if an order can be edited, specifically for use on the Edit Order screen.
	 *
	 * @return bool
	 */
	public function is_editable() {
		return ! in_array( $this->get_status(), array( 'partial', 'paid' ), true );
	}

	/**
	 * Returns if an order has been paid for based on the order status.
	 *
	 * @since 1.10
	 * @return bool
	 */
	public function is_paid() {
		return $this->is_status( 'paid' );
	}

	/**
	 * Checks if the invoice is draft.
	 *
	 * @since 1.1.0
	 * @return bool
	 */
	public function is_draft() {
		return $this->is_status( 'draft' );
	}

	/**
	 * Checks if the invoice is due.
	 *
	 * @since 1.1.0
	 * @return bool
	 */
	public function is_due() {
		$due_date = $this->get_due_date();

		return ! ( empty( $due_date ) || $this->is_paid() ) && strtotime( date_i18n( 'Y-m-d 23:59:00' ) ) > strtotime( date_i18n( 'Y-m-d 23:59:00', strtotime( $due_date ) ) );
	}

	/**
	 * Check if tax inclusive or not.
	 *
	 * @since 1.1.0
	 *
	 * @return mixed|null
	 */
	public function is_tax_inclusive() {
		return ! empty( $this->get_tax_inclusive() );
	}

	/**
	 * Get the type of discount.
	 *
	 * @since 1.1.0
	 *
	 * @return bool
	 */
	public function is_fixed_discount() {
		return 'percentage' !== $this->get_discount_type();
	}

	/**
	 * Check if an key is valid.
	 *
	 * @param string $key Order key.
	 *
	 * @return bool
	 */
	public function is_key_valid( $key ) {
		return $key === $this->get_key( 'edit' );
	}

	/**
	 * Checks if an order needs payment, based on status and order total.
	 *
	 * @return bool
	 */
	public function needs_payment() {
		return ! $this->is_status( 'paid' ) && $this->get_total() > 0;
	}
}
