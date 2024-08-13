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
		'creator_id',
		'uuid',
	);

	/**
	 * The attributes of the model.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $attributes = array(
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
	 * The attributes that should be cast.
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
		'creator_id'       => 'int',
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
	| Property Definition Methods
	|--------------------------------------------------------------------------
	| This section contains static methods that define and return specific
	| property values related to the model.
	| These methods are accessible without creating an instance of the model.
	|--------------------------------------------------------------------------
	*/

	/*
	|--------------------------------------------------------------------------
	| Accessors, Mutators and Relationship Methods
	|--------------------------------------------------------------------------
	| This section contains methods for getting and setting attributes (accessors
	| and mutators) as well as defining relationships between models.
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
	public function set_discount_type_attribute( $type ) {
		if ( ! in_array( $type, array( 'fixed', 'percentage' ), true ) ) {
			$type = 'fixed';
		}
		$this->attributes['discount_type'] = $type;
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
	| Helper Methods
	|--------------------------------------------------------------------------
	| This section contains utility methods that are not directly related to this
	| object but can be used to support its functionality.
	|--------------------------------------------------------------------------
	*/
}
