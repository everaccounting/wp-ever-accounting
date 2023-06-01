<?php


/**
 * Inherited Methods
 *
 * @method void wantToTest( $text )
 * @method void wantTo( $text )
 * @method void execute( $callable )
 * @method void expectTo( $prediction )
 * @method void expect( $prediction )
 * @method void amGoingTo( $argumentation )
 * @method void am( $role )
 * @method void lookForwardTo( $achieveValue )
 * @method void comment( $description )
 * @method void pause()
 *
 * @SuppressWarnings(PHPMD)
 */
class WpunitTester extends \Codeception\Actor {
	use _generated\WpunitTesterActions;


	// Create category factory helper.
	public function create_category( $data = array(), $create = true ) {
		$type_id = wp_rand( 0, 2 );
		$types   = eac_get_category_types();
		$type    = isset( $types[ $type_id ] ) ? $types[ $type_id ] : current( $types );

		$default = array(
			'name'        => 'Test Category' . wp_rand( 1, 100 ),
			'type'        => $type,
			'description' => 'Test Category Description' . wp_rand( 1, 100 ),
			'color'       => '#000000',
			'status'      => wp_rand( 0, 1 ) ? 'active' : 'inactive',
			'updated_at'  => null,
			'created_at'  => null,
		);

		$data = wp_parse_args( $data, $default );

		if ( $create ) {
			return eac_insert_category( $data );
		}

		return $data;
	}

	// Create many categories factory helper.
	public function create_categories( $count = 1, $data = array(), $create = true ) {
		$categories = array();
		for ( $i = 0; $i < $count; $i ++ ) {
			$categories[] = $this->create_category( $data, $create );
		}

		return $categories;
	}

	// Create product factory helper.
	public function create_product( $data = array(), $create = true ) {
		// random tax_ids for product.
		$tax_ids = array();
		$taxes   = eac_get_taxes();
		if ( empty( $taxes ) ) {
			$this->create_taxes( 5 );
			$taxes = eac_get_taxes();
		}
		$count = wp_rand( 0, 5 );
		for ( $i = 0; $i < $count; $i ++ ) {
			$tax_ids[] = $taxes[ wp_rand( 0, count( $taxes ) - 1 ) ]->get_id();
		}
		$tax_ids   = array_unique( $tax_ids );
		$tax_ids   = implode( ',', $tax_ids );

		// random category_id for product.
		$categories = eac_get_categories();
		//If empty category then create one.
		if ( empty( $categories ) ) {
			$this->create_categories( 1, array( 'type' => 'product' ) );
			$categories = eac_get_categories();
		}
		$category_id = $categories[ wp_rand( 0, count( $categories ) - 1 ) ]->get_id();

		$default = array(
			'name'        => 'Test Product' . wp_rand( 1, 100 ),
			'price'       => wp_rand( 1, 100 ),
			'unit'        => 'box',
			'description' => 'Test Product Description' . wp_rand( 1, 100 ),
			'category_id' => ! empty( $category_id ) ? $category_id : 0,
			'taxable'     => wp_rand( 0, 1 ) ? 'yes' : 'no',
			'tax_ids'     => $tax_ids,
			'status'      => wp_rand( 0, 1 ) ? 'active' : 'inactive',
			'updated_at'  => null,
			'created_at'  => null,
		);

		$data = wp_parse_args( $data, $default );

		if ( $create ) {
			return eac_insert_product( $data );
		}

		return $data;
	}

	// Create many products factory helper.
	public function create_products( $count = 10, $data = array(), $create = true ) {
		$products = array();
		for ( $i = 0; $i < $count; $i ++ ) {
			$products[] = $this->create_product( $data, $create );
		}

		return $products;
	}

