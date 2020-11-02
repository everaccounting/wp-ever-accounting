<?php
/**
 * Handle the invoice object.
 *
 * @since       1.1.0
 *
 * @package     EverAccounting
 */

namespace EverAccounting;

use EverAccounting\Abstracts\Base_Object;

defined( 'ABSPATH' ) || exit();

/**
 * Class Invoice
 *
 * @since 1.1.0
 */
class Invoice extends Base_Object {
	/**
	 * This is the name of this object type.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	public $object_type = 'invoice';

	/***
	 * Object table name.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	public $table = 'ea_invoices';

	/**
	 * Item Data array.
	 *
	 * @since 1.1.0
	 * @var array
	 */
	protected $data = array(
		'invoice_number'     => '',
		'order_number'       => '',
		'status'             => '',
		'invoiced_at'        => null,
		'due_at'             => null,
		'subtotal'           => 0.0000,
		'discount'           => 0.0000,
		'tax'                => 0.0000,
		'shipping'           => 0.0000,
		'total'              => 0.0000,
		'currency_code'      => 'USD',
		'currency_rate'      => 1,
		'category_id'        => '',
		'contact_name'       => '',
		'contact_email'      => null,
		'contact_tax_number' => null,
		'contact_phone'      => null,
		'contact_address'    => null,
		'note'               => null,
		'footer'             => null,
		'attachment'         => null,
		'parent_id'          => 0,
		'creator_id'         => null,
		'date_created'       => null
	);

