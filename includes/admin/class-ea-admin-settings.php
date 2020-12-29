<?php
/**
 * Admin Settings.
 *
 * @since       1.0.2
 * @subpackage  Admin
 * @package     EverAccounting
 */

/**
 * Class Settings
 *
 * @since   1.0.2
 * @package EverAccounting\Admin
 */
class EAccounting_Admin_Settings {

	/**
	 * EAccounting_Admin_Settings constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_settings_page' ), 100 );
		//add_filter( 'eaccounting_settings', array( $this, 'register_settings' ) );
		//add_filter( 'eaccounting_settings_emails', array( $this, 'register_email_settings' ) );
		add_filter( 'eaccounting_settings_tab_currencies', array( $this, 'render_currencies_tab' ) );
		add_filter( 'eaccounting_settings_tab_categories', array( $this, 'render_categories_tab' ) );
		//add_filter( 'eaccounting_settings_tab_taxes', array( $this, 'render_taxes_tab' ) );
	}

	/**
	 * Registers the page.
	 *
	 */
	public function register_settings_page() {
		add_submenu_page(
			'eaccounting',
			__( 'Settings', 'wp-ever-accounting' ),
			__( 'Settings', 'wp-ever-accounting' ),
			'manage_options',
			'ea-settings',
			array( $this, 'display_settings_page' )
		);
	}


