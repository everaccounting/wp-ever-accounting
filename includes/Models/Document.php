<?php

namespace EverAccounting\Models;

use ByteKit\Models\Relation;

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
 * @property int    $id ID of the document.
 * @property string $type Type of the document.
 * @property string $status Status of the document.
 * @property string $number Number of the document.
 * @property int    $contact_id Contact ID of the document.
 * @property double $items_total Item total of the document.
 * @property double $discount_total Discount total of the document.
 * @property double $shipping_total Shipping total of the document.
 * @property double $fees_total Fees total of the document.
 * @property double $tax_total Tax total of the document.
 * @property double $total Total of the document.
 * @property double $total_paid Total paid of the document.
 * @property double $balance Balance of the document.
 * @property float  $discount_amount Discount amount of the document.
 * @property string $discount_type Discount type of the document.
 * @property array  $billing_data Billing data of the document.
 * @property string $reference Reference of the document.
 * @property string $note Note of the document.
 * @property bool   $tax_inclusive Tax inclusive of the document.
 * @property bool   $vat_exempt Vat exempt of the document.
 * @property string $issue_date Issue date of the document.
 * @property string $due_date Due date of the document.
 * @property string $sent_date Sent date of the document.
 * @property string $payment_date Payment date of the document.
 * @property string $currency_code Currency code of the document.
 * @property double $exchange_rate Exchange rate of the document.
 * @property int    $parent_id Parent ID of the document.
 * @property string $created_via Created via of the document.
 * @property int    $author_id Author ID of the document.
 * @property string $uuid UUID of the document.
 * @property string $date_updated Date updated of the document.
 * @property string $date_created Date created of the document.
 *
 * @property string $billing_name Name of the billing contact.
 * @property string $billing_company Company of the billing contact.
 * @property string $billing_address_1 Address line 1 of the billing contact.
 * @property string $billing_address_2 Address line 2 of the billing contact.
 * @property string $billing_city City of the billing contact.
 * @property string $billing_state State of the billing contact.
 * @property string $billing_postcode Postcode of the billing contact.
 * @property string $billing_country Country of the billing contact.
 * @property string $billing_phone Phone of the billing contact.
 * @property string $billing_email Email of the billing contact.
 * @property string $billing_vat_number VAT number of the billing contact.
 * @property bool   $billing_vat_exempt VAT exempt of the billing contact.
 * @property string $formatted_billing_address Formatted billing address.
 *
 * @property double $formatted_items_total Formatted items total.
 * @property double $formatted_discount_total Formatted discount total.
 * @property double $formatted_shipping_total Formatted shipping total.
 * @property double $formatted_fees_total Formatted fees total.
 * @property double $formatted_tax_total Formatted tax total.
 * @property double $formatted_total Formatted total.
 * @property double $formatted_total_paid Formatted total paid.
 * @property double $formatted_balance Formatted balance.
 * @property array  formatted_itemized_taxes Formatted itemized taxes.
 */
class Document extends Model {
	/**
	 * Meta type declaration for the object.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $meta_type = 'ea_document';

	/**
	 * The table associated with the model.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $table = 'ea_documents';

	/**
	 * Table columns.
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
	 * The model's attributes.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $attributes = array(
		'discount_type' => 'fixed',
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
		'vat_exempt'      => 'bool',
		'exchange_rate'   => 'double',
		'parent_id'       => 'int',
		'author_id'       => 'int',
	);


	/**
	 * The attributes that aren't mass assignable.
	 *
	 * @since 1.0.0
	 * @var string[]|bool
	 */
	protected $guarded = array();

	/**
	 * Whether the model should be timestamped.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	protected $timestamps = true;

	/**
	 * document items will be stored here, sometimes before they persist in the DB.
	 *
	 * @since 1.1.0
	 *
	 * @var DocumentItem[]
	 */
	protected $items = null;

	/**
	 * document items that need deleting are stored here.
	 *
	 * @since 1.1.0
	 *
	 * @var DocumentItem[]
	 */
	protected $deletable = array();

	/**
	 * Create a new model instance.
	 *
	 * @param string|int|array $attributes Attributes.
	 *
	 * @throws \InvalidArgumentException If table name or object type is not set.
	 * @return void
	 */
	public function __construct( $attributes = 0 ) {
		$this->attributes['tax_inclusive'] = filter_var( eac_price_includes_tax(), FILTER_VALIDATE_BOOLEAN );
		$this->attributes['currency_code'] = eac_get_base_currency();
		$this->attributes['author_id']     = get_current_user_id();
		$this->attributes['uuid']          = wp_generate_uuid4();
		parent::__construct( $attributes );
	}

