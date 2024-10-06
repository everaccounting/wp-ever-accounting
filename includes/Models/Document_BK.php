<?php

namespace EverAccounting\Models;

use ByteKit\Models\Relation;
use ByteKit\Models\Relations\BelongsTo;
use ByteKit\Models\Relations\HasMany;
use ByteKit\Models\Relations\HasOne;

/**
 * Document model.
 *
 * Calculations:
 * When tax is inclusive:
 * 1. Calculate tax amount from subtotal as inclusive.
 * $subtotal_tax = array_sum( eac_calculate_taxes( $subtotal, $rates, true, true ) ); // note that inclusive parameter is true.
 * $subtotal = $subtotal - $subtotal_tax;
 * 2. Discount is also inclusive.
 * $discount_tax = array_sum( eac_calculate_taxes( $discount, $rates, true, true ) ); // note that inclusive parameter is true.
 * $discount = $discount - $discount_tax;
 * 3. Calculate shipping tax as exclusive and store the shipping tax.
 * $shipping_tax = array_sum( eac_calculate_taxes( $shipping, $rates, false, true ) ); // note that inclusive parameter is false.
 * 4. Calculate fee tax as exclusive and store the fee tax.
 * $fee_tax = array_sum( eac_calculate_taxes( $fee, $rates, false, true ) ); // note that inclusive parameter is false.
 * 5. Calculate total tax.
 * $total_tax = $subtotal_tax + $shipping_tax + $fee_tax - $discount_tax;
 * 6. Calculate total.
 * $total = $subtotal + $shipping + $fee - $discount + $total_tax;
 * When tax is exclusive:
 * 1. Calculate tax amount from subtotal as exclusive.
 * $subtotal_tax = array_sum( eac_calculate_taxes( $subtotal, $rates, false, true ) ); // note that inclusive parameter is false.
 * 2. Discount is also exclusive.
 * $discount_tax = array_sum( eac_calculate_taxes( $discount, $rates, false, true ) ); // note that inclusive parameter is false.
 * 3. Calculate shipping tax as exclusive and store the shipping tax.
 * $shipping_tax = array_sum( eac_calculate_taxes( $shipping, $rates, false, true ) ); // note that inclusive parameter is false.
 * 4. Calculate fee tax as exclusive and store the fee tax.
 * $fee_tax = array_sum( eac_calculate_taxes( $fee, $rates, false, true ) ); // note that inclusive parameter is false.
 * 5. Calculate total tax.
 * $total_tax = $subtotal_tax + $shipping_tax + $fee_tax - $discount_tax;
 * 6. Calculate total.
 * $total = $subtotal + $shipping + $fee - $discount + $total_tax;
 * *
 * * Note: Round the values to 2 decimal places only when calculating the document totals.
 * *
 *
 * @since 1.0.0
 * @author  Sultan Nasir Uddin <manikdrmc@gmail.com>
 * @package EverAccounting
 * @subpackage Models
 *
 * @property int      $id ID of the document.
 * @property string   $type Type of the document.
 * @property string   $status Status of the document.
 * @property string   $number Number of the document.
 * @property int      $contact_id Contact ID of the document.
 * @property double   $items_total Item total of the document.
 * @property double   $discount_total Discount total of the document.
 * @property double   $shipping_total Shipping total of the document.
 * @property double   $fees_total Fees total of the document.
 * @property double   $tax_total Tax total of the document.
 * @property double   $total Total of the document.
 * @property double   $total_paid Total paid of the document.
 * @property double   $balance Balance of the document.
 * @property float    $discount_amount Discount amount of the document.
 * @property string   $discount_type Discount type of the document.
 * @property array    $billing_data Billing data of the document.
 * @property string   $reference Reference of the document.
 * @property string   $note Note of the document.
 * @property bool     $tax_inclusive Tax inclusive of the document.
 * @property bool     $vat_exempt Vat exempt of the document.
 * @property string   $issue_date Issue date of the document.
 * @property string   $due_date Due date of the document.
 * @property string   $sent_date Sent date of the document.
 * @property string   $payment_date Payment date of the document.
 * @property string   $currency Currency code of the document.
 * @property double   $conversion Exchange rate of the document.
 * @property int      $parent_id Parent ID of the document.
 * @property string   $created_via Created via of the document.
 * @property int      $creator_id Author ID of the document.
 * @property string   $uuid UUID of the document.
 * @property string   $updated_at Date updated of the document.
 * @property string   $created_at Date created of the document.
 *
 * @property string   $billing_name Name of the billing contact.
 * @property string   $billing_company Company of the billing contact.
 * @property string   $billing_address_1 Address line 1 of the billing contact.
 * @property string   $billing_address_2 Address line 2 of the billing contact.
 * @property string   $billing_city City of the billing contact.
 * @property string   $billing_state State of the billing contact.
 * @property string   $billing_postcode Postcode of the billing contact.
 * @property string   $billing_country Country of the billing contact.
 * @property string   $billing_phone Phone of the billing contact.
 * @property string   $billing_email Email of the billing contact.
 * @property string   $billing_vat_number VAT number of the billing contact.
 * @property bool     $billing_vat_exempt VAT exempt of the billing contact.
 * @property string   $formatted_billing_address Formatted billing address.
 *
 * @property double   $formatted_name Formatted name.
 * @property double   $formatted_items_total Formatted items total.
 * @property double   $formatted_discount_total Formatted discount total.
 * @property double   $formatted_shipping_total Formatted shipping total.
 * @property double   $formatted_fees_total Formatted fees total.
 * @property double   $formatted_tax_total Formatted tax total.
 * @property double   $formatted_total Formatted total.
 * @property double   $formatted_total_paid Formatted total paid.
 * @property double   $formatted_balance Formatted balance.
 * @property array    formatted_itemized_taxes Formatted itemized taxes.
 * @property Currency $currency Currency object.
 * @property Revenue[]|Expense[] $transactions Transactions relation.
 */
