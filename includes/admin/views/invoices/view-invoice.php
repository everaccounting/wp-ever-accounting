<?php
/**
 * Render Single invoice
 *
 * Page: Sales
 * Tab: Invoices
 *
 * @since       1.1.0
 * @subpackage  Admin/Views/Invoices
 * @package     EverAccounting
 *
 * @var int $invoice_id
 */

defined( 'ABSPATH' ) || exit();

try {
	$invoice = new \EverAccounting\Models\Invoice( $invoice_id );
} catch ( Exception $e ) {
	wp_die( $e->getMessage() );
}

if ( empty( $invoice ) || ! $invoice->exists() ) {
	wp_die( __( 'Sorry, Invoice does not exist', 'wp-ever-accounting' ) );
}

$company_country          = eaccounting()->settings->get( 'company_country' );
$company_country_nicename = ! empty( $company_country ) ? eaccounting_get_countries()[ $company_country ] : '';
$company_address          = eaccounting_format_address(
	array(
		'street'   => eaccounting()->settings->get( 'company_address' ),
		'city'     => eaccounting()->settings->get( 'company_city' ),
		'state'    => eaccounting()->settings->get( 'company_state' ),
		'postcode' => eaccounting()->settings->get( 'company_postcode' ),
		'country'  => $company_country_nicename,
	)
);

$notes = eaccounting_get_notes(
	array(
		'document_id'   => $invoice->get_id(),
		'document_type' => 'invoice',
	)
);
eaccounting_get_template('invoice/invoice.php', array('invoice' => $invoice ) );
?>
<div id="ea-invoice" class="columns-2">

	<div class="ea-admin-page__content">
		<div class="ea-card">
			<div class="ea-card__header">
				<h3 class="ea-card__title"><?php _e('Invoice','wp-ever-accounting');?></h3>
				<div>
					<?php
					echo sprintf(
						'<a href="%s" class="button-secondary button">%s</a>',
						esc_url(
							add_query_arg(
								array(
									'page'       => 'ea-sales',
									'action'     => 'edit',
									'invoice_id' => $invoice->get_id(),
								),
								admin_url( 'admin.php' )
							)
						),
						__( 'Edit', 'wp-ever-accounting' )
					);
					?>
					<button class="button-primary button receive-payment"><?php _e('Receive Payment','wp-ever-accounting');?>Receive Payment</button>
				</div>
			</div>
			<div class="ea-card__inside">

				<div class="ea-invoice view-mode">
					<?php if ( $invoice->is_status( 'paid' ) ) : ?>
						<div class="ea-invoice__watermark">
							<p>
								<?php echo esc_html( $invoice->get_status_nicename() ); ?>
							</p>
						</div>
					<?php endif; ?>
					<div class="ea-invoice__header">
						<div class="ea-invoice__logo">
							<img src="https://wpeveraccounting.com/wp-content/uploads/2020/09/Logo-same-size-plugin-ever.svg" alt="WP Ever Accounting">
						</div>

						<div class="ea-invoice__title"><?php _e('Invoice','');?></div>
					</div>

					<div class="ea-invoice__columns">
						<div>
							<table class="ea-invoice__party">
								<tr>
									<th>
										<div class="ea-invoice__subtitle"><?php _e('From','wp-ever-accounting');?></div>
									</th>
									<td>
										<div class="ea-invoice__company"><?php _e('BYTEEVER LIMITED','wp-ever-accounting');?></div>
										<div class="ea-invoice__address">
											<?php echo $company_address; ?>
										</div>
									</td>
								</tr>
							</table>

							<table class="ea-invoice__party">
								<tr>
									<th>
										<div class="ea-invoice__subtitle"><?php _e('To','wp-ever-accounting');?></div>
									</th>
									<td>
										<div class="ea-invoice__company">
											<?php echo empty( $invoice->get_name() ) ? '&mdash;' : esc_html( $invoice->get_name() ); ?>
										</div>
										<div class="ea-invoice__address">
												<span class="ea-invoice__address-line">
													<?php echo empty( $invoice->get_address() ) ? '&mdash;' : nl2br( $invoice->get_address() ); ?>
												</span>
										</div>
									</td>
								</tr>
							</table>

						</div>


						<div class="ea-invoice__props">
							<table>
								<tr>
									<th class="ea-invoice__props-label"><?php _e('Invoice Number','wp-ever-accounting');?></th>
									<td class="ea-invoice__props-content">
										<?php echo empty( $invoice->get_invoice_number() ) ? '&mdash;' : esc_html( $invoice->get_invoice_number( 'view' ) ); ?>
									</td>
								</tr>
								<tr>
									<th class="ea-invoice__props-label"><?php _e('Order Number','wp-ever-accounting');?></th>
									<td class="ea-invoice__props-content">
										<?php echo empty( $invoice->get_order_number() ) ? '&mdash;' : esc_html( $invoice->get_order_number( 'view' ) ); ?>
									</td>
								</tr>
								<tr>
									<th class="ea-invoice__props-label"><?php _e('Invoice Date','wp-ever-accounting');?></th>
									<td class="ea-invoice__props-content">
										<?php echo empty( $invoice->get_issue_date() ) ? '&mdash;' : eaccounting_format_datetime( $invoice->get_issue_date(), 'M j, Y' ); ?>
									</td>
								</tr>
								<tr>
									<th class="ea-invoice__props-label"><?php _e('Payment Date','wp-ever-accounting');?></th>
									<td class="ea-invoice__props-content">
										<?php echo empty( $invoice->get_payment_date() ) ? '&mdash;' : eaccounting_format_datetime( $invoice->get_payment_date(), 'M j, Y' ); ?>
									</td>
								</tr>
								<tr>
									<th class="ea-invoice__props-label"><?php _e('Due Date','wp-ever-accounting');?></th>
									<td class="ea-invoice__props-content">
										<?php echo empty( $invoice->get_due_date() ) ? '&mdash;' : eaccounting_format_datetime( $invoice->get_due_date(), 'M j, Y' ); ?>
									</td>
								</tr>
								<tr>
									<th class="ea-invoice__props-label"><?php _e('Invoice Status','wp-ever-accounting');?></th>
									<td class="ea-invoice__props-content">
										<?php echo empty( $invoice->get_status() ) ? '&mdash;' : esc_html( $invoice->get_status_nicename() ); ?>
									</td>
								</tr>
							</table>
						</div>
					</div>

					<?php eaccounting_get_admin_template( 'html-invoice-items', array( 'invoice' => $invoice ) ); ?>


					<p class="ea-invoice__terms">
						<strong><?php _e('Terms:','wp-ever-accounting');?> </strong>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusantium aliquid autem cum cumque doloribus iste nisi nulla numquam quisquam tempore!
					</p>

				</div>

			</div>
		</div>


	</div>

	<div class="ea-admin-page__sidebar">
		<div class="ea-card">
			<div class="ea-card__header">
				<h3 class="ea-card__title">
					<?php esc_html_e( 'Invoice Actions', 'wp-ever-accounting' ); ?>
				</h3>
			</div>

			<?php eaccounting_get_admin_template( 'html-invoice-actions', array( 'invoice' => $invoice ) ); ?>
		</div><!--.ea-card-->


		<div class="ea-card">
			<div class="ea-card__header">
				<h3 class="ea-card__title"><?php _e('Invoice Notes','wp-ever-accounting');?></h3>
			</div>
			<div class="ea-card__inside padding-zero">
				<?php eaccounting_get_admin_template( 'html-invoice-notes', array( 'notes' => $notes ) ); ?>


				<form id="invoice-note-insert" method="post" class="ea-invoice-notes__add">
					<p>
						<label for="invoice_note"><?php _e('Add note','wp-ever-accounting');?></label>
						<textarea type="text" name="invoice_note" id="invoice_note" class="input-text" cols="20" rows="5" autocomplete="off" spellcheck="false"></textarea>
					</p>

					<p>
						<label for="invoice_note_type" class="screen-reader-text"><?php _e('Note type','wp-ever-accounting');?></label>
						<select name="invoice_note_type" id="invoice_note_type">
							<option value=""><?php _e('Private note','wp-ever-accounting');?></option>
							<option value="customer"><?php _e('Note to customer','wp-ever-accounting');?></option>
						</select>
						<button type="button" class="add_invoice_note button"><?php _e('Add','wp-ever-accounting');?></button>
					</p>
					<input type="hidden" name="action" value="eaccounting_add_invoice_note">
					<input type="hidden" name="invoice_id" value="<?php esc_attr( $invoice->get_id() ); ?>">
					<?php wp_nonce_field( 'ea_add_invoice_note', 'nonce' ); ?>
				</form>

			</div>
		</div>

		<div class="ea-card">
			<div class="ea-card__header">
				<h3 class="ea-card__title">
					<?php esc_html_e( 'Payments Received', 'wp-ever-accounting' ); ?>
				</h3>
			</div>

			<div class="ea-card__inside padding-zero">
				<?php eaccounting_get_admin_template( 'html-invoice-payments', array( 'invoice' => $invoice ) ); ?>
			</div>

		</div>
	</div>