	// Create customer factory helper.
	public function create_customer( $data = array(), $create = true ) {
		$countries = eac_get_countries();
		$country  = $countries[ wp_rand( 0, count( $countries ) - 1 ) ];
		$currencies = eac_get_currencies();
		$currency  = $currencies[ wp_rand( 0, count( $currencies ) - 1 ) ];

		$default = array(
			'name'          => 'Test Customer' . wp_rand( 1, 100 ),
			'company'       => 'Test Company' . wp_rand( 1, 100 ),
			'email'         => 'test' . wp_rand( 1, 100 ) . '@gmail.com',
			'phone'         => '1234567890',
			'address_1'     => 'Test Address 1' . wp_rand( 1, 100 ),
			'address_2'     => 'Test Address 2' . wp_rand( 1, 100 ),
			'city'          => 'Test City' . wp_rand( 1, 100 ),
			'state'         => 'Test State' . wp_rand( 1, 100 ),
			'postcode'      => '123456',
			'country'       => $country,
			'website'       => 'http://test' . wp_rand( 1, 100 ) . '.com',
			'vat_number'    => '1234567890',
			'currency_code' => $currency,
			'type'          => 'customer',
			'status'        => wp_rand( 0, 1 ) ? 'active' : 'inactive',
			'thumbnail_id'  => null,
			'creator_id'    => null,
			'updated_at'    => null,
			'created_at'    => null,
		);

		$data = wp_parse_args( $data, $default );

		if ( $create ) {
			return eac_insert_customer( $data );
		}

		return $data;
	}

	// Create many customers factory helper.
	public function create_customers( $count = 10, $data = array(), $create = true ) {
		$customers = array();
		for ( $i = 0; $i < $count; $i ++ ) {
			$customers[] = $this->create_customer( $data, $create );
		}

		return $customers;
	}

	// Create vendor factory helper.
	public function create_vendor( $data = array(), $create = true ) {
		$countries = eac_get_countries();
		$country  = $countries[ wp_rand( 0, count( $countries ) - 1 ) ];
		$currencies = eac_get_currencies();
		$currency  = $currencies[ wp_rand( 0, count( $currencies ) - 1 ) ];

		$default = array(
			'name'          => 'Test Vendor' . wp_rand( 1, 100 ),
			'company'       => 'Test Company' . wp_rand( 1, 100 ),
			'email'         => 'test' . wp_rand( 1, 100 ) . '@gmail.com',
			'phone'         => '1234567890',
			'address_1'     => 'Test Address 1' . wp_rand( 1, 100 ),
			'address_2'     => 'Test Address 2' . wp_rand( 1, 100 ),
			'city'          => 'Test City' . wp_rand( 1, 100 ),
			'state'         => 'Test State' . wp_rand( 1, 100 ),
			'postcode'      => '123456',
			'country'       => $country,
			'website'       => 'http://test' . wp_rand( 1, 100 ) . '.com',
			'vat_number'    => '1234567890',
			'currency_code' => $currency,
			'type'          => 'vendor',
			'status'        => wp_rand( 0, 1 ) ? 'active' : 'inactive',
			'thumbnail_id'  => null,
			'creator_id'    => null,
			'updated_at'    => null,
			'created_at'    => null,
		);

		$data = wp_parse_args( $data, $default );

		if ( $create ) {
			return eac_insert_vendor( $data );
		}

		return $data;
	}

	// Create many vendors factory helper.
	public function create_vendors( $count = 10, $data = array(), $create = true ) {
		$vendors = array();
		for ( $i = 0; $i < $count; $i ++ ) {
			$vendors[] = $this->create_vendor( $data, $create );
		}

		return $vendors;
	}

	// Create account factory helper.
	public function create_account( $data = array(), $create = true ) {
		$currencies = eac_get_currencies();
		$currency  = $currencies[ wp_rand( 0, count( $currencies ) - 1 ) ];

		$default = array(
			'name'            => 'Test Account' . wp_rand( 1, 100 ),
			'type'            => 'bank',
			'number'          => '1234567890',
			'opening_balance' => 0.0000,
			'bank_name'       => 'Test Bank Name' . wp_rand( 1, 100 ),
			'bank_phone'      => '1234567890',
			'bank_address'    => 'Test Bank Address' . wp_rand( 1, 100 ),
			'status'          => wp_rand( 0, 1 ) ? 'active' : 'inactive',
			'currency_code'   => $currency,
			'creator_id'      => null,
			'updated_at'      => null,
			'created_at'      => null,
		);

		$data = wp_parse_args( $data, $default );

		if ( $create ) {
			return eac_insert_account( $data );
		}

		return $data;
	}

