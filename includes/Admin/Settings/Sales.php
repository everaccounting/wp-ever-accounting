<?php

namespace EverAccounting\Admin\Settings;

defined( 'ABSPATH' ) || exit;

/**
 * Class Sales.
 *
 * @since   1.0.0
 * @package EverAccounting\Admin\Settings
 */
class Sales extends Page {
	/**
	 * GeneralSettingsPage constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->id    = 'sales';
		$this->label = __( 'Sales', 'wp-ever-accounting' );

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
			''         => __( 'Options', 'wp-ever-accounting' ),
			'invoices' => __( 'Invoices', 'wp-ever-accounting' ),
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
		$payment_accounts   = eac_get_accounts( array( 'include' => get_option( 'eac_default_sales_account_id' ) ) );
		$payment_categories = eac_get_categories( array( 'include' => get_option( 'eac_default_sales_category_id' ) ) );

		return array(
			// Sales defaults section.
			array(
				'title' => __( 'Default Settings', 'wp-ever-accounting' ),
				'type'  => 'title',
				'desc'  => __( 'Default values will be automatically filled in when you create a new sale related record.', 'wp-ever-accounting' ),
				'id'    => 'sales_defaults',
			),
			// Default payment account.
			array(
				'title'            => __( 'Sales Account', 'wp-ever-accounting' ),
				'desc'             => __( 'The default account to which the payments will be credited.', 'wp-ever-accounting' ),
				'id'               => 'eac_default_sales_account_id',
				'type'             => 'select',
				'options'          => array( eac_get_currency( get_option( 'eac_default_sales_account_id' ) ) ),
				'option_key'       => 'id',
				'option_value'     => 'formatted_name',
				'default'          => '',
				'data-placeholder' => __( 'Select an account&hellip;', 'wp-ever-accounting' ),
				'desc_tip'         => true,
				'class'            => 'eac_select2',
				'data-action'      => 'eac_json_search',
				'data-type'        => 'currency',
			),
			// Default payment method.
			// Default category.
			array(
				'title'            => __( 'Sales Category', 'wp-ever-accounting' ),
				'desc'             => __( 'The default category for sales.', 'wp-ever-accounting' ),
				'id'               => 'eac_default_sales_category_id',
				'type'             => 'select',
				'default'          => '',
				'options'          => array( eac_get_currency( get_option( 'eac_default_sales_category_id' ) ) ),
				'option_key'       => 'id',
				'option_value'     => 'formatted_name',
				'desc_tip'         => true,
				'class'            => 'eac_select2',
				'data-placeholder' => __( 'Select a category&hellip;', 'wp-ever-accounting' ),
				'data-action'      => 'eac_json_search',
				'data-type'        => 'currency',
			),
			// tax.
			array(
				'title'       => __( 'Sales Taxes', 'wp-ever-accounting' ),
				'desc'        => __( 'The default tax for sales.', 'wp-ever-accounting' ),
				'id'          => 'eac_default_sales_taxes',
				'type'        => 'select',
				'default'     => '',
				'multiple'    => true,
				'class'       => 'eac-select2',
				'options'     => wp_list_pluck( eac_get_taxes(), 'formatted_name', 'id' ),
				'placeholder' => __( 'Select a tax&hellip;', 'wp-ever-accounting' ),
				'desc_tip'    => true,
				'attrs'       => array(
					'data-action' => 'eac_json_search',
					'data-type'   => 'tax',
				),
			),
			array(
				'title'       => __( 'Payment Method', 'wp-ever-accounting' ),
				'desc'        => __( 'The default payment method for sales.', 'wp-ever-accounting' ),
				'id'          => 'eac_default_sales_payment_method',
				'type'        => 'select',
				'options'     => eac_get_payment_methods(),
				'default'     => '',
				'class'       => 'eac-select2',
				'placeholder' => __( 'Select a payment method&hellip;', 'wp-ever-accounting' ),
				'desc_tip'    => true,
			),
			// end of sales defaults section.
			array(
				'type' => 'sectionend',
				'id'   => 'sales_defaults',
			),
			// payment settings section
			array(
				'title' => __( 'Payment Settings', 'wp-ever-accounting' ),
				'desc'  => __( 'Customize how your payment number gets generated automatically when you create a new payment.', 'wp-ever-accounting' ),
				'type'  => 'title',
				'id'    => 'payment_settings',
			),
			// prefix.
			array(
				'title'       => __( 'Number Prefix', 'wp-ever-accounting' ),
				'desc'        => __( 'The prefix of the payment number.', 'wp-ever-accounting' ),
				'id'          => 'eac_payment_prefix',
				'type'        => 'text',
				'placeholder' => 'e.g. PAY-',
				'default'     => 'PAY-',
				'desc_tip'    => true,
			),
			// minimum digits of payment number.
			array(
				'title'       => __( 'Minimum Digits', 'wp-ever-accounting' ),
				'desc'        => __( 'The minimum digits of the payment number.', 'wp-ever-accounting' ),
				'id'          => 'eac_payment_digits',
				'type'        => 'number',
				'placeholder' => 'e.g. 4',
				'default'     => 4,
				'desc_tip'    => true,
			),
			// end payment settings section
			array(
				'type' => 'sectionend',
				'id'   => 'payment_settings',
			),
		);
	}

	/**
	 * Get settings or the default section.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	protected function get_settings_for_invoices_section() {
		return array(
			// invoice settings section
			array(
				'title' => __( 'Invoice Settings', 'wp-ever-accounting' ),
				'desc'  => __( 'Customize how your invoice number gets generated automatically when you create a new invoice.', 'wp-ever-accounting' ),
				'type'  => 'title',
				'id'    => 'general_settings',
			),
			// prefix.
			array(
				'title'       => __( 'Number Prefix', 'wp-ever-accounting' ),
				'desc'        => __( 'The prefix of the invoice number.', 'wp-ever-accounting' ),
				'id'          => 'eac_invoice_prefix',
				'type'        => 'text',
				'placeholder' => 'e.g. INV-',
				'default'     => 'INV-',
				'desc_tip'    => true,
			),
			// minimum digits of invoice number.
			array(
				'title'       => __( 'Minimum Digits', 'wp-ever-accounting' ),
				'desc'        => __( 'The minimum digits of the invoice number.', 'wp-ever-accounting' ),
				'id'          => 'eac_invoice_digits',
				'type'        => 'number',
				'placeholder' => 'e.g. 4',
				'default'     => 4,
				'desc_tip'    => true,
			),
			// due date.
			array(
				'title'       => __( 'Due Date', 'wp-ever-accounting' ),
				'desc'        => __( 'Specify how due date is automatically set when you create an invoice.', 'wp-ever-accounting' ),
				'id'          => 'eac_invoice_due_date',
				'type'        => 'number',
				'placeholder' => 'e.g. 30',
				'default'     => 30,
				'desc_tip'    => true,
			),
			// Retrospective Edits.
			array(
				'title'    => __( 'Retrospective Edits', 'wp-ever-accounting' ),
				'desc'     => __( 'Based on your country\'s laws or your preference, you can restrict users from editing finalised invoices.', 'wp-ever-accounting' ),
				'id'       => 'eac_invoice_retrospective_edits',
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
			// defaults section start
			array(
				'title' => __( 'Invoice Defaults', 'wp-ever-accounting' ),
				'desc'  => __( 'Customize the default values of your invoices.', 'wp-ever-accounting' ),
				'type'  => 'title',
				'id'    => 'defaults_settings',
			),
			// note.
			array(
				'title'       => __( 'Notes', 'wp-ever-accounting' ),
				'desc'        => __( 'The note that will be added to the invoice automatically when you create a new invoice.', 'wp-ever-accounting' ),
				'id'          => 'eac_invoice_note',
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
				'title' => __( 'Invoice Columns', 'wp-ever-accounting' ),
				'desc'  => __( 'Customize the columns of your invoices.', 'wp-ever-accounting' ),
				'type'  => 'title',
				'id'    => 'columns_settings',
			),

			// item name.
			array(
				'title'       => __( 'Item Label', 'wp-ever-accounting' ),
				'desc'        => __( 'The name of the item column.', 'wp-ever-accounting' ),
				'id'          => 'eac_invoice_col_item_label',
				'type'        => 'text',
				'placeholder' => 'e.g. Item',
				'default'     => __( 'Item', 'wp-ever-accounting' ),
				'desc_tip'    => true,
			),
			// price label.
			array(
				'title'       => __( 'Price Label', 'wp-ever-accounting' ),
				'desc'        => __( 'The name of the price column.', 'wp-ever-accounting' ),
				'id'          => 'eac_invoice_col_price_label',
				'type'        => 'text',
				'placeholder' => 'e.g. Price',
				'default'     => __( 'Price', 'wp-ever-accounting' ),
				'desc_tip'    => true,
			),
			// quantity label.
			array(
				'title'       => __( 'Quantity Label', 'wp-ever-accounting' ),
				'desc'        => __( 'The name of the quantity column.', 'wp-ever-accounting' ),
				'id'          => 'eac_invoice_col_quantity_label',
				'type'        => 'text',
				'placeholder' => 'e.g. Quantity',
				'default'     => __( 'Quantity', 'wp-ever-accounting' ),
				'desc_tip'    => true,
			),
			// tax label.
			array(
				'title'       => __( 'Tax Label', 'wp-ever-accounting' ),
				'desc'        => __( 'The name of the tax column.', 'wp-ever-accounting' ),
				'id'          => 'eac_invoice_col_tax_label',
				'type'        => 'text',
				'placeholder' => 'e.g. Tax',
				'default'     => __( 'Tax', 'wp-ever-accounting' ),
				'desc_tip'    => true,
			),
			// subtotal label.
			array(
				'title'       => __( 'Subtotal Label', 'wp-ever-accounting' ),
				'desc'        => __( 'The name of the subtotal column.', 'wp-ever-accounting' ),
				'id'          => 'eac_invoice_col_subtotal_label',
				'type'        => 'text',
				'placeholder' => 'e.g. Subtotal',
				'default'     => __( 'Subtotal', 'wp-ever-accounting' ),
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
