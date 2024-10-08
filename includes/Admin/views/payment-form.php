<?php
/**
 * Payment form.
 *
 * @package EverAccounting
 * @version 1.0.0
 * @var $payment \EverAccounting\Models\Payment  Payment model.
 */

defined( 'ABSPATH' ) || exit;
?>
<form id="eac-payment-form" method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
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
								esc_url( admin_url( 'admin.php?page=eac-banking&tab=accounts&add=yes' ) ),
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
								esc_url( admin_url( 'admin.php?page=eac-misc&tab=categories&add=yes&type=income' ) ),
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

		</div><!-- .column-1 -->
		<div class="column-2">

			<div class="eac-card">

				<div class="eac-card__header">
					<h3 class="eac-card__title"><?php esc_html_e( 'Actions', 'wp-ever-accounting' ); ?></h3>
				</div><!-- .eac-card__header -->

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
					?>
				</div><!-- .eac-card__body -->
				<div class="eac-card__footer">
					<?php if ( $payment->exists() ) : ?>
						<a class="eac_confirm_delete del" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'action', 'delete', admin_url( 'admin.php?page=eac-sales&tab=payments&id=' . $payment->id ) ), 'bulk-payments' ) ); ?>">
							<?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?>
						</a>
						<button class="button button-primary tw-h-full"><?php esc_html_e( 'Update Payment', 'wp-ever-accounting' ); ?></button>
					<?php else : ?>
						<button class="button button-primary button-large tw-w-full"><?php esc_html_e( 'Add Payment', 'wp-ever-accounting' ); ?></button>
					<?php endif; ?>
				</div><!-- .eac-card__footer -->
			</div><!-- .eac-card -->

			<div class="eac-card">
				<div class="eac-card__header">
					<h3 class="eac-card__title"><?php esc_html_e( 'Attachment', 'wp-ever-accounting' ); ?></h3>
				</div>
				<div class="eac-card__body">
					<?php eac_file_uploader( array( 'value' => $payment->attachment_id ) ); ?>
				</div>
			</div>


		</div><!-- .column-2 -->
	</div><!-- .eac-poststuff -->

	<?php wp_nonce_field( 'eac_edit_payment' ); ?>
	<input type="hidden" name="action" value="eac_edit_payment"/>
	<input type="hidden" name="id" value="<?php echo esc_attr( $payment->id ); ?>"/>
</form>