class Document_BK extends Model {

	/**
	 * The table associated with the model.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $table = 'ea_documents';

	/**
	 * Meta type declaration for the object.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $meta_type = 'ea_document';

	/**
	 * The table columns of the model.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $columns = array(
		'id',
		'type',
		'status',
		'number',
		'contact_id',
		'items_total',
		'discount_total',
		'shipping_total',
		'fees_total',
		'tax_total',
		'total',
		'total_paid',
		'balance',
		'discount_amount',
		'discount_type',
		'billing_data',
		'reference',
		'note',
		'tax_inclusive',
		'vat_exempt',
		'issue_date',
		'due_date',
		'sent_date',
		'payment_date',
		'currency',
		'conversion',
		'parent_id',
		'created_via',
		'creator_id',
		'uuid',
	);

	/**
	 * The model's data properties.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $props = array(
		'discount_type' => 'fixed',
		'billing_data'  => array(
			'name'       => '',
			'company'    => '',
			'address_1'  => '',
			'address_2'  => '',
			'city'       => '',
			'state'      => '',
			'postcode'   => '',
			'country'    => '',
			'phone'      => '',
			'email'      => '',
			'vat_number' => '',
		),
		'vat_exempt'    => false,
		'status'        => 'draft',
	);

	/**
	 * The properties that should be cast.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $casts = array(
		'items_total'     => 'double',
		'discount_total'  => 'double',
		'shipping_total'  => 'double',
		'fees_total'      => 'double',
		'tax_total'       => 'double',
		'total'           => 'double',
		'total_paid'      => 'double',
		'balance'         => 'double',
		'discount_amount' => 'float',
		'billing_data'    => 'array',
		'tax_inclusive'   => 'bool',
		'issue_date'      => 'date',
		'due_date'        => 'date',
		'sent_date'       => 'date',
		'payment_date'    => 'date',
		'vat_exempt'      => 'bool',
		'conversion'   => 'double',
		'parent_id'       => 'int',
		'creator_id'       => 'int',
	);

	/**
	 * Whether the model should be timestamped.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	protected $has_timestamps = true;

	/**
	 * Create a new model instance.
	 *
	 * @param string|int|array $attributes Attributes.
	 * @return void
	 */
	public function __construct( $attributes = 0 ) {
		$this->props['currency'] = eac_base_currency();
		$this->props['creator_id']     = get_current_user_id();
		$this->props['uuid']          = wp_generate_uuid4();
		$this->props['created_at']  = wp_date( 'Y-m-d H:i:s' );
		parent::__construct( $attributes );
	}

