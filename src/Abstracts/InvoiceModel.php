<?php
/**
 * Abstract Order Model.
 *
 * Handles generic data interaction which is implemented by
 * the different repository classes.
 *
 */

namespace EverAccounting\Abstracts;


use EverAccounting\Models\Currency;
use EverAccounting\Models\Item;
use EverAccounting\Models\LineItem;

/**
 * Class OrderModel
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Abstracts
 */
abstract class InvoiceModel extends ResourceModel {
	/**
	 * Type of order.
	 */
	const TYPE = '';

	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = self::TYPE;

	/**
	 * @since 1.1.0
	 * @var string
	 */
	public $cache_group = 'eaccounting_invoice';

	/**
	 * Item Data array.
	 *
	 * @since 1.1.0
	 * @var array
	 */
	protected $data = array(
		'type'           => self::TYPE,
		'number'         => '',
		'order_number'   => '',
		'status'         => 'draft',
		'issue_date'     => null,
		'due_date'       => null,
		'payment_date'   => null,
		'category_id'    => null,
		'contact_id'     => null,
		'name'           => '',
		'phone'          => '',
		'email'          => '',
		'tax_number'     => '',
		'postcode'       => '',
		'address'        => '',
		'country'        => '',
		'subtotal'       => 0.00,
		'total_discount' => 0.00,
		'total_tax'      => 0.00,
		'total_shipping' => 0.00,
		'total'          => 0.00,
		'note'           => '',
		'footer'         => '',
		'attachment_id'  => null,
		'currency_code'  => null,
		'currency_rate'  => null,
		'key'            => null,
		'parent_id'      => null,
		'creator_id'     => null,
		'date_created'   => null,
	);

	/**
	 * Temporarily stores discount
	 *
	 * @since 1.1.0
	 * @var float
	 */
	protected $discount = 0;

	/**
	 * Order items will be stored here, sometimes before they persist in the DB.
	 *
	 * @since 1.1.0
	 * @var array
	 */
	protected $items = array();

	/**
	 * Order items that need deleting are stored here.
	 *
	 * @since 1.1.0
	 * @var array
	 */
	protected $items_to_delete = array();

	/**
	 * Taxes will be stored here, sometimes before they persist in the DB.
	 *
	 * @since 1.1.0
	 * @var array
	 */
	protected $taxes = array();

	/**
	 * Stores the status transition information.
	 *
	 * @since 1.1.0
	 * @var bool|array
	 */
	protected $status_transition = false;


	/**
	 * Get invoice ID based on field type.
	 *
	 * @since 1.1.0
	 *
	 * @param string $field
	 * @param        $value
	 *
	 * @return int|mixed
	 */
	public static function get_id_by( $value, $field = 'key' ) {
		global $wpdb;
		// Valid fields.
		$fields = array( 'key', 'number', 'order_number' );
		// Ensure a field has been passed.
		if ( empty( $field ) || ! in_array( $field, $fields, true ) ) {
			return 0;
		}

		// Ensure valid invoice types.
		if ( empty( $type ) || ! array_key_exists( $type, eaccounting_get_invoice_types() ) ) {
			return 0;
		}

		// Maybe retrieve from the cache.
		$invoice_id = wp_cache_get( "$field-$value", 'invoice' );
		if ( false !== $invoice_id ) {
			return $invoice_id;
		}

		// Fetch from the db.
		$table      = $wpdb->prefix . 'ea_invoices';
		$invoice_id = (int) $wpdb->get_var(
			$wpdb->prepare( "SELECT `post_id` FROM $table WHERE `$field`=%s  AND type=%s LIMIT 1", $value, self::TYPE )
		);

		// Update the cache with our data
		wp_cache_set( "$field-$value", $invoice_id, 'invoice' );

		return $invoice_id;
	}

	/*
	|--------------------------------------------------------------------------
	| abstracts
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get all the available status for the object.
	 *
	 * @since 1.1.0
	 * @return array
	 */
	abstract public function get_statuses();


