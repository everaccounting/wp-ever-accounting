<?php

namespace EverAccounting\Admin\Settings;

defined( 'ABSPATH' ) || exit;

/**
 * Class Sales.
 *
 * @since   1.0.0
 * @package EverAccounting\Admin\Settings
 */
class Purchases extends Page {
	/**
	 * GeneralSettingsPage constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->id    = 'purchases';
		$this->label = __( 'Purchases', 'wp-ever-accounting' );

		parent::__construct();
	}

	/**
	 * Get own sections for this page.
	 * Derived classes should override this method if they define sections.
	 * There should always be one default section with an empty string as identifier.
	 *
	 * Example:
	 * return array(
	 *   ''        => __( 'General', 'wp-ever-accounting' ),
	 *   'foobars' => __( 'Foos & Bars', 'wp-ever-accounting' ),
	 * );
	 *
	 * @return array An associative array where keys are section identifiers and the values are translated section names.
	 */
	protected function get_own_sections() {
		$sections = array(
			''      => __( 'Options', 'wp-ever-accounting' ),
			'bills' => __( 'Bills', 'wp-ever-accounting' ),
		);

		return $sections;
	}


	/**
	 * Get settings or the default section.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	protected function get_settings_for_default_section() {
		$expense_accounts   = eac_get_accounts( array( 'include' => get_option( 'eac_default_expenses_account_id' ) ) );
		$expense_categories = eac_get_categories( array( 'include' => get_option( 'eac_default_expense_category_id' ) ) );

		return array(
			// Purchase defaults section.
			array(
				'title' => __( 'Default Settings', 'wp-ever-accounting' ),
				'type'  => 'title',
				'desc'  => __( 'Default values will be automatically filled in when you create a new purchase related record.', 'wp-ever-accounting' ),
				'id'    => 'purchase_defaults',
			),
			// Default payment account.
			array(
				'title'       => __( 'Purchases Account', 'wp-ever-accounting' ),
				'desc'        => __( 'The default account to which the expense will be debited.', 'wp-ever-accounting' ),
				'id'          => 'eac_default_expenses_account_id',
				'type'        => 'select',
				'options'     => wp_list_pluck( $expense_accounts, 'formatted_name', 'id' ),
				'default'     => '',
				'placeholder' => __( 'Select an account&hellip;', 'wp-ever-accounting' ),
				'desc_tip'    => true,
				'class'       => 'eac-select2',
				'attrs'       => array(
					'data-action' => 'eac_json_search',
					'data-type'   => 'account',
				),
			),
			// Default category.
			array(
				'title'       => __( 'Purchases Category', 'wp-ever-accounting' ),
				'desc'        => __( 'The default category for purchases.', 'wp-ever-accounting' ),
				'id'          => 'eac_default_expense_category_id',
				'type'        => 'select',
				'options'     => wp_list_pluck( $expense_categories, 'formatted_name', 'id' ),
				'placeholder' => __( 'Select a category&hellip;', 'wp-ever-accounting' ),
				'desc_tip'    => true,
				'class'       => 'eac-select2',
				'attrs'       => array(
					'data-action' => 'eac_json_search',
					'data-type'   => 'expense_category',
				),
			),
			// tax.
			array(
				'title'       => __( 'Purchases Taxes', 'wp-ever-accounting' ),
				'desc'        => __( 'The default tax for purchases.', 'wp-ever-accounting' ),
				'id'          => 'eac_default_purchases_taxes',
				'type'        => 'select',
				'multiple'    => true,
				'class'       => 'eac-select2',
				'options'     => wp_list_pluck( eac_get_taxes( [ 'limit' => - 1 ] ), 'formatted_name', 'id' ),
				'placeholder' => __( 'Select a tax&hellip;', 'wp-ever-accounting' ),
				'desc_tip'    => true,
			),
			// Default payment method.
			array(
				'title'       => __( 'Payment Method', 'wp-ever-accounting' ),
				'desc'        => __( 'The default payment method for purchases.', 'wp-ever-accounting' ),
				'id'          => 'eac_default_purchases_payment_method',
				'type'        => 'select',
				'class'       => 'eac-select2',
				'options'     => eac_get_payment_methods(),
				'default'     => '',
				'placeholder' => __( 'Select a payment method&hellip;', 'wp-ever-accounting' ),
				'desc_tip'    => true,
			),
			// end of purchase defaults section.
			array(
				'type' => 'sectionend',
				'id'   => 'purchase_defaults',
			),
			array(
				'title' => __( 'Expense Settings', 'wp-ever-accounting' ),
				'desc'  => __( 'Customize how your expense number gets generated automatically when you create a new expense.', 'wp-ever-accounting' ),
				'type'  => 'title',
				'id'    => 'expense_settings',
			),
			// prefix.
			array(
				'title'       => __( 'Number Prefix', 'wp-ever-accounting' ),
				'desc'        => __( 'The prefix of the expense number.', 'wp-ever-accounting' ),
				'id'          => 'eac_expense_prefix',
				'type'        => 'text',
				'placeholder' => 'e.g. EXP-',
				'default'     => 'EXP-',
				'desc_tip'    => true,
			),
			// minimum digits of expense number.
			array(
				'title'       => __( 'Minimum Digits', 'wp-ever-accounting' ),
				'desc'        => __( 'The minimum digits of the expense number.', 'wp-ever-accounting' ),
				'id'          => 'eac_expense_digits',
				'type'        => 'number',
				'placeholder' => 'e.g. 4',
				'default'     => 4,
				'desc_tip'    => true,
			),
			// end expense settings section
			array(
				'type' => 'sectionend',
				'id'   => 'expense_settings',
			),
		);

	}

	/**
	 * Get expenses settings array.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function get_settings_for_bills_section() {
		return array(
			array(
				'title' => __( 'Bill Settings', 'wp-ever-accounting' ),
				'desc'  => __( 'Customize how your bill number gets generated automatically when you create a new bill.', 'wp-ever-accounting' ),
				'type'  => 'title',
				'id'    => 'general_settings',
			),
			// prefix.
			array(
				'title'       => __( 'Number Prefix', 'wp-ever-accounting' ),
				'desc'        => __( 'The prefix of the bill number.', 'wp-ever-accounting' ),
				'id'          => 'eac_bill_prefix',
				'type'        => 'text',
				'placeholder' => 'e.g. BILL-',
				'default'     => 'BILL-',
				'desc_tip'    => true,
			),
			// minimum digits of bill number.
			array(
				'title'       => __( 'Minimum Digits', 'wp-ever-accounting' ),
				'desc'        => __( 'The minimum digits of the bill number.', 'wp-ever-accounting' ),
				'id'          => 'eac_bill_digits',
				'type'        => 'number',
				'placeholder' => 'e.g. 4',
				'default'     => 4,
				'desc_tip'    => true,
			),
			// due date.
			array(
				'title'       => __( 'Due Date', 'wp-ever-accounting' ),
				'desc'        => __( 'Specify how due date is automatically set when you create an bill.', 'wp-ever-accounting' ),
				'id'          => 'eac_bill_due_date',
				'type'        => 'number',
				'placeholder' => 'e.g. 30',
				'default'     => 30,
				'desc_tip'    => true,
			),
			// Retrospective Edits.
			array(
				'title'    => __( 'Retrospective Edits', 'wp-ever-accounting' ),
				'desc'     => __( 'Based on your country\'s laws or your preference, you can restrict users from editing finalised bills.', 'wp-ever-accounting' ),
				'id'       => 'eac_bill_retrospective_edits',
				'type'     => 'select',
				'options'  => array(
					'disable_on_partial_paid' => __( 'Disable after partial payment', 'wp-ever-accounting' ),
					'disable_on_paid'         => __( 'Disable after paid', 'wp-ever-accounting' ),
					'disable_on_sent'         => __( 'Disable after sent', 'wp-ever-accounting' ),
				),
				'default'  => 'disable_on_partial_paid',
				'desc_tip' => true,
			),
			// end section.
			array(
				'type' => 'sectionend',
				'id'   => 'general_settings',
			),
			array(
				'title' => __( 'Bill Defaults', 'wp-ever-accounting' ),
				'desc'  => __( 'Customize the default values of your bills.', 'wp-ever-accounting' ),
				'type'  => 'title',
				'id'    => 'defaults_settings',
			),
			// note.
			array(
				'title'       => __( 'Notes', 'wp-ever-accounting' ),
				'desc'        => __( 'The note that will be added to the bill automatically when you create a new bill.', 'wp-ever-accounting' ),
				'id'          => 'eac_bill_note',
				'type'        => 'textarea',
				'placeholder' => 'e.g. Thank you for your business!',
				'default'     => 'Thank you for your business!',
				'desc_tip'    => true,
			),
			// end section.
			array(
				'type' => 'sectionend',
				'id'   => 'defaults_settings',
			),

			// Columns section start
			array(
				'title' => __( 'Bill Columns', 'wp-ever-accounting' ),
				'desc'  => __( 'Customize the columns of your bills.', 'wp-ever-accounting' ),
				'type'  => 'title',
				'id'    => 'columns_settings',
			),

			// item name.
			array(
				'title'       => __( 'Item Label', 'wp-ever-accounting' ),
				'desc'        => __( 'The name of the item column.', 'wp-ever-accounting' ),
				'id'          => 'eac_bill_item_label',
				'type'        => 'text',
				'placeholder' => 'e.g. Item',
				'default'     => 'Items',
				'desc_tip'    => true,
			),
			// price label.
			array(
				'title'       => __( 'Price Label', 'wp-ever-accounting' ),
				'desc'        => __( 'The name of the price column.', 'wp-ever-accounting' ),
				'id'          => 'eac_bill_price_label',
				'type'        => 'text',
				'placeholder' => 'e.g. Price',
				'default'     => 'Price',
				'desc_tip'    => true,
			),
			// quantity label.
			array(
				'title'       => __( 'Quantity Label', 'wp-ever-accounting' ),
				'desc'        => __( 'The name of the quantity column.', 'wp-ever-accounting' ),
				'id'          => 'eac_bill_quantity_label',
				'type'        => 'text',
				'placeholder' => 'e.g. Quantity',
				'default'     => 'Qty',
				'desc_tip'    => true,
			),
			// discount label.
			array(
				'title'       => __( 'Discount Label', 'wp-ever-accounting' ),
				'desc'        => __( 'The name of the discount column.', 'wp-ever-accounting' ),
				'id'          => 'eac_bill_discount_label',
				'type'        => 'text',
				'placeholder' => 'e.g. Discount',
				'default'     => 'Discount',
				'desc_tip'    => true,
			),
			// end section.
			array(
				'type' => 'sectionend',
				'id'   => 'columns_settings',
			),
		);
	}

}
