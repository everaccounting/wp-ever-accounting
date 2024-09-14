<?php

namespace EverAccounting\Models;

use ByteKit\Models\Relations\HasMany;

/**
 * Invoice model.
 *
 * @since 1.0.0
 * @package EverAccounting
 * @subpackage Models
 * @extends Document
 *
 * @author  Sultan Nasir Uddin <manikdrmc@gmail.com>
 *
 * @property int            $id Invoice ID.
 * @property DocumentItem[] $lines Invoice lines.
 */
class Invoice extends Document {
	/**
	 * The type of the object. Used for actions and filters. e.g. post, user, etc.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $object_type = 'invoice';


	/**
	 * Default query variables passed to Query class when parsing.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $query_vars = array(
		'type' => 'invoice',
	);

	/**
	 * Create a new model instance.
	 *
	 * @param string|array|object $props The model attributes.
	 */
	public function __construct( $props = array() ) {
		$due_after        = get_option( 'eac_invoice_due_date', 7 );
		$_attributes      = array(
			'type'          => $this->get_object_type(),
			'issue_date'    => current_time( 'mysql' ),
			'due_date'      => wp_date( 'Y-m-d', strtotime( '+' . $due_after . ' days' ) ),
			'notes'         => get_option( 'eac_invoice_notes', '' ),
			'currency_code' => eac_get_base_currency(),
			'creator_id'    => get_current_user_id(),
			'uuid'          => wp_generate_uuid4(),
		);
		$this->attributes = array_merge( $this->attributes, $_attributes );
		parent::__construct( $props );
	}

	/*
	|--------------------------------------------------------------------------
	| Helper Methods
	|--------------------------------------------------------------------------
	| This section contains utility methods that are not directly related to this
	| object but can be used to support its functionality.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Set next available number.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_next_number() {
		$max    = $this->get_max_number();
		$prefix = get_option( 'eac_invoice_prefix', strtoupper( substr( $this->get_object_type(), 0, 3 ) ) . '-' );
		$number = str_pad( $max + 1, get_option( 'eac_invoice_digits', 4 ), '0', STR_PAD_LEFT );

		return $prefix . $number;
	}
}
