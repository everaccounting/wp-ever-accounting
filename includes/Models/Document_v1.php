<?php

namespace EverAccounting\Models;

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
 */
class Document_v1 extends Model {
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
	protected function set_billing_attribute( $prop, $value ) {
		$this->data['billing_data'][ $prop ] = $value;
	}


	/**
	 * Get billing name.
	 *
	 * @since  1.1.0
	 * @return string
	 */
	public function get_billing_name_attribute() {
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
	public function get_billing_company_attribute() {
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
	public function get_billing_address_1_attribute() {
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
	public function get_billing_address_2_attribute() {
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
	public function get_billing_city_attribute() {
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
	public function get_billing_state_attribute() {
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
	public function get_billing_postcode_attribute() {
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
	public function get_billing_country_attribute() {
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
	public function get_billing_phone_attribute() {
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
	public function get_billing_email_attribute() {
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
	public function get_billing_vat_number_attribute() {
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
	public function save() {
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
}
