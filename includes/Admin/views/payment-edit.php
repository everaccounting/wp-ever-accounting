<?php
/**
 * Payment Edit view.
 *
 * This page handles the views of the payment edit page.
 *
 * @since 1.0.0
 *
 * @subpackage EverAccounting/Admin/Views
 * @package EverAccounting
 * @var Payment $payment Payment object.
 */

use EverAccounting\Models\Payment;

defined( 'ABSPATH' ) || exit;

$id      = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );
$payment = Payment::make( $id );

?>
<div class="eac-section-header">
	<h1 class="wp-heading-inline">
		<?php if ( $payment->exists() ) : ?>
			<?php esc_html_e( 'Edit Payment', 'wp-ever-accounting' ); ?>
		<?php else : ?>
			<?php esc_html_e( 'Add Payment', 'wp-ever-accounting' ); ?>
		<?php endif; ?>
		<a href="<?php echo esc_attr( remove_query_arg( array( 'action', 'id' ) ) ); ?>" title="<?php esc_attr_e( 'Go back', 'wp-ever-accounting' ); ?>">
			<span class="dashicons dashicons-undo"></span>
		</a>
	</h1>

	<?php if ( $payment->exists() ) : ?>
		<a class="button" href="<?php echo esc_url( add_query_arg( array( 'action' => 'view' ) ) ); ?>">
			<?php esc_html_e( 'View Payment', 'wp-ever-accounting' ); ?>
		</a>
	<?php endif; ?>
</div>

