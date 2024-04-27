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
	 * The model's data.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $data = array(
		'items' => array(),
	);

	/**
	 * Model's property casts.
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

		'items'           => 'array',
	);

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
	 * Document constructor.
	 *
	 * @param int|object|array $data Object ID, post object, or array of data.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $data = 0 ) {
		$this->data['tax_inclusive'] = filter_var( eac_price_includes_tax(), FILTER_VALIDATE_BOOLEAN );
		$this->data['currency_code'] = eac_get_base_currency();
		$this->data['author_id']     = get_current_user_id();
		$this->data['date_created']  = wp_date( 'Y-m-d H:i:s' );
		$this->data['uuid']          = wp_generate_uuid4();
		parent::__construct( $data );
	}

	/*
	|--------------------------------------------------------------------------
	| Prop methods
	|--------------------------------------------------------------------------
	| The following methods are used to get and set properties of the object.
	*/

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

		if ( isset( $this->data['billing_data'][ $prop ] ) ) {
			$value = $this->data['billing_data'][ $prop ];
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
		$this->data['billing_data'][ $prop ] = $value;
	}

	/**
	 * Get billing name.
	 *
	 * @since  1.1.0
	 * @return string
	 */
	public function get_billing_name_prop() {
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
	public function get_billing_company_prop() {
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
	 * Get billing address_1 address.
	 *
	 * @since  1.1.0
	 * @return string
	 */
	public function get_billing_address_1_prop() {
		return $this->get_billing_prop( 'address_1' );
	}

	/**
	 * Set billing address_1 address.
	 *
	 * @param string $address_1 Billing address_1 address.
	 *
	 * @since  1.1.0
	 */
	public function set_billing_address_1_prop( $address_1 ) {
		$this->set_billing_prop( 'address_1', sanitize_text_field( $address_1 ) );
	}


	/**
	 * Get billing address_2 address.
	 *
	 * @since  1.1.0
	 * @return string
	 */
	public function get_billing_address_2_prop() {
		return $this->get_billing_prop( 'address_2' );
	}

	/**
	 * Set billing address_2 address.
	 *
	 * @param string $address_2 Billing address_2 address.
	 *
	 * @since  1.1.0
	 */
	public function set_billing_address_2_prop( $address_2 ) {
		$this->set_billing_prop( 'address_2', eac_clean( $address_2 ) );
	}

	/**
	 * Get billing city address.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	public function get_billing_city_prop() {
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
	public function get_billing_state_prop() {
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
	public function get_billing_postcode_prop() {
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
	public function get_billing_country_prop() {
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
	public function get_billing_phone_prop() {
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
	public function get_billing_email_prop() {
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
	public function get_billing_vat_number_prop() {
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

	/*
	|--------------------------------------------------------------------------
	| Relation methods
	|--------------------------------------------------------------------------
	| Methods for defining and accessing relationships between objects.
	*/
	/**
	 * Get document transactions.
	 *
	 * @since 1.0.0
	 * @return Relation
	 */
	protected function transactions() {
		return $this->has_many( Transaction::class, 'document_id', 'id' );
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	| Methods which create, read, update and delete discounts from the database.
	|
	*/
	/**
	 * Saves an object in the database.
	 *
	 * @throws \Exception When the invoice is already paid.
	 * @since 1.0.0
	 * @return true|\WP_Error True on success, WP_Error on failure.
	 */
	public function save123() {
		global $wpdb;
		$this->calculate_totals();

		// contact id is required.
		if ( empty( $this->get_contact_id() ) ) {
			return new \WP_Error( 'missing_required', __( 'Contact ID is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_type() ) ) {
			return new \WP_Error( 'missing_required', __( 'Type is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_status() ) ) {
			return new \WP_Error( 'missing_required', __( 'Status is required.', 'wp-ever-accounting' ) );
		}

		// Once the invoice is paid, contact can't be changed.
		if ( $this->get_total_paid() > 0 && in_array( 'contact_id', $this->changes, true ) ) {
			return new \WP_Error( 'invalid-argument', __( 'Contact can\'t be changed once the document is paid.', 'wp-ever-accounting' ) );
		}

		// check if the document number is already exists.
		if ( empty( $this->get_number() ) ) {
			$next_number = $this->get_next_number();
			$this->set_number( $next_number );
		}

		// If date created is not set, set it to now.
		if ( empty( $this->get_date_created() ) ) {
			$this->set_date_created( current_time( 'mysql' ) );
		}

		// If It's update, set the updated date.
		if ( $this->exists() ) {
			$this->set_date_updated( current_time( 'mysql' ) );
		}

		// uuid is required.
		if ( empty( $this->get_uuid() ) ) {
			$this->set_uuid( wp_generate_uuid4() );
		}

		try {
			$wpdb->query( 'START TRANSACTION' );
			$saved = parent::save();
			if ( is_wp_error( $saved ) ) {
				throw new \Exception( $saved->get_error_message() );
			}

			foreach ( $this->get_items() as $item ) {
				$item->set_document_id( $this->get_id() );
				$saved = $item->save();
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

			$wpdb->query( 'COMMIT' );

			return true;
		} catch ( \Exception $e ) {
			$wpdb->query( 'ROLLBACK' );

			return new \WP_Error( 'db-error', $e->getMessage() );
		}
	}

	/**
	 * Deletes an object from the database.
	 *
	 * @param bool $force_delete Whether to bypass trash and force deletion. Default false.
	 *
	 * @since 1.0.0
	 * @return bool|\WP_Error True on success, false or WP_Error on failure.
	 */
	public function delete( $force_delete = false ) {
		$deleted = parent::delete( $force_delete );

		if ( $deleted ) {
			foreach ( $this->get_items() as $item ) {
				$item->delete( $force_delete );
			}

			foreach ( $this->get_taxes() as $tax ) {
				$tax->delete( $force_delete );
			}

			foreach ( $this->get_notes() as $note ) {
				$note->delete( $force_delete );
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
		$default = array(
			'id'          => 0, // line id.
			'item_id'     => 0, // item id not line id be careful.
			'type'        => 'item', // 'line_item', 'fee', 'shipping
			'name'        => '',
			'description' => '',
			'unit'        => '',
			'price'       => 0,
			'quantity'    => 1,
			'taxable'     => $this->is_calculating_tax(),
			'tax_ids'     => '',
		);

		if ( is_object( $data ) ) {
			$data = $data instanceof \stdClass ? get_object_vars( $data ) : $data->get_data();
		} elseif ( is_numeric( $data ) ) {
			$data = array( 'item_id' => $data );
		}

		// The data must be a line item with id or a new array with product_id and additional data.
		if ( ! isset( $data['id'] ) && ! isset( $data['item_id'] ) ) {
			return;
		}

		if ( ! empty( $data['item_id'] ) ) {
			$product       = eac_get_item( $data['item_id'] );
			$product_data  = $product ? $product->get_data() : array();
			$accepted_keys = array(
				'name',
				'type',
				'description',
				'unit',
				'price',
				'taxable',
				'tax_ids',
			);
			// if the currency is not the as the base currency, we need to convert the price.

			if ( eac_get_base_currency() !== $this->currency_code ) {
				$price                 = eac_convert_money( $product_data['price'], eac_get_base_currency(), $this->currency_code );
				$product_data['price'] = $price;
			}

			$product_data = wp_array_slice_assoc( $product_data, $accepted_keys );
			$data         = wp_parse_args( $data, $product_data );
		}

		$data                = wp_parse_args( $data, $default );
		$data['name']        = wp_strip_all_tags( $data['name'] );
		$data['description'] = wp_strip_all_tags( $data['description'] );
		$data['description'] = wp_trim_words( $data['description'], 20, '' );
		$data['unit']        = wp_strip_all_tags( $data['unit'] );
		$data['tax_ids']     = wp_parse_id_list( $data['tax_ids'] );
		$data['document_id'] = $this->id;

		$item = new DocumentItem( $data['id'] );
		$item->fill( $data );
		if ( ! empty( $data['tax_ids'] ) ) {
			$item->set_taxes_by_ids( $data['tax_ids'] );
		}

		// if product id is not set then it is not product item.
		if ( empty( $item->item_id ) || empty( $item->quantity ) ) {
			return;
		}

		// Check if the item is set to be deleted and all the data matches. If so, remove it from the deletable list and add it to the items list.
		foreach ( $this->deletable as $key => $deletable_item ) {
			if ( $deletable_item->is_similar( $item ) ) {
				unset( $this->deletable[ $key ] );
				$deletable_item->fill( $data );
				$this->items[] = $deletable_item;

				return;
			}
		}

		// Check if the item already exists in the items list and all the data matches. If so, update the quantity.
		foreach ( $this->get_items() as $key => $existing_item ) {
			if ( $existing_item->is_similar( $item ) ) {
				$existing_item->quantity += $item->quantity;

				return;
			}
		}

		$this->items[] = $item;
	}

	/**
	 * Delete items.
	 *
	 * @since 1.1.6
	 *
	 * return void
	 */
	public function delete_items() {
		foreach ( $this->get_items() as $item ) {
			$item->delete();
		}
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
	 * @return DocumentItemTax[]
	 * @since 1.0.0
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
	 * Prepare object for database.
	 * This method is called before saving the object to the database.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function calculate_totals() {
//		$transactions = $this->transactions()->query(
//			array(
//				'status'   => 'completed',
//				'orderby'  => 'id',
//				'order'    => 'ASC',
//				'limit'    => - 1,
//				'no_count' => true,
//			)
//		);
//
		$total_paid = 0;
//		foreach ( $transactions as $transaction ) {
//			$total_paid += eac_convert_money( $transaction->amount, $transaction->currency_code, $this->currency_code, $transaction->exchange_rate, $this->exchange_rate );
//		}
		$this->total_paid = $total_paid;
		$this->calculate_item_prices();
		$this->calculate_item_subtotals();
		$this->calculate_item_discounts();
		$this->calculate_item_taxes();
		$this->calculate_item_totals();
	}

	/**
	 * Convert totals to selected currency.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	protected function calculate_item_prices() {
		// if the currency is changed, we need to convert the totals.
		if ( ! $this->is_prop_changed( 'currency_code' ) ) {
			return;
		}
		foreach ( $this->get_items() as $item ) {
			$price       = eac_convert_money( $item->price, $this->original['currency_code'], $this->currency_code );
			$item->price = $price;
		}
	}

	/**
	 * Calculate item subtotals.
	 *
	 * @return void
	 */
	protected function calculate_item_subtotals() {
		$items = $this->get_items();

		foreach ( $items as $item ) {
			$price        = $item->price;
			$qty          = $item->quantity;
			$subtotal     = $price * $qty;
			$subtotal_tax = array_sum( eac_calculate_taxes( $subtotal, $item->get_taxes(), $this->tax_inclusive ) );
			// If the tax is inclusive, we need to subtract the tax amount from the line subtotal.
			if ( $this->tax_inclusive ) {
				$subtotal -= $subtotal_tax;
			}

			$subtotal = max( 0, $subtotal );

			$item->subtotal     = $subtotal;
			$item->subtotal_tax = $subtotal_tax;
		}
	}

	/**
	 * Calculate item discounts.
	 *
	 * @return void
	 */
	protected function calculate_item_discounts() {
		$items           = $this->get_items( 'standard' );
		$discount_amount = $this->discount_amount;
		$discount_type   = $this->discount_typ;

		// sort the items array by price.

		// Reset item discounts.
		foreach ( $items as $item ) {
			$item->discount = 0;
		}

		// First apply the discount to the items.
		if ( $discount_amount > 0 && ! empty( $discount_type ) ) {
			$this->apply_discount( $discount_amount, $items, $discount_type );
		}

		foreach ( $items as $item ) {
			$discount     = $item->discount;
			$discount_tax = array_sum( eac_calculate_taxes( $discount, $item->get_taxes(), $this->tax_inclusive ) );
			if ( $this->tax_inclusive ) {
				$discount -= $discount_tax;
			}
			$discount = max( 0, $discount );

			$item->discount     = $discount;
			$item->discount_tax = $discount_tax;
		}
	}

	/**
	 * Calculate item taxes.
	 *
	 * @return void
	 */
	protected function calculate_item_taxes() {
		$items = $this->get_items();
		// Calculate item taxes.
		foreach ( $items as $item ) {
			$taxable_amount = $item->subtotal - $item->discount;
			$taxable_amount = max( 0, $taxable_amount );
			$taxes          = eac_calculate_taxes( $taxable_amount, $item->get_taxes(), false );
			$line_tax       = 0;
			foreach ( $item->get_taxes() as $tax ) {
				$amount      = isset( $taxes[ $tax->tax_id ] ) ? $taxes[ $tax->tax_id ] : 0;
				$tax->amount = $amount;
				$line_tax   += $amount;
			}
			$item->tax_total = $line_tax;
		}
	}

	/**
	 * Calculate item totals.
	 *
	 * @return void
	 */
	protected function calculate_item_totals() {
		foreach ( $this->get_items() as $item ) {
			$total       = $item->subtotal + $item->tax_total - $item->discount;
			$total       = max( 0, $total );
			$item->total = $total;
		}
	}

	/**
	 * Apply discounts.
	 *
	 * @param float  $amount Discount amount.
	 * @param array  $items Items.
	 * @param string $type Discount type.
	 *
	 * @return float Total discount.
	 * @since 1.0.0
	 */
	public function apply_discount( $amount = 0, $items = array(), $type = 'fixed' ) {
		$total_discounted = 0;
		if ( 'fixed' === $type ) {
			$total_discounted = $this->apply_fixed_discount( $amount, $items );
		} elseif ( 'percent' === $type ) {
			$total_discounted = $this->apply_percentage_discount( $amount, $items );
		}

		return $total_discounted;
	}

	/**
	 * Apply fixed discount.
	 *
	 * @param float          $amount Discount amount.
	 * @param DocumentItem[] $items Items.
	 *
	 * @return float Total discounted.
	 * @since 1.0.0
	 */
	public function apply_fixed_discount( $amount, $items ) {
		$total_discount = 0;
		$item_count     = 0;

		foreach ( $items as $item ) {
			$item_count += (float) $item->quantity;
		}

		if ( $amount <= 0 || empty( $items ) || $item_count <= 0 ) {
			return $total_discount;
		}

		$per_item_discount = $amount / $item_count;
		if ( $per_item_discount > 0 ) {
			foreach ( $items as $item ) {
				$discounted_price = $item->get_discounted_price();
				$discount         = $per_item_discount * (float) $item->quantity;
				$discount         = eac_round_number( $discount );
				$discount         = min( $discounted_price, $discount );
				$item->discount   = $item->discount + $discount;

				$total_discount += $discount;
			}

			$total_discount = round( $total_discount, 2 );
			$amount         = round( $amount, 2 );
			// If there is still discount remaining, repeat the process.
			if ( $total_discount > 0 && $total_discount < $amount ) {
				$total_discount += $this->apply_fixed_discount( $amount - $total_discount, $items );
			}
		} elseif ( $amount > 0 ) {
			$total_discount += $this->apply_discount_remainder( $amount, $items );
		}

		return $total_discount;
	}

	/**
	 * Apply percentage discount.
	 *
	 * @param float          $amount Discount amount.
	 * @param DocumentItem[] $items Items.
	 */
	public function apply_percentage_discount( $amount, $items ) {
		$total_discount = 0;
		$document_total = 0;

		if ( $amount <= 0 || empty( $items ) ) {
			return $total_discount;
		}

		foreach ( $items as $item ) {
			$discounted_price = $item->get_discounted_price();
			// If the item is not created yet, we need to calculate the discounted price without tax.
			$discount        = $discounted_price * ( $amount / 100 );
			$discount        = min( $discounted_price, $discount );
			$item->discount  = $item->discount + $discount;
			$total_discount += $discount;
			$document_total += $discounted_price;
		}
		// Work out how much discount would have been given to the cart as a whole and compare to what was discounted on all line items.
		$document_discount = round( $document_total * ( $amount / 100 ), 2 );
		$total_discount    = round( $total_discount, 2 );

		if ( $total_discount < $document_discount && $amount > 0 ) {
			$total_discount += $this->apply_discount_remainder( $amount - $total_discount, $items );
		}

		return $total_discount;
	}

	/**
	 * Apply remainder discount.
	 *
	 * @param float          $amount Discount amount.
	 * @param DocumentItem[] $items Items.
	 *
	 * @return float
	 * @since 1.0.0
	 */
	public function apply_discount_remainder( $amount, $items ) {
		$total_discount = 0;
		foreach ( $items as $item ) {
			$quantity = $item->quantity;
			for ( $i = 0; $i < $quantity; $i++ ) {
				$discounted_price = $item->get_discounted_price();
				$discount         = min( $discounted_price, 1 );
				$item->discount   = $item->discount + $discount;
				$total_discount  += $discount;
				if ( $total_discount >= $amount ) {
					break 2;
				}
			}
			if ( $total_discount >= $amount ) {
				break;
			}
		}

		return $total_discount;
	}


	/**
	 * Get totals.
	 *
	 * @param string $type Type of items.
	 * @param string $column Column name.
	 * @param bool   $round Round the value or not.
	 *
	 * @since 1.0.0
	 */
	public function get_items_totals( $type, $column = 'total', $round = false ) {
		$items = $this->get_items( $type );
		$total = 0;
		foreach ( $items as $item ) {
			$caller = "get_{$column}";
			$amount = is_callable( array( $item, $caller ) ) ? $item->$caller() : 0;
			$total += $round ? round( $amount, 2 ) : $amount;
		}
		return $round ? round( $total, 2 ) : $total;
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
			$index = md5( $tax->get_tax_id() . $tax->get_rate() );
			if ( ! isset( $taxes[ $index ] ) ) {
				$taxes[ $index ] = $tax;
			} else {
				$taxes[ $index ]->merge( $tax );
			}
		}

		return $taxes;
	}

	/**
	 * Is calculating tax.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_calculating_tax() {
		return 'yes' !== eac_tax_enabled() && ! $this->vat_exempt;
	}
}
