<?php
/**
 * Abstract class for document object.
 *
 * @package     EverAccounting\Models
 * @class       Currency
 * @version     1.1.0
 */

namespace EverAccounting\Models;

use EverAccounting\Abstracts\ResourceModel;
use EverAccounting\Core\Repositories;
use EverAccounting\Traits\AttachmentTrait;
use EverAccounting\Traits\CurrencyTrait;

abstract class Document extends ResourceModel {
	use AttachmentTrait;
	use CurrencyTrait;

	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = 'document';

	/**
	 * @since 1.1.0
	 *
	 * @var string
	 */
	public $cache_group = 'ea_documents';

	/**
	 * The name of the repository.
	 *
	 * @since 1.1.0
	 *
	 * @var
	 */
	protected $repository_name = 'documents';


	/**
	 * Item Data array.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $data = array(
		'document_number' => '',
		'type'            => '',
		'order_number'    => '',
		'status'          => 'draft',
		'issue_date'      => null,
		'due_date'        => null,
		'payment_date'    => null,
		'category_id'     => null,
		'contact_id'      => null,
		'address'         => array(
			'name'       => '',
			'company'    => '',
			'street'     => '',
			'city'       => '',
			'state'      => '',
			'postcode'   => '',
			'country'    => '',
			'email'      => '',
			'phone'      => '',
			'vat_number' => '',
		),
		'discount'        => 0.00,
		'discount_type'   => 'percentage',
		'subtotal'        => 0.00,
		'total_tax'       => 0.00,
		'total_discount'  => 0.00,
		'total'           => 0.00,
		'tax_inclusive'   => 1,
		'terms'           => '',
		'attachment_id'   => null,
		'currency_code'   => null,
		'currency_rate'   => 1,
		'key'             => null,
		'parent_id'       => null,
		'creator_id'      => null,
		'date_created'    => null,
	);

	/**
	 * Order items will be stored here, sometimes before they persist in the DB.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $items = array();

	/**
	 * Order items that need deleting are stored here.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $items_to_delete = array();

	/**
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $status_transition = array();


	/**
	 * Get the document if ID is passed, otherwise the account is new and empty.
	 *
	 * @since 1.1.0
	 *
	 * @param int|object $document object to read.
	 *
	 */
	public function __construct( $document = 0 ) {
		parent::__construct( $document );
		//Load repository
		$this->repository = Repositories::load( $this->repository_name );
	}


	/*
	|--------------------------------------------------------------------------
	| Object Specific data methods
	|--------------------------------------------------------------------------
	*/