	/**
	 * Convert the model instance to an array.
	 *
	 * @return array
	 */
//	public function to_array() {
//		$data          = parent::to_array();
//		$data['items'] = array();
//		foreach ( $this->get_items() as $item ) {
//			$data['items'][] = $item->to_array();
//		}
//
//		return $data;
//	}

	/*
	|--------------------------------------------------------------------------
	| Attributes & Relations
	|--------------------------------------------------------------------------
	| Define the attributes and relations of the model.
	*/
	/**
	 * Set status.
	 *
	 * @param string $status Status.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public function set_status( $status ) {
		$status = in_array( $status, eac_get_invoice_statuses(), true ) ? $status : 'draft';
		$this->set_attribute_value( 'status', $status );
	}

	/**
	 * Set discount type.
	 *
	 * @param string $type Discount type.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public function set_discount_type( $type ) {
		$this->attributes['discount_type'] = in_array( $type, array( 'fixed', 'percent' ), true ) ? $type : 'fixed';
	}

	/**
	 * Gets a prop for a getter method.
	 *
	 * @param string $prop Name of prop to get.
	 *
	 * @since  1.1.0
	 * @return mixed
	 */
	protected function get_billing( $prop ) {
		$value = null;

		if ( isset( $this->attributes['billing_data'][ $prop ] ) ) {
			$value = $this->attributes['billing_data'][ $prop ];
		}

		return $value;
	}

	/**
	 * Sets a prop for a setter method.
	 *
	 * @param string $prop Name of prop to set.
	 * @param mixed  $value Value of the prop.
	 *
	 * @since 1.1.0
	 */
	protected function set_billing( $prop, $value ) {
		if ( array_key_exists( $prop, $this->attributes['billing_data'] ) ) {
			$this->attributes['billing_data'][ $prop ] = $value;
		}
	}

	/**
	 * Get billing name.
	 *
	 * @since  1.1.0
	 * @return string
	 */
	protected function get_billing_name() {
		return $this->get_billing( 'name' );
	}

	/**
	 * Set billing name.
	 *
	 * @param string $name Billing name.
	 *
	 * @since  1.1.0
	 */
	public function set_billing_name( $name ) {
		$this->set_billing( 'name', $name );
	}

	/**
	 * Get billing company name.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	protected function get_billing_company() {
		return $this->get_billing( 'company' );
	}

	/**
	 * Set billing company name.
	 *
	 * @param string $company Billing company name.
	 *
	 * @since  1.1.0
	 */
	public function set_billing_company( $company ) {
		$this->set_billing( 'company', eac_clean( $company ) );
	}

	/**
	 * Get billing address_1 address.
	 *
	 * @since  1.1.0
	 * @return string
	 */
	protected function get_billing_address_1() {
		return $this->get_billing( 'address_1' );
	}

	/**
	 * Set billing address_1 address.
	 *
	 * @param string $address_1 Billing address_1 address.
	 *
	 * @since  1.1.0
	 */
	public function set_billing_address_1( $address_1 ) {
		$this->set_billing( 'address_1', sanitize_text_field( $address_1 ) );
	}


	/**
	 * Get billing address_2 address.
	 *
	 * @since  1.1.0
	 * @return string
	 */
	protected function get_billing_address_2() {
		return $this->get_billing( 'address_2' );
	}

	/**
	 * Set billing address_2 address.
	 *
	 * @param string $address_2 Billing address_2 address.
	 *
	 * @since  1.1.0
	 */
	public function set_billing_address_2( $address_2 ) {
		$this->set_billing( 'address_2', eac_clean( $address_2 ) );
	}

	/**
	 * Get billing city address.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	protected function get_billing_city() {
		return $this->get_billing( 'city' );
	}

	/**
	 * Set billing city address.
	 *
	 * @param string $city Billing city address.
	 *
	 * @since  1.1.0
	 */
	public function set_billing_city( $city ) {
		$this->set_billing( 'city', eac_clean( $city ) );
	}