	/**
	 * Generates a new number for the invoice.
	 */
	abstract public function maybe_set_number();

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/
	/**
	 * Return the invoice type.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_type( $context = 'edit' ) {
		return $this->get_prop( 'type', $context );
	}

	/**
	 * Get type nicename.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	public function get_type_nicename() {
		$types = eaccounting_get_invoice_types();

		return isset( $types[ $this->get_type() ] ) ? $types[ $this->get_type() ] : $this->get_type();
	}


	/**
	 * Return the invoice number.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_number( $context = 'edit' ) {
		return $this->get_prop( 'number', $context );
	}

	/**
	 * Return the order number.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_order_number( $context = 'edit' ) {
		return $this->get_prop( 'order_number', $context );
	}

	/**
	 * Return the status.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_status( $context = 'edit' ) {
		return $this->get_prop( 'status', $context );
	}

	/**
	 * Get invoice status nice name.
	 *
	 * @since 1.1.0
	 * @return mixed|string
	 */
	public function get_status_nicename() {
		return isset( $this->get_statuses()[ $this->get_status() ] ) ? $this->get_statuses()[ $this->get_status() ] : $this->get_status();
	}

	/**
	 * Return the invoiced at.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_issue_date( $context = 'edit' ) {
		return $this->get_prop( 'issue_date', $context );
	}

	/**
	 * Return the due at.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_due_date( $context = 'edit' ) {
		return $this->get_prop( 'due_date', $context );
	}

	/**
	 * Return the completed at.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_payment_date( $context = 'edit' ) {
		return $this->get_prop( 'payment_date', $context );
	}

	/**
	 * Return the category id.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_category_id( $context = 'edit' ) {
		return $this->get_prop( 'category_id', $context );
	}

	/**
	 * Return the contact id.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_contact_id( $context = 'edit' ) {
		return $this->get_prop( 'contact_id', $context );
	}

	/**
	 * Return the contact name.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_name( $context = 'edit' ) {
		return $this->get_prop( 'name', $context );
	}

	/**
	 * Return the customer phone.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_phone( $context = 'edit' ) {
		return $this->get_prop( 'phone', $context );
	}

	/**
	 * Return the customer email.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_email( $context = 'edit' ) {
		return $this->get_prop( 'email', $context );
	}

	/**
	 * Return the customer tax number.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_tax_number( $context = 'edit' ) {
		return $this->get_prop( 'tax_number', $context );
	}

	/**
	 * Return the customer postcode.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_postcode( $context = 'edit' ) {
		return $this->get_prop( 'postcode', $context );
	}

	/**
	 * Return the customer address.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_address( $context = 'edit' ) {
		return $this->get_prop( 'address', $context );
	}

	/**
	 * Return the customer country.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_country( $context = 'edit' ) {
		return $this->get_prop( 'country', $context );
	}

	/**
	 * Returns the contact info.
	 *
	 * @since 1.1.0
	 *
	 * @param string $context View or edit context.
	 *
	 * @return array
	 */
	public function get_contact_info( $context = 'view' ) {

		return array(
			'id'         => $this->get_contact_id(),
			'name'       => $this->get_name( $context ),
			'phone'      => $this->get_phone( $context ),
			'email'      => $this->get_email( $context ),
			'tax_number' => $this->get_tax_number( $context ),
			'postcode'   => $this->get_postcode( $context ),
			'address'    => $this->get_address( $context ),
			'country'    => $this->get_country( $context ),
		);

	}

	/**
	 * Get the invoice subtotal.
	 *
	 * @since 1.1.0
	 *
	 * @param string $context View or edit context.
	 *
	 * @return float
	 */
	public function get_subtotal( $context = 'view' ) {
		return (float) $this->get_prop( 'subtotal', $context );
	}