	/**
	 * All available statuses.
	 *
	 * @since 1.0.1
	 *
	 * @return array
	 */
	public static function get_statuses() {
		return array();
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/
	/**
	 * Return the document number.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_document_number( $context = 'edit' ) {
		return $this->get_prop( 'document_number', $context );
	}

	/**
	 * Generate document number.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public function maybe_set_document_number() {
		if ( empty( $this->get_document_number() ) ) {
			$number = $this->get_id();
			if ( empty( $number ) ) {
				$number = $this->repository->get_next_number( $this );
			}
			$this->set_document_number( $this->generate_number( $number ) );
		}
	}

	/**
	 * Get internal type.
	 *
	 * @param string $context
	 *
	 * @return string
	 */
	public function get_type( $context = 'edit' ) {
		return $this->get_prop( 'type', $context );
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
	 *
	 * @return mixed|string
	 */
	public function get_status_nicename() {
		return isset( $this->get_statuses()[ $this->get_status() ] ) ? $this->get_statuses()[ $this->get_status() ] : $this->get_status();
	}

	/**
	 * Return the issued at.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context
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
	 * @param string $context
	 *
	 * @return string
	 */
	public function get_due_date( $context = 'edit' ) {
		return $this->get_prop( 'due_date', $context );
	}

	/**
	 * Return the due at.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context
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
	 * Return the address.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context
	 *
	 * @return string
	 */
	public function get_address( $context = 'edit' ) {
		return $this->get_prop( 'address', $context );
	}

	/**
	 * Gets a prop for a getter method.
	 *
	 * @since  1.1.0
	 *
	 * @param string $prop    Name of prop to get.
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return mixed
	 */
	protected function get_address_prop( $prop, $context = 'view' ) {
		$value = null;

		if ( array_key_exists( $prop, $this->data['address'] ) ) {
			$value = isset( $this->changes['address'][ $prop ] ) ? $this->changes['address'][ $prop ] : $this->data['address'][ $prop ];

			if ( 'view' === $context ) {
				$value = apply_filters( $this->get_hook_prefix() . 'address' . '_' . $prop, $value, $this );
			}
		}

		return $value;
	}

	/**
	 * Get name.
	 *
	 * @since 1.1.0
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string
	 */
	public function get_name( $context = 'view' ) {
		return $this->get_address_prop( 'name', $context );
	}

	/**
	 * Get company.
	 *
	 * @since 1.1.0
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string
	 */
	public function get_company( $context = 'view' ) {
		return $this->get_address_prop( 'company', $context );
	}

	/**
	 * Get street.
	 *
	 * @since 1.1.0
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string
	 */
	public function get_street( $context = 'view' ) {
		return $this->get_address_prop( 'street', $context );
	}

	/**
	 * Get city.
	 *
	 * @since 1.1.0
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string
	 */
	public function get_city( $context = 'view' ) {
		return $this->get_address_prop( 'city', $context );
	}

	/**
	 * Get state.
	 *
	 * @since 1.1.0
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string
	 */
	public function get_state( $context = 'view' ) {
		return $this->get_address_prop( 'state', $context );
	}

	/**
	 * Get postcode.
	 *
	 * @since 1.1.0
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string
	 */
	public function get_postcode( $context = 'view' ) {
		return $this->get_address_prop( 'postcode', $context );
	}

	/**
	 * Get country.
	 *
	 * @since 1.1.0
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string
	 */
	public function get_country( $context = 'view' ) {
		return $this->get_address_prop( 'country', $context );
	}

	/**
	 * Get email.
	 *
	 * @since 1.1.0
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string
	 */
	public function get_email( $context = 'view' ) {
		return $this->get_address_prop( 'email', $context );
	}

	/**
	 * Get phone.
	 *
	 * @since 1.1.0
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string
	 */
	public function get_phone( $context = 'view' ) {
		return $this->get_address_prop( 'phone', $context );
	}

	/**
	 * Get vat_number.
	 *
	 * @since 1.1.0
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string
	 */
	public function get_vat_number( $context = 'view' ) {
		return $this->get_address_prop( 'vat_number', $context );
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
	public function get_discount( $context = 'view' ) {
		return $this->get_prop( 'discount', $context );
	}

	/**
	 * Get the invoice discount type.
	 *
	 * @since 1.1.0
	 *
	 * @param string $context View or edit context.
	 *
	 * @return float
	 */
	public function get_discount_type( $context = 'view' ) {
		return $this->get_prop( 'discount_type', $context );
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
	 * Get the document total.
	 *
	 * @since 1.1.0
	 *
	 * @param string $context View or edit context.
	 *
	 * @return float
	 */
	public function get_total( $context = 'view' ) {
		return (float) $this->get_prop( 'total', $context );
	}

	/**
	 * Get tax inclusive or not.
	 *
	 * @since 1.1.0
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 */
	public function get_tax_inclusive( $context = 'edit' ) {
		if ( ! $this->exists() ) {
			return eaccounting_prices_include_tax();
		}

		return $this->get_prop( 'tax_inclusive', $context );
	}

	/**
	 * Return the terms.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context
	 *
	 * @return string
	 */
	public function get_terms( $context = 'edit' ) {
		return $this->get_prop( 'terms', $context );
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
	 * Set the document key.
	 *
	 * @since 1.1.0
	 */
	public function maybe_set_key() {
		$key = $this->get_key();
		if ( empty( $key ) ) {
			$this->set_key( $this->generate_key() );
		}
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
	 *
	 * @return DocumentItem[]
	 */
	public function get_items() {
		if ( $this->exists() && empty( $this->items ) ) {
			$items = $this->repository->get_items( $this );
			foreach ( $items as $item_id => $item ) {
				if ( ! array_key_exists( $item_id, $this->items_to_delete ) ) {
					$this->items[ $item_id ] = $item;
				}
			}
		}

		return $this->items;
	}

	/**
	 * Get item ids.
	 *
	 * @since 1.1.0
	 *
	 * @return array
	 */
	public function get_item_ids() {
		$ids = array();
		foreach ( $this->get_items() as $item ) {
			$ids[] = $item->get_item_id();
		}

		return $ids;
	}

	/**
	 * Get the invoice items.
	 *
	 * @since 1.1.0
	 *
	 * @return array
	 */
	public function get_taxes() {
		$taxes = array();
		if ( empty( $this->get_items() ) ) {
			foreach ( $this->get_items() as $item ) {
				$taxes[ $item->get_item_id() ] = $item->get_tax();
			}
		}

		return array();
	}

	/**
	 * @since 1.1.0
	 *
	 * @param      $item_id
	 * @param bool $by_line_id
	 *
	 * @return DocumentItem|int
	 */
	public function get_item( $item_id, $by_line_id = false ) {
		$items = $this->get_items();

		// Search for item id.
		if ( ! empty( $items ) && ! $by_line_id ) {
			foreach ( $items as $id => $item ) {
				if ( isset( $items[ $item_id ] ) ) {
					return $items[ $item_id ];
				}
			}
		} elseif ( ! empty( $items ) && $by_line_id ) {
			foreach ( $items as $item ) {
				if ( $item->get_id() === absint( $item_id ) ) {
					return $item;
				}
			}
		}

		return false;
	}

	/**
	 * Get all class data in array format.
	 *
	 * @since 3.0.0
	 * @return array
	 */
	public function get_data() {
		return array_merge(
			parent::get_data(),
			array(
				'line_items' => $this->get_items(),
			)
		);
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/

	/**
	 * set the number.
	 *
	 * @since  1.1.0
	 *
	 * @param string $document_number .
	 *
	 */
	public function set_document_number( $document_number ) {
		$this->set_prop( 'document_number', eaccounting_clean( $document_number ) );
	}

	/**
	 * set the number.
	 *
	 * @since  1.1.0
	 *
	 * @param string $order_number .
	 *
	 */
	public function set_order_number( $order_number ) {
		$this->set_prop( 'order_number', eaccounting_clean( $order_number ) );
	}

	/**
	 * set the number.
	 *
	 * @since  1.1.0
	 *
	 * @param string $type .
	 *
	 */
	public function set_type( $type ) {
		$this->set_prop( 'type', eaccounting_clean( $type ) );
	}

	/**
	 * set the status.
	 *
	 * @since  1.1.0
	 *
	 * @param string $status .
	 *
	 */
	public function set_status( $status ) {
		$statuses   = $this->get_statuses();
		$old_status = $this->get_status();

		if ( ! array_key_exists( $status, $statuses ) ) {
			return array(
				'from' => $old_status,
				'to'   => $old_status,
			);
		}

		$this->set_prop( 'status', eaccounting_clean( $status ) );

		if ( true === $this->object_read && $old_status !== $status ) {
			$this->status_transition = array(
				'from' => ! empty( $this->status_transition['from'] ) ? $this->status_transition['from'] : $old_status,
				'to'   => $status,
			);
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
	 * set the customer_id.
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
	 * set the address.
	 *
	 * @since  1.1.0
	 *
	 * @param int $address .
	 *
	 */
	public function set_address( $address ) {
		$this->set_prop( 'address', maybe_unserialize( $address ) );
	}

	/**
	 * Sets a prop for a setter method.
	 *
	 * @since 1.1.0
	 *
	 * @param string $prop  Name of prop to set.
	 * @param mixed  $value Value of the prop.
	 */
	protected function set_address_prop( $prop, $value ) {
		if ( array_key_exists( $prop, $this->data['address'] ) ) {
			if ( true === $this->object_read ) {
				if ( $value !== $this->data['address'][ $prop ] || ( isset( $this->changes['address'] ) && array_key_exists( $prop, $this->changes['address'] ) ) ) {
					$this->changes['address'][ $prop ] = $value;
				}
			} else {
				$this->data['address'][ $prop ] = $value;
			}
		}
	}

	/**
	 * Set name.
	 *
	 * @since 1.1.0
	 *
	 * @param string $name name.
	 */
	public function set_name( $name ) {
		$this->set_address_prop( 'name', eaccounting_clean( $name ) );
	}

	/**
	 * Set company.
	 *
	 * @since 1.1.0
	 *
	 * @param string $company company.
	 */
	public function set_company( $company ) {
		$this->set_address_prop( 'company', eaccounting_clean( $company ) );
	}

	/**
	 * Set street.
	 *
	 * @since 1.1.0
	 *
	 * @param string $street street.
	 */
	public function set_street( $street ) {
		$this->set_address_prop( 'street', eaccounting_clean( $street ) );
	}

	/**
	 * Set city.
	 *
	 * @since 1.1.0
	 *
	 * @param string $city city.
	 */
	public function set_city( $city ) {
		$this->set_address_prop( 'city', eaccounting_clean( $city ) );
	}

	/**
	 * Set state.
	 *
	 * @since 1.1.0
	 *
	 * @param string $state state.
	 */
	public function set_state( $state ) {
		$this->set_address_prop( 'state', eaccounting_clean( $state ) );
	}

	/**
	 * Set postcode.
	 *
	 * @since 1.1.0
	 *
	 * @param string $postcode postcode.
	 */
	public function set_postcode( $postcode ) {
		$this->set_address_prop( 'postcode', eaccounting_clean( $postcode ) );
	}

	/**
	 * Set country.
	 *
	 * @since 1.1.0
	 *
	 * @param string $country country.
	 */
	public function set_country( $country ) {
		$this->set_address_prop( 'country', eaccounting_clean( $country ) );
	}

	/**
	 * Set email.
	 *
	 * @since 1.1.0
	 *
	 * @param string $email email.
	 */
	public function set_email( $email ) {
		$this->set_address_prop( 'email', sanitize_email( $email ) );
	}

	/**
	 * Set phone.
	 *
	 * @since 1.1.0
	 *
	 * @param string $phone phone.
	 */
	public function set_phone( $phone ) {
		$this->set_address_prop( 'phone', eaccounting_clean( $phone ) );
	}

	/**
	 * Set vat_number.
	 *
	 * @since 1.1.0
	 *
	 * @param string $vat_number vat_number.
	 */
	public function set_vat_number( $vat_number ) {
		$this->set_address_prop( 'vat_number', eaccounting_clean( $vat_number ) );
	}

	/**
	 * set the discount.
	 *
	 * @since  1.1.0
	 *
	 * @param float $discount .
	 *
	 */
	public function set_discount( $discount ) {
		$this->set_prop( 'discount', abs( eaccounting_format_decimal( $discount, 2 ) ) );
	}

	/**
	 * set the discount type.
	 *
	 * @since  1.1.0
	 *
	 * @param float $discount_type .
	 *
	 */
	public function set_discount_type( $discount_type ) {
		if ( in_array( $discount_type, array( 'percentage', 'fixed' ), true ) ) {
			$this->set_prop( 'discount_type', $discount_type );
		}
	}

	/**
	 * set the subtotal.
	 *
	 * @since  1.1.0
	 *
	 * @param float $subtotal .
	 *
	 */
	public function set_subtotal( $subtotal ) {
		$this->set_prop( 'subtotal', eaccounting_format_decimal( $subtotal, 2 ) );
	}

	/**
	 * set the tax.
	 *
	 * @since  1.1.0
	 *
	 * @param float $tax .
	 *
	 */
	public function set_total_tax( $tax ) {
		$this->set_prop( 'total_tax', eaccounting_format_decimal( $tax, 2 ) );
	}

	/**
	 * set the tax.
	 *
	 * @since  1.1.0
	 *
	 * @param float $discount .
	 *
	 */
	public function set_total_discount( $discount ) {
		$this->set_prop( 'total_discount', eaccounting_format_decimal( $discount, 2 ) );
	}

	/**
	 * set the total.
	 *
	 * @since  1.1.0
	 *
	 * @param float $total .
	 *
	 */
	public function set_total( $total ) {
		$this->set_prop( 'total', eaccounting_format_decimal( $total, 2 ) );
	}

	/**
	 * set the note.
	 *
	 * @since  1.1.0
	 *
	 * @param string $note .
	 *
	 */
	public function set_tax_inclusive( $type ) {
		$this->set_prop( 'tax_inclusive', eaccounting_bool_to_number( $type ) );
	}

	/**
	 * set the note.
	 *
	 * @since  1.1.0
	 *
	 * @param string $note .
	 *
	 */
	public function set_terms( $terms ) {
		$this->set_prop( 'terms', eaccounting_sanitize_textarea( $terms ) );
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
		if ( eaccounting_sanitize_currency_code( $currency_code ) ) {
			$this->set_prop( 'currency_code', eaccounting_clean( $currency_code ) );
		}

		if ( $this->get_currency_code() && ( ! $this->exists() || array_key_exists( 'currency_code', $this->changes ) ) ) {
			$currency = eaccounting_get_currency( $this->get_currency_code() );
			$this->set_currency_rate( $currency->get_rate() );
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
		if ( ! empty( $currency_rate ) ) {
			$this->set_prop( 'currency_rate', eaccounting_format_decimal( $currency_rate, 4 ) );
		}
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
		$key = strtolower( eaccounting_clean( $value ) );
		$this->set_prop( 'key', substr( $key, 0, 30 ) );
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
	 * @since 1.1.0
	 *
	 * @param $status
	 *
	 * @return bool
	 */
	public function is_status( $status ) {
		return $this->get_status() === eaccounting_clean( $status );
	}

	/**
	 * Check if an invoice is editable.
	 *
	 * @since 1.1.0
	 *
	 * @return bool
	 */
	public function is_editable() {
		return ! in_array( $this->get_status(), array( 'paid' ), true );
	}

	/**
	 * Check if tax inclusive or not.
	 *
	 * @since 1.1.0
	 *
	 * @return mixed|null
	 */
	public function is_tax_inclusive() {
		return ! empty( $this->get_tax_inclusive() );
	}

	/**
	 * Get the type of discount.
	 *
	 * @since 1.1.0
	 *
	 * @return bool
	 */
	public function is_fixed_discount() {
		return 'percentage' !== $this->get_discount_type();
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	|
	| Used for database transactions.
	|
	*/

	/**
	 * Set the document items.
	 *
	 * @since 1.1.0
	 *
	 * @param array|DocumentItem[] $items items.
	 */
	public function set_items( $items, $append = false ) {
		// Remove existing items.
		$old_item_ids = $this->get_item_ids();

		// Ensure that we have an array.
		if ( ! is_array( $items ) ) {
			return;
		}
		$new_item_ids = array();
		foreach ( $items as $item ) {
			$new_item_ids[] = $this->add_item( $item );
		}

		if ( ! $append ) {
			$remove_item_ids = array_diff( $old_item_ids, $new_item_ids );
			foreach ( $remove_item_ids as $remove_item_id ) {
				$this->remove_item( $remove_item_id );
			}
		}
	}

	/**
	 * Adds an item to the document.
	 *
	 * @param array $item
	 *
	 * @return \WP_Error|Bool
	 */
	public abstract function add_item( $args );

	/**
	 * Remove item from the order.
	 *
	 * @param int  $item_id Item ID to delete.
	 *
	 * @param bool $by_line_id
	 *
	 * @return false|void
	 */
	public function remove_item( $item_id, $by_line_id = false ) {
		$item = $this->get_item( $item_id, $by_line_id );

		if ( ! $item ) {
			return false;
		}

		// Unset and remove later.
		$this->items_to_delete[ $item->get_item_id() ] = $item;
		unset( $this->items[ $item->get_item_id() ] );
	}

	/**
	 * Calculate total.
	 *
	 * @since 1.1.0
	 * @throws \Exception
	 */
	public function calculate_totals() {
		$subtotal       = 0;
		$total_tax      = 0;
		$total_discount = 0;
		$discount_rate  = $this->get_discount();

		// before calculating need to know subtotal so we can apply fixed discount
		if ( $this->is_fixed_discount() ) {
			$subtotal_discount = 0;
			foreach ( $this->get_items() as $item ) {
				$subtotal_discount += ( $item->get_price() * $item->get_quantity() );
			}
			$discount_rate = ( ( $this->get_discount() * 100 ) / $subtotal_discount );
		}

		foreach ( $this->get_items() as $item ) {
			$item_subtotal         = ( $item->get_price() * $item->get_quantity() );
			$item_discount         = $item_subtotal * ( $discount_rate / 100 );
			$item_subtotal_for_tax = $item_subtotal - $item_discount;
			$item_tax_rate         = ( $item->get_tax_rate() / 100 );
			$item_tax              = eaccounting_calculate_tax( $item_subtotal_for_tax, $item_tax_rate, $this->is_tax_inclusive() );
			if ( 'tax_subtotal_rounding' !== eaccounting()->settings->get( 'tax_subtotal_rounding', 'tax_subtotal_rounding' ) ) {
				$item_tax = eaccounting_format_decimal( $item_tax, 2 );
			}
			if ( $this->is_tax_inclusive() ) {
				$item_subtotal -= $item_tax;
			}
			$item_total = $item_subtotal - $item_discount + $item_tax;
			if ( $item_total < 0 ) {
				$item_total = 0;
			}

			$item->set_subtotal( $item_subtotal );
			$item->set_discount( $item_discount );
			$item->set_tax( $item_tax );
			$item->set_total( $item_total );

			$subtotal       += $item->get_subtotal();
			$total_tax      += $item->get_tax();
			$total_discount += $item->get_discount();
		}

		$this->set_subtotal( $subtotal );
		$this->set_total_tax( $total_tax );
		$this->set_total_discount( $total_discount );
		$total = $this->get_subtotal() - $this->get_total_discount() + $this->get_total_tax();
		if ( $total < 0 ) {
			$total = 0;
		}
		$this->set_total( $total );

		if ( ( 0 < $this->get_total_paid() ) && ( $this->get_total_paid() < $this->get_total() ) ) {
			$this->set_status( 'partial' );
		} elseif ( $this->get_total_paid() >= $this->get_total() ) { // phpcs:ignore
			$this->set_status( 'paid' );
		}

		return array(
			'subtotal'       => $this->get_subtotal(),
			'total_tax'      => $this->get_total_tax(),
			'total_discount' => $this->get_total_discount(),
			'total'          => $this->get_total(),
		);
	}

	/**
	 * Generate number.
	 *
	 * @since 1.1.0
	 *
	 * @param $number
	 *
	 * @return string
	 */
	public function generate_number( $number ) {
		$prefix           = 'DOC-';
		$padd             = 5;
		$formatted_number = zeroise( absint( $number ), $padd );
		$number           = apply_filters( 'eaccounting_generate_' . sanitize_key( $this->get_type() ) . '_number', $prefix . $formatted_number );

		return $number;
	}

	/**
	 * Generate key.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	public function generate_key() {
		$key = 'ea_' . apply_filters( 'eaccounting_generate_' . sanitize_key( $this->get_type() ) . '_key', 'document_' . wp_generate_password( 19, false ) );

		return strtolower( $key );
	}

	/**
	 * Save all document items which are part of this order.
	 */
	protected function save_items() {
		foreach ( $this->items_to_delete as $item ) {
			if ( $item->exists() ) {
				$item->delete();
			}
		}

		$this->items_to_delete = array();

		$items = array_filter( $this->items );
		// Add/save items.
		foreach ( $items as $item ) {
			$item->set_document_id( $this->get_id() );
			$item->set_currency_code( $this->get_currency_code() );
			$item->save();
		}
	}

}
