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

$company_address = eaccounting_format_address(
	array(
		'street'   => eaccounting()->settings->get( 'company_address' ),
		'city'     => eaccounting()->settings->get( 'company_city' ),
		'state'    => eaccounting()->settings->get( 'company_state' ),
		'postcode' => eaccounting()->settings->get( 'company_postcode' ),
		'country'  => eaccounting()->settings->get( 'company_country' ),
	)
);

$notes    = eaccounting_get_notes(
	array(
		'document_id'   => $invoice->get_id(),
		'document_type' => 'invoice',
	)
);
$edit_url = admin_url( 'admin.php' ) . '?page=ea-sales&action=edit&invoice_id=' . $invoice->get_id();
?>
	<div id="ea-invoice" class="columns-2">
		<div class="ea-admin-page__content">

			<div class="ea-card">
				<div class="ea-card__header">
					<h3 class="ea-card__title">
						<?php /* translators: %s invoice number */ ?>
						<?php echo sprintf( __( 'Invoice #%s', 'wp-ever-accounting' ), $invoice->get_invoice_number() ); ?>
					</h3>
					<div>
						<?php
						if ( $invoice->is_editable() ) {
							echo sprintf(
								'<a href="%s" class="button-secondary button">%s</a>',
								esc_url( $edit_url ),
								__( 'Edit', 'wp-ever-accounting' )
							);
						}
						?>
						<?php if ( ! $invoice->is_status( 'paid' ) ) : ?>
							<button class="button-primary button receive-payment" data-currency="<?php echo esc_attr( $invoice->get_currency_code() ); ?>"><?php _e( 'Receive Payment', 'wp-ever-accounting' ); ?></button>
						<?php endif; ?>
						<?php do_action( 'eaccounting_invoice_header_actions', $invoice ); ?>
					</div>
				</div>

				<div class="ea-card__inside">

				</div>

				<div class="ea-card__body">
					<?php eaccounting_get_admin_template( 'invoices/partials/items', array( 'invoice' => $invoice, 'mode' => 'view' ) ); //phpcs:ignore ?>
				</div>

				<?php if ( ! empty( $invoice->get_terms() ) ) : ?>
					<div class="ea-card__inside">
						<p class="ea-invoice__terms">
							<strong><?php _e( 'Terms:', 'wp-ever-accounting' ); ?> </strong>
							<?php esc_html_e( $invoice->get_terms() ); ?>
						</p>
					</div>
				<?php endif; ?>

				<?php if ( $invoice->get_attachment_id() ) : ?>
					<div class="ea-card__footer">
						<div>
							<h3 class="ea-card__subtitle"><?php esc_html_e( 'Attachment', 'wp-ever-accounting' ); ?></h3>
							<br>
							<?php echo $invoice->get_attachment_image(); ?>
						</div>
					</div>
				<?php endif; ?>

			</div>

		</div><!--.ea-admin-page__content-->


		<div class="ea-admin-page__sidebar">

			<?php eaccounting_get_admin_template( 'invoices/partials/actions', array( 'invoice' => $invoice ) ); ?>
			<?php eaccounting_get_admin_template( 'invoices/partials/payments', array( 'invoice' => $invoice ) ); ?>
			<?php eaccounting_get_admin_template( 'invoices/partials/notes', array( 'invoice' => $invoice ) ); ?>

		</div><!--.ea-admin-page__sidebar-->


	</div>
<?php
if ( ! $invoice->is_status( 'paid' ) ) {
	eaccounting_get_admin_template( 'js/modal-invoice-payment', array( 'invoice' => $invoice ) );
}