	// Create many accounts factory helper.
	public function create_accounts( $count = 10, $data = array(), $create = true ) {
		$accounts = array();
		for ( $i = 0; $i < $count; $i ++ ) {
			$accounts[] = $this->create_account( $data, $create );
		}

		return $accounts;
	}

	// Create payment factory helper.
	public function create_payment( $data = array(), $create = true ) {
		// get one of the accounts' currency.
		$accounts = eac_get_accounts();
		if ( empty( $accounts ) ) {
			$this->create_accounts( wp_rand(1, 5) );
			$accounts = eac_get_accounts();
		}
		$account = $accounts[ wp_rand( 0, count( $accounts ) - 1 ) ];
		// random previous transaction date.
		$payment_date = date( 'Y-m-d', strtotime( '-' . wp_rand( 1, 100 ) . ' days' ) );

		// category.
		$categories = eac_get_categories();
		if ( empty( $categories ) ) {
			$this->create_categories( 1, array( 'type' => 'payment' ) );
			$categories = eac_get_categories();
		}
		$category = $categories[ wp_rand( 0, count( $categories ) - 1 ) ];

		// customer.
		$contacts = eac_get_customers();
		if ( empty( $contacts ) ) {
			$this->create_customers( 1 );
			$contacts = eac_get_customers();
		}
		$contact = $contacts[ wp_rand( 0, count( $contacts ) - 1 ) ];
		//payment method.
		$payment_methods = eac_get_payment_methods();
		$payment_method = $payment_methods[ wp_rand( 0, count( $payment_methods ) - 1 ) ];


		$default = array(
			'type'           => 'payment',
			'voucher_number' => '',
			'payment_date'   => $payment_date,
			'amount'         => wp_rand( 1, 1000 ),
			'currency_rate'  => wp_rand( 0.1, 50 ),
			'currency_code'  => $account->get_currency_code(),
			'account_id'     => $account->get_id(),
			'document_id'    => null, // ID of the document associated with the transaction.
			'contact_id'     => $contact->get_id(),
			'category_id'    => $category->get_id(),
			'note'           => 'Lorem note'. wp_rand( 1, 100 ),
			'payment_method' => $payment_method,
			'reference'      => 'Lorem reference'. wp_rand( 1, 100 ),
			'attachment_id'  => null, // ID of any attachments associated with the transaction.
			'parent_id'      => 0,    // ID of the parent transaction, if any.
			'reconciled'     => 0,    // whether the transaction has been reconciled.
			'unique_hash'    => '',   // token used for payment processing, if any.
			'creator_id'     => null, // ID of the user who created the transaction.
			'updated_at'     => null, // date and time the transaction was last updated.
			'created_at'     => null, // date and time the transaction was created.
		);

		$data = wp_parse_args( $data, $default );

		if ( $create ) {
			return eac_insert_payment( $data );
		}

		return $data;
	}

	// Create many payments factory helper.
	public function create_payments( $count = 10, $data = array(), $create = true ) {
		$payments = array();
		for ( $i = 0; $i < $count; $i ++ ) {
			$payments[] = $this->create_payment( $data, $create );
		}

		return $payments;
	}