	/**
	 * Get billing state address.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	protected function get_billing_state() {
		return $this->get_billing( 'state' );
	}

	/**
	 * Set billing state address.
	 *
	 * @param string $state Billing state address.
	 *
	 * @since  1.1.0
	 */
	public function set_billing_state( $state ) {
		$this->set_billing( 'state', eac_clean( $state ) );
	}

	/**
	 * Get billing postcode code address.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	protected function get_billing_postcode() {
		return $this->get_billing( 'postcode' );
	}

	/**
	 * Set billing postcode code address.
	 *
	 * @param string $postcode Billing postcode code address.
	 *
	 * @since  1.1.0
	 */
	public function set_billing_postcode( $postcode ) {
		$this->set_billing( 'postcode', eac_clean( $postcode ) );
	}

	/**
	 * Get billing country address.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	protected function get_billing_country() {
		return $this->get_billing( 'country' );
	}

	/**
	 * Set billing country address.
	 *
	 * @param string $country Billing country address.
	 *
	 * @since  1.1.0
	 */
	public function set_billing_country( $country ) {
		$this->set_billing( 'country', eac_clean( $country ) );
	}

	/**
	 * Get billing phone number.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	protected function get_billing_phone() {
		return $this->get_billing( 'phone' );
	}

	/**
	 * Set billing phone number.
	 *
	 * @param string $phone Billing phone number.
	 *
	 * @since  1.1.0
	 */
	public function set_billing_phone( $phone ) {
		$this->set_billing( 'phone', eac_clean( $phone ) );
	}

	/**
	 * Get billing email address.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	protected function get_billing_email() {
		return $this->get_billing( 'email' );
	}

	/**
	 * Set billing email address.
	 *
	 * @param string $email Billing email address.
	 *
	 * @since  1.1.0
	 */
	public function set_billing_email( $email ) {
		$this->set_billing( 'email', eac_clean( $email ) );
	}

	/**
	 * Get billing vat number.
	 *
	 * @since  1.1.0
	 * @return string
	 */
	protected function get_billing_vat_number() {
		return $this->get_billing( 'vat_number' );
	}

	/**
	 * Set billing vat number.
	 *
	 * @param string $vat Billing vat number.
	 *
	 * @since  1.1.0
	 */
	public function set_billing_vat_number( $vat ) {
		$this->set_billing( 'vat_number', eac_clean( $vat ) );
	}

	/**
	 * Get formatted billing address.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_formatted_billing_address() {
		$data = array(
			'name'      => $this->billing_name,
			'company'   => $this->billing_company,
			'address_1' => $this->billing_address_1,
			'address_2' => $this->billing_address_2,
			'city'      => $this->billing_city,
			'state'     => $this->billing_state,
			'postcode'  => $this->billing_postcode,
			'country'   => $this->billing_country,
			'vat'       => $this->billing_vat_number,
			'phone'     => $this->billing_phone,
			'email'     => $this->billing_email,
		);

		return eac_get_formatted_address( $data );
	}

	/**
	 * Get formatted items total.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	protected function get_formatted_items_total() {
		return eac_format_amount( $this->items_total, $this->currency );
	}

	/**
	 * Get formatted discount total.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	protected function get_formatted_discount_total() {
		return eac_format_amount( $this->discount_total, $this->currency );
	}

	/**
	 * Get formatted shipping total.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	protected function get_formatted_shipping_total() {
		return eac_format_amount( $this->shipping_total, $this->currency );
	}

	/**
	 * Get formatted fees total.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	protected function get_formatted_fees_total() {
		return eac_format_amount( $this->fees_total, $this->currency );
	}

	/**
	 * Get formatted tax total.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	protected function get_formatted_tax_total() {
		return eac_format_amount( $this->tax_total, $this->currency );
	}

	/**
	 * Get formatted total.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	protected function get_formatted_total() {
		return eac_format_amount( $this->total, $this->currency );
	}

	/**
	 * Get formatted total paid.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	protected function get_formatted_total_paid() {
		return eac_format_amount( $this->total_paid, $this->currency );
	}

	/**
	 * Get formatted balance.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	protected function get_formatted_balance() {
		return eac_format_amount( $this->balance, $this->currency );
	}

	/**
	 * Get formatted itemized taxes.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	protected function get_formatted_itemized_taxes() {
		$taxes = $this->get_merged_taxes();
		$list  = array();
		foreach ( $taxes as $tax ) {
			if ( $tax->amount > 0 ) {
				$list[ $tax->formatted_name ] = eac_format_amount( $tax->amount, $this->currency );
			}
		}

		return $list;
	}


	/**
	 * Get formatted invoice name.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	protected function get_formatted_name() {
		// example: #INV-0001 (Paid) - John Doe.
		$invoice_name = $this->number;
		if ( $this->is_paid() ) {
			$invoice_name .= ' (' . __( 'Paid', 'wp-ever-accounting' ) . ')';
		}
		if ( ! empty( $this->billing_name ) ) {
			$invoice_name .= ' - ' . $this->billing_name;
		}

		return $invoice_name;
	}

	/**
	 * Get document transactions.
	 *
	 * @since 1.0.0
	 * @return HasMany
	 */
	public function transactions() {
		return $this->has_many( Transaction::class, 'document_id' );
	}

