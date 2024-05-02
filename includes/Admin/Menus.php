<?php

namespace EverAccounting\Admin;

use EverAccounting\Models\Account;
use EverAccounting\Models\Category;
use EverAccounting\Models\Currency;
use EverAccounting\Models\Customer;
use EverAccounting\Models\Invoice;
use EverAccounting\Models\Item;
use EverAccounting\Models\Revenue;
use EverAccounting\Models\Tax;
use EverAccounting\Models\Vendor;

/**
 * Menus class.
 *
 * @since 1.0.0
 * @package EverAccounting\Admin\Controllers
 */
class Menus {

	/**
	 * Main menu slug.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const PARENT_SLUG = 'ever-accounting';

	/**
	 * Current tab.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $current_tab;

	/**
	 * List tables.
	 *
	 * @var \WP_List_Table
	 */
	private $list_table;

	/**
	 * Menus constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'current_screen', array( $this, 'setup_screen' ), - 1 );
		add_filter( 'set-screen-option', array( $this, 'screen_option' ), 10, 3 );
		add_action( 'current_screen', array( $this, 'setup_list_table' ) );
		add_action( 'ever_accounting_admin_items', array( $this, 'render_items_tab' ) );
		add_action( 'ever_accounting_admin_sales_revenues', array( $this, 'render_revenues_tab' ) );
		add_action( 'ever_accounting_admin_sales_invoices', array( $this, 'render_invoices_tab' ) );
		add_action( 'ever_accounting_admin_sales_customers', array( $this, 'render_customers_tab' ) );
		add_action( 'ever_accounting_admin_purchases_payments', array( $this, 'render_payments_tab' ) );
		add_action( 'ever_accounting_admin_purchases_bills', array( $this, 'render_bills_tab' ) );
		add_action( 'ever_accounting_admin_purchases_vendors', array( $this, 'render_vendors_tab' ) );
		add_action( 'ever_accounting_admin_banking_accounts', array( $this, 'render_accounts_tab' ) );
		add_action( 'ever_accounting_admin_misc_categories', array( $this, 'render_categories_tab' ) );
		add_action( 'ever_accounting_admin_misc_currencies', array( $this, 'render_currencies_tab' ) );
		add_action( 'ever_accounting_admin_misc_taxes', array( $this, 'render_taxes_tab' ) );
		add_action( 'ever_accounting_admin_tools_import', array( $this, 'render_import_tab' ) );
		add_action( 'ever_accounting_admin_tools_export', array( $this, 'render_export_tab' ) );
		add_action( 'ever_accounting_admin_reports_payments', array( $this, 'render_payments_report_tab' ) );
		add_action( 'ever_accounting_admin_reports_expenses', array( $this, 'render_expenses_report_tab' ) );
		add_action( 'ever_accounting_admin_reports_profits', array( $this, 'render_profits_report_tab' ) );
		add_action( 'ever_accounting_admin_reports_taxes', array( $this, 'render_taxes_report_tab' ) );
	}

	/**
	 * Add admin menus.
	 *
	 * @since 1.0.0
	 */
	public function admin_menu() {
		global $menu, $admin_page_hooks;
		if ( current_user_can( 'manage_options' ) ) {
			$menu[] = array( '', 'read', 'ea-separator', '', 'wp-menu-separator accounting' );
		}
		$icon = 'data:image/svg+xml;base64,' . base64_encode( '<svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24"><path style=" stroke:none;fill-rule:nonzero;fill:rgb(93.333333%,93.333333%,93.333333%);fill-opacity:1;" d="M 18 1.609375 C 14.292969 -0.539062 9.707031 -0.539062 6 1.609375 C 2.292969 3.757812 0 7.714844 0 12 C 0 16.285156 2.292969 20.242188 6 22.390625 C 9.707031 24.539062 14.292969 24.539062 18 22.390625 C 21.707031 20.242188 24 16.285156 24 12 C 24 7.714844 21.707031 3.757812 18 1.609375 Z M 18.371094 13.390625 L 17.496094 18.070312 C 17.339844 18.898438 16.621094 19.488281 15.78125 19.488281 L 14.664062 19.488281 C 14.832031 18.625 15 17.761719 15.167969 16.894531 C 13.738281 18.347656 11.039062 19.691406 8.964844 19.691406 C 7.65625 19.691406 6.574219 19.222656 5.722656 18.300781 C 4.871094 17.375 4.441406 16.199219 4.441406 14.785156 C 4.441406 12.898438 5.125 11.230469 6.480469 9.78125 C 6.527344 9.730469 6.574219 9.683594 6.625 9.636719 C 7.980469 8.257812 9.996094 7.273438 11.964844 7.667969 C 13.65625 8.003906 14.914062 9.457031 15.3125 11.089844 L 15.371094 11.292969 C 15.503906 11.84375 15.203125 12.324219 14.652344 12.46875 L 8.484375 14.039062 L 8.484375 13.164062 L 13.90625 11.304688 C 13.824219 11.136719 13.726562 10.96875 13.609375 10.800781 C 13.019531 9.984375 12.226562 9.574219 11.242188 9.574219 C 10.019531 9.574219 8.941406 10.078125 8.039062 11.074219 C 7.714844 11.4375 7.453125 11.808594 7.246094 12.203125 C 7.007812 12.660156 6.851562 13.140625 6.757812 13.644531 C 6.707031 13.945312 6.671875 14.257812 6.671875 14.578125 C 6.671875 15.492188 6.960938 16.246094 7.523438 16.835938 C 8.101562 17.425781 8.832031 17.6875 9.71875 17.710938 C 10.765625 17.746094 12.238281 17.328125 13.296875 16.777344 C 13.894531 16.464844 14.605469 16.5 15.15625 16.882812 L 15.167969 16.894531 C 15.179688 16.824219 15.191406 16.753906 15.214844 16.679688 C 15.503906 15.191406 15.792969 13.714844 16.078125 12.226562 C 16.378906 10.65625 16.65625 9.109375 15.816406 7.65625 C 15.144531 6.46875 13.859375 5.855469 12.527344 5.773438 C 11.460938 5.699219 10.367188 5.929688 9.382812 6.335938 C 9.300781 6.371094 8.460938 6.757812 8.460938 6.769531 C 8.460938 6.769531 7.609375 5.257812 7.609375 5.257812 C 7.570312 5.171875 9.144531 4.5 9.289062 4.453125 C 9.898438 4.222656 10.523438 4.042969 11.160156 3.9375 C 12.421875 3.707031 13.738281 3.730469 14.976562 4.09375 C 16.621094 4.585938 18.011719 5.84375 18.515625 7.5 C 19.105469 9.382812 18.636719 11.878906 18.371094 13.390625 Z M 18.371094 13.390625 "/></svg>' ); // phpcs:ignore
		add_menu_page(
			__( 'Accounting', 'wp-ever-accounting' ),
			__( 'Accounting', 'wp-ever-accounting' ),
			'manage_options',
			self::PARENT_SLUG,
			null,
			$icon,
			'54.5'
		);
		$admin_page_hooks['ever-accounting'] = 'ever-accounting';

		add_submenu_page(
			self::PARENT_SLUG,
			__( 'Dashboard', 'wp-ever-accounting' ),
			__( 'Dashboard', 'wp-ever-accounting' ),
			'manage_options',
			self::PARENT_SLUG,
			function () {
				$page_hook = 'dashboard';
				$tabs      = array();
				include_once __DIR__ . '/views/dashboard.php';
			},
		);

		$submenus = Utilities::get_menus();

		usort(
			$submenus,
			function ( $a, $b ) {
				$a = isset( $a['position'] ) ? $a['position'] : PHP_INT_MAX;
				$b = isset( $b['position'] ) ? $b['position'] : PHP_INT_MAX;

				return $a - $b;
			}
		);
		foreach ( $submenus as $submenu ) {
			$submenu = wp_parse_args(
				$submenu,
				array(
					'page_title' => '',
					'menu_title' => '',
					'capability' => 'manage_options',
					'menu_slug'  => '',
					'callback'   => null,
					'position'   => '10',
					'page_hook'  => null,
					'tabs'       => array(),
					'load_hook'  => null,
				)
			);
			if ( ! is_callable( $submenu['callback'] ) && ! empty( $submenu['page_hook'] ) ) {
				$submenu['callback'] = function () use ( $submenu ) {
					$page_hook = $submenu['page_hook'];
					$tabs      = $submenu['tabs'];
					include_once __DIR__ . '/views/admin-page.php';
				};
			}
			$load = add_submenu_page(
				self::PARENT_SLUG,
				$submenu['page_title'],
				$submenu['menu_title'],
				$submenu['capability'],
				$submenu['menu_slug'],
				$submenu['callback'],
				$submenu['position']
			);
			if ( ! empty( $submenu['load_hook'] ) && is_callable( $submenu['load_hook'] ) ) {
				add_action( 'load-' . $load, $submenu['load_hook'] );
			}
		}
	}

