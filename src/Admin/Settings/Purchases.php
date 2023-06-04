<?php

namespace EverAccounting\Admin\Settings;

defined( 'ABSPATH' ) || exit;

/**
 * Class Sales.
 *
 * @since   1.0.0
 * @package EverAccounting\Admin\Settings
 */
class Purchases extends \EverAccounting\Admin\SettingsTab {
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
			''         => __( 'Bills', 'wp-ever-accounting' ),
			'expenses' => __( 'Expenses', 'wp-ever-accounting' ),
		);

		return $sections;
	}


	/**
	 * Get settings or the default section.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	protected function get_settings_for_default_section() {
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

	/**
	 * Get expenses settings array.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_settings_for_expenses_section() {
		return array(
			// expense settings section
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

}