	/**
	 * Convert the model instance to an array.
	 *
	 * @return array
	 */
	public function to_array() {
		$data          = parent::to_array();
		$data['items'] = array();
		foreach ( $this->get_items() as $item ) {
			$data['items'][] = $item->to_array();
		}

		return $data;
	}

	/*
	|--------------------------------------------------------------------------
	| Attributes & Relations
	|--------------------------------------------------------------------------
	| Define the attributes and relations of the model.
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
		$this->attributes['discount_type'] = in_array( $type, array( 'fixed', 'percentage' ), true ) ? $type : 'fixed';
	}

	/**
	 * Gets a prop for a getter method.
	 *
	 * @param string $prop Name of prop to get.
	 *
	 * @since  1.1.0
	 * @return mixed
	 */
	protected function get_billing_attribute( $prop ) {
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
	protected function set_billing_attribute( $prop, $value ) {
		$this->attributes['billing_data'][ $prop ] = $value;
	}

	/**
	 * Get billing name.
	 *
	 * @since  1.1.0
	 * @return string
	 */
	protected function get_billing_name_attribute() {
		return $this->get_billing_attribute( 'name' );
	}

	/**
	 * Set billing name.
	 *
	 * @param string $name Billing name.
	 *
	 * @since  1.1.0
	 */
	public function set_billing_name_attribute( $name ) {
		$this->set_billing_attribute( 'name', $name );
	}

	/**
	 * Get billing company name.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	protected function get_billing_company_attribute() {
		return $this->get_billing_attribute( 'company' );
	}

	/**
	 * Set billing company name.
	 *
	 * @param string $company Billing company name.
	 *
	 * @since  1.1.0
	 */
	public function set_billing_company_attribute( $company ) {
		$this->set_billing_attribute( 'company', eac_clean( $company ) );
	}

	/**
	 * Get billing address_1 address.
	 *
	 * @since  1.1.0
	 * @return string
	 */
	protected function get_billing_address_1_attribute() {
		return $this->get_billing_attribute( 'address_1' );
	}

	/**
	 * Set billing address_1 address.
	 *
	 * @param string $address_1 Billing address_1 address.
	 *
	 * @since  1.1.0
	 */
	public function set_billing_address_1_attribute( $address_1 ) {
		$this->set_billing_attribute( 'address_1', sanitize_text_field( $address_1 ) );
	}


	/**
	 * Get billing address_2 address.
	 *
	 * @since  1.1.0
	 * @return string
	 */
	protected function get_billing_address_2_attribute() {
		return $this->get_billing_attribute( 'address_2' );
	}

	/**
	 * Set billing address_2 address.
	 *
	 * @param string $address_2 Billing address_2 address.
	 *
	 * @since  1.1.0
	 */
	public function set_billing_address_2_attribute( $address_2 ) {
		$this->set_billing_attribute( 'address_2', eac_clean( $address_2 ) );
	}

	/**
	 * Get billing city address.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	protected function get_billing_city_attribute() {
		return $this->get_billing_attribute( 'city' );
	}

	/**
	 * Set billing city address.
	 *
	 * @param string $city Billing city address.
	 *
	 * @since  1.1.0
	 */
	public function set_billing_city_attribute( $city ) {
		$this->set_billing_attribute( 'city', eac_clean( $city ) );
	}

	/**
	 * Get billing state address.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	protected function get_billing_state_attribute() {
		return $this->get_billing_attribute( 'state' );
	}

	/**
	 * Set billing state address.
	 *
	 * @param string $state Billing state address.
	 *
	 * @since  1.1.0
	 */
	public function set_billing_state_attribute( $state ) {
		$this->set_billing_attribute( 'state', eac_clean( $state ) );
	}

	/**
	 * Get billing postcode code address.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	protected function get_billing_postcode_attribute() {
		return $this->get_billing_attribute( 'postcode' );
	}

	/**
	 * Set billing postcode code address.
	 *
	 * @param string $postcode Billing postcode code address.
	 *
	 * @since  1.1.0
	 */
	public function set_billing_postcode_attribute( $postcode ) {
		$this->set_billing_attribute( 'postcode', eac_clean( $postcode ) );
	}

	/**
	 * Get billing country address.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	protected function get_billing_country_attribute() {
		return $this->get_billing_attribute( 'country' );
	}

	/**
	 * Set billing country address.
	 *
	 * @param string $country Billing country address.
	 *
	 * @since  1.1.0
	 */
	public function set_billing_country_attribute( $country ) {
		$this->set_billing_attribute( 'country', eac_clean( $country ) );
	}

	/**
	 * Get billing phone number.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	protected function get_billing_phone_attribute() {
		return $this->get_billing_attribute( 'phone' );
	}

	/**
	 * Set billing phone number.
	 *
	 * @param string $phone Billing phone number.
	 *
	 * @since  1.1.0
	 */
	public function set_billing_phone_attribute( $phone ) {
		$this->set_billing_attribute( 'phone', eac_clean( $phone ) );
	}

	/**
	 * Get billing email address.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	protected function get_billing_email_attribute() {
		return $this->get_billing_attribute( 'email' );
	}

	/**
	 * Set billing email address.
	 *
	 * @param string $email Billing email address.
	 *
	 * @since  1.1.0
	 */
	public function set_billing_email_attribute( $email ) {
		$this->set_billing_attribute( 'email', eac_clean( $email ) );
	}

	/**
	 * Get billing vat number.
	 *
	 * @since  1.1.0
	 * @return string
	 */
	protected function get_billing_vat_number_attribute() {
		return $this->get_billing_attribute( 'vat_number' );
	}

	/**
	 * Set billing vat number.
	 *
	 * @param string $vat Billing vat number.
	 *
	 * @since  1.1.0
	 */
	public function set_billing_vat_number_attribute( $vat ) {
		$this->set_billing_attribute( 'vat_number', eac_clean( $vat ) );
	}

	/**
	 * Get formatted items total.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	protected function get_formatted_items_total_attribute() {
		return eac_format_money( $this->items_total, $this->currency_code );
	}

	/**
	 * Get formatted discount total.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	protected function get_formatted_discount_total_attribute() {
		return eac_format_money( $this->discount_total, $this->currency_code );
	}

	/**
	 * Get formatted shipping total.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	protected function get_formatted_shipping_total_attribute() {
		return eac_format_money( $this->shipping_total, $this->currency_code );
	}

	/**
	 * Get formatted fees total.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	protected function get_formatted_fees_total_attribute() {
		return eac_format_money( $this->fees_total, $this->currency_code );
	}

	/**
	 * Get formatted tax total.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	protected function get_formatted_tax_total_attribute() {
		return eac_format_money( $this->tax_total, $this->currency_code );
	}

	/**
	 * Get formatted total.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	protected function get_formatted_total_attribute() {
		return eac_format_money( $this->total, $this->currency_code );
	}

	/**
	 * Get formatted total paid.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	protected function get_formatted_total_paid_attribute() {
		return eac_format_money( $this->total_paid, $this->currency_code );
	}

	/**
	 * Get formatted balance.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	protected function get_formatted_balance_attribute() {
		return eac_format_money( $this->balance, $this->currency_code );
	}

	/**
	 * Get formatted itemized taxes.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	protected function get_formatted_itemized_taxes_attribute() {
		$taxes = $this->get_merged_taxes();
		$list  = array();
		foreach ( $taxes as $tax ) {
			if ( $tax->amount > 0 ) {
				$list[ $tax->name ] = eac_format_money( $tax->amount, $this->currency_code );
			}
		}

		return $list;
	}

	/**
	 * Get document transactions.
	 *
	 * @since 1.0.0
	 * @return Relation
	 */
	protected function transactions() {
		return $this->has_many( Transaction::class, 'document_id' );
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	| Methods for saving, updating, and deleting objects.
	*/

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
	 * @return DocumentItemTax[]
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
	| Calculations
	|--------------------------------------------------------------------------
	| This section contains methods for calculating totals.
	*/

	/**
	 * Convert totals to selected currency.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	protected function calculate_item_prices() {
		// if the currency is changed, we need to convert the totals.
		if ( ! $this->is_attribute_changed( 'currency_code' ) ) {
			return;
		}
		foreach ( $this->get_items() as $item ) {
			$price       = eac_convert_money( $item->price, $this->original['currency_code'], $this->currency_code );
			$item->price = $price;
		}
	}

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
	 * @return DocumentItemTax[]
	 * @since 1.0.0
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
}