	/**
	 * Displays the settings page.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public function display_settings_page() {
		$tabs     = eaccounting_get_settings_tabs();
		$sections = eaccounting_get_settings_sections();
		// Get current tab/section.
		$first_tab         = current( array_keys( $tabs ) );
		$requested_tab     = isset( $_GET['tab'] ) ? sanitize_title( $_GET['tab'] ) : $first_tab;
		$current_tab       = array_key_exists( $requested_tab, $tabs ) ? $requested_tab : $first_tab;
		$requested_section = isset( $_GET['section'] ) ? sanitize_title( $_GET['section'] ) : 'main';
		$tab_sections      = isset( $sections[ $current_tab ] ) ? $sections[ $current_tab ] : array();
		$current_section   = isset($tab_sections[$requested_section]) ? $requested_section : current( array_keys( $tab_sections ) );
		$tab_exists        = ! empty( $current_tab ) || has_action( 'eaccounting_settings_' . $current_tab ) || has_action( 'eaccounting_settings_tab_' . $current_tab );
		$current_tab_label = isset( $tabs[ $current_tab ] ) ? $tabs[ $current_tab ] : '';
		if ( ! $tab_exists ) {
			wp_safe_redirect( admin_url( 'admin.php?page=ea-settings' ) );
			exit;
		}
		ob_start();
		include dirname( __FILE__ ) . '/views/admin-page-settings.php';
		echo ob_get_clean();
	}

	public function register_settings( $registered ) {
		$accounts   = eaccounting_get_accounts(
			array(
				'include' => eaccounting()->settings->get( 'default_account' ),
				'return'  => 'raw',
			)
		);
		$currencies = eaccounting_get_currencies( array( 'return' => 'raw' ) );

		$settings = array(
			'general' => apply_filters(
				'eaccounting_settings_general',
				array(
					'company_settings'       => array(
						'name' => __( 'Company Settings', 'wp-ever-accounting' ),
						'desc' => '',
						'type' => 'header',
					),
					'company_name'           => array(
						'name' => __( 'Name', 'wp-ever-accounting' ),
						'type' => 'text',
						'tip'  => 'XYZ Company',
						'attr' => array(
							'required'    => 'required',
							'placeholder' => __( 'XYZ Company', 'wp-ever-accounting' ),
						),
					),
					'company_email'          => array(
						'name'              => __( 'Email', 'wp-ever-accounting' ),
						'type'              => 'text',
						'std'               => get_option( 'admin_email' ),
						'sanitize_callback' => 'sanitize_email',
					),
					'company_phone'          => array(
						'name' => __( 'Phone Number', 'wp-ever-accounting' ),
						'type' => 'text',
					),
					'company_tax_number'     => array(
						'name' => __( 'Tax Number', 'wp-ever-accounting' ),
						'type' => 'text',
					),
					'company_address'        => array(
						'name' => __( 'Street', 'wp-ever-accounting' ),
						'type' => 'textarea',
					),
					'company_city'           => array(
						'name' => __( 'City', 'wp-ever-accounting' ),
						'type' => 'text',
					),
					'company_state'          => array(
						'name' => __( 'State', 'wp-ever-accounting' ),
						'type' => 'text',
					),
					'company_postcode'       => array(
						'name' => __( 'Zip/Postcode', 'wp-ever-accounting' ),
						'type' => 'text',
					),
					'company_country'        => array(
						'name'    => __( 'Country', 'wp-ever-accounting' ),
						'type'    => 'select',
						'class'   => 'ea-select2',
						'options' => array( '' => __( 'Select Country', 'wp-ever-accounting' ) ) + eaccounting_get_countries(),
					),
					'company_logo'           => array(
						'name' => __( 'Logo', 'wp-ever-accounting' ),
						'type' => 'upload',
					),
					'local_settings'         => array(
						'name' => __( 'Localisation Settings', 'wp-ever-accounting' ),
						'desc' => '',
						'type' => 'header',
					),
					'financial_year_start'   => array(
						'name'  => __( 'Financial Year Start', 'wp-ever-accounting' ),
						'std'   => '01-01',
						'class' => 'ea-financial-start',
						'type'  => 'text',
					),
					'default_settings'       => array(
						'name' => __( 'Default Settings', 'wp-ever-accounting' ),
						'desc' => '',
						'type' => 'header',
					),
					'default_account'        => array(
						'name'    => __( 'Account', 'wp-ever-accounting' ),
						'type'    => 'select',
						'class'   => 'ea-select2',
						'options' => array( '' => __( 'Select default account', 'wp-ever-accounting' ) ) + wp_list_pluck( $accounts, 'name', 'id' ),
						'attr'    => array(
							'data-placeholder' => __( 'Select Account', 'wp-ever-accounting' ),
							'data-url'         => eaccounting()->ajax_url(),
							'data-ajax_action' => 'eaccounting_get_accounts',
							'data-nonce'       => wp_create_nonce( 'ea_get_accounts' ),
							'data-map'         => 'return {text: option.name + " (" + option.currency_code +")"  , id:option.id}',
							'data-modal_id'    => '#ea-modal-add-account',
							'data-add_text'    => __( 'Add New', 'wp-ever-accounting' ),
						),
					),
					'default_currency'       => array(
						'name'    => __( 'Currency', 'wp-ever-accounting' ),
						'type'    => 'select',
						'std'     => 'USD',
						'desc'    => __( 'Default currency rate will update to 1', 'wp-ever-accounting' ),
						'class'   => 'ea-select2',
						'options' => array( '' => __( 'Select default currency', 'wp-ever-accounting' ) ) + wp_list_pluck( array_values( $currencies ), 'name', 'code' ),
						'attr'    => array(
							'data-placeholder' => __( 'Select Currency', 'wp-ever-accounting' ),
							'data-url'         => eaccounting()->ajax_url(),
							'data-ajax_action' => 'eaccounting_get_currencies',
							'data-nonce'       => wp_create_nonce( 'ea_get_currencies' ),
							'data-map'         => 'return {text: option.name + " (" + option.symbol +")"  , id:option.code}',
							'data-modal_id'    => '#ea-modal-add-currency',
							'data-add_text'    => __( 'Add New', 'wp-ever-accounting' ),
						),
					),
					'default_payment_method' => array(
						'name'    => __( 'Payment Method', 'wp-ever-accounting' ),
						'std'     => 'cash',
						'type'    => 'select',
						'options' => eaccounting_get_payment_methods(),
					),
					'invoice_prefix'         => array(
						'name'    => __( 'Invoice Prefix', 'wp-ever-accounting' ),
						'std'     => 'INV-',
						'type'    => 'text',
						'section' => 'invoice',
					),
					'invoice_digit'          => array(
						'name'    => __( 'Minimum Digits', 'wp-ever-accounting' ),
						'std'     => '5',
						'type'    => 'number',
						'section' => 'invoice',
					),
					'invoice_title'          => array(
						'name'    => __( 'Invoice Title', 'wp-ever-accounting' ),
						'std'     => '',
						'type'    => 'text',
						'section' => 'invoice',
					),
					'invoice_subheading'     => array(
						'name'    => __( 'Invoice Subheading', 'wp-ever-accounting' ),
						'std'     => '',
						'type'    => 'text',
						'section' => 'invoice',
					),
					'invoice_notes'          => array(
						'name'    => __( 'Invoice Notes', 'wp-ever-accounting' ),
						'std'     => '',
						'type'    => 'textarea',
						'section' => 'invoice',
					),
					'invoice_footer'         => array(
						'name'    => __( 'Invoice Footer', 'wp-ever-accounting' ),
						'std'     => '',
						'type'    => 'textarea',
						'section' => 'invoice',
					),
					'invoice_item_label'     => array(
						'name'    => __( 'Item Label', 'wp-ever-accounting' ),
						'std'     => __( 'Item', 'wp-ever-accounting' ),
						'type'    => 'text',
						'section' => 'invoice',
					),
					'invoice_price_label'    => array(
						'name'    => __( 'Price Label', 'wp-ever-accounting' ),
						'std'     => __( 'Price', 'wp-ever-accounting' ),
						'type'    => 'text',
						'section' => 'invoice',
					),
					'invoice_quantity_label' => array(
						'name'    => __( 'Quantity Label', 'wp-ever-accounting' ),
						'std'     => __( 'Quantity', 'wp-ever-accounting' ),
						'type'    => 'text',
						'section' => 'invoice',
					),
					'bill_prefix'            => array(
						'name'    => __( 'Bill Prefix', 'wp-ever-accounting' ),
						'std'     => 'BILL-',
						'type'    => 'text',
						'section' => 'bill',
					),
					'bill_digit'             => array(
						'name'    => __( 'Bill Digits', 'wp-ever-accounting' ),
						'std'     => '5',
						'type'    => 'number',
						'section' => 'bill',
					),
					'bill_title'             => array(
						'name'    => __( 'Bill Title', 'wp-ever-accounting' ),
						'std'     => '',
						'type'    => 'text',
						'section' => 'bill',
					),
					'bill_subheading'        => array(
						'name'    => __( 'Bill Subheading', 'wp-ever-accounting' ),
						'std'     => '',
						'type'    => 'text',
						'section' => 'bill',
					),
					'bill_notes'             => array(
						'name'    => __( 'Bill Notes', 'wp-ever-accounting' ),
						'std'     => '',
						'type'    => 'textarea',
						'section' => 'bill',
					),
					'bill_footer'            => array(
						'name'    => __( 'Bill Footer', 'wp-ever-accounting' ),
						'std'     => '',
						'type'    => 'textarea',
						'section' => 'bill',
					),
					'bill_item_label'        => array(
						'name'    => __( 'Item Label', 'wp-ever-accounting' ),
						'std'     => __( 'Item', 'wp-ever-accounting' ),
						'type'    => 'text',
						'section' => 'bill',
					),
					'bill_price_label'       => array(
						'name'    => __( 'Price Label', 'wp-ever-accounting' ),
						'std'     => __( 'Price', 'wp-ever-accounting' ),
						'type'    => 'text',
						'section' => 'bill',
					),
					'bill_quantity_label'    => array(
						'name'    => __( 'Quantity Label', 'wp-ever-accounting' ),
						'std'     => __( 'Quantity', 'wp-ever-accounting' ),
						'type'    => 'text',
						'section' => 'bill',
					),
					'tax_settings'  => array(
						'name'    => __( 'Tax Settings', 'wp-ever-accounting' ),
						'desc'    => '',
						'type'    => 'header',
						'section' => 'tax',
					),
					'enable_taxes'           => array(
						'name'    => __( 'Enable Taxes', 'wp-ever-accounting' ),
						'type'    => 'checkbox',
						'std'     => 'yes',
						'desc'    => __( 'Enable tax rates and calculations.', 'wp-ever-accounting' ),
						'section' => 'tax',
					),
					'tax_subtotal_rounding'  => array(
						'name'    => __( 'Rounding', 'wp-ever-accounting' ),
						'type'    => 'checkbox',
						'desc'    => __( 'Round tax at subtotal level, instead of rounding per tax rate.', 'wp-ever-accounting' ),
						'section' => 'tax',
					),
					'prices_include_tax'     => array(
						'name'    => __( 'Prices entered with tax', 'wp-ever-accounting' ),
						'type'    => 'select',
						'std'     => 'yes',
						'section' => 'tax',
						'options' => array(
							'yes' => __( 'Yes, I will enter prices inclusive of tax', 'wp-ever-accounting' ),
							'no'  => __( 'No, I will enter prices exclusive of tax', 'wp-ever-accounting' ),
						),
					),
					'tax_display_totals'     => array(
						'name'    => __( 'Display tax totals	', 'wp-ever-accounting' ),
						'type'    => 'select',
						'std'     => 'total',
						'section' => 'tax',
						'options' => array(
							'total'      => __( 'As a single total', 'wp-ever-accounting' ),
							'individual' => __( 'As individual tax rates', 'wp-ever-accounting' ),
						),
					),
				)
			),
			'emails'  => apply_filters(
				'eaccounting_settings_emails',
				array()
			),
		);

		return array_merge( $registered, $settings );
	}

	/**
	 * Add email settings.
	 *
	 * @since 1.1.0
	 *
	 * @param $settings
	 *
	 * @return array
	 */
	public function register_email_settings( $settings ) {
		$available_tags = array(
			__( '{invoice_number}', 'wp-ever-accounting' ),
			__( '{invoice_total}', 'wp-ever-accounting' ),
			__( '{invoice_due_date}', 'wp-ever-accounting' ),
			__( '{invoice_admin_url}', 'wp-ever-accounting' ),
			__( '{name}', 'wp-ever-accounting' ),
			__( '{company_name}', 'wp-ever-accounting' ),
			__( '{company_email}', 'wp-ever-accounting' ),
			__( '{company_tax_number}', 'wp-ever-accounting' ),
			__( '{company_phone}', 'wp-ever-accounting' ),
			__( '{company_address}', 'wp-ever-accounting' ),
		);
		$email_settings = array(
			'default_settings'                    => array(
				'name' => __( 'Email sender options', 'wp-ever-accounting' ),
				'desc' => '',
				'type' => 'header',
			),
			'email_from_name'                     => array(
				'name' => __( 'From Name', 'wp-ever-accounting' ),
				'std'  => site_url(),
				'type' => 'text',
			),
			'email_from'                          => array(
				'name' => __( 'From Email', 'wp-ever-accounting' ),
				'std'  => get_option( 'admin_email' ),
				'type' => 'text',
			),
			'admin_email'                         => array(
				'name' => __( 'Admin Email', 'wp-ever-accounting' ),
				'std'  => get_option( 'admin_email' ),
				'type' => 'text',
			),
			'email_sections_title'                => array(
				'name' => __( 'Email notifications', 'wp-ever-accounting' ),
				'desc' => __( 'Email notifications sent from Ever Accounting are listed below. Click on an email to configure it.', 'wp-ever-accounting' ),
				'type' => 'header',
			),
			'email_sections'                      => array(
				'type'     => '',
				'callback' => array( $this, 'email_sections' ),
			),
			'email_customer_invoice_header'       => array(
				'name'    => __( 'Customer Invoice', 'wp-ever-accounting' ),
				'desc'    => __( 'These emails are sent to the customer whenever an invoice is created.', 'wp-ever-accounting' ),
				'type'    => 'header',
				'section' => 'customer_invoice',
			),
			'email_customer_invoice_active'       => array(
				'name'    => __( 'Enable/Disable', 'wp-ever-accounting' ),
				'type'    => 'checkbox',
				'section' => 'customer_invoice',
				'desc'    => __( 'Enable this email notification', 'wp-ever-accounting' ),
			),
			'email_customer_invoice_subject'      => array(
				'name'    => __( 'Subject', 'wp-ever-accounting' ),
				'type'    => 'text',
				'section' => 'customer_invoice',
				'std'     => __( '[{site_title}] Customer Invoice #{invoice_number}', 'wp-ever-accounting' ),
			),
			'email_customer_invoice_heading'      => array(
				'name'    => __( 'Email Heading', 'wp-ever-accounting' ),
				'type'    => 'text',
				'section' => 'customer_invoice',
				'std'     => __( 'Customer Invoice #{invoice_number}', 'wp-ever-accounting' ),
			),
			'email_customer_invoice_body'         => array(
				'name'    => __( 'Email Body', 'wp-ever-accounting' ),
				'type'    => 'rich_editor',
				'section' => 'customer_invoice',
				'std'     => __( 'Dear {name}, Your Invoice has been prepared {invoice_number}, You can see the invoice details below ,Best Regards,{company_name}', 'wp-ever-accounting' ),
			),
			'email_customer_invoice_tags'         => array(
				'name'    => __( 'Available Tags', 'wp-ever-accounting' ),
				'type'    => 'html',
				'section' => 'customer_invoice',
				'class'   => 'email-tags',
				'html'    => implode( ',', $available_tags ),
			),
			'email_customer_invoice_note_header'  => array(
				'name'    => __( 'Customer Invoice Note', 'wp-ever-accounting' ),
				'desc'    => __( 'These emails are sent to the customer whenever an invoice is created.', 'wp-ever-accounting' ),
				'type'    => 'header',
				'section' => 'customer_invoice_note',
			),
			'email_customer_invoice_note_active'  => array(
				'name'    => __( 'Enable/Disable', 'wp-ever-accounting' ),
				'type'    => 'checkbox',
				'section' => 'customer_invoice_note',
				'desc'    => __( 'Enable this email notification', 'wp-ever-accounting' ),
			),
			'email_customer_invoice_note_subject' => array(
				'name'    => __( 'Subject', 'wp-ever-accounting' ),
				'type'    => 'text',
				'section' => 'customer_invoice_note',
				'std'     => __( '[{site_title}] Customer Invoice Note#{invoice_number}', 'wp-ever-accounting' ),
			),
			'email_customer_invoice_note_heading' => array(
				'name'    => __( 'Email Heading', 'wp-ever-accounting' ),
				'type'    => 'text',
				'section' => 'customer_invoice_note',
				'std'     => __( 'Customer Invoice Note #{invoice_number}', 'wp-ever-accounting' ),
			),
			'email_customer_invoice_note_body'    => array(
				'name'    => __( 'Email Body', 'wp-ever-accounting' ),
				'type'    => 'rich_editor',
				'section' => 'customer_invoice_note',
				'std'     => __( 'Dear {name}, Your Invoice has been prepared {invoice_number}, You can see the invoice notes below ,Best Regards,{company_name}', 'wp-ever-accounting' ),
			),
			'email_customer_invoice_note_tags'    => array(
				'name'    => __( 'Available Tags', 'wp-ever-accounting' ),
				'type'    => 'html',
				'section' => 'customer_invoice_note',
				'class'   => 'email-tags',
				'html'    => implode( ',', $available_tags ),
			),
			'email_new_invoice_header'            => array(
				'name'    => __( 'New Invoice Notification', 'wp-ever-accounting' ),
				'desc'    => __( 'These emails are sent to the site admin whenever there is a new invoice.', 'wp-ever-accounting' ),
				'type'    => 'header',
				'section' => 'new_invoice',
			),
			'email_new_invoice_active'            => array(
				'name'    => __( 'Enable/Disable', 'wp-ever-accounting' ),
				'type'    => 'checkbox',
				'section' => 'new_invoice',
				'desc'    => __( 'Enable this email notification', 'wp-ever-accounting' ),
			),
			'email_new_invoice_subject'           => array(
				'name'    => __( 'Subject', 'wp-ever-accounting' ),
				'type'    => 'text',
				'section' => 'new_invoice',
				'std'     => __( '[{site_title}] New Invoice created #{invoice_number}', 'wp-ever-accounting' ),
			),
			'email_new_invoice_heading'           => array(
				'name'    => __( 'Email Heading', 'wp-ever-accounting' ),
				'type'    => 'text',
				'section' => 'new_invoice',
				'std'     => __( 'New Invoice #{invoice_number}', 'wp-ever-accounting' ),
			),
			'email_new_invoice_body'              => array(
				'name'    => __( 'Email Body', 'wp-ever-accounting' ),
				'type'    => 'rich_editor',
				'section' => 'new_invoice',
				'std'     => __( 'Hello, A New Invoice is placed for #{invoice_number}, Check invoice details here <a href="{invoice_admin_url}">View</a> ,Best Regards,{company_name}', 'wp-ever-accounting' ),
			),
			'email_new_invoice_tags'              => array(
				'name'    => __( 'Available Tags', 'wp-ever-accounting' ),
				'type'    => 'html',
				'section' => 'new_invoice',
				'class'   => 'email-tags',
				'html'    => implode( ',', $available_tags ),
			),
			'email_cancelled_invoice_header'      => array(
				'name'    => __( 'Cancelled Invoice Notification', 'wp-ever-accounting' ),
				'desc'    => __( 'These emails are sent to the site admin whenever an invoice is cancelled.', 'wp-ever-accounting' ),
				'type'    => 'header',
				'section' => 'cancelled_invoice',
			),
			'email_cancelled_invoice_active'      => array(
				'name'    => __( 'Enable/Disable', 'wp-ever-accounting' ),
				'type'    => 'checkbox',
				'section' => 'cancelled_invoice',
				'desc'    => __( 'Enable this email notification', 'wp-ever-accounting' ),
			),
			'email_cancelled_invoice_subject'     => array(
				'name'    => __( 'Subject', 'wp-ever-accounting' ),
				'type'    => 'text',
				'section' => 'cancelled_invoice',
				'std'     => __( '[{site_title}] Invoice cancelled #{invoice_number}', 'wp-ever-accounting' ),
			),
			'email_cancelled_invoice_heading'     => array(
				'name'    => __( 'Email Heading', 'wp-ever-accounting' ),
				'type'    => 'text',
				'section' => 'cancelled_invoice',
				'std'     => __( 'Cancelled Invoice #{invoice_number}', 'wp-ever-accounting' ),
			),
			'email_cancelled_invoice_body'        => array(
				'name'    => __( 'Email Body', 'wp-ever-accounting' ),
				'type'    => 'rich_editor',
				'section' => 'cancelled_invoice',
				'std'     => __( 'Hello, An Invoice get cancelled. Invoice Number #{invoice_number}, Check invoice details here <a href="{invoice_admin_url}">View</a> ,Best Regards,{company_name}', 'wp-ever-accounting' ),
			),
			'email_cancelled_invoice_tags'        => array(
				'name'    => __( 'Available Tags', 'wp-ever-accounting' ),
				'type'    => 'html',
				'section' => 'cancelled_invoice',
				'class'   => 'email-tags',
				'html'    => implode( ',', $available_tags ),
			),
			'email_failed_invoice_header'         => array(
				'name'    => __( 'Failed Invoice Notification', 'wp-ever-accounting' ),
				'desc'    => __( 'These emails are sent to the site admin whenever an invoice is failed.', 'wp-ever-accounting' ),
				'type'    => 'header',
				'section' => 'failed_invoice',
			),
			'email_failed_invoice_active'         => array(
				'name'    => __( 'Enable/Disable', 'wp-ever-accounting' ),
				'type'    => 'checkbox',
				'section' => 'failed_invoice',
				'desc'    => __( 'Enable this email notification', 'wp-ever-accounting' ),
			),
			'email_failed_invoice_subject'        => array(
				'name'    => __( 'Subject', 'wp-ever-accounting' ),
				'type'    => 'text',
				'section' => 'failed_invoice',
				'std'     => __( '[{site_title}] Failed cancelled #{invoice_number}', 'wp-ever-accounting' ),
			),
			'email_failed_invoice_heading'        => array(
				'name'    => __( 'Email Heading', 'wp-ever-accounting' ),
				'type'    => 'text',
				'section' => 'failed_invoice',
				'std'     => __( 'Failed Invoice #{invoice_number}', 'wp-ever-accounting' ),
			),
			'email_failed_invoice_body'           => array(
				'name'    => __( 'Email Body', 'wp-ever-accounting' ),
				'type'    => 'rich_editor',
				'section' => 'failed_invoice',
				'std'     => __( 'Hello, Invoice #{invoice_number} get failed for the customer {name} with a total of {invoice_total}, Check invoice details here <a href="{invoice_admin_url}">View</a> ,Best Regards,{company_name}', 'wp-ever-accounting' ),
			),
			'email_failed_invoice_tags'           => array(
				'name'    => __( 'Available Tags', 'wp-ever-accounting' ),
				'type'    => 'html',
				'section' => 'failed_invoice',
				'class'   => 'email-tags',
				'html'    => implode( ',', $available_tags ),
			),
			'email_completed_invoice_header'      => array(
				'name'    => __( 'Completed Invoice Notification', 'wp-ever-accounting' ),
				'desc'    => __( 'These emails are sent to the site admin whenever an invoice is completed.', 'wp-ever-accounting' ),
				'type'    => 'header',
				'section' => 'completed_invoice',
			),
			'email_completed_invoice_active'      => array(
				'name'    => __( 'Enable/Disable', 'wp-ever-accounting' ),
				'type'    => 'checkbox',
				'section' => 'completed_invoice',
				'desc'    => __( 'Enable this email notification', 'wp-ever-accounting' ),
			),
			'email_completed_invoice_subject'     => array(
				'name'    => __( 'Subject', 'wp-ever-accounting' ),
				'type'    => 'text',
				'section' => 'completed_invoice',
				'std'     => __( '[{site_title}] Invoice completed #{invoice_number}', 'wp-ever-accounting' ),
			),
			'email_completed_invoice_heading'     => array(
				'name'    => __( 'Email Heading', 'wp-ever-accounting' ),
				'type'    => 'text',
				'section' => 'completed_invoice',
				'std'     => __( 'Completed Invoice #{invoice_number}', 'wp-ever-accounting' ),
			),
			'email_completed_invoice_body'        => array(
				'name'    => __( 'Email Body', 'wp-ever-accounting' ),
				'type'    => 'rich_editor',
				'section' => 'completed_invoice',
				'std'     => __( 'Hello, Invoice #{invoice_number} is completed for the customer {name} with a total of {invoice_total}, Check invoice details here <a href="{invoice_admin_url}">View</a> ,Best Regards,{company_name}', 'wp-ever-accounting' ),
			),
			'email_completed_invoice_tags'        => array(
				'name'    => __( 'Available Tags', 'wp-ever-accounting' ),
				'type'    => 'html',
				'section' => 'completed_invoice',
				'class'   => 'email-tags',
				'html'    => implode( ',', $available_tags ),
			),
		);

		return array_merge( $settings, $email_settings );
	}

