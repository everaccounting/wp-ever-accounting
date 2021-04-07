<?php

namespace EverAccounting\Tests\Framework\Helpers;

class Document_Helper {

	public static function create_document( $save = true, $props = array() ) {
		$default = array(
			'document_number' => '',
			'type'            => 'invoice',
			'order_number'    => '',
			'status'          => 'draft',
			'issue_date'      => date( 'Y-m-d' ),
			'due_date'        => strtotime( '+15 days', date( 'Y-m-d' ) ),
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

		);

	}
}