	/**
	 * Get the invoice if ID is passed, otherwise the invoice is new and empty.
	 * This class should NOT be instantiated, but the eaccounting_get_invoice function
	 * should be used. It is possible, but the aforementioned are preferred and are the only
	 * methods that will be maintained going forward.
	 *
	 * @param int|object|Category $data object to read.
	 *
	 * @since 1.1.0
	 *
	 */
	public function __construct( $data = 0 ) {
		parent::__construct( $data );

		if ( is_numeric( $data ) && $data > 0 ) {
			$this->set_id( $data );
		} elseif ( $data instanceof self ) {
			$this->set_id( $data->get_id() );
		} elseif ( ! empty( $data->id ) ) {
			$this->set_id( $data->id );
		} else {
			$this->set_id( 0 );
		}

		if ( $this->get_id() > 0 && ! $this->object_read ) {
			$this->read();
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get invoice number
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_invoice_number( $context = 'edit' ) {
		return $this->get_prop( 'invoice_number', $context );
	}

	/**
	 * Get invoice order number
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_order_number( $context = 'edit' ) {
		return $this->get_prop( 'order_number', $context );
	}

	/**
	 * Get invoice status
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_status( $context = 'edit' ) {
		return $this->get_prop( 'status', $context );
	}

	/**
	 * Get invoice at
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_invoiced_at( $context = 'edit' ) {
		return $this->get_prop( 'invoiced_at', $context );
	}

	/**
	 * Get invoice due_at
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_due_at( $context = 'edit' ) {
		return $this->get_prop( 'due_at', $context );
	}

	/**
	 * Get invoice total
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_subtotal( $context = 'edit' ) {
		return $this->get_prop( 'subtotal', $context );
	}

	/**
	 * Get invoice discount
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_discount( $context = 'edit' ) {
		return $this->get_prop( 'discount', $context );
	}

	/**
	 * Get invoice tax
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_tax( $context = 'edit' ) {
		return $this->get_prop( 'tax', $context );
	}

	/**
	 * Get invoice shipping
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_shipping( $context = 'edit' ) {
		return $this->get_prop( 'shipping', $context );
	}

	/**
	 * Get invoice total
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_total( $context = 'edit' ) {
		return $this->get_prop( 'total', $context );
	}

	/**
	 * Get invoice currency_code
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_currency_code( $context = 'edit' ) {
		return $this->get_prop( 'currency_code', $context );
	}

	/**
	 * Get invoice currency_rate
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_currency_rate( $context = 'edit' ) {
		return $this->get_prop( 'currency_rate', $context );
	}

	/**
	 * Get invoice category_id
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_category_id( $context = 'edit' ) {
		return $this->get_prop( 'category_id', $context );
	}

	/**
	 * Get invoice contact_name
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_contact_name( $context = 'edit' ) {
		return $this->get_prop( 'contact_name', $context );
	}

	/**
	 * Get invoice contact_email
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_contact_email( $context = 'edit' ) {
		return $this->get_prop( 'contact_email', $context );
	}

	/**
	 * Get invoice contact_tax_number
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_contact_tax_number( $context = 'edit' ) {
		return $this->get_prop( 'contact_tax_number', $context );
	}

	/**
	 * Get invoice contact_phone
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_contact_phone( $context = 'edit' ) {
		return $this->get_prop( 'contact_phone', $context );
	}

	/**
	 * Get invoice contact_address
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_contact_address( $context = 'edit' ) {
		return $this->get_prop( 'contact_address', $context );
	}

	/**
	 * Get invoice note
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_note( $context = 'edit' ) {
		return $this->get_prop( 'note', $context );
	}

	/**
	 * Get invoice footer
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_footer( $context = 'edit' ) {
		return $this->get_prop( 'footer', $context );
	}

	/**
	 * Get invoice attachment
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_attachment( $context = 'edit' ) {
		return $this->get_prop( 'attachment', $context );
	}

	/**
	 * Get invoice parent_id
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_parent_id( $context = 'edit' ) {
		return $this->get_prop( 'parent_id', $context );
	}

	/**
	 * Get invoice creator_id
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_creator_id( $context = 'edit' ) {
		return $this->get_prop( 'creator_id', $context );
	}

	/**
	 * Get invoice date_created
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_date_created( $context = 'edit' ) {
		return $this->get_prop( 'date_created', $context );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/
	/**
	 *  Set invoice_number
	 *
	 * @param $invoice_number
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_invoice_number( $invoice_number ) {
		$this->set_prop( 'invoice_number', absint( $invoice_number ) );
	}

	/**
	 *  Set order_number
	 *
	 * @param $order_number
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_order_number( $order_number ) {
		$this->set_prop( 'order_number', absint( $order_number ) );
	}

	/**
	 *  Set invoiced_at
	 *
	 * @param $invoiced_at
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_invoiced_at( $invoiced_at ) {
		$this->set_date_prop( 'invoiced_at', $invoiced_at );
	}

	/**
	 *  Set subtotal
	 *
	 * @param $subtotal
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_subtotal( $subtotal ) {
		$this->set_prop( 'subtotal', eaccounting_sanitize_price( $subtotal ) );
	}

	/**
	 *  Set discount
	 *
	 * @param $discount
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_discount( $discount ) {
		$this->set_prop( 'discount', eaccounting_sanitize_price( $discount ) );
	}

	/**
	 *  Set tax
	 *
	 * @param $tax
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_tax( $tax ) {
		$this->set_prop( 'tax', eaccounting_sanitize_price( $tax ) );
	}

	/**
	 *  Set total
	 *
	 * @param $total
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_total( $total ) {
		$this->set_prop( 'total', eaccounting_sanitize_price( $total ) );
	}

	/**
	 *  Set currency_code
	 *
	 * @param $currency_code
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_currency_code( $currency_code ) {
		$this->set_prop( 'currency_code', eaccounting_clean( $currency_code ) );
	}

	/**
	 *  Set currency_rate
	 *
	 * @param $currency_rate
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_currency_rate( $currency_rate ) {
		$this->set_prop( 'currency_rate', eaccounting_clean( $currency_rate ) );
	}

	/**
	 *  Set category_id
	 *
	 * @param $category_id
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_category_id( $category_id ) {
		$this->set_prop( 'category_id', eaccounting_clean( $category_id ) );
	}

	/**
	 *  Set contact_name
	 *
	 * @param $contact_name
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_contact_name( $contact_name ) {
		$this->set_prop( 'contact_name', eaccounting_clean( $contact_name ) );
	}

	/**
	 *  Set contact_email
	 *
	 * @param $contact_email
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_contact_email( $contact_email ) {
		$this->set_prop( 'name', sanitize_email( $contact_email ) );
	}

	/**
	 *  Set contact_tax_number
	 *
	 * @param $contact_tax_number
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_contact_tax_number( $contact_tax_number ) {
		$this->set_prop( 'contact_tax_number', absint( $contact_tax_number ) );
	}


	/**
	 *  Set contact_phone
	 *
	 * @param $contact_phone
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_contact_phone( $contact_phone ) {
		$this->set_prop( 'contact_phone', eaccounting_clean( $contact_phone ) );
	}

	/**
	 *  Set contact_address
	 *
	 * @param $contact_address
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_contact_address( $contact_address ) {
		$this->set_prop( 'contact_address', sanitize_textarea_field( $contact_address ) );
	}

	/**
	 *  Set note
	 *
	 * @param $note
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_note( $note ) {
		$this->set_prop( 'note', sanitize_textarea_field( $note ) );
	}

	/**
	 *  Set footer
	 *
	 * @param $footer
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_footer( $footer ) {
		$this->set_prop( 'footer', sanitize_textarea_field( $footer ) );
	}

	/**
	 *  Set attachment
	 *
	 * @param $attachment
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_attachment( $attachment ) {
		if ( ! empty( $attachment ) ) {
			$attachment = esc_url_raw( $attachment );
		}
		$this->set_prop( 'attachment', $attachment );
	}

	/**
	 *  Set parent_id
	 *
	 * @param $parent_id
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_parent_id( $parent_id ) {
		$this->set_prop( 'parent_id', absint( $parent_id ) );
	}


}
