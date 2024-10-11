<?php

namespace EverAccounting\Admin;

use EverAccounting\Models\Payment;

defined( 'ABSPATH' ) || exit;

/**
 * Class Payments
 *
 * @package EverAccounting\Admin\Sales
 */
class Payments {

	/**
	 * Payments constructor.
	 */
	public function __construct() {
		add_filter( 'eac_sales_page_tabs', array( __CLASS__, 'register_tabs' ) );
		add_action( 'load_eac_sales_page_payments', array( __CLASS__, 'setup_page' ) );
		add_action( 'eac_sales_page_payments', array( __CLASS__, 'render_page' ) );
		add_action( 'eac_payment_meta_boxes_core', array( __CLASS__, 'form_fields' ), - 1, 2 );
		add_action( 'eac_payment_meta_boxes_side', array( __CLASS__, 'payment_actions' ), - 1, 2 );
		add_action( 'eac_payment_meta_boxes_side', array( __CLASS__, 'payment_actions' ), - 1, 2 );
	}

	/**
	 * Register tab.
	 *
	 * @param array $tabs Tabs.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public static function register_tabs( $tabs ) {
		$tabs['payments'] = __( 'Payments', 'wp-ever-accounting' );

		return $tabs;
	}

	/**
	 * setup page.
	 *
	 * @param string $action Current action.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public static function setup_page( $action ) {
		global $list_table;
		$id = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );

		// if id set but payment not found, redirect to payments list.
		if ( ! empty( $id ) && empty( EAC()->payments->get( $id ) ) ) {
			wp_safe_redirect( remove_query_arg( array( 'id', 'action' ) ) );
			exit;
		}

		if ( ! in_array( $action, array( 'add', 'edit', 'view' ), true ) ) {
			$screen     = get_current_screen();
			$list_table = new ListTables\Payments();
			$list_table->prepare_items();
			$screen->add_option(
				'per_page',
				array(
					'label'   => __( 'Number of items per page:', 'wp-ever-accounting' ),
					'default' => 20,
					'option'  => 'eac_payments_per_page',
				)
			);
		}
	}

	/**
	 * Render page.
	 *
	 * @param string $action Current action.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public static function render_page( $action ) {
		$id     = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );

		if ( in_array( $action, array( 'add', 'edit', 'view' ), true ) ) {
			$payment = Payment::make( $id );
			include __DIR__ . '/views/payment-view.php';
		} else {
			include __DIR__ . '/views/payment-list.php';
		}
	}

	/**
	 * Payment attributes.
	 *
	 * @param Payment $payment Payment object.
	 * @param string  $action Current action.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public static function render_fields( $payment, $action ) {
		?>
		<div class="eac-card">
			<div class="eac-card__header">
				<h3 class="eac-card__title"><?php esc_html_e( 'Payment Attributes', 'wp-ever-accounting' ); ?></h3>
			</div>
			<div class="eac-card__body grid--fields">
				<?php
				eac_form_field(
					array(
						'label'       => __( 'Date', 'wp-ever-accounting' ),
						'type'        => 'date',
						'name'        => 'date',
						'placeholder' => 'yyyy-mm-dd',
						'value'       => $payment->date,
						'required'    => true,
						'class'       => 'eac_datepicker',
					)
				);

				eac_form_field(
					array(
						'label'       => __( 'Payment #', 'wp-ever-accounting' ),
						'type'        => 'text',
						'name'        => 'payment_number',
						'value'       => $payment->number,
						'default'     => $payment->get_next_number(),
						'placeholder' => $payment->get_next_number(),
						'required'    => true,
						'readonly'    => true,
					)
				);

				eac_form_field(
					array(
						'label'            => __( 'Account', 'wp-ever-accounting' ),
						'type'             => 'select',
						'name'             => 'account_id',
						'options'          => array( $payment->account ),
						'value'            => $payment->account_id,
						'class'            => 'eac_select2',
						'tooltip'          => __( 'Select the account.', 'wp-ever-accounting' ),
						'option_value'     => 'id',
						'option_label'     => 'formatted_name',
						'data-placeholder' => __( 'Select an account', 'wp-ever-accounting' ),
						'data-action'      => 'eac_json_search',
						'data-type'        => 'account',
						'required'         => true,
						'suffix'           => sprintf(
							'<a class="addon" href="%s" target="_blank" title="%s"><span class="dashicons dashicons-plus"></span></a>',
							esc_url( admin_url( 'admin.php?page=eac-banking&tab=accounts&action=add' ) ),
							__( 'Add Account', 'wp-ever-accounting' )
						),
					)
				);

				// exchange rate.
				eac_form_field(
					array(
						'label'       => __( 'Exchange Rate', 'wp-ever-accounting' ),
						'type'        => 'number',
						'name'        => 'exchange_rate',
						'value'       => $payment->exchange_rate,
						'placeholder' => '1.00',
						'required'    => true,
						'class'       => 'eac_exchange_rate',
						'prefix'      => '1 ' . eac_base_currency() . ' = ',
						'attr-step'   => 'any',
					)
				);

				eac_form_field(
					array(
						'label'         => __( 'Amount', 'wp-ever-accounting' ),
						'name'          => 'amount',
						'placeholder'   => '0.00',
						'value'         => $payment->amount,
						'required'      => true,
						'tooltip'       => __( 'Enter the amount in the currency of the selected account, use (.) for decimal.', 'wp-ever-accounting' ),
						'data-currency' => $payment->currency,
						'class'         => 'eac_amount',
					)
				);

				eac_form_field(
					array(
						'label'            => __( 'Category', 'wp-ever-accounting' ),
						'type'             => 'select',
						'name'             => 'category_id',
						'value'            => $payment->category_id,
						'options'          => array( $payment->category ),
						'option_value'     => 'id',
						'option_label'     => 'formatted_name',
						'placeholder'      => __( 'Select category', 'wp-ever-accounting' ),
						'class'            => 'eac_select2',
						'data-placeholder' => __( 'Select category', 'wp-ever-accounting' ),
						'data-action'      => 'eac_json_search',
						'data-type'        => 'category',
						'data-subtype'     => 'payment',
						'suffix'           => sprintf(
							'<a class="addon" href="%s" target="_blank" title="%s"><span class="dashicons dashicons-plus"></span></a>',
							esc_url( admin_url( 'admin.php?page=eac-misc&tab=categories&action=add&type=income' ) ),
							__( 'Add Category', 'wp-ever-accounting' )
						),
					)
				);

				eac_form_field(
					array(
						'label'            => __( 'Customer', 'wp-ever-accounting' ),
						'type'             => 'select',
						'name'             => 'contact_id',
						'options'          => array( $payment->customer ),
						'value'            => $payment->customer_id,
						'class'            => 'eac_select2',
						'tooltip'          => __( 'Select the customer.', 'wp-ever-accounting' ),
						'option_value'     => 'id',
						'option_label'     => 'formatted_name',
						'data-placeholder' => __( 'Select a customer', 'wp-ever-accounting' ),
						'data-action'      => 'eac_json_search',
						'data-type'        => 'customer',
						'suffix'           => sprintf(
							'<a class="addon" href="%s" target="_blank" title="%s"><span class="dashicons dashicons-plus"></span></a>',
							esc_url( admin_url( 'admin.php?page=eac-purchases&tab=customers&action=add' ) ),
							__( 'Add Vendor', 'wp-ever-accounting' )
						),
					)
				);

				eac_form_field(
					array(
						'label'       => __( 'Payment Method', 'wp-ever-accounting' ),
						'type'        => 'select',
						'name'        => 'mode',
						'value'       => $payment->mode,
						'options'     => eac_get_payment_methods(),
						'placeholder' => __( 'Select &hellip;', 'wp-ever-accounting' ),
					)
				);

				eac_form_field(
					array(
						'label'            => __( 'Invoice', 'wp-ever-accounting' ),
						'type'             => 'select',
						'name'             => 'invoice_id',
						'value'            => $payment->document_id,
						'options'          => array( $payment->document ),
						'option_value'     => 'id',
						'option_label'     => 'formatted_name',
						'placeholder'      => __( 'Select invoice', 'wp-ever-accounting' ),
						'class'            => 'eac_select2',
						'data-placeholder' => __( 'Select invoice', 'wp-ever-accounting' ),
						'data-action'      => 'eac_json_search',
						'data-type'        => 'invoice',
					)
				);

				eac_form_field(
					array(
						'label'       => __( 'Reference', 'wp-ever-accounting' ),
						'type'        => 'text',
						'name'        => 'reference',
						'value'       => $payment->reference,
						'placeholder' => __( 'Enter reference', 'wp-ever-accounting' ),
					)
				);
				eac_form_field(
					array(
						'label'         => __( 'Note', 'wp-ever-accounting' ),
						'type'          => 'textarea',
						'name'          => 'note',
						'value'         => $payment->note,
						'placeholder'   => __( 'Enter description', 'wp-ever-accounting' ),
						'wrapper_class' => 'is--full',
					)
				);
				?>
			</div>
		</div>
		<?php
	}
}
