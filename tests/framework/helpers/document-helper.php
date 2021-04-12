<?php

namespace EverAccounting\Tests\Framework\Helpers;


class Document_Helper {

	public static function create_document( $save = true, $props = array() ) {
		$date     = date( "Y-m-d 00:00:00" );// current date
		$due_date = strtotime( date( "Y-m-d 00:00:00", strtotime( $date ) ) . " +2 week" );

		$default = array(
			'document_number' => '',
			'type'            => 'invoice',
			'order_number'    => '',
			'status'          => 'draft',
			'issue_date'      => $date,
			'due_date'        => date( 'Y-m-d 00:00:00', $due_date ),
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
			'discount'        => '0.00',
			'discount_type'   => 'percentage',
			'subtotal'        => '0.00',
			'total_tax'       => '0.00',
			'total_discount'  => '0.00',
			'total_fees'      => '0.00',
			'total_shipping'  => '0.00',
			'total'           => '0.00',
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

		$props = array_merge( $default, $props );

		if ( is_null( $props['contact_id'] ) ) {
			if ( 'invoice' == $props['type'] ) {
				$contact               = Customer_Helper::create_customer();
				$props['contact_id'] = $contact->get_id();
			}
			else if ( 'bill' == $props['type'] ) {
				$contact               = Vendor_Helper::create_vendor();
				$props['contact_id'] = $contact->get_id();
			}

			$props['address']['name']       = $contact->get_name();
			$props['address']['company']    = $contact->get_company();
			$props['address']['street']     = $contact->get_street();
			$props['address']['city']       = $contact->get_city();
			$props['address']['state']      = $contact->get_state();
			$props['address']['postcode']   = $contact->get_postcode();
			$props['address']['country']    = $contact->get_country();
			$props['address']['email']      = $contact->get_email();
			$props['address']['phone']      = $contact->get_phone();
			$props['address']['vat_number'] = $contact->get_vat_number();
		}


		if ( is_null( $props['currency_code'] ) ) {
			$currency                 = Currency_Helper::create_currency();
			$props['currency_code'] = $currency->get_code();
			$props['currency_rate'] = $currency->get_rate();
		}

		if ( is_null( $props['category_id'] ) ) {
			if('invoice' == $props['type']){
				$category               = Category_Helper::create_category( true, array( 'name' => 'inv-category', 'type' => 'income' ) );
				$props['category_id'] = $category->get_id();
			}

			else if('bill' == $props['type']){
				$category               = Category_Helper::create_category( true, array( 'name' => 'bill-category', 'type' => 'expense' ) );
				$props['category_id'] = $category->get_id();

			}
		}

		if ( $save ) {
			if ( 'invoice' == $props['type'] ) {
				return eaccounting_insert_invoice( $props, false );
			}
			else if ( 'bill' == $props['type'] ) {
				return eaccounting_insert_bill( $props, false );
			}
		}

		return $props;

	}
}