	/**
	 * Setup screen.
	 *
	 * @since 1.0.0
	 */
	public function setup_screen() {
		$screen = get_current_screen();
		// If we are not on our plugin's screen, exit.
		if ( empty( $screen ) || ! in_array( $screen->id, Utilities::get_screen_ids(), true ) ) {
			return;
		}
		$tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		$this->current_tab = $tab;
	}

	/**
	 * Set screen option.
	 *
	 * @param mixed  $status Screen option value. Default false.
	 * @param string $option Option name.
	 * @param mixed  $value New option value.
	 *
	 * @since 1.0.0
	 * @return mixed
	 */
	public function screen_option( $status, $option, $value ) {
		$options = apply_filters(
			'ever_accounting_screen_options',
			array(
				'eac_items_per_page',
				'eac_accounts_per_page',
				'eac_categories_per_page',
				'eac_currencies_per_page',
				'eac_taxes_per_page',
				'eac_revenues_per_page',
				'eac_invoices_per_page',
				'eac_customers_per_page',
				'eac_payments_per_page',
				'eac_bills_per_page',
				'eac_vendors_per_page',
			)
		);
		if ( in_array( $option, $options, true ) ) {
			return $value;
		}

		return $status;
	}

	/**
	 * Current screen.
	 *
	 * @since 1.0.0
	 */
	public function setup_list_table() {
		$screen = get_current_screen();
		if ( Utilities::is_add_screen() || Utilities::is_edit_screen() || Utilities::is_view_screen() || ! in_array( $screen->id, Utilities::get_screen_ids(), true ) ) {
			return;
		}
		$args = array(
			'label'   => __( 'Per page', 'wp-ever-accounting' ),
			'default' => 20,
		);
		$page = sanitize_key( implode( '-', array_filter( array( $screen->id, $this->current_tab ) ) ) );
		$page = preg_replace( '/^.*?eac-/', 'eac-', $page );
		switch ( $page ) {
			case 'eac-items':
			case 'eac-items-items':
				$this->list_table = new ListTables\ItemsTable();
				$this->list_table->prepare_items();
				$args['option'] = 'eac_items_per_page';
				add_screen_option( 'per_page', $args );
				break;
			case 'eac-items-categories':
				$this->list_table = new ListTables\CategoriesTable();
				$this->list_table->prepare_items();
				$args['option'] = 'eac_items_categories_per_page';
				add_screen_option( 'per_page', $args );
				break;
			case 'eac-sales':
			case 'eac-sales-revenues':
				$this->list_table = new ListTables\RevenuesTable();
				$this->list_table->prepare_items();
				$args['option'] = 'eac_sales_revenues_per_page';
				add_screen_option( 'per_page', $args );
				break;
			case 'eac-sales-invoices':
				$this->list_table = new ListTables\InvoicesTable();
				$this->list_table->prepare_items();
				$args['option'] = 'eac_sales_invoices_per_page';
				add_screen_option( 'per_page', $args );
				break;
			case 'eac-sales-customers':
				$this->list_table = new ListTables\CustomersTable();
				$this->list_table->prepare_items();
				$args['option'] = 'eac_customers_per_page';
				add_screen_option( 'per_page', $args );
				break;
			case 'eac-purchases':
			case 'eac-purchases-payments':
				$this->list_table = new ListTables\PaymentsTable();
				$this->list_table->prepare_items();
				$args['option'] = 'eac_purchases_payments_per_page';
				add_screen_option( 'per_page', $args );
				break;
			case 'eac-purchases-bills':
				$this->list_table = new ListTables\BillsTable();
				$this->list_table->prepare_items();
				$args['option'] = 'eac_purchases_bills_per_page';
				add_screen_option( 'per_page', $args );
				break;
			case 'eac-purchases-vendors':
				$this->list_table = new ListTables\VendorsTable();
				$this->list_table->prepare_items();
				$args['option'] = 'eac_purchases_vendors_per_page';
				add_screen_option( 'per_page', $args );
				break;
			case 'eac-banking':
			case 'eac-banking-accounts':
				$this->list_table = new ListTables\AccountsTable();
				$this->list_table->prepare_items();
				$args['option'] = 'eac_accounts_per_page';
				add_screen_option( 'per_page', $args );
				break;
			case 'eac-misc':
			case 'eac-misc-categories':
				$this->list_table = new ListTables\CategoriesTable();
				$this->list_table->prepare_items();
				$args['option'] = 'eac_categories_per_page';
				add_screen_option( 'per_page', $args );
				break;
			case 'eac-misc-currencies':
				$this->list_table = new ListTables\CurrenciesTable();
				$this->list_table->prepare_items();
				$args['option'] = 'eac_currencies_per_page';
				add_screen_option( 'per_page', $args );
				break;
			case 'eac-misc-taxes':
				$this->list_table = new ListTables\TaxesTable();
				$this->list_table->prepare_items();
				$args['option'] = 'eac_taxes_per_page';
				add_screen_option( 'per_page', $args );
				break;
		}
	}

