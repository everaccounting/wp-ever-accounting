<?php
/**
 * View invoice.
 *
 * @since 1.0.0
 * @package EverAccounting
 * @subpackage EverAccounting/Admin/Views/Sales/Invoices
 *
 * @var EverAccounting\Models\Invoice $document
 */

defined( 'ABSPATH' ) || exit;

$payment = new EverAccounting\Models\Payment();

$actions = array(
	array(
		'url'  => admin_url( 'admin.php?page=eac-sales&tab=invoices&action=edit&invoice_id=' . $document->id ),
		'text' => __( 'Edit', 'wp-ever-accounting' ),
	),
	array(
		'url'  => wp_nonce_url( admin_url( 'admin.php?page=eac-sales&tab=invoices&action=delete&invoice_id=' . $document->id ), 'eac_delete_invoice' ),
		'text' => __( 'Delete', 'wp-ever-accounting' ),
	),
	array(
		'url'  => wp_nonce_url( admin_url( 'admin.php?page=eac-sales&tab=invoices&action=clone&invoice_id=' . $document->id ), 'eac_clone_invoice' ),
		'text' => __( 'Clone', 'wp-ever-accounting' ),
	),
);
$actions = apply_filters( 'eac_invoice_actions', $actions, $document->id );
?>
<h1 class="wp-heading-inline">
	#<?php echo esc_html( $document->number ); ?>
	<a href="<?php echo esc_attr( remove_query_arg( 'view' ) ); ?>" title="<?php esc_attr_e( 'Go back', 'wp-ever-accounting' ); ?>">
		<span class="dashicons dashicons-undo"></span>
	</a>
</h1>