	// Create expense factory helper.
	public function create_expense( $data = array(), $create = true ) {
		// get one of the accounts' currency.
		$accounts = eac_get_accounts();
		if ( empty( $accounts ) ) {
			$this->create_accounts( wp_rand( 1, 5 ) );
			$accounts = eac_get_accounts();
		}
		$account = $accounts[ wp_rand( 0, count( $accounts ) - 1 ) ];
		// random previous transaction date.
		$expense_date = date( 'Y-m-d', strtotime( '-' . wp_rand( 1, 100 ) . ' days' ) );

		// category.
		$categories = eac_get_categories();
		if ( empty( $categories ) ) {
			$this->create_categories( 1, array( 'type' => 'expense' ) );
			$categories = eac_get_categories();
		}
		$category = $categories[ wp_rand( 0, count( $categories ) - 1 ) ];

		// vendor.
		$contacts = eac_get_vendors();
		if ( empty( $contacts ) ) {
			$this->create_vendors( 1 );
			$contacts = eac_get_vendors();
		}
		$contact = $contacts[ wp_rand( 0, count( $contacts ) - 1 ) ];
		//payment method.
		$payment_methods = eac_get_payment_methods();
		$payment_method  = $payment_methods[ wp_rand( 0, count( $payment_methods ) - 1 ) ];


		$default = array(
			'type'           => 'expense',
			'voucher_number' => '',
			'expense_date'   => $expense_date,
			'amount'         => wp_rand( 1, 1000 ),
			'currency_rate'  => wp_rand( 0.1, 50 ),
			'currency_code'  => $account->get_currency_code(),
			'account_id'     => $account->get_id(),
			'document_id'    => null, // ID of the document associated with the transaction.
			'contact_id'     => $contact->get_id(),
			'category_id'    => $category->get_id(),
			'note'           => 'Lorem note' . wp_rand( 1, 100 ),
			'payment_method' => $payment_method,
			'reference'      => 'Lorem reference' . wp_rand( 1, 100 ),
			'attachment_id'  => null, // ID of any attachments associated with the transaction.
			'parent_id'      => 0,    // ID of the parent transaction, if any.
			'reconciled'     => 0,    // whether the transaction has been reconciled.
			'unique_hash'    => '',   // token used for payment processing, if any.
			'creator_id'     => null, // ID of the user who created the transaction.
			'updated_at'     => null, // date and time the transaction was last updated.
			'created_at'     => null, // date and time the transaction was created.
		);

		$data = wp_parse_args( $data, $default );

		if ( $create ) {
			return eac_insert_expense( $data );
		}

		return $data;
	}

	// Create many expenses factory helper.
	public function create_expenses( $count = 10, $data = array(), $create = true ) {
		$expenses = array();
		for ( $i = 0; $i < $count; $i ++ ) {
			$expenses[] = $this->create_expense( $data, $create );
		}

		return $expenses;
	}

	// Create transfer factory helper.
	public function create_transfer( $data = array(), $create = true ) {
		// get one of the accounts' currency.
		$accounts = eac_get_accounts();
		if ( empty( $accounts ) ) {
			$this->create_accounts( wp_rand( 1, 5 ) );
			$accounts = eac_get_accounts();
		}
		$account1 = $accounts[ wp_rand( 0, count( $accounts ) - 1 ) ];
		$account2 = $accounts[ wp_rand( 0, count( $accounts ) - 1 ) ];
		$amount   = wp_rand( 1, 1000 );
		// random previous transaction date.
		$transfer_date = date( 'Y-m-d', strtotime( '-' . wp_rand( 1, 100 ) . ' days' ) );
		//payment method.
		$payment_methods = eac_get_payment_methods();
		$payment_method  = $payment_methods[ wp_rand( 0, count( $payment_methods ) - 1 ) ];


		$default = array(
			'date'            => $transfer_date,
			'from_account_id' => $account1->get_id(),
			'amount'          => $amount,
			'to_account_id'   => $account2->get_id(),
			'payment_method'  => $payment_method,
			'reference'       => 'Lorem reference' . wp_rand( 1, 100 ),
			'note'            => 'Lorem note' . wp_rand( 1, 100 ),
			'creator_id'      => null,
			'updated_at'      => null,
			'created_at'      => null,
		);

		$data = wp_parse_args( $data, $default );

		if ( $create ) {
			return eac_insert_transfer( $data );
		}

		return $data;

	}

	// Create many transfers factory helper.
	public function create_transfers( $count = 10, $data = array(), $create = true ) {
		$transfers = array();
		for ( $i = 0; $i < $count; $i ++ ) {
			$transfers[] = $this->create_transfer( $data, $create );
		}

		return $transfers;
	}


