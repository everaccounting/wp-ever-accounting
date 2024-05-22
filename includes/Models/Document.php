<?php

namespace EverAccounting\Models;

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
 * @property string   $currency_code Currency code of the document.
 * @property double   $exchange_rate Exchange rate of the document.
 * @property int      $parent_id Parent ID of the document.
 * @property string   $created_via Created via of the document.
 * @property int      $author_id Author ID of the document.
 * @property string   $uuid UUID of the document.
 * @property string   $date_updated Date updated of the document.
 * @property string   $date_created Date created of the document.
 *
 * @property string   $billing_name Name of the billing contact.
 * @property string   $billing_company Company of the billing contact.
 * @property string   $billing_address Address line 1 of the billing contact.
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
 * @property DocumentLineTax[] $taxes Taxes of the document.
 * @property DocumentLine[] $lines Lines of the document.
 */
class Document extends Model {

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
		'currency_code',
		'exchange_rate',
		'parent_id',
		'created_via',
		'author_id',
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
		'exchange_rate'   => 'double',
		'parent_id'       => 'int',
		'author_id'       => 'int',
	);

	/**
	 * Whether the model should be timestamped.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	protected $timestamps = true;

	/*
	|--------------------------------------------------------------------------
	| Prop Definition Methods
	|--------------------------------------------------------------------------
	| This section contains methods that define and provide specific prop values
	| related to the model, such as statuses or types. These methods can be accessed
	| without instantiating the model.
	|--------------------------------------------------------------------------
	*/

	/*
	|--------------------------------------------------------------------------
	| Accessors, Mutators, Relationship and Validation Methods
	|--------------------------------------------------------------------------
	| This section contains methods for getting and setting properties (accessors
	| and mutators) as well as defining relationships between models. It also includes
	| a data validation method that ensures data integrity before saving.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Set discount type.
	 *
	 * @param string $type Discount type.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public function set_discount_type_props( $type ) {
		if ( ! in_array( $type, array( 'fixed', 'percentage' ), true ) ) {
			$type = 'fixed';
		}
		$this->set_prop_value( 'discount_type', $type );
	}

	/**
	 * Gets a prop for a getter method.
	 *
	 * @param string $prop Name of prop to get.
	 *
	 * @since  1.1.0
	 * @return mixed
	 */
	protected function get_billing_prop( $prop ) {
		$value = null;

		if ( isset( $this->props['billing_data'][ $prop ] ) ) {
			$value = $this->props['billing_data'][ $prop ];
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
	protected function set_billing_prop( $prop, $value ) {
		if ( array_key_exists( $prop, $this->props['billing_data'] ) ) {
			$this->props['billing_data'][ $prop ] = $value;
		}
	}

	/**
	 * Get billing name.
	 *
	 * @since  1.1.0
	 * @return string
	 */
	protected function get_billing_name_prop() {
		return $this->get_billing_prop( 'name' );
	}

	/**
	 * Set billing name.
	 *
	 * @param string $name Billing name.
	 *
	 * @since  1.1.0
	 */
	public function set_billing_name_prop( $name ) {
		$this->set_billing_prop( 'name', $name );
	}

	/**
	 * Get billing company name.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	protected function get_billing_company_prop() {
		return $this->get_billing_prop( 'company' );
	}

	/**
	 * Set billing company name.
	 *
	 * @param string $company Billing company name.
	 *
	 * @since  1.1.0
	 */
	public function set_billing_company_prop( $company ) {
		$this->set_billing_prop( 'company', eac_clean( $company ) );
	}

	/**
	 * Get billing address address.
	 *
	 * @since  1.1.0
	 * @return string
	 */
	protected function get_billing_address_prop() {
		return $this->get_billing_prop( 'address' );
	}

	/**
	 * Set billing address.
	 *
	 * @param string $address Billing address.
	 *
	 * @since  1.1.0
	 */
	public function set_billing_address_prop( $address ) {
		$this->set_billing_prop( 'address', sanitize_text_field( $address ) );
	}

	/**
	 * Get billing city address.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	protected function get_billing_city_prop() {
		return $this->get_billing_prop( 'city' );
	}

	/**
	 * Set billing city address.
	 *
	 * @param string $city Billing city address.
	 *
	 * @since  1.1.0
	 */
	public function set_billing_city_prop( $city ) {
		$this->set_billing_prop( 'city', eac_clean( $city ) );
	}

	/**
	 * Get billing state address.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	protected function get_billing_state_prop() {
		return $this->get_billing_prop( 'state' );
	}

	/**
	 * Set billing state address.
	 *
	 * @param string $state Billing state address.
	 *
	 * @since  1.1.0
	 */
	public function set_billing_state_prop( $state ) {
		$this->set_billing_prop( 'state', eac_clean( $state ) );
	}

	/**
	 * Get billing postcode code address.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	protected function get_billing_postcode_prop() {
		return $this->get_billing_prop( 'postcode' );
	}

	/**
	 * Set billing postcode code address.
	 *
	 * @param string $postcode Billing postcode code address.
	 *
	 * @since  1.1.0
	 */
	public function set_billing_postcode_prop( $postcode ) {
		$this->set_billing_prop( 'postcode', eac_clean( $postcode ) );
	}

	/**
	 * Get billing country address.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	protected function get_billing_country_prop() {
		return $this->get_billing_prop( 'country' );
	}

	/**
	 * Set billing country address.
	 *
	 * @param string $country Billing country address.
	 *
	 * @since  1.1.0
	 */
	public function set_billing_country_prop( $country ) {
		$this->set_billing_prop( 'country', eac_clean( $country ) );
	}

	/**
	 * Get billing phone number.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	protected function get_billing_phone_prop() {
		return $this->get_billing_prop( 'phone' );
	}

	/**
	 * Set billing phone number.
	 *
	 * @param string $phone Billing phone number.
	 *
	 * @since  1.1.0
	 */
	public function set_billing_phone_prop( $phone ) {
		$this->set_billing_prop( 'phone', eac_clean( $phone ) );
	}

	/**
	 * Get billing email address.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	protected function get_billing_email_prop() {
		return $this->get_billing_prop( 'email' );
	}

	/**
	 * Set billing email address.
	 *
	 * @param string $email Billing email address.
	 *
	 * @since  1.1.0
	 */
	public function set_billing_email_prop( $email ) {
		$this->set_billing_prop( 'email', eac_clean( $email ) );
	}

	/**
	 * Get billing vat number.
	 *
	 * @since  1.1.0
	 * @return string
	 */
	protected function get_billing_vat_number_prop() {
		return $this->get_billing_prop( 'vat_number' );
	}

	/**
	 * Set billing vat number.
	 *
	 * @param string $vat Billing vat number.
	 *
	 * @since  1.1.0
	 */
	public function set_billing_vat_number_prop( $vat ) {
		$this->set_billing_prop( 'vat_number', eac_clean( $vat ) );
	}

	/**
	 * Get formatted billing address.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_formatted_billing_address_prop() {
		$data = array(
			'name'     => $this->billing_name,
			'company'  => $this->billing_company,
			'address'  => $this->billing_address,
			'city'     => $this->billing_city,
			'state'    => $this->billing_state,
			'postcode' => $this->billing_postcode,
			'country'  => $this->billing_country,
			'vat'      => $this->billing_vat_number,
			'phone'    => $this->billing_phone,
			'email'    => $this->billing_email,
		);

		return eac_get_formatted_address( $data );
	}

	/**
	 * Contact relation.
	 *
	 * @since 1.0.0
	 * @return BelongsTo
	 */
	public function contact() {
		return $this->belongs_to( Contact::class, 'contact_id' );
	}

	/**
	 * Parent relation.
	 *
	 * @since 1.0.0
	 * @return BelongsTo
	 */
	public function parent() {
		return $this->belongs_to( self::class, 'parent_id' );
	}

	/**
	 * Currency relation.
	 *
	 * @since 1.0.0
	 * @return BelongsTo
	 */
	public function currency() {
		return $this->belongs_to( Currency::class, 'currency_code', 'code' );
	}

	/**
	 * Items relation.
	 *
	 * @since 1.0.0
	 * @return HasMany
	 */
	public function lines() {
		return $this->has_many( DocumentLine::class, 'document_id' );
	}

	/**
	 * Tax relation.
	 *
	 * @since 1.0.0
	 * @return HasMany
	 */
	public function taxes() {
		return $this->has_many( DocumentLineTax::class, 'document_id' );
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD Methods
	|--------------------------------------------------------------------------
	| This section contains methods for creating, reading, updating, and deleting
	| objects in the database.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Delete the object from the database.
	 *
	 * @since 1.0.0
	 * @return array|false true on success, false on failure.
	 */
	public function delete() {
		$this->lines()->delete();
		$this->taxes()->delete();
		return parent::delete();
	}

	/*
	|--------------------------------------------------------------------------
	| Helper Methods
	|--------------------------------------------------------------------------
	| This section contains utility methods that are not directly related to this
	| object but can be used to support its functionality.
	|--------------------------------------------------------------------------
	*/
}