	/**
	 * Get the invoice discount total.
	 *
	 * @since 1.1.0
	 *
	 * @param string $context View or edit context.
	 *
	 * @return float
	 */
	public function get_total_discount( $context = 'view' ) {
		return (float) $this->get_prop( 'total_discount', $context );
	}

	/**
	 * Get the invoice tax total.
	 *
	 * @since 1.1.0
	 *
	 * @param string $context View or edit context.
	 *
	 * @return float
	 */
	public function get_total_tax( $context = 'view' ) {
		return (float) $this->get_prop( 'total_tax', $context );
	}

	/**
	 * Get the invoice shipping total.
	 *
	 * @since 1.1.0
	 *
	 * @param string $context View or edit context.
	 *
	 * @return float
	 */
	public function get_total_shipping( $context = 'view' ) {
		return (float) $this->get_prop( 'total_shipping', $context );
	}

	/**
	 * Get the invoice total.
	 *
	 * @since 1.1.0
	 * @return float
	 */
	public function get_total( $context ) {
		$total = (float) $this->get_prop( 'total', $context );
		if ( 0 > $total ) {
			$total = 0;
		}

		return $total;
	}

	/**
	 * Return the note.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_note( $context = 'edit' ) {
		return $this->get_prop( 'note', $context );
	}

	/**
	 * Return the footer.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_footer( $context = 'edit' ) {
		return $this->get_prop( 'footer', $context );
	}

	/**
	 * Return the attachment.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_attachment_id( $context = 'edit' ) {
		return $this->get_prop( 'attachment_id', $context );
	}

	/**
	 * Return the currency code.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_currency_code( $context = 'edit' ) {
		return $this->get_prop( 'currency_code', $context );
	}

	/**
	 * Return the currency rate.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_currency_rate( $context = 'edit' ) {
		return $this->get_prop( 'currency_rate', $context );
	}

	/**
	 * Return the invoice key.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_key( $context = 'edit' ) {
		return $this->get_prop( 'key', $context );
	}

	/**
	 * Return the parent id.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_parent_id( $context = 'edit' ) {
		return $this->get_prop( 'parent_id', $context );
	}

	/**
	 * Get the invoice items.
	 *
	 * @since 1.1.0
	 *
	 * @return LineItem[]
	 */
	public function get_items() {
		if ( empty( $this->items ) && $this->exists() ) {
			$this->items = $this->repository->read_items( $this );
		}

		return $this->items;
	}

	/**
	 * Get item ids.
	 *
	 * @since 1.1.0
	 * @return array
	 */
	public function get_item_ids() {
		$ids = array();
		foreach ( $this->get_items() as $item ) {
			$ids[] = $item->get_id();
		}

		return $ids;
	}

	/**
	 * Get the invoice items.
	 *
	 * @since 1.1.0
	 *
	 * @return LineTax[]
	 */
	public function get_taxes() {
		if ( empty( $this->taxes ) && $this->exists() ) {
			$this->taxes = $this->repository->read_taxes( $this );
		}

		return $this->taxes;
	}

	/**
	 * Retrieves a specific item.
	 *
	 * @since 1.0.19
	 */
	public function get_item( $id ) {
		foreach ( $this->get_items() as $item ) {
			if ( $item->get_id() === absint( $id ) ) {
				return $item;
			}
		}

		return null;
	}