	/**
	 * Items Tab.
	 *
	 * @since 1.0.0
	 */
	public function render_items_tab() {
		$edit = Utilities::is_edit_screen();
		$item = new Item( $edit );
		if ( ! empty( $edit ) && ! $item->exists() ) {
			wp_safe_redirect( remove_query_arg( 'edit' ) );
			exit();
		}
		if ( Utilities::is_add_screen() ) {
			include __DIR__ . '/views/items/items/add.php';
		} elseif ( $edit ) {
			include __DIR__ . '/views/items/items/edit.php';
		} else {
			include __DIR__ . '/views/items/items/items.php';
		}
	}

	/**
	 * Revenues Tab.
	 *
	 * @since 1.0.0
	 */
	public function render_revenues_tab() {
		$edit    = Utilities::is_edit_screen();
		$view    = Utilities::is_view_screen();
		$id      = $edit ?? $view;
		$revenue = new Revenue( $id );
		if ( ! empty( $id ) && ! $revenue->exists() ) {
			wp_safe_redirect( remove_query_arg( 'edit' ) );
			exit();
		}
		if ( Utilities::is_add_screen() ) {
			include __DIR__ . '/views/sales/revenues/add.php';
		} elseif ( $edit ) {
			include __DIR__ . '/views/sales/revenues/edit.php';
		} elseif ( $view ) {
			include __DIR__ . '/views/sales/revenues/view.php';
		} else {
			include __DIR__ . '/views/sales/revenues/revenues.php';
		}
	}

