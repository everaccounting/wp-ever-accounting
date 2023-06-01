<?php

namespace EverAccounting\Models;

defined( 'ABSPATH' ) || exit;

/**
 * Class Invoice.
 *
 * @since   1.0.0
 * @package EverAccounting\Models
 */
class Invoice extends Document {

	/**
	 * Object type.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const OBJECT_TYPE = 'invoice';

	/**
	 * Invoice constructor.
	 *
	 * @param int|object|array $data Object ID, post object, or array of data.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $data = 0 ) {
		$due_after                        = get_option( 'eac_invoice_due_date', 7 );
		$notes                            = get_option( 'eac_invoice_notes', '' );
		$due_date                         = wp_date( 'Y-m-d', strtotime( '+' . $due_after . ' days' ) );
		$this->core_data['type']          = static::OBJECT_TYPE;
		$this->core_data['issued_at']     = wp_date( 'Y-m-d' );
		$this->core_data['due_at']        = $due_date;
		$this->core_data['document_note'] = $notes;
		parent::__construct( $data );

		// after reading check if the contact is a customer.
		if ( $this->exists() && static::OBJECT_TYPE !== $this->get_type() ) {
			$this->set_id( 0 );
			$this->set_defaults();
		}
	}
}
