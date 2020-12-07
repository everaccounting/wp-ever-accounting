<?php
/**
 * Handle the invoice object.
 *
 * @package     EverAccounting\Models
 * @class       Currency
 * @version     1.1.0
 */

namespace EverAccounting\Models;

use EverAccounting\Abstracts\ResourceModel;
use EverAccounting\Core\Exception;
use EverAccounting\Core\Repositories;
use EverAccounting\Repositories\Invoices;

defined( 'ABSPATH' ) || exit;

/**
 * Class Invoice
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Models
 */
class Invoice1 extends ResourceModel {
	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = 'invoice';

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
		'type'           => 'invoice',
		'number'         => '',
		'order_number'   => '',
		'status'         => 'draft',
		'issued_at'      => null,
		'due_at'         => null,
		'completed_at'   => null,
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

		//extra not saved in database but for populating automatically

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
	 * Taxes that need deleting are stored here.
	 *
	 * @since 1.1.0
	 * @var array
	 */
	protected $taxes_to_delete = array();

	/**
	 * Stores the status transition information.
	 *
	 * @since 1.1.0
	 * @var bool|array
	 */
	protected $status_transition = false;

	/**
	 * Get the account if ID is passed, otherwise the account is new and empty.
	 *
	 * @since 1.1.0
	 *
	 * @param int|object|Invoice $invoice object to read.
	 *
	 */
	public function __construct( $invoice = 0 ) {
		parent::__construct( $invoice );

		if ( $invoice instanceof self ) {
			$this->set_id( $invoice->get_id() );
		} elseif ( is_numeric( $invoice ) ) {
			$this->set_id( $invoice );
		} elseif ( ! empty( $invoice->id ) ) {
			$this->set_id( $invoice->id );
		} elseif ( is_array( $invoice ) ) {
			$this->set_props( $invoice );
		} elseif ( is_string( $invoice ) && $invoice_id = self::get_invoice_id( $invoice, 'key' ) ) { // phpcs: ignore
			$this->set_id( $invoice_id );
		} elseif ( is_string( $invoice ) && $invoice_id = self::get_invoice_id( $invoice, 'invoice_number' ) ) { // phpcs: ignore
			$this->set_id( $invoice_id );
		} else {
			$this->set_object_read( true );
		}

		//Load repository
		$this->repository = Repositories::load( $this->object_type );

		if ( $this->get_id() > 0 ) {
			$this->repository->read( $this );
		}
	}

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
	public static function get_invoice_id( $value, $field = 'key', $type = 'invoice' ) {
		global $wpdb;
		// Valid fields.
		$fields = array( 'id', 'key', 'number', 'order_number' );
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
			$wpdb->prepare( "SELECT `post_id` FROM $table WHERE `$field`=%s  AND type=%s LIMIT 1", $value, $type )
		);

		// Update the cache with our data
		wp_cache_set( "$field-$value", $invoice_id, 'invoice' );