	/**
	 * Invoices Tab.
	 *
	 * @since 1.0.0
	 */
	public function render_invoices_tab() {
		$edit     = Utilities::is_edit_screen();
		$view     = Utilities::is_view_screen();
		$id       = $edit ?? $view;
		$document = new Invoice( $id );
		if ( ! empty( $id ) && ! $document->exists() ) {
			wp_safe_redirect( remove_query_arg( 'edit' ) );
			exit();
		}
		if ( Utilities::is_add_screen() ) {
			include __DIR__ . '/views/sales/invoices/add.php';
		} elseif ( $edit ) {
			include __DIR__ . '/views/sales/invoices/edit.php';
		} elseif ( $view ) {
			include __DIR__ . '/views/sales/invoices/view.php';
		} else {
			include __DIR__ . '/views/sales/invoices/invoices.php';
		}
	}

	/**
	 * Customers Tab.
	 *
	 * @since 1.0.0
	 */
	public function render_customers_tab() {
		$edit     = Utilities::is_edit_screen();
		$view     = Utilities::is_view_screen();
		$id       = $edit ?? $view;
		$customer = new Customer( $edit );
		if ( ! empty( $id ) && ! $customer->exists() ) {
			wp_safe_redirect( remove_query_arg( 'edit' ) );
			exit();
		}
		if ( Utilities::is_add_screen() ) {
			include __DIR__ . '/views/sales/customers/add.php';
		} elseif ( $edit ) {
			include __DIR__ . '/views/sales/customers/edit.php';
		} elseif ( $view ) {
			include __DIR__ . '/views/sales/customers/view.php';
		} else {
			include __DIR__ . '/views/sales/customers/customers.php';
		}
	}