</div>

<script type="text/template" id="ea-modal-add-invoice-payment" data-title="<?php esc_html_e( 'Add Payment', 'wp-ever-accounting' ); ?>">
	<form action="" method="post">
		<?php
		eaccounting_text_input(
			array(
				'label'       => __( 'Date', 'wp-ever-accounting' ),
				'name'        => 'date',
				'placeholder' => __( 'Enter Date', 'wp-ever-accounting' ),
				'data_type'   => 'date',
				'value'       => date( 'Y-m-d' ),
				'required'    => true,
			)
		);
		eaccounting_text_input(
			array(
				'label'       => __( 'Amount', 'wp-ever-accounting' ),
				'name'        => 'amount',
				'value'       => $invoice->get_total_due(),
				'data_type'   => 'price',
				'required'    => true,
				'placeholder' => __( 'Enter Amount', 'wp-ever-accounting' ),
				/* translators: %s amount */
					'desc'    => sprintf( __( 'Total amount due:%s', 'wp-ever-accounting' ), eaccounting_price( $invoice->get_total_due(), $invoice->get_currency_code() ) ),
			)
		);
		eaccounting_account_dropdown(
			array(
				'label'       => __( 'Account', 'wp-ever-accounting' ),
				'name'        => 'account_id',
				'creatable'   => false,
				'placeholder' => __( 'Select Account', 'wp-ever-accounting' ),
				'required'    => true,
			)
		);
		eaccounting_payment_method_dropdown(
			array(
				'label'    => __( 'Payment Method', 'wp-ever-accounting' ),
				'name'     => 'payment_method',
				'required' => true,
				'value'    => '',
			)
		);
		eaccounting_textarea(
			array(
				'label'       => __( 'Description', 'wp-ever-accounting' ),
				'name'        => 'description',
				'value'       => '',
				'required'    => false,
				'placeholder' => __( 'Enter description', 'wp-ever-accounting' ),
			)
		);
		eaccounting_hidden_input(
			array(
				'name'  => 'invoice_id',
				'value' => $invoice->get_id(),
			)
		);

		eaccounting_hidden_input(
			array(
				'name'  => 'action',
				'value' => 'ea_add_invoice_payment',
			)
		);
		wp_nonce_field( 'ea_add_invoice_payment' );
		?>
	</form>
</script>

