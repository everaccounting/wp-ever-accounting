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
use EverAccounting\Abstracts\ResourceRepository;


abstract class Document extends ResourceModel {
	/**
	 * Contains a reference to the repository for this class.
	 *
	 * @since 1.1.0
	 *
	 * @var ResourceRepository
	 */
	protected $repository;

	/**
	 * Order items will be stored here, sometimes before they persist in the DB.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $line_items = array();

	/**
	 * Order items that need deleting are stored here.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $line_items_to_delete = array();

	/**
	 * @since 1.1.0
	 *
	 * @var array
	 */
	private $status_transition = array();

	/**
	 * All available document statuses.
	 *
	 * @since 1.0.1
	 *
	 * @return array
	 */
	abstract public function get_statuses();

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
	 * Get the invoice discount total.
	 *
	 * @since 1.1.0
	 *
	 * @param string $context View or edit context.
	 *
	 * @return float
	 */
	public function get_discount( $context = 'view' ) {
		return (float) $this->get_prop( 'discount', $context );
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
	 * @since 1.1.0
	 *
	 * @param string $context
	 *
	 * @return array|mixed|string
	 */
	public function get_extra( $context = 'view' ) {
		return $this->get_prop( 'extra' );
	}

	/**
	 * Get the invoice items.
	 *
	 * @since 1.1.0
	 *
	 *
	 * @return LineItem[]
	 */
	public function get_line_items() {
		if ( $this->exists() && empty( $this->line_items ) ) {
			$line_items = $this->repository->get_line_items( $this );
			foreach ( $line_items as $item_id => $line_item ) {
				if ( ! array_key_exists( $item_id, $this->line_items_to_delete ) ) {
					$this->line_items[ $item_id ] = $line_item;
				}
			}
		}

		return $this->line_items;
	}

	/**
	 * Get item ids.
	 *
	 * @since 1.1.0
	 *
	 * @return array
	 */
	public function get_line_item_ids() {
		$ids = array();
		foreach ( $this->get_line_items() as $item ) {
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
		if ( empty( $this->get_line_items() ) ) {
			foreach ( $this->get_line_items() as $item ) {
				$taxes[ $item->get_item_id() ] = $item->get_total_tax();
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
	 * @return LineItem|int
	 */
	public function get_line_item( $item_id, $by_line_id = false ) {
		$items = $this->get_line_items();

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
		if ( array_key_exists( $status, $statuses ) ) {
			$this->set_prop( 'status', eaccounting_clean( $status ) );
		}
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
	 * @param int $customer_id .
	 *
	 */
	public function set_customer_id( $customer_id ) {
		$this->set_prop( 'customer_id', absint( $customer_id ) );
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
	 * set the discount.
	 *
	 * @since  1.1.0
	 *
	 * @param float $discount .
	 *
	 */
	public function set_discount( $discount ) {
		$this->set_prop( 'discount', eaccounting_format_decimal( $discount, 2 ) );
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
	abstract public function add_line_item( $args );


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
		$line_item = $this->get_line_item( $item_id, $by_line_id );

		if ( ! $line_item ) {
			return false;
		}

		// Unset and remove later.
		$this->line_items_to_delete[ $line_item->get_item_id() ] = $line_item;
		unset( $this->line_items[ $line_item->get_item_id() ] );
	}


	/**
	 * Add note.
	 *
	 * @since 1.1.0
	 *
	 * @param       $note
	 * @param false $customer_note
	 *
	 * @return Note|false|int|\WP_Error
	 */
	public function add_note( $note, $customer_note = false ) {
		if ( ! $this->exists() ) {
			return false;
		}
		if ( $customer_note ) {
			do_action( 'eaccounting_invoice_customer_note', $note, $this );
		}

		$author = 'Ever Accounting';
		// If this is an admin comment or it has been added by the user.
		if ( is_user_logged_in() ) {
			$user   = get_user_by( 'id', get_current_user_id() );
			$author = $user->display_name;
		}

		return eaccounting_insert_note(
			array(
				'parent_id' => $this->get_id(),
				'type'      => 'invoice',
				'note'      => $note,
				'highlight' => $customer_note,
				'author'    => $author,
			)
		);
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
		return ! in_array( $this->get_status(), array( 'partial', 'paid' ), true );
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
}
