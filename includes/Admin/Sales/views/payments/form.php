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
				<div class="grid--fields">
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
							'label'       => __( 'Payment Number', 'wp-ever-accounting' ),
							'type'        => 'text',
							'name'        => 'payment_number',
							'value'       => $payment->number,
							'default'     => $payment->get_next_number(),
							'placeholder' => $payment->get_next_number(),
							'required'    => true,
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
							'data-currency' => $payment->currency_code,
						)
					);

					eac_form_field(
						array(
							'label'            => esc_html__( 'Currency', 'wp-ever-accounting' ),
							'name'             => 'currency_code',
							'default'          => eac_base_currency(),
							'value'            => $payment->currency_code,
							'type'             => 'select',
							'options'          => eac_get_currencies(),
							'option_value'     => 'code',
							'option_label'     => 'formatted_name',
							'placeholder'      => esc_html__( 'Select a currency', 'wp-ever-accounting' ),
							'class'            => 'eac_select2',
							'data-action'      => 'eac_json_search',
							'data-type'        => 'currency',
							'data-allow-clear' => 'false',
							'required'         => true,
						)
					);

					eac_form_field(
						array(
							'label'         => __( 'Exchange Rate', 'wp-ever-accounting' ),
							'type'          => 'number',
							'name'          => 'exchange_rate',
							'default'       => 1,
							'value'         => $payment->exchange_rate,
							'placeholder'   => '0.00',
							'tooltip'       => __( 'Enter the exchange rate for the selected currency.', 'wp-ever-accounting' ),
							'required'      => true,
							'prefix'        => "1 $payment->currency_code = ",
							'suffix'        => eac_base_currency(),
							'wrapper_style' => eac_base_currency() === $payment->currency_code ? 'display: none;' : '',
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
							'suffix'           => sprintf(
								'<a class="addon" href="%s" target="_blank" title="%s"><span class="dashicons dashicons-plus"></span></a>',
								esc_url( admin_url( 'admin.php?page=eac-banking&tab=accounts&add=yes' ) ),
								__( 'Add Account', 'wp-ever-accounting' )
							)
						)
					);

					eac_form_field(
						array(
							'label'            => __( 'Vendor', 'wp-ever-accounting' ),
							'type'             => 'select',
							'name'             => 'vendor_id',
							'options'          => array( $payment->vendor ),
							'value'            => $payment->vendor_id,
							'class'            => 'eac_select2',
							'tooltip'          => __( 'Select the vendor.', 'wp-ever-accounting' ),
							'option_value'     => 'id',
							'option_label'     => 'formatted_name',
							'data-placeholder' => __( 'Select a vendor', 'wp-ever-accounting' ),
							'data-action'      => 'eac_json_search',
							'data-type'        => 'vendor',
							'suffix'           => sprintf(
								'<a class="addon" href="%s" target="_blank" title="%s"><span class="dashicons dashicons-plus"></span></a>',
								esc_url( admin_url( 'admin.php?page=eac-purchases&tab=vendors&add=yes' ) ),
								__( 'Add Vendor', 'wp-ever-accounting' )
							),
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
							'label'       => __( 'Payment Method', 'wp-ever-accounting' ),
							'type'        => 'select',
							'name'        => 'payment_method',
							'value'       => $payment->payment_method,
							'options'     => eac_get_payment_methods(),
							'placeholder' => __( 'Select &hellip;', 'wp-ever-accounting' ),
						)
					);

					eac_form_field(
						array(
							'label'            => __( 'Bill', 'wp-ever-accounting' ),
							'type'             => 'select',
							'name'             => 'bill_id',
							'value'            => $payment->document_id,
							'options'          => array( $payment->document ),
							'option_value'     => 'id',
							'option_label'     => 'formatted_name',
							'placeholder'      => __( 'Select bill', 'wp-ever-accounting' ),
							'class'            => 'eac_select2',
							'data-placeholder' => __( 'Select bill', 'wp-ever-accounting' ),
							'data-action'      => 'eac_json_search',
							'data-type'        => 'bill',
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
							'label'         => __( 'Notes', 'wp-ever-accounting' ),
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
						<input type="hidden" name="account_id"
							   value="<?php echo esc_attr( $payment->account_id ); ?>"/>
						<input type="hidden" name="id" value="<?php echo esc_attr( $payment->id ); ?>"/>
					<?php endif; ?>
					<input type="hidden" name="action" value="eac_edit_payment"/>
					<?php wp_nonce_field( 'eac_edit_payment' ); ?>
					<?php if ( $payment->exists() ) : ?>
						<a class="eac_confirm_delete del" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'action', 'delete', admin_url( 'admin.php?page=eac-sales&tab=payments&id=' . $payment->id ) ), 'bulk-payments' ) ); ?>">
							<?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?>
						</a>
					<?php endif; ?>
					<?php if ( $payment->exists() ) : ?>
						<button
							class="button button-primary tw-h-full"><?php esc_html_e( 'Update Payment', 'wp-ever-accounting' ); ?></button>
					<?php else : ?>
						<button
							class="button button-primary button-large tw-w-full"><?php esc_html_e( 'Add Payment', 'wp-ever-accounting' ); ?></button>
					<?php endif; ?>
				</div><!-- .eac-card__footer -->

			</div>

		</div><!-- .column-2 -->
	</div><!-- .eac-poststuff -->
</form>