	/**
	 * Retrieves a specific tax item.
	 *
	 * @since 1.0.19
	 */
	public function get_tax_item( $id ) {
		foreach ( $this->get_taxes() as $item ) {
			if ( $item->get_id() === absint( $id ) ) {
				return $item;
			}
		}

		return null;
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/


	/**
	 * set the invoice type.
	 *
	 * @since  1.1.0
	 *
	 * @param string $type .
	 *
	 */
	public function set_type( $type ) {
		if ( ! empty( $type ) && array_key_exists( $type, eaccounting_get_invoice_types() ) ) {
			$this->set_prop( 'type', eaccounting_clean( $type ) );
		}
	}


	/**
	 * set the number.
	 *
	 * @since  1.1.0
	 *
	 * @param string $number .
	 *
	 */
	public function set_number( $number ) {
		$this->set_prop( 'number', eaccounting_clean( $number ) );
	}

	/**
	 * set the order number.
	 *
	 * @since  1.1.0
	 *
	 * @param string $order_number .
	 *
	 */
	protected function set_order_number( $order_number ) {
		$this->set_prop( 'order_number', eaccounting_clean( $order_number ) );
	}

	/**
	 * set the status.
	 *
	 * @since  1.1.0
	 *
	 * @param string $status .
	 * @param string $note
	 * @param bool   $by_user
	 *
	 * @return array
	 */
	public function set_status( $status, $note = '', $by_user = false ) {
		$old_status = $this->get_status();
		$statuses   = $this->get_statuses();
		$this->set_prop( 'status', eaccounting_clean( $status ) );

		if ( isset( $statuses['draft'] ) ) {
			unset( $statuses['draft'] );
		}

		// If setting the status, ensure it's set to a valid status.
		if ( true === $this->object_read ) {

			// Only allow valid new status.
			if ( ! array_key_exists( $status, $statuses ) ) {
				$status = 'draft';
			}
		}

		if ( true === $this->object_read && $old_status !== $status ) {
			$this->status_transition = array(
				'from' => ! empty( $this->status_transition['from'] ) ? $this->status_transition['from'] : $old_status,
				'to'   => $status,
				'note' => $note,
			);

			$this->maybe_set_payment_date();
		}

		return array(
			'from' => $old_status,
			'to'   => $status,
		);
	}

	/**
	 * Set date when the invoice was created.
	 *
	 * @since 1.1.0
	 *
	 * @param string $date Value to set.
	 */
	public function set_issue_date( $date ) {
		$this->set_date_prop( 'issue_date', $date );
	}

	/**
	 * set the due at.
	 *
	 * @since  1.1.0
	 *
	 * @param string $due_date .
	 *
	 */
	public function set_due_date( $due_date ) {
		$this->set_date_prop( 'due_date', $due_date );
	}

	/**
	 * set the completed at.
	 *
	 * @since  1.1.0
	 *
	 * @param string $payment_date .
	 *
	 */
	public function set_payment_date( $payment_date ) {
		$this->set_date_prop( 'payment_date', $payment_date );
	}

	/**
	 * set the category id.
	 *
	 * @since  1.1.0
	 *
	 * @param int $category_id .
	 *
	 */
	public function set_category_id( $category_id ) {
		$this->set_prop( 'category_id', absint( $category_id ) );
	}

	/**
	 * set the contact_id.
	 *
	 * @since  1.1.0
	 *
	 * @param int $contact_id .
	 *
	 */
	public function set_contact_id( $contact_id ) {
		$this->set_prop( 'contact_id', absint( $contact_id ) );
	}

	/**
	 * set the contact name.
	 *
	 * @since  1.1.0
	 *
	 * @param string $contact_name .
	 *
	 */
	public function set_name( $name ) {
		$this->set_prop( 'name', eaccounting_clean( $name ) );
	}

	/**
	 * set the contact phone.
	 *
	 * @since  1.1.0
	 *
	 * @param string $phone .
	 *
	 */
	public function set_phone( $phone ) {
		$this->set_prop( 'phone', eaccounting_clean( $phone ) );
	}

	/**
	 * set the email.
	 *
	 * @since  1.1.0
	 *
	 * @param string $email .
	 *
	 */
	public function set_email( $email ) {
		$this->set_prop( 'email', sanitize_email( $email ) );
	}

	/**
	 * set the contact tax number.
	 *
	 * @since  1.1.0
	 *
	 * @param string $tax_number .
	 *
	 */
	public function set_tax_number( $tax_number ) {
		$this->set_prop( 'tax_number', eaccounting_clean( $tax_number ) );
	}

	/**
	 * set the contact postcode.
	 *
	 * @since  1.1.0
	 *
	 * @param string $postcode .
	 *
	 */
	public function set_postcode( $postcode ) {
		$this->set_prop( 'postcode', eaccounting_clean( $postcode ) );
	}

	/**
	 * set the contact address.
	 *
	 * @since  1.1.0
	 *
	 * @param string $address .
	 *
	 */
	public function set_address( $address ) {
		$this->set_prop( 'address', eaccounting_sanitize_textarea( $address ) );
	}

	/**
	 * set the contact country.
	 *
	 * @since  1.1.0
	 *
	 * @param string $country .
	 *
	 */
	public function set_country( $country ) {
		if ( ! empty( $country ) && array_key_exists( $country, eaccounting_get_countries() ) ) {
			$this->set_prop( 'country', eaccounting_clean( $country ) );
		}
	}

	/**
	 * set the subtotal.
	 *
	 * @since  1.1.0
	 *
	 * @param DOUBLE $subtotal .
	 *
	 */
	private function set_subtotal( $subtotal ) {
		$this->set_prop( 'subtotal', eaccounting_sanitize_price( $subtotal ) );
	}

	/**
	 * set the discount.
	 *
	 * @since  1.1.0
	 *
	 * @param DOUBLE $discount .
	 *
	 */
	public function set_total_discount( $discount ) {
		$this->set_prop( 'total_discount', eaccounting_sanitize_price( $discount ) );
	}

	/**
	 * set the tax.
	 *
	 * @since  1.1.0
	 *
	 * @param DOUBLE $tax .
	 *
	 */
	private function set_total_tax( $tax ) {
		$this->set_prop( 'total_tax', eaccounting_sanitize_price( $tax ) );
	}

	/**
	 * set the shipping.
	 *
	 * @since  1.1.0
	 *
	 * @param DOUBLE $shipping .
	 *
	 */
	private function set_total_shipping( $shipping ) {
		$this->set_prop( 'total_shipping', eaccounting_sanitize_price( $shipping ) );
	}

	/**
	 * set the total.
	 *
	 * @since  1.1.0
	 *
	 * @param DOUBLE $total .
	 *
	 */
	private function set_total( $total ) {
		$this->set_prop( 'total', eaccounting_sanitize_price( $total ) );
	}

	/**
	 * set the note.
	 *
	 * @since  1.1.0
	 *
	 * @param string $note .
	 *
	 */
	public function set_note( $note ) {
		$this->set_prop( 'note', eaccounting_sanitize_textarea( $note ) );
	}

	/**
	 * set the footer.
	 *
	 * @since  1.1.0
	 *
	 * @param string $footer .
	 *
	 */
	public function set_footer( $footer ) {
		$this->set_prop( 'footer', eaccounting_sanitize_textarea( $footer ) );
	}

	/**
	 * set the attachment.
	 *
	 * @since  1.1.0
	 *
	 * @param string $attachment .
	 *
	 */
	public function set_attachment_id( $attachment ) {
		$this->set_prop( 'attachment_id', absint( $attachment ) );
	}

	/**
	 * set the currency code.
	 *
	 * @since  1.1.0
	 *
	 * @param string $currency_code .
	 *
	 */
	public function set_currency_code( $currency_code ) {
		if ( eaccounting_get_currency_data( $currency_code ) ) {
			$this->set_prop( 'currency_code', eaccounting_clean( $currency_code ) );
		}
	}

	/**
	 * set the currency rate.
	 *
	 * @since  1.1.0
	 *
	 * @param double $currency_rate .
	 *
	 */
	public function set_currency_rate( $currency_rate ) {
		$this->set_prop( 'currency_rate', eaccounting_clean( $currency_rate ) );
	}

	/**
	 * set the parent id.
	 *
	 * @since  1.1.0
	 *
	 * @param int $parent_id .
	 *
	 */
	public function set_parent_id( $parent_id ) {
		$this->set_prop( 'parent_id', absint( $parent_id ) );
	}

	/**
	 * Set the invoice key.
	 *
	 * @since 1.1.0
	 *
	 * @param string $value New key.
	 */
	public function set_key( $value ) {
		$key = eaccounting_clean( $value );
		$this->set_prop( 'key', $key );
	}

	/**
	 * set the discount.
	 *
	 * @since  1.1.0
	 *
	 * @param DOUBLE $discount .
	 *
	 */
	public function set_discount( $discount ) {
		$this->discount = eaccounting_sanitize_number( $discount, true );
	}


	/**
	 * Set the invoice items.
	 *
	 * @since 1.1.0
	 *
	 * @param LineItem[] $value items.
	 */
	public function set_items( $value ) {
		// Old items.
		$old_items = $this->items;

		// Remove existing items.
		$this->items = array();

		// Ensure that we have an array.
		if ( ! is_array( $value ) ) {
			return;
		}

		$new_item_ids = array();
		foreach ( $value as $item ) {
			$this->add_item( $item );
			$new_item_ids[ $item->get_id() ];
		}

		// Lets remove old items
		foreach ( $old_items as $old_item ) {
			if ( ! in_array( $old_item->get_id(), $new_item_ids, true ) ) {
				$this->items_to_delete[] = $old_item;
			}
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Boolean methods
	|--------------------------------------------------------------------------
	|
	| Return true or false.
	|
	*/


	/**
	 * Checks if the invoice has a given status.
	 *
	 * @param $status
	 *
	 * @return bool
	 */
	public function is_status( $status ) {
		return $this->get_status() === eaccounting_clean( $status );
	}

	/**
	 * Checks if the invoice is of a given type.
	 */
	public function is_type( $type ) {
		return $this->get_type() === eaccounting_clean( $type );
	}

	/*
	|--------------------------------------------------------------------------
	| Additional methods
	|--------------------------------------------------------------------------
	|
	| Does extra thing as helper functions.
	|
	*/

	/**
	 * Adds an item to the invoice.
	 *
	 * @param array $item
	 *
	 * @return \WP_Error|Bool
	 */
	public function add_item( $args ) {
		$this->get_items();
		$args = wp_parse_args( $args, array( 'item_id' => null ) );
		$item = new Item( $args['item_id'] );
		if ( ! $item->exists() ) {
			throw new \Exception( __( 'Invalid Item ID', 'wp-ever-accounting' ) );
		}

		$default = array(
			'item_id'    => $item->get_id(),
			'item_name'  => $item->get_name(),
			'item_sku'   => $item->get_sku(),
			'item_price' => $item->get_sale_price(),
			'quantity'   => 1,
			'discount'   => 0,
			'taxes'      => $item->get_sales_tax_rate_ids(),
		);

		$args = wp_parse_args( $args, $default );

		$line_item = new LineItem();
		$line_item->set_props( $args );
		$line_item->set_subtotal( $line_item->get_item_price() * $line_item->get_quantity() );
		$line_item->apply_discount( $this->discount );
		$discounted_subtotal = $line_item->get_discounted_subtotal();

		if ( eaccounting_tax_enabled() ) {
			$line_tax_total = 0;
			$tax_ids        = wp_parse_id_list( $args['tax_ids'] );
			if ( ! empty( $tax_ids ) ) {
				foreach ( $tax_ids as $tax_id ) {
					$tax = new Tax( $tax_id );
					if ( ! $tax->exists() ) {
						continue;
					}

					switch ( $tax->get_type() ) {
						case 'compound':
							$tax_amount = ( ( $discounted_subtotal + $line_tax_total ) / 100 ) * $tax->get_rate();
							break;
						case 'fixed':
							$tax_amount = $tax->get_rate() * $line_item->get_quantity();
							break;
						default:
							$tax_amount = ( $discounted_subtotal / 100 ) * $tax->get_rate();
							break;
					}

					$line_tax_total += $tax_amount;
					$line_tax        = new LineTax();
					$line_tax->set_props(
						array(
							'item_id'  => $line_item->get_id(),
							'tax_id'   => $tax->get_id(),
							'tax_name' => $tax->get_name(),
							'tax_rate' => $tax->get_rate(),
							'total'    => $tax_amount,
						)
					);

					$this->taxes[] = $line_tax;
				}
			}
		}

		$line_item->calculate_total();
		$this->get_items();
		$this->items[] = $line_item;

		return true;
	}

	/**
	 * Remove item from the invoice.
	 *
	 * @param int $item_id Item ID to delete.
	 *
	 * @return false|void
	 */
	public function remove_item( $item_id ) {
		$item = $this->get_item( $item_id );
		if ( ! empty( $item ) ) {
			// Unset and remove later.
			$this->items_to_delete[] = $item;
			foreach ( $this->get_items() as $key => $item ) {
				if ( $item->get_id() === absint( $item_id ) ) {
					unset( $this->items[ $key ] );
				}
			}
		}
	}

	/**
	 * Updates an invoice status.
	 */
	public function update_status( $new_status = false, $note = '', $by_user = false ) {
		// Update the status.
		$this->set_status( $new_status, $note, $by_user );

		// Save the invoice.
		return $this->save();
	}

	/**
	 * Save data to the database.
	 *
	 * @since 1.1.0
	 * @throws \Exception
	 * @return int invoice ID
	 */
	public function save() {
		if ( empty( $this->get_currency_code() ) ) {
			throw new \Exception( __( 'Currency code is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_currency_rate() ) ) {
			$currency = new Currency( $this->get_currency_code() );
			$this->set_currency_rate( $currency->get_rate() );
		}

		$this->maybe_set_payment_date();
		$this->maybe_set_key();
		$this->maybe_set_number();
		parent::save();
		$this->save_items();
		$this->save_taxes();
		$this->clear_cache();
		$this->status_transition();

		return $this->get_id();
	}

	/**
	 * Maybe set date paid.
	 *
	 * Sets the date paid variable when transitioning to the payment complete
	 * invoice status.
	 *
	 * @since 1.1.0
	 */
	public function maybe_set_payment_date() {

		if ( ! $this->get_payment_date( 'edit' ) && $this->is_status( 'paid' ) ) {
			$this->set_payment_date( current_time( 'mysql' ) );
		}
	}

	/**
	 * Set the invoice key.
	 *
	 * @since 1.1.0
	 */
	public function maybe_set_key() {
		if ( empty( $this->get_key() ) ) {
			$auth_key = defined( 'EA_ORDER_KEY' ) ? EA_ORDER_KEY : '';
			$type     = $this->get_type();
			$key      = strtolower(
				$type . md5( $this->get_id() . date( 'Y-m-d H:i:s' ) . $auth_key . uniqid( 'ea-', true ) )
			);
			$this->set_key( $key );
		}
	}

	/**
	 * Save all line items which are part of this invoice.
	 */
	protected function save_items() {
		foreach ( $this->items_to_delete as $item ) {
			if ( $item->exist() ) {
				$item->delete();
			}
		}
		$this->items_to_delete = array();

		// Add/save items.
		foreach ( $this->items as $item ) {
			$item->set_invoice_id( $this->get_id() );
			$item->save();
		}

	}

	/**
	 * Save all tax items which are part of this invoice.
	 */
	protected function save_taxes() {
		$item_ids = $this->get_item_ids();
		// Add/save items.
		foreach ( $this->taxes as $key => $tax ) {
			if ( ! in_array( $tax->get_item_id(), $item_ids, true ) && $tax->exists() ) {
				$tax->delete();
			}
			unset( $this->taxes[ $key ] );
		}

		// Add/save items.
		foreach ( $this->taxes as $item ) {
			$item->set_order_id( $this->get_id() );
			$item->save();
		}

	}


}