	/**
	 * Payments Tab.
	 *
	 * @since 1.0.0
	 */
	public function render_payments_tab() {
		$edit = Utilities::is_edit_screen();
		// $payment = new Payment( $edit );
		// if ( ! empty( $edit ) && ! $payment->exists() ) {
		// wp_safe_redirect( remove_query_arg( 'edit' ) );
		// exit();
		// }
		if ( Utilities::is_add_screen() ) {
			include __DIR__ . '/views/purchases/payments/add.php';
		} elseif ( $edit ) {
			include __DIR__ . '/views/purchases/payments/edit.php';
		} else {
			include __DIR__ . '/views/purchases/payments/payments.php';
		}
	}

	/**
	 * Bills Tab.
	 *
	 * @since 1.0.0
	 */
	public function render_bills_tab() {
		$edit = Utilities::is_edit_screen();
		// $bill = new Bill( $edit );
		// if ( ! empty( $edit ) && ! $bill->exists() ) {
		// wp_safe_redirect( remove_query_arg( 'edit' ) );
		// exit();
		// }
		if ( Utilities::is_add_screen() ) {
			include __DIR__ . '/views/purchases/bills/add.php';
		} elseif ( $edit ) {
			include __DIR__ . '/views/purchases/bills/edit.php';
		} else {
			include __DIR__ . '/views/purchases/bills/bills.php';
		}
	}

	/**
	 * Vendors Tab.
	 *
	 * @since 1.0.0
	 */
	public function render_vendors_tab() {
		$edit   = Utilities::is_edit_screen();
		$vendor = new Vendor( $edit );
		if ( ! empty( $edit ) && ! $vendor->exists() ) {
			wp_safe_redirect( remove_query_arg( 'edit' ) );
			exit();
		}
		if ( Utilities::is_add_screen() ) {
			include __DIR__ . '/views/purchases/vendors/add.php';
		} elseif ( $edit ) {
			include __DIR__ . '/views/purchases/vendors/edit.php';
		} else {
			include __DIR__ . '/views/purchases/vendors/vendors.php';
		}
	}

	/**
	 * Accounts Tab.
	 *
	 * @since 1.0.0
	 */
	public function render_accounts_tab() {
		$edit    = Utilities::is_edit_screen();
		$account = new Account( $edit );
		if ( ! empty( $edit ) && ! $account->exists() ) {
			wp_safe_redirect( remove_query_arg( 'edit' ) );
			exit();
		}
		if ( Utilities::is_add_screen() ) {
			include __DIR__ . '/views/banking/accounts/add.php';
		} elseif ( $edit ) {
			include __DIR__ . '/views/banking/accounts/edit.php';
		} else {
			include __DIR__ . '/views/banking/accounts/accounts.php';
		}
	}

