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
			'discount'        => 0.00,
			'discount_type'   => 'percentage',
			'subtotal'        => 0.00,
			'total_tax'       => 0.00,
			'total_discount'  => 0.00,
			'total_fees'      => 0.00,
			'total_shipping'  => 0.00,
			'total'           => 0.00,
			'tax_inclusive'   => 1,
			'note'            => '',
			'terms'           => '',
			'attachment_id'   => null,
			'currency_code'   => null,
			'currency_rate'   => 0.00,
			'key'             => null,
			'parent_id'       => null,
			'date_created'    => date( 'Y-m-d' )
		);
		if ( is_null( $default['contact_id'] ) ) {
			if ( 'invoice' == $default['type'] ) {
				$contact               = Customer_Helper::create_customer();
				$default['contact_id'] = $contact->get_id();
			}
			if ( 'bill' == $default['type'] ) {
				$contact               = Vendor_Helper::create_vendor();
				$default['contact_id'] = $contact->get_id();
			}
			$default['address']['name']       = $contact->get_name();
			$default['address']['company']    = $contact->get_company();
			$default['address']['street']     = $contact->get_street();
			$default['address']['city']       = $contact->get_city();
			$default['address']['state']      = $contact->get_state();
			$default['address']['postcode']   = $contact->get_postcode();
			$default['address']['country']    = $contact->get_country();
			$default['address']['email']      = $contact->get_email();
			$default['address']['phone']      = $contact->get_phone();
			$default['address']['vat_number'] = $contact->get_vat_number();
		}

		if ( is_null( $default['currency_code'] ) ) {
			$currency                 = Currency_Helper::create_currency();
			$default['currency_code'] = $currency->get_code();
			$default['currency_rate'] = $currency->get_rate();
		}

		$props = array_merge( $default, $props );

		if ( $save ) {
			if ( 'invoice' == $default['type'] ) {
				return eaccounting_insert_invoice( $props, false );
			}
			if ( 'bill' == $default['type'] ) {
				return eaccounting_insert_bill( $props, false );
			}
		}

		return $props;

	}
}