	/**
	 * Add emails sections
	 *
	 * @since 1.1.0
	 *
	 * @param $args
	 *
	 */
	function email_sections( $args ) {
		$notifications = apply_filters(
			'eaccounting_email_notifications',
			array(
				'customer_invoice'      => __( 'Customer Invoice', 'wp-ever-accounting' ),
				'customer_invoice_note' => __( 'Customer Note', 'wp-ever-accounting' ),
				'new_invoice'           => __( 'New Invoice Notification', 'wp-ever-accounting' ),
				'cancelled_invoice'     => __( 'Cancelled Invoice Notification', 'wp-ever-accounting' ),
				'failed_invoice'        => __( 'Failed Invoice Notification', 'wp-ever-accounting' ),
				'completed_invoice'     => __( 'Completed Invoice Notification', 'wp-ever-accounting' ),
			)
		);
		?>
		<table class="form-table widefat ea-emails">
			<thead>
			<tr>
				<th class="ea-emails-email"><?php esc_html_e( 'Email', 'wp-ever-accounting' ); ?></th>
				<th class="ea-emails-status"><?php esc_html_e( 'Status', 'wp-ever-accounting' ); ?></th>
				<th class="ea-emails-manage"><?php esc_html_e( 'Manage', 'wp-ever-accounting' ); ?></th>
			</tr>
			</thead>
			<tbody>
			<?php foreach ( $notifications as $key => $title ) : ?>
				<tr>
					<td>
						<?php
						echo sprintf(
							'<a href="%s"><strong>%s</strong></a>',
							esc_url(
								add_query_arg(
									array(
										'page'    => 'ea-settings',
										'tab'     => 'emails',
										'section' => $key,

									)
								)
							),
							esc_html( $title )
						);
						?>
					</td>
					<td><?php echo sprintf( '<span class="email-status %s"><span class="dashicons dashicons-yes-alt">&nbsp;</span></span>', eaccounting()->settings->get( 'email_' . $key . '_active' ) === 'yes' ? 'active' : 'inactive' ); ?> </td>
					<td>
						<?php
						echo sprintf(
							'<a href="%s" class="button button-secondary">%s</a>',
							esc_url(
								add_query_arg(
									array(
										'page'    => 'ea-settings',
										'tab'     => 'emails',
										'section' => $key,

									)
								)
							),
							__( 'Manage', 'wp-ever-accounting' )
						);
						?>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	}

	/**
	 * @since 1.1.0
	 */
	public function render_currencies_tab() {
		$requested_view = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
		if ( in_array( $requested_view, array( 'add', 'edit' ), true ) ) {
			$currency_id = isset( $_GET['currency_id'] ) ? absint( $_GET['currency_id'] ) : null;
			include dirname( __FILE__ ) . '/views/currencies/edit-currency.php';
		} else {
			include dirname( __FILE__ ) . '/views/currencies/list-currency.php';
		}
	}

	/**
	 * @since 1.1.0
	 */
	public function render_categories_tab() {
		$requested_view = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
		if ( in_array( $requested_view, array( 'add', 'edit' ), true ) ) {
			$category_id = isset( $_GET['category_id'] ) ? absint( $_GET['category_id'] ) : null;
			include dirname( __FILE__ ) . '/views/categories/edit-category.php';
		} else {
			include dirname( __FILE__ ) . '/views/categories/list-category.php';
		}
	}
}

return new EAccounting_Admin_Settings();