<div class="eac-row">
	<div class="eac-col-9">
		<?php eac_get_template( 'invoice.php', array( 'invoice' => $document ) ); ?>

		<div class="eac-card">
			<div class="eac-card__header">
				<h2 class="eac-card__title"><?php esc_html_e( 'Payments', 'wp-ever-accounting' ); ?></h2>
			</div>
			<div class="eac-card__body padding-0">
				<table class="widefat fixed striped">
					<thead>
					<tr>
						<th class="payment-number"><?php esc_html_e( 'Number', 'wp-ever-accounting' ); ?></th>
						<th class="payment-date"><?php esc_html_e( 'Date', 'wp-ever-accounting' ); ?></th>
						<th class="payment-amount"><?php esc_html_e( 'Amount', 'wp-ever-accounting' ); ?></th>
						<th class="payment-method"><?php esc_html_e( 'Method', 'wp-ever-accounting' ); ?></th>
						<th class="payment-reference"><?php esc_html_e( 'Reference', 'wp-ever-accounting' ); ?></th>
					</tr>
					</thead>
					<tbody>
					<?php if ( empty( $document->transactions ) ) : ?>
						<tr>
							<td colspan="5"><?php esc_html_e( 'No payments found.', 'wp-ever-accounting' ); ?></td>
						</tr>
					<?php else : ?>
						<?php foreach ( $document->transactions()->query( array( 'limit' => - 1 ) ) as $payment ) : ?>
							<tr>
								<td class="payment-number">
									<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-sales&tab=payments&action=view&payment_id=' . $payment->id ) ); ?>">
										<?php echo esc_html( $payment->number ); ?>
									</a>
								</td>
								<td class="payment-date"><?php echo esc_html( $payment->date ); ?></td>
								<td class="payment-amount"><?php echo esc_html( $payment->formatted_amount ); ?></td>
								<td class="payment-method"><?php echo $payment->payment_method ? esc_html( $payment->payment_method ) : '&mdash;'; ?></td>
								<td class="payment-reference"><?php echo $payment->reference ? esc_html( $payment->reference ) : '&mdash;'; ?></td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>

	</div>
	<div class="eac-col-3">

		<?php if ( $document->needs_payment() ) : ?>
			<form name="eac-invoice-add-payment" method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
				<div class="eac-card">
					<div class="eac-card__header">
						<h2 class="eac-card__title"><?php esc_html_e( 'Add Payment', 'wp-ever-accounting' ); ?></h2>
					</div>
					<div class="eac-card__body">
						<?php
						eac_form_field(
							array(
								'label'       => __( 'Date', 'wp-ever-accounting' ),
								'type'        => 'date',
								'name'        => 'date',
								'placeholder' => 'YYYY-MM-DD',
								'value'       => wp_date( 'Y-m-d' ),
								'required'    => true,
							)
						);
						eac_form_field(
							array(
								'label'       => __( 'Amount', 'wp-ever-accounting' ),
								'name'        => 'amount',
								'placeholder' => '0.00',
								'value'       => $document->balance,
								'required'    => true,
								'tooltip'     => __( 'Enter the amount in the currency of the selected account, use (.) for decimal.', 'wp-ever-accounting' ),
								'prefix'      => eac_get_currency_symbol( $document->currency_code ),
							)
						);
						eac_form_field(
							array(
								'label'            => __( 'Receiving Account', 'wp-ever-accounting' ),
								'type'             => 'select',
								'name'             => 'account_id',
								'options'          => array(),
								'required'         => true,
								'class'            => 'eac_select2',
								'tooltip'          => __( 'Select the receiving account.', 'wp-ever-accounting' ),
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
							),
						);
						eac_form_field(
							array(
								'label'       => __( 'Payment Method', 'wp-ever-accounting' ),
								'type'        => 'select',
								'name'        => 'payment_method',
								'options'     => eac_get_payment_methods(),
								'placeholder' => __( 'Select &hellip;', 'wp-ever-accounting' ),
							)
						);
						eac_form_field(
							array(
								'label'       => __( 'Notes', 'wp-ever-accounting' ),
								'type'        => 'textarea',
								'name'        => 'note',
								'placeholder' => __( 'Enter description', 'wp-ever-accounting' ),
								'rows'        => 2,
							)
						);
						?>
					</div>
					<div class="eac-card__footer">
						<button type="submit" class="button button-secondary button-large tw-w-full">
							<?php esc_html_e( 'Add Payment', 'wp-ever-accounting' ); ?>
						</button>
					</div>
				</div>
				<?php wp_nonce_field( 'eac_invoice_payment' ); ?>
				<input type="hidden" name="invoice_id" value="<?php echo esc_attr( $document->id ); ?>">
				<input type="hidden" name="action" value="eac_add_invoice_payment">
			</form>
		<?php endif; ?>

		<div class="eac-card">
			<div class="eac-card__header">
				<h3 class="eac-card__title"><?php esc_html_e( 'Notes', 'wp-ever-accounting' ); ?></h3>
			</div>
			<div class="eac-card__body">
				<form action="">
					<div class="eac-form-field">
						<label for="note"><?php esc_html_e( 'Add Note', 'wp-ever-accounting' ); ?></label>
						<textarea name="note" id="note" cols="30" rows="2" required="required" placeholder="Enter Note"></textarea>
					</div>
					<input type="hidden" name="object_id" value="<?php echo esc_attr( $payment_id ); ?>">
					<input type="hidden" name="object_type" value="payment">
					<?php wp_nonce_field( 'wcsn_add_note' ); ?>
					<button class="button"><?php esc_html_e( 'Add Note', 'wp-ever-accounting' ); ?></button>
				</form>
			</div>

			<div class="eac-card__body">
				<ul id="payment-notes" class="eac-notes">
					<li class="note">
						<div class="note__header">
							<div class="note__author">
								<?php echo get_avatar( get_current_user_id(), 32 ); ?>
								<span class="note__author-name"><?php echo get_the_author_meta( 'display_name', get_current_user_id() ); ?></span>
							</div>
							<div class="note__date">
								<?php echo date_i18n( 'M d, Y', strtotime( current_time( 'mysql' ) ) ); ?>
							</div>
						</div>
						<div class="note__content">
							<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusantium, quibusdam.</p>
						</div>
						<div class="note__actions">
							<a href="#" class="note__action"><?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?></a>
						</div>
					</li>
				</ul>
			</div>
		</div>
	</div><!-- .eac-col-3 -->
</div>