	/**
	 * Get document currency.
	 *
	 * @since 1.0.0
	 * @return BelongsTo
	 */
	protected function currency() {
		return $this->belongs_to( Currency::class, 'currency', 'code' );
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	| Methods for saving, updating, and deleting objects.
	*/
	/**
	 * Saves an object in the database.
	 *
	 * @throws \Exception When the invoice is already paid.
	 * @since 1.0.0
	 * @return true|\WP_Error True on success, WP_Error on failure.
	 */
	public function save() {
		if ( empty( $this->contact_id ) ) {
			return new \WP_Error( 'missing_required', __( 'Contact ID is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $this->type ) ) {
			return new \WP_Error( 'missing_required', __( 'Type is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->status ) ) {
			return new \WP_Error( 'missing_required', __( 'Status is required.', 'wp-ever-accounting' ) );
		}

		// Once the invoice is paid, contact can't be changed.
		if ( $this->total_paid > 0 && $this->is_attribute_changed( 'contact_id' ) ) {
			return new \WP_Error( 'invalid-argument', __( 'Contact can\'t be changed once the document is paid.', 'wp-ever-accounting' ) );
		}

		// check if the document number is already exists.
		if ( empty( $this->number ) ) {
			$next_number  = $this->get_next_number();
			$this->number = $next_number;
		}

		// Check if there any other document with the same number. If so, generate a new number.
		$document = $this->find(
			array(
				'number' => $this->number,
				'type'   => $this->type,
			)
		);
		if ( ! empty( $document ) && $document->id !== $this->id ) {
			$next_number  = $this->get_next_number();
			$this->number = $next_number;
		}

		$billing_data = array_filter( array_values( $this->billing_data ) );
		if ( empty( $billing_data ) ) {
			$contact = Contact::find( $this->contact_id );
			if ( ! empty( $contact ) ) {
				$this->billing_name       = $contact->name;
				$this->billing_company    = $contact->company;
				$this->billing_address_1  = $contact->address_1;
				$this->billing_address_2  = $contact->address_2;
				$this->billing_city       = $contact->city;
				$this->billing_state      = $contact->state;
				$this->billing_postcode   = $contact->postcode;
				$this->billing_country    = $contact->country;
				$this->billing_phone      = $contact->phone;
				$this->billing_email      = $contact->email;
				$this->billing_vat_number = $contact->vat_number;
			}
		}

		// uuid is required.
		if ( empty( $this->uuid ) ) {
			$this->uuid = wp_generate_uuid4();
		}

		try {
			$this->wpdb()->query( 'START TRANSACTION' );
			$saved = parent::save();
			if ( is_wp_error( $saved ) ) {
				throw new \Exception( $saved->get_error_message() );
			}

			foreach ( $this->get_items() as $item ) {
				$item->document_id = $this->id;
				$saved             = $item->save();
				if ( is_wp_error( $saved ) ) {
					throw new \Exception( $saved->get_error_message() );
				}
			}

			foreach ( $this->deletable as $deletable ) {
				if ( $deletable->exists() && ! $deletable->delete() ) {
					// translators: %s: error message.
					throw new \Exception( sprintf( __( 'Error while deleting items. error: %s', 'wp-ever-accounting' ), $wpdb->last_error ) );
				}
			}

			$this->wpdb()->query( 'COMMIT' );

			return true;
		} catch ( \Exception $e ) {
			$wpdb->query( 'ROLLBACK' );

			return new \WP_Error( 'db-error', $e->getMessage() );
		}
	}

	/**
	 * Deletes an object from the database.
	 *
	 * @since 1.0.0
	 * @return bool|\WP_Error True on success, false or WP_Error on failure.
	 */
	public function delete() {
		$deleted = parent::delete();

		if ( $deleted ) {
			foreach ( $this->get_items() as $item ) {
				$item->delete();
			}

			foreach ( $this->get_taxes() as $tax ) {
				$tax->delete();
			}

			foreach ( $this->get_notes() as $note ) {
				$note->delete();
			}
		}

		return $deleted;
	}

	/*
	|--------------------------------------------------------------------------
	| Line Items related methods
	|--------------------------------------------------------------------------
	| These methods are related to line items.
	*/

	/**
	 * Get items.
	 *
	 * @param string $type Type of the item.
	 *
	 * @return DocumentItem[]
	 */
	public function get_items( $type = null ) {
		if ( is_null( $this->items ) ) {
			$this->items = array();

			if ( $this->exists() ) {
				$this->items = DocumentItem::query(
					array(
						'document_id' => $this->id,
						'orderby'     => 'id',
						'order'       => 'ASC',
						'limit'       => - 1,
						'no_count'    => true,
					)
				);
			}
		}

		// Filter by type.
		if ( ! empty( $type ) && 'all' !== $type ) {
			return array_filter(
				$this->items,
				function ( $item ) use ( $type ) {
					if ( 'line_item' === $type ) {
						return 'standard' === $item->type;
					}

					return $item->type === $type;
				}
			);
		}

		return $this->items;
	}

	/**
	 * Set items, this will replace the existing items.
	 *
	 * @param array $items Items.
	 *
	 * @return void
	 */
	public function set_items( $items ) {
		$old_items       = array_merge( $this->get_items(), $this->deletable );
		$this->items     = array();
		$this->deletable = array_filter(
			$old_items,
			function ( $item ) {
				return $item->exists();
			}
		);

		if ( ! is_array( $items ) ) {
			$items = wp_parse_id_list( $items );
		}

		foreach ( $items as $item ) {
			$this->add_item( $item );
		}

		// Go through deletable items and if they are in the new items list, remove them from the deletable list.
		foreach ( $this->deletable as $key => $item ) {
			foreach ( $this->items as $new_item ) {
				if ( $item->id === $new_item->id ) {
					unset( $this->deletable[ $key ] );
				}
			}
		}
	}

	/**
	 * Get item.
	 *
	 * @param int $item_id Line ID.
	 *
	 * @return DocumentItem|false False if not found, DocumentItem if found.
	 */
	public function get_item( $item_id ) {
		if ( ! empty( $item_id ) ) {
			foreach ( $this->items as $item ) {
				if ( $item->id === $item_id ) {
					return $item;
				}
			}
		}

		return false;
	}

	/**
	 * Add item.
	 *
	 * Subtotal, discount is restricted to pass in item data.
	 *
	 * @param array $data Item.
	 *
	 * @return void
	 */
	public function add_item( $data ) {
		// Implement this method as per the requirements.
	}


	/*
	|--------------------------------------------------------------------------
	|  Taxes related methods
	|--------------------------------------------------------------------------
	| These methods are related to line items taxes.
	*/
	/**
	 * Get merged taxes.
	 *
	 * @since 1.0.0
	 * @return DocumentTax[]
	 */
	public function get_taxes() {
		$taxes = array();
		foreach ( $this->get_items() as $item ) {
			foreach ( $item->get_taxes() as $tax ) {
				$index = md5( $tax->tax_id . $tax->rate );
				if ( ! isset( $taxes[ $index ] ) ) {
					$taxes[ $index ] = $tax;
				} else {
					$taxes[ $index ]->merge( $tax );
				}
			}
		}

		return $taxes;
	}

	/*
	|--------------------------------------------------------------------------
	|  Notes related methods
	|--------------------------------------------------------------------------
	| These methods are related to notes.
	*/
	/**
	 * Add note.
	 *
	 * @param array $data Note data.
	 *
	 * @return Note| \WP_Error Note ID on success, WP_Error otherwise.
	 * @since 1.0.0
	 */
	public function add_note( $data ) {
		$data = wp_parse_args(
			$data,
			array(
				'parent_id'    => $this->id,
				'parent_type'  => get_class( $this ),
				'content'      => '',
				'creator_id'   => get_current_user_id(),
				'created_at' => current_time( 'mysql' ),
			)
		);

		if ( empty( $data['note'] ) ) {
			return new \WP_Error( 'missing_required', __( 'Note is required.', 'wp-ever-accounting' ) );
		}

		$note  = new Note( $data );
		$saved = $note->save();
		if ( is_wp_error( $saved ) ) {
			return $saved;
		}

		return $note;
	}

	/**
	 * Get notes.
	 *
	 * @param array $args Query arguments.
	 *
	 * @return Note[]
	 * @since 1.0.0
	 */
	public function get_notes( $args = array() ) {
		$args = array_merge(
			array(
				'parent_id'   => $this->id,
				'parent_type' => get_class( $this ),
				'limit'       => - 1,
			),
			$args
		);

		return eac_get_notes( $args );
	}

	/*
	|--------------------------------------------------------------------------
	| Calculations
	|--------------------------------------------------------------------------
	| This section contains methods for calculating totals.
	*/

	/*
	|--------------------------------------------------------------------------
	| Helper methods.
	|--------------------------------------------------------------------------
	| Utility methods which don't directly relate to this object but may be
	| used by this object.
	*/
	/**
	 * Is calculating tax.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_calculating_tax() {
		return 'yes' !== eac_tax_enabled() && ! $this->vat_exempt;
	}

	/**
	 * Get max voucher number.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_max_number() {
		return (int) $this->wpdb()->get_var(
			$this->wpdb()->prepare(
				"SELECT MAX(REGEXP_REPLACE(number, '[^0-9]', '')) FROM {$this->wpdb()->prefix}{$this->get_table()} WHERE type = %s",
				$this->type
			)
		);
	}

	/**
	 * Set next transaction number.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_next_number() {
		$max    = $this->get_max_number();
		$prefix = strtoupper( substr( $this->type, 0, 3 ) ) . '-';
		$next   = str_pad( $max + 1, 4, '0', STR_PAD_LEFT );

		return $prefix . $next;
	}


	/**
	 * Get merged taxes.
	 *
	 * @since 1.0.0
	 * @return DocumentTax[]
	 */
	public function get_merged_taxes() {
		$taxes = array();
		foreach ( $this->get_taxes() as $tax ) {
			$index = md5( $tax->tax_id . $tax->rate );
			if ( ! isset( $taxes[ $index ] ) ) {
				$taxes[ $index ] = $tax;
			} else {
				$taxes[ $index ]->merge( $tax );
			}
		}

		return $taxes;
	}

	/**
	 * is editable.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function is_editable() {
		return $this->total_paid <= 0;
	}

	/**
	 * Checks if the invoice has a given status.
	 *
	 * @param string $status Status to check.
	 *
	 * @return bool
	 * @since 1.1.0
	 */
	public function is_status( $status ) {
		return $this->status === $status;
	}


	/**
	 * Returns if an order has been paid for based on the order status.
	 *
	 * @return bool
	 * @since 1.10
	 */
	public function is_paid() {
		return $this->is_status( 'paid' );
	}

	/**
	 * Checks if the invoice is draft.
	 *
	 * @return bool
	 * @since 1.1.0
	 */
	public function is_draft() {
		return $this->is_status( 'draft' );
	}

	/**
	 * Checks if an order needs payment, based on status and order total.
	 *
	 * @return bool
	 */
	public function needs_payment() {
		return ! $this->is_status( 'paid' ) && $this->total > 0;
	}
}