	// Create tax factory helper.
	public function create_tax( $data = array(), $create = true ) {
		$default = array(
			'name'        => 'Test Tax' . wp_rand( 1, 100 ),
			'rate'        => wp_rand( 1, 100 ),
			'is_compound' => wp_rand( 0, 1 ) ? 'yes' : 'no',
			'description' => 'Test Tax Description' . wp_rand( 1, 100 ),
			'status'      => wp_rand( 0, 1 ) ? 'active' : 'inactive',
			'updated_at'  => null,
			'created_at'  => null,
		);

		$data = wp_parse_args( $data, $default );

		if ( $create ) {
			return eac_insert_tax( $data );
		}

		return $data;
	}

	// Create many taxes factory helper.
	public function create_taxes( $count = 1, $data = array(), $create = true ) {
		$taxes = array();
		for ( $i = 0; $i < $count; $i ++ ) {
			$taxes[] = $this->create_tax( $data, $create );
		}

		return $taxes;
	}

	// Create document tax factory helper.
	public function create_document_tax( $data = array(), $create = true ) {
		$taxes = eac_get_taxes();
		if ( empty( $taxes ) ) {
			$this->create_taxes( wp_rand( 1, 5 ) );
			$taxes = eac_get_taxes();
		}
		$tax = $taxes[ wp_rand( 0, count( $taxes ) - 1 ) ];
		$default = array(
			'id'            => null,
			'item_id'       => null,
			'tax_id'        => $tax->get_id(),
			'document_id'   => null,
			'name'          => $tax->get_name(),
			'rate'          => $tax->get_rate(),
			'is_compound'   => $tax->is_compound(),
			'subtotal'      => 0.00,
			'discount'      => 0.00,
			'shipping'      => 0.00,
			'fee'           => 0.00,
			'total'         => 0.00,
		);

		$data = wp_parse_args( $data, $default );

		if ( $create ) {
			$doc_tax = new \EverAccounting\Models\DocumentTax($data['id']);
			$doc_tax->set_props($data);
			$error =  $doc_tax->save();
			if ( is_wp_error( $error ) ) {
				return $error;
			}

			return $doc_tax;
		}

		return $data;
	}

	// Create many document taxes factory helper.
	public function create_document_taxes( $count = 10, $data = array(), $create = true ) {
		$document_taxes = array();
		for ( $i = 0; $i < $count; $i ++ ) {
			$document_taxes[] = $this->create_document_tax( $data, $create );
		}

		return $document_taxes;
	}

	// Create document item factory helper.
	public function create_document_item( $data = array(), $create = true ) {
		$items = eac_get_products();
		if ( empty( $items ) ) {
			$this->create_products( wp_rand( 1, 5 ) );
			$items = eac_get_products();
		}
		$item = $items[ wp_rand( 0, count( $items ) - 1 ) ];
		$default = array(
			'id'            => null,
			'item_id'       => $item->get_id(),
			'document_id'   => null,
			'name'          => $item->get_name(),
			'description'   => $item->get_description(),
			'quantity'      => wp_rand( 1, 10 ),
			'price'         => $item->get_price(),
			'tax'           => 0.00,
			'subtotal'      => 0.00,
			'discount'      => 0.00,
			'shipping'      => 0.00,
			'fee'           => 0.00,
			'total'         => 0.00,
		);

		$data = wp_parse_args( $data, $default );

		if ( $create ) {
			$doc_item = new \EverAccounting\Models\DocumentItem($data['id']);
			$doc_item->set_props($data);
			$doc_item->save();
			return $doc_item;
		}

		return $data;
	}

	// Create many document items factory helper.
	public function create_document_items( $count = 10, $data = array(), $create = true ) {
		$document_items = array();
		for ( $i = 0; $i < $count; $i ++ ) {
			$document_items[] = $this->create_document_item( $data, $create );
		}

		return $document_items;
	}
}
