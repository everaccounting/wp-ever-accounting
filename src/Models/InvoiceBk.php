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
class InvoiceBk extends ResourceModel {
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
		'invoice_number'      => '',
		'order_number'        => '',
		'status'              => 'draft',
		'invoiced_at'         => null,
		'due_date'              => null,
		'completed_at'        => null,
		'category_id'         => null,
		'customer_id'         => null,
		'customer_name'       => null,
		'customer_phone'      => null,
		'customer_email'      => null,
		'customer_tax_number' => null,
		'customer_postcode'   => '',
		'customer_address'    => '',
		'customer_country'    => '',
		'subtotal'            => 0.00,
		'discount_total'      => 0.00,
		'tax_total'           => 0.00,
		'shipping_total'      => 0.00,
		'total'               => 0.00,
		'note'                => '',
		'footer'              => '',
		'currency_code'       => null,
		'currency_rate'       => null,
		'attachment_id'       => null,
		'transaction_id'      => null,
		'key'                 => null,
		'parent_id'           => null,
		'creator_id'          => null,
		'date_created'        => null,
	);

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
		} elseif ( is_string( $invoice ) && $invoice_id = self::get_invoice_id( $invoice, 'transaction_id' ) ) { // phpcs: ignore
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
	 * @param        $value
	 * @param string $field
	 * @since 1.1.0
	 *
	 * @return int|mixed
	 */
	public static function get_invoice_id( $value, $field = 'key' ) {
		global $wpdb;
		// Valid fields.
		$fields = array( 'id', 'key', 'invoice_number', 'transaction_id', 'order_number' );
		// Ensure a field has been passed.
		if ( empty( $field ) || ! in_array( $field, $fields, true ) ) {
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
			$wpdb->prepare( "SELECT `post_id` FROM $table WHERE `$field`=%s LIMIT 1", $value )
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
	 * Return the subtotal
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_subtotal( $context = 'edit' ) {
		return $this->get_prop( 'subtotal', $context );
	}

	/**
	 * Return the discount.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_discount( $context = 'edit' ) {
		return $this->get_prop( 'discount', $context );
	}

	/**
	 * Return the tax.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_tax( $context = 'edit' ) {
		return $this->get_prop( 'tax', $context );
	}

	/**
	 * Return the shipping.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_shipping( $context = 'edit' ) {
		return $this->get_prop( 'shipping', $context );
	}

	/**
	 * Return the total.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_total( $context = 'edit' ) {
		return $this->get_prop( 'total', $context );
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
	 * Return the contact name.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_contact_name( $context = 'edit' ) {
		return $this->get_prop( 'contact_name', $context );
	}

	/**
	 * Return the contact email.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_contact_email( $context = 'edit' ) {
		return $this->get_prop( 'contact_email', $context );
	}

	/**
	 * Return the contact tax number.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_contact_tax_number( $context = 'edit' ) {
		return $this->get_prop( 'contact_tax_number', $context );
	}

	/**
	 * Return the contact phone.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_contact_phone( $context = 'edit' ) {
		return $this->get_prop( 'contact_phone', $context );
	}

	/**
	 * Return the contact address.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_contact_address( $context = 'edit' ) {
		return $this->get_prop( 'contact_address', $context );
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
	 * set the invoice number.
	 *
	 * @since  1.1.0
	 *
	 * @param string $invoice_number .
	 *
	 */
	public function set_invoice_number( $invoice_number ) {
		$this->set_prop( 'invoice_number', eaccounting_clean( $invoice_number ) );
	}

	/**
	 * set the order number.
	 *
	 * @since  1.1.0
	 *
	 * @param string $invoice_number .
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
	 * @param string $invoice_number .
	 *
	 */
	public function set_status( $status ) {
		$this->set_prop( 'status', eaccounting_clean( $status ) );
	}

	/**
	 * set the invoiced_at.
	 *
	 * @since  1.1.0
	 *
	 * @param string $invoiced_at .
	 *
	 */
	public function set_invoiced_at( $invoiced_at ) {
		$this->set_date_prop( 'invoiced_at', $invoiced_at );
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
	public function set_discount( $discount ) {
		$this->set_prop( 'discount', eaccounting_sanitize_price( $discount ) );
	}

	/**
	 * set the tax.
	 *
	 * @since  1.1.0
	 *
	 * @param DOUBLE $tax .
	 *
	 */
	private function set_tax( $tax ) {
		$this->set_prop( 'tax', eaccounting_sanitize_price( $tax ) );
	}

	/**
	 * set the shipping.
	 *
	 * @since  1.1.0
	 *
	 * @param DOUBLE $shipping .
	 *
	 */
	private function set_shipping( $shipping ) {
		$this->set_prop( 'shipping', eaccounting_sanitize_price( $shipping ) );
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
	public function set_contact_name( $contact_name ) {
		$this->set_prop( 'contact_name', eaccounting_clean( $contact_name ) );
	}

	/**
	 * set the contact_email.
	 *
	 * @since  1.1.0
	 *
	 * @param string $contact_email .
	 *
	 */
	public function set_contact_email( $contact_email ) {
		$this->set_prop( 'contact_email', sanitize_email( $contact_email ) );
	}

	/**
	 * set the contact tax number.
	 *
	 * @since  1.1.0
	 *
	 * @param string $contact_tax_number .
	 *
	 */
	public function set_contact_tax_number( $contact_tax_number ) {
		$this->set_prop( 'contact_tax_number', eaccounting_clean( $contact_tax_number ) );
	}

	/**
	 * set the contact phone.
	 *
	 * @since  1.1.0
	 *
	 * @param string $contact_phone .
	 *
	 */
	public function set_contact_phone( $contact_phone ) {
		$this->set_prop( 'contact_phone', eaccounting_clean( $contact_phone ) );
	}

	/**
	 * set the contact address.
	 *
	 * @since  1.1.0
	 *
	 * @param string $contact_address .
	 *
	 */
	public function set_contact_address( $contact_address ) {
		$this->set_prop( 'contact_address', eaccounting_sanitize_textarea( $contact_address ) );
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

	/*
	|--------------------------------------------------------------------------
	| Object methods
	|--------------------------------------------------------------------------
	|
	| These methods will return object instead of id
	|
	*/

	public function get_attachment() {
		if ( ! empty( $this->get_attachment_id() ) ) {
			$post = get_post( $this->get_attachment_id() );
			if ( 'attachment' !== $post->post_type ) {
				return null;
			}

			return $post;
		}

		return null;
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
	 * @param int $id Item ID to delete.
	 *
	 * @return false|void
	 */
	public function remove_item( $id ) {
		foreach ( $this->get_items() as $key => $item ) {
			if ( $item->get_id() === absint( $id ) ) {
				// Unset and remove later.
				$this->items_to_delete[] = $item;
				unset( $this->items[ $key ] );
			}
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
			'tax_ids'    => $item->get_sales_tax_rate_ids(),
		);

		$args = wp_parse_args( $args, $default );

		$line_item = new InvoiceItem();
		$line_item->set_props( $args );
		$line_item->set_sub_total( $line_item->get_item_price() * $line_item->get_quantity() );
		$line_item->apply_discount( $this->get_discount() );
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

	/**
	 * @since 1.1.0
	 */
	public function calculate_totals() {
		$sub_total = 0;
		$discount  = 0;
		$tax       = 0;
		$shipping  = 0;

		foreach ( $this->get_items() as $item ) {
			$sub_total += (float) $item->get_sub_total();
			$discount  += (float) $item->get_discount();
			$tax       += (float) $item->get_tax();
		}

		$total = $sub_total + $tax + $shipping - $discount;
		$this->set_subtotal( $sub_total );
		$this->set_tax( $tax );
		$this->set_discount( $discount );
		$this->set_shipping( $shipping );
		$this->set_total( $total );
	}


	public function email_invoice() {

	}

	public function mark_sent() {

	}

	public function mark_cancelled() {

	}

	public function mark_paid() {

	}

	public function duplicate() {

	}


	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	|
	| Methods which create, read, update and delete discounts from the database.
	|
	*/

	/**
	 * Save should create or update based on object existence.
	 *
	 * @since  1.1.0
	 * @throws Exception
	 * @return \Exception|bool
	 */
	public function save() {
		if ( empty( $this->get_currency_code() ) ) {
			throw new Exception( 'empty_prop', __( 'Currency code is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_currency_rate() ) ) {
			$currency = new Currency( $this->get_currency_code() );
			$this->set_currency_rate( $currency->get_rate() );
		}

		parent::save();
		$this->save_items();

		return $this->exists();
	}

	/**
	 * Save all order items which are part of this order.
	 */
	protected function save_items() {
		$items_changed = false;

		foreach ( $this->items_to_delete as $item ) {
			$item->delete();
			$items_changed = true;
		}
		$this->items_to_delete = array();

		// Add/save items.
		foreach ( $this->items as $item ) {
			$item->set_invoice_id( $this->get_id() );
			$item->save();
		}

	}


}
