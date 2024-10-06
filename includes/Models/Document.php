<?php

namespace EverAccounting\Models;

use ByteKit\Models\Relations\BelongsTo;
use ByteKit\Models\Relations\HasMany;

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
 * @property int             $id ID of the document.
 * @property string          $type Type of the document.
 * @property string          $status Status of the document.
 * @property string          $number Number of the document.
 * @property string          $reference Reference of the document.
 * @property double          $subtotal Item total of the document.
 * @property double          $discount Discount total of the document.
 * @property double          $tax Tax total of the document.
 * @property double          $total Total of the document.
 * @property double          $balance Balance of the document.
 * @property float           $discount_value Discount amount of the document.
 * @property string          $discount_type Discount type of the document.
 * @property int             $contact_id Contact ID of the document.
 * @property string          $contact_name Name of the contact.
 * @property string          $contact_email Email of the contact.
 * @property string          $contact_phone Phone of the contact.
 * @property string          $contact_address Address of the contact.
 * @property string          $contact_city City of the contact.
 * @property string          $contact_state State of the contact.
 * @property string          $contact_zip Zip of the contact.
 * @property string          $contact_country Country of the contact.
 * @property string          $contact_tax_number Tax number of the contact.
 * @property string          $note Note of the document.
 * @property string          $terms Terms of the document.
 * @property string          $issue_date Issue date of the document.
 * @property string          $due_date Due date of the document.
 * @property string          $sent_date Sent date of the document.
 * @property string          $payment_date Payment date of the document.
 * @property string          $currency Currency code of the document.
 * @property double          $conversion Exchange rate of the document.
 * @property int             $parent_id Parent ID of the document.
 * @property string          $created_via Created via of the document.
 * @property int             $creator_id Author ID of the document.
 * @property string          $uuid UUID of the document.
 * @property string          $updated_at Date updated of the document.
 * @property string          $created_at Date created of the document.
 *
 * @property double          $formatted_name Formatted name.
 * @property double          $formatted_subtotal Formatted items total.
 * @property double          $formatted_discount Formatted discount total.
 * @property double          $formatted_tax Formatted tax total.
 * @property double          $formatted_total Formatted total.
 * @property double          $formatted_balance Formatted balance.
 * @property array           formatted_itemized_taxes Formatted itemized taxes.
 * @property DocumentTax[]   $taxes Taxes of the document.
 * @property DocumentItem[]  $items Lines of the document.
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
		'contact_id',
		'type',
		'status',
		'number',
		'reference',
		'subtotal',
		'discount',
		'tax',
		'total',
		'discount_value',
		'discount_type',
		'reference',
		'note',
		'terms',
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
	 * The attributes of the model.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $attributes = array(
		'discount_type' => 'fixed',
		'status'        => 'draft',
	);

	/**
	 * The attributes that should be cast.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $casts = array(
		'number'         => 'string',
		'reference'      => 'string',
		'subtotal'       => 'double',
		'discount'       => 'double',
		'tax'            => 'double',
		'total'          => 'double',
		'discount_value' => 'float',
		'contact_id'     => 'int',
		'issue_date'     => 'date',
		'due_date'       => 'date',
		'sent_date'      => 'date',
		'payment_date'   => 'date',
		'conversion'     => 'float',
		'parent_id'      => 'int',
		'creator_id'     => 'int',
	);

	/**
	 * Whether the model should be timestamped.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	protected $has_timestamps = true;

	/*
	|--------------------------------------------------------------------------
	| Accessors, Mutators and Relationship Methods
	|--------------------------------------------------------------------------
	| This section contains methods for getting and setting attributes (accessors
	| and mutators) as well as defining relationships between models.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get formatted tax total.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	protected function get_formatted_tax() {
		return eac_format_amount( $this->tax, $this->currency );
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
	 * Set discount type.
	 *
	 * @param string $type Discount type.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public function set_discount_type( $type ) {
		if ( ! in_array( $type, array( 'fixed', 'percent' ), true ) ) {
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
	 * Items relation.
	 *
	 * @since 1.0.0
	 * @return HasMany
	 */
	public function items() {
		return $this->has_many( DocumentItem::class, 'document_id' );
	}

	/**
	 * Tax relation.
	 *
	 * @since 1.0.0
	 * @return HasMany
	 */
	public function taxes() {
		return $this->has_many( DocumentTax::class, 'document_id' );
	}

	/**
	 * Transactions relation.
	 *
	 * @since 1.0.0
	 * @return HasMany
	 */
	public function transactions() {
		return $this->has_many( Transaction::class, 'document_id' );
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
	 * @return true|\WP_Error True on success, WP_Error on failure.
	 */
	public function delete() {
		$return = parent::delete();
		if ( $return ) {
			$this->items()->delete();
			$this->taxes()->delete();
			$this->transactions()->delete();
		}

		return $return;
	}

	/*
	|--------------------------------------------------------------------------
	| Helper Methods
	|--------------------------------------------------------------------------
	| This section contains utility methods that are not directly related to this
	| object but can be used to support its functionality.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get max number.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_max_number() {
		global $wpdb;
		$number = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT number FROM {$wpdb->prefix}{$this->table} WHERE type = %s AND number IS NOT NULL AND number != '' ORDER BY number DESC",
				esc_sql( $this->type )
			)
		);

		// if number is not empty, using regular expression to extract the number.
		if ( ! empty( $number ) ) {
			preg_match( '/\d+$/', $number, $matches );
			$number = ! empty( $matches ) ? $matches[0] : 0;
		}

		return (int) $number;
	}
}