		return $invoice_id;
	}

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
		$statuses = eaccounting_get_invoice_statuses();

		return isset( $statuses[ $this->get_status() ] ) ? $statuses[ $this->get_status() ] : $this->get_status();
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
	public function get_issued_at( $context = 'edit' ) {
		return $this->get_prop( 'issued_at', $context );
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
	public function get_due_at( $context = 'edit' ) {
		return $this->get_prop( 'due_at', $context );
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
	public function get_completed_at( $context = 'edit' ) {
		return $this->get_prop( 'completed_at', $context );
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
		$statuses   = eaccounting_get_invoice_statuses();
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

			$this->maybe_set_completed_at();
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
	public function set_issued_at( $date ) {
		$this->set_date_prop( 'issued_at', $date );
	}

	/**
	 * set the due at.
	 *
	 * @since  1.1.0
	 *
	 * @param string $due_at .
	 *
	 */
	public function set_due_at( $due_at ) {
		$this->set_date_prop( 'due_at', $due_at );
	}

	/**
	 * set the completed at.
	 *
	 * @since  1.1.0
	 *
	 * @param string $completed_at .
	 *
	 */
	public function set_completed_at( $completed_at ) {
		$this->set_date_prop( 'completed_at', $completed_at );
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
	 * set the discount.
	 *
	 * @since  1.1.0
	 *
	 * @param DOUBLE $discount .
	 *
	 */
	public function set_discount( $discount ) {
		$this->discount = eaccounting_sanitize_price( $discount );
	}

	/**
	 * Set the invoice items.
	 *
	 * @since 1.1.0
	 *
	 * @param InvoiceItem[] $value items.
	 */
	public function set_items( $value ) {

		// Remove existing items.
		$this->items = array();

		// Ensure that we have an array.
		if ( ! is_array( $value ) ) {
			return;
		}

		foreach ( $value as $item ) {
			$this->add_item( $item );
		}

	}

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
			throw new Exception( 'invalid_item_id', __( 'Invalid Item ID', 'wp-ever-accounting' ) );
		}

		$default = array(
			'item_id'    => $item->get_id(),
			'item_name'  => $item->get_name(),
			'item_sku'   => $item->get_sku(),
			'item_price' => $item->get_sale_price(),
			'quantity'   => 1,
			'discount'   => 0,
			'tax_ids'    => $item->get_sales_tax_ids(),
		);

		$args = wp_parse_args( $args, $default );

		$line_item = new InvoiceItem();
		$line_item->set_props( $args );
		$line_item->set_sub_total( $line_item->get_item_price() * $line_item->get_quantity() );
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
					$line_item->add_tax_item( $tax->get_id(), $tax->get_name(), $tax_amount );
				}
			}
		}

		$line_item->calculate_total();
		$this->get_items();
		$this->items[] = $line_item;

		return true;
	}


	/*
	 * Get the invoice items.
	 *
	 * @since 1.1.0
	 *
	 * @return InvoiceItem[]
	 */
	public function get_items() {
		if ( empty( $this->items ) && $this->exists() ) {
			$this->items = $this->repository->read_items( $this );
		}

		return $this->items;
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
	 * Remove item from the order.
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

		// Save the order.
		return $this->save();
	}

	/**
	 * Save data to the database.
	 *
	 * @since 1.1.0
	 * @throws Exception
	 * @return int invoice ID
	 */
	public function save() {
		if ( empty( $this->get_currency_code() ) ) {
			throw new Exception( 'empty_prop', __( 'Currency code is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_currency_rate() ) ) {
			$currency = new Currency( $this->get_currency_code() );
			$this->set_currency_rate( $currency->get_rate() );
		}

		$this->maybe_set_completed_at();
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
	 * order status.
	 *
	 * @since 1.1.0
	 */
	public function maybe_set_completed_at() {

		if ( ! $this->get_completed_at( 'edit' ) && $this->is_status( 'paid' ) ) {
			$this->set_completed_at( current_time( 'mysql' ) );
		}
	}

	/**
	 * Set the invoice key.
	 *
	 * @since 1.1.0
	 */
	public function maybe_set_key() {
		if ( empty( $this->get_key() ) ) {
			$auth_key = defined( 'INV_KEY' ) ? INV_KEY : '';
			$type     = $this->get_type();
			$key      = strtolower(
				$type . md5( $this->get_id() . date( 'Y-m-d H:i:s' ) . $auth_key . uniqid( 'ea', true ) )
			);
			$this->set_key( $key );
		}
	}

	/**
	 * Generates a new number for the invoice.
	 */
	public function maybe_set_number() {
		$number = $this->get_id();

	}

	/**
	 * Save all order items which are part of this order.
	 */
	protected function save_items() {
		foreach ( $this->items_to_delete as $item ) {
			$item->delete();
		}
		$this->items_to_delete = array();

		// Add/save items.
		foreach ( $this->items as $item ) {
			$item->set_invoice_id( $this->get_id() );
			$item->save();
		}

	}

	/**
	 * Save all order items which are part of this order.
	 */
	protected function save_taxes() {
		foreach ( $this->taxes_to_delete as $item ) {
			$item->delete();
		}

		$this->taxes_to_delete = array();

		// Add/save items.
		foreach ( $this->taxes as $item ) {
			$item->set_invoice_id( $this->get_id() );
			$item->save();
		}

	}

	/**
	 * Handle the status transition.
	 */
	protected function status_transition() {
		$status_transition = $this->status_transition;

		// Reset status transition variable.
		$this->status_transition = false;

		if ( $status_transition ) {
			try {
				do_action( 'eaccounting_' . $this->get_type() . '_status_' . $status_transition['to'], $this->get_id(), $this );

				if ( ! empty( $status_transition['from'] ) ) {
					/* translators: 1: old order status 2: new order status */
					$transition_note = sprintf( __( 'Status changed from %1$s to %2$s.', 'wp-ever-accounting' ), $status_transition['from'], $status_transition['to'] );

					// Note the transition occurred.
					$this->add_note( $transition_note, false, true );

					do_action( 'eaccounting_' . $this->get_type() . '_status_' . $status_transition['from'] . '_to_' . $status_transition['to'], $this->get_id(), $this );
					do_action( 'eaccounting_' . $this->get_type() . '_status_changed', $this->get_id(), $status_transition['from'], $status_transition['to'], $this );

					// Work out if this was for a payment, and trigger a payment_status hook instead.
					if (
						in_array( $status_transition['from'], array( 'cancelled', 'pending', 'viewed', 'approved', 'overdue', 'unpaid' ), true )
						&& in_array( $status_transition['to'], array( 'paid', 'partial' ), true )
					) {
						do_action( 'eaccounting_' . $this->get_type() . '_payment_status_changed', $this, $status_transition );
					}
				} else {
					/* translators: %s: new invoice status */
					$transition_note = sprintf( __( 'Status set to %s.', 'wp-ever-accounting' ), $status_transition['to'], $this );

					// Note the transition occurred.
					$this->add_note( trim( $status_transition['note'] . ' ' . $transition_note ), 0, false );
				}
			} catch ( Exception $e ) {
				$this->add_note( __( 'Error during status transition.', 'wp-ever-accounting' ) . ' ' . $e->getMessage() );
			}
		}
	}


	/**
	 * Adds a note to an invoice.
	 *
	 * @param string $note The note being added.
	 *
	 * @return int|false The new note's ID on success, false on failure.
	 *
	 */
	public function add_note( $note = '', $notify = false, $added_by_user = false ) {

		// Bail if no note specified or this invoice is not yet saved.
		if ( ! $note || ! $this->exists() || ( ! is_user_logged_in() && $added_by_user ) ) {
			return false;
		}

		$author       = 'System';
		$author_email = 'bot@wpeveraccounting.com';

		// If this is an admin comment or it has been added by the user.
		if ( is_user_logged_in() && ( $added_by_user ) ) {
			$user         = get_user_by( 'id', get_current_user_id() );
			$author       = $user->display_name;
			$author_email = $user->user_email;
		}
	}
}