<form id="eac-edit-payment" name="payment" method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">

	<div class="eac-poststuff">
		<div class="column-1">
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
							'name'        => 'payment_date',
							'value'       => $payment->payment_date,
							'placeholder' => 'yyyy-mm-dd',
							'class'       => 'eac_datepicker',
							'required'    => true,
						)
					);

					eac_form_field(
						array(
							'label'       => __( 'Payment #', 'wp-ever-accounting' ),
							'type'        => 'text',
							'name'        => 'payment_number',
							'value'       => $payment->number,
							'placeholder' => $payment->get_next_number(),
							'default'     => $payment->get_next_number(),
							'readonly'    => true,
							'required'    => true,
						)
					);

					eac_form_field(
						array(
							'label'            => __( 'Account', 'wp-ever-accounting' ),
							'type'             => 'select',
							'name'             => 'account_id',
							'value'            => $payment->account_id,
							'options'          => array( $payment->account ),
							'option_value'     => 'id',
							'option_label'     => 'formatted_name',
							'class'            => 'eac_select2',
							'data-placeholder' => __( 'Select an account', 'wp-ever-accounting' ),
							'data-action'      => 'eac_json_search',
							'data-type'        => 'account',
							'required'         => true,
							'suffix'           => sprintf(
								'<a class="addon" href="%s" target="_blank" title="%s"><span class="dashicons dashicons-plus"></span></a>',
								esc_url( admin_url( 'admin.php?page=eac-banking&tab=accounts&action=add' ) ),
								__( 'Add Account', 'wp-ever-accounting' )
							),
							'tooltip'          => __( 'Select the account.', 'wp-ever-accounting' ),
						)
					);

					eac_form_field(
						array(
							'label'         => __( 'Exchange Rate', 'wp-ever-accounting' ),
							'name'          => 'exchange_rate',
							'value'         => $payment->exchange_rate,
							'default'       => 1,
							'placeholder'   => '1.00',
							'class'         => 'eac_exchange_rate',
							'required'      => true,
							'prefix'        => '1 ' . eac_base_currency() . ' = ',
							'attr-step'     => 'any',
							'readonly'      => $payment->currency === eac_base_currency(),
							'data-currency' => $payment->currency,
						)
					);

					eac_form_field(
						array(
							'label'         => __( 'Amount', 'wp-ever-accounting' ),
							'name'          => 'amount',
							'value'         => $payment->amount,
							'placeholder'   => '0.00',
							'class'         => 'eac_amount',
							'required'      => true,
							'tooltip'       => __( 'Enter the amount in the currency of the selected account, use (.) for decimal.', 'wp-ever-accounting' ),
							'data-currency' => $payment->currency,
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
							'class'            => 'eac_select2',
							'placeholder'      => __( 'Select category', 'wp-ever-accounting' ),
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
							'value'            => $payment->customer_id,
							'options'          => array( $payment->customer ),
							'option_value'     => 'id',
							'option_label'     => 'formatted_name',
							'class'            => 'eac_select2',
							'data-placeholder' => __( 'Select a customer', 'wp-ever-accounting' ),
							'data-action'      => 'eac_json_search',
							'data-type'        => 'customer',
							'suffix'           => sprintf(
								'<a class="addon" href="%s" target="_blank" title="%s"><span class="dashicons dashicons-plus"></span></a>',
								esc_url( admin_url( 'admin.php?page=eac-purchases&tab=customers&action=add' ) ),
								__( 'Add Vendor', 'wp-ever-accounting' )
							),
							'tooltip'          => __( 'Select the customer.', 'wp-ever-accounting' ),
						)
					);

					eac_form_field(
						array(
							'label'       => __( 'Payment Method', 'wp-ever-accounting' ),
							'type'        => 'select',
							'name'        => 'payment_method',
							'value'       => $payment->payment_method,
							'options'     => eac_get_payment_methods(),
							'placeholder' => __( 'Select &hellip;', 'wp-ever-accounting' ),
						)
					);

					if ( $payment->invoice_id ) {
						// readonly select field.
						eac_form_field(
							array(
								'label'    => __( 'Invoice', 'wp-ever-accounting' ),
								'type'     => 'text',
								'name'     => 'invoice',
								'value'    => $payment->invoice->number,
								'readonly' => true,
							)
						);
						printf( '<input type="hidden" name="invoice_id" value="%d">', esc_attr( $payment->document_id ) );
					}

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
			/**
			 * Fires action to inject custom meta boxes in the main column.
			 *
			 * @param Payment $payment Payment object.
			 *
			 * @since 1.0.0
			 */
			do_action( 'eac_payment_edit_core_meta_boxes', $payment );
			?>
		</div>
		<div class="column-2">

			<div class="eac-card">
				<div class="eac-card__header">
					<h3 class="eac-card__title"><?php esc_html_e( 'Save', 'wp-ever-accounting' ); ?></h3>
				</div>
				<div class="eac-card__body">
					<?php
					eac_form_field(
						array(
							'label'       => __( 'Status', 'wp-ever-accounting' ),
							'type'        => 'select',
							'id'          => 'status',
							'options'     => EAC()->payments->get_statuses(),
							'value'       => $payment->status,
							'placeholder' => __( 'Select status', 'wp-ever-accounting' ),
							'required'    => true,
						)
					);

					/**
					 * Fires to add custom actions.
					 *
					 * @param Payment $payment Payment object.
					 *
					 * @since 2.0.0
					 */
					do_action( 'eac_payment_edit_misc_actions', $payment );
					?>
				</div>

				<div class="eac-card__footer">
					<?php if ( $payment->exists() ) : ?>
						<a class="del del_confirm" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'action', 'delete', $payment->get_edit_url() ), 'bulk-payments' ) ); ?>">
							<?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?>
						</a>
						<button class="button button-primary"><?php esc_html_e( 'Update Payment', 'wp-ever-accounting' ); ?></button>
					<?php else : ?>
						<button class="button button-primary button-large tw-w-full"><?php esc_html_e( 'Add Payment', 'wp-ever-accounting' ); ?></button>
					<?php endif; ?>
				</div>
			</div><!-- .eac-card -->

			<?php
			/**
			 * Fires action to inject custom meta boxes in the side column.
			 *
			 * @param Payment $payment Payment object.
			 *
			 * @since 1.0.0
			 */
			do_action( 'eac_payment_edit_side_meta_boxes', $payment );
			?>

		</div><!-- .column-2 -->
	</div><!-- .eac-poststuff -->

	<?php wp_nonce_field( 'eac_edit_payment' ); ?>
	<input type="hidden" name="action" value="eac_edit_payment"/>
	<input type="hidden" name="id" value="<?php echo esc_attr( $payment->id ); ?>"/>
</form>
