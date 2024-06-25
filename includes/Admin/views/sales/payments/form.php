<?php
/**
 * Admin Payment Form.
 * Page: Sales
 * Tab: Payment
 *
 * @package EverAccounting
 * @version 1.0.0
 * @var $payment \EverAccounting\Models\Payment Payment object.
 */

defined( 'ABSPATH' ) || exit;
?>
	<form id="eac-payment-form" method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
		<div class="eac-poststuff">
			<div class="column-1">
				<div class="eac-card">
					<div class="eac-card__header">
						<h2 class="eac-card__title"><?php esc_html_e( 'Payment details', 'wp-ever-accounting' ); ?></h2>
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
								'label'            => __( 'Account', 'wp-ever-accounting' ),
								'type'             => 'select',
								'name'             => 'account_id',
								'options'          => array( $payment->account ),
								'value'            => $payment->account_id,
								'required'         => true,
								'class'            => 'eac_select2',
								'tooltip'          => __( 'Select the receiving account.', 'wp-ever-accounting' ),
								'disabled'         => $payment->exists(),
								'option_value'     => 'id',
								'option_label'     => 'formatted_name',
								'data-placeholder' => __( 'Select an account', 'wp-ever-accounting' ),
								'data-action'      => 'eac_json_search',
								'data-type'        => 'account',
								'suffix'           => sprintf(
									'<a class="addon" href="%s" target="_blank" title="%s"><span class="dashicons dashicons-plus"></span></a>',
									esc_url( admin_url( 'admin.php?page=eac-banking&tab=accounts&add=yes' ) ),
									__( 'Add Account', 'wp-ever-accounting' )
								),
							)
						);
						eac_form_field(
							array(
								'label'          => __( 'Amount', 'wp-ever-accounting' ),
								'name'           => 'amount',
								'placeholder'    => '0.00',
								'value'          => $payment->amount,
								'required'       => true,
								'class'          => 'eac_inputmask',
								'tooltip'        => __( 'Enter the amount in the currency of the selected account, use (.) for decimal.', 'wp-ever-accounting' ),
								'data-inputmask' => '"alias": "decimal","placeholder": "0.00", "rightAlign": false',

							)
						);
						eac_form_field(
							array(
								'label'            => __( 'Customer', 'wp-ever-accounting' ),
								'type'             => 'select',
								'name'             => 'contact_id',
								'value'            => $payment->contact_id,
								'options'          => array( $payment->customer ),
								'option_value'     => 'id',
								'option_label'     => 'formatted_name',
								'default'          => filter_input( INPUT_GET, 'customer_id', FILTER_SANITIZE_NUMBER_INT ),
								'disabled'         => $payment->exists() && $payment->contact_id,
								'data-placeholder' => __( 'Select customer', 'wp-ever-accounting' ),
								'data-action'      => 'eac_json_search',
								'data-type'        => 'customer',
								'class'            => 'eac_select2',
								'suffix'           => sprintf(
									'<a class="button" href="%s" target="_blank" title="%s"><span class="dashicons dashicons-plus"></span></a>',
									esc_url( admin_url( 'admin.php?page=eac-sales&tab=customers&add=yes' ) ),
									__( 'Add customer', 'wp-ever-accounting' )
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
								'data-subtype'     => 'income',
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
						$invoices = array();
						eac_form_field(
							array(
								'label'            => __( 'Invoice', 'wp-ever-accounting' ),
								'type'             => 'select',
								'name'             => 'document_id',
								'value'            => $payment->document_id,
								'default'          => filter_input( INPUT_GET, 'document_id', FILTER_SANITIZE_NUMBER_INT ),
								'options'          => wp_list_pluck( $payment->invoice, 'formatted_name', 'id' ),
								'placeholder'      => __( 'Select invoice', 'wp-ever-accounting' ),
								'disabled'         => $payment->exists() && $payment->document_id,
								'class'            => 'eac_select2',
								'data-placeholder' => __( 'Select invoice', 'wp-ever-accounting' ),
								'tooltip'          => __( 'Select the invoice related to this payment., ignore if not applicable.', 'wp-ever-accounting' ),
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
					<div class="eac-card__header">
						<h2 class="eac-card__title"><?php esc_html_e( 'Actions', 'wp-ever-accounting' ); ?></h2>
					</div>
					<div class="eac-card__body">
						<?php
						eac_form_field(
							array(
								'label'       => __( 'Status', 'wp-ever-accounting' ),
								'type'        => 'select',
								'id'          => 'status',
								'options'     => \EverAccounting\Models\Payment::get_statuses(),
								'value'       => $payment->status,
								'placeholder' => __( 'Select status', 'wp-ever-accounting' ),
							)
						);
						?>
					</div>
					<div class="eac-card__footer">
						<?php if ( $payment->exists() ) : ?>
							<input type="hidden" name="account_id" value="<?php echo esc_attr( $payment->account_id ); ?>"/>
							<input type="hidden" name="id" value="<?php echo esc_attr( $payment->id ); ?>"/>
						<?php endif; ?>
						<input type="hidden" name="action" value="eac_edit_payment"/>
						<?php wp_nonce_field( 'eac_edit_payment' ); ?>
						<?php if ( $payment->exists() ) : ?>
							<a class="eac_confirm_delete del" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'action', 'delete', admin_url( 'admin.php?page=eac-sales&tab=payments&id=' . $payment->id ) ), 'bulk-payments' ) ); ?>"><?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?></a>
						<?php endif; ?>
						<?php if ( $payment->exists() ) : ?>
							<button class="button button-primary"><?php esc_html_e( 'Update Payment', 'wp-ever-accounting' ); ?></button>
						<?php else : ?>
							<button class="button button-primary eac-w-100"><?php esc_html_e( 'Add Payment', 'wp-ever-accounting' ); ?></button>
						<?php endif; ?>
					</div>
				</div>

				<div class="eac-card">
					<div class="eac-card__header">
						<h2 class="eac-card__title"><?php esc_html_e( 'Attachment', 'wp-ever-accounting' ); ?></h2>
					</div>
					<div class="eac-card__body">
						<?php
						eac_form_field(
							array(
								'type'        => 'file',
								'name'        => 'attachment',
								'value'       => '113',
								'placeholder' => __( 'Select file', 'wp-ever-accounting' ),
								'tooltip'     => __( 'Upload a file related to this payment.', 'wp-ever-accounting' ),
							)
						);
						?>
					</div>
				</div>


			</div><!-- .column-2 -->

		</div><!-- .eac-poststuff -->
	</form>
<?php