	/**
	 * Categories Tab.
	 *
	 * @since 1.0.0
	 */
	public function render_categories_tab() {
		$edit     = Utilities::is_edit_screen();
		$category = new Category( $edit );
		if ( ! empty( $edit ) && ! $category->exists() ) {
			wp_safe_redirect( remove_query_arg( 'edit' ) );
			exit();
		}
		if ( Utilities::is_add_screen() ) {
			include __DIR__ . '/views/misc/categories/add.php';
		} elseif ( $edit ) {
			include __DIR__ . '/views/misc/categories/edit.php';
		} else {
			include __DIR__ . '/views/misc/categories/categories.php';
		}
	}

	/**
	 * Currencies Tab.
	 *
	 * @since 1.0.0
	 */
	public function render_currencies_tab() {
		$edit     = Utilities::is_edit_screen();
		$currency = new Currency( $edit );
		if ( ! empty( $edit ) && ! $currency->exists() ) {
			wp_safe_redirect( remove_query_arg( 'edit' ) );
			exit();
		}

		if ( $edit ) {
			include __DIR__ . '/views/misc/currencies/edit.php';

		} else {
			include __DIR__ . '/views/misc/currencies/currencies.php';
		}
	}

	/**
	 * Taxes Tab.
	 *
	 * @since 1.0.0
	 */
	public function render_taxes_tab() {
		$edit = Utilities::is_edit_screen();
		$tax  = new Tax( $edit );
		if ( ! empty( $edit ) && ! $tax->exists() ) {
			wp_safe_redirect( remove_query_arg( 'edit' ) );
			exit();
		}
		if ( Utilities::is_add_screen() ) {
			include __DIR__ . '/views/misc/taxes/add.php';
		} elseif ( $edit ) {
			include __DIR__ . '/views/misc/taxes/edit.php';
		} else {
			include __DIR__ . '/views/misc/taxes/taxes.php';
		}
	}

	/**
	 * Import Tab.
	 *
	 * @since 1.0.0
	 */
	public function render_import_tab() {
		include __DIR__ . '/views/import/customers.php';
		include __DIR__ . '/views/import/vendors.php';
		include __DIR__ . '/views/import/accounts.php';
		include __DIR__ . '/views/import/items.php';
		include __DIR__ . '/views/import/revenues.php';
		include __DIR__ . '/views/import/payments.php';
		include __DIR__ . '/views/import/categories.php';
	}

	/**
	 * Export Tab.
	 *
	 * @since 1.0.0
	 */
	public function render_export_tab() {
		include __DIR__ . '/views/export/items.php';
		include __DIR__ . '/views/export/accounts.php';
		include __DIR__ . '/views/export/customers.php';
		include __DIR__ . '/views/export/categories.php';
		include __DIR__ . '/views/export/vendors.php';

		// TODO: Need to add bellow export options.
//		include __DIR__ . '/views/export/payments.php';
//		include __DIR__ . '/views/export/expenses.php';
	}

	/**
	 * Payments report tab.
	 *
	 * @since 1.0.0
	 */
	public function render_payments_report_tab() {
		include __DIR__ . '/views/reports/payments.php';
	}

	/**
	 * Expenses report tab.
	 *
	 * @since 1.0.0
	 */
	public function render_expenses_report_tab() {
		include __DIR__ . '/views/reports/expenses.php';
	}

	/**
	 * Profits report tab.
	 *
	 * @since 1.0.0
	 */
	public function render_profits_report_tab() {
		include __DIR__ . '/views/reports/profits.php';
	}

	/**
	 * Taxes report tab.
	 *
	 * @since 1.0.0
	 */
	public function render_taxes_report_tab() {
		include __DIR__ . '/views/reports/taxes.php';
	}
}
