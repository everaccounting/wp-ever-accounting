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

$edit_url = admin_url( 'admin.php' ) . '?page=ea-sales&action=edit&invoice_id=' . $invoice->get_id();
?>
	<div id="ea-invoice" class="columns-2">
		<div class="ea-admin-page__content">

			<div class="ea-card">
				<div class="ea-card__header">
					<h3 class="ea-card__title">
						<?php esc_html_e( 'Invoice', 'wp-ever-accounting' ); ?>
					</h3>
					<div>
						<button onclick="history.go(-1);" class="button-secondary"><?php _e( 'Go Back', 'wp-ever-accounting' ); ?></button>
					</div>
				</div>

				<div class="ea-card__body">
					<div class="ea-document__watermark">
						<p>
							<?php echo esc_html( $invoice->get_status_nicename() ); ?>
						</p>
					</div>
					<div class="ea-card__inside">

						<?php eaccounting_get_admin_template( 'invoices/partials/header', array( 'invoice' => $invoice ) ); //phpcs:ignore ?>


						<div class="ea-document__details">
							<div class="ea-document__details-column">
								<?php eaccounting_get_admin_template( 'invoices/partials/addresses', array( 'invoice' => $invoice ) ); //phpcs:ignore ?>

							</div>
							<div class="ea-document__details-column">
								<?php eaccounting_get_admin_template( 'invoices/partials/meta', array( 'invoice' => $invoice ) ); //phpcs:ignore ?>
							</div>
						</div>


					</div>
					<?php eaccounting_get_admin_template( 'invoices/partials/items', array( 'invoice' => $invoice, 'mode' => 'view' ) ); //phpcs:ignore ?>
				</div>

				<?php if ( ! empty( $invoice->get_terms() ) ) : ?>
					<div class="ea-card__inside">
						<p class="ea-document__terms">
							<strong><?php _e( 'Terms:', 'wp-ever-accounting' ); ?> </strong>
							<?php esc_html_e( $invoice->get_terms() ); ?>
						</p>
					</div>
				<?php endif; ?>

				<div class="ea-card__footer">
					<div>
						<?php if ( ! empty( $invoice->get_attachment_id() ) ) : ?>
							<a href="<?php echo esc_url( $invoice->get_attachment_url() ); ?>" target="_blank"><?php esc_html_e( 'Attachment', 'wp-ever-accounting' ); ?></a>
						<?php endif; ?>
					</div>
					<div>
						<?php do_action( 'eaccounting_invoice_before_action_buttons', $invoice ); ?>
						<?php
						if ( $invoice->is_editable() ) {
							echo sprintf(
								'<a href="%s" class="button-secondary button">%s</a>',
								esc_url( $edit_url ),
								__( 'Edit', 'wp-ever-accounting' )
							);
						}
						?>

						<button id="print-invoice" class="button-secondary button"><?php _e( 'Print', 'wp-ever-accounting' ); ?></button>

						<?php if ( ! $invoice->is_status( 'paid' ) ) : ?>
							<button class="button-primary button receive-payment" data-currency="<?php echo esc_attr( $invoice->get_currency_code() ); ?>"><?php _e( 'Add Payment', 'wp-ever-accounting' ); ?></button>
						<?php endif; ?>
						<?php do_action( 'eaccounting_invoice_after_action_buttons', $invoice ); ?>
					</div>
				</div>

			</div>


		</div><!--.ea-admin-page__content-->


		<div class="ea-admin-page__sidebar">

			<?php eaccounting_get_admin_template( 'invoices/partials/actions', array( 'invoice' => $invoice ) ); ?>
			<?php eaccounting_get_admin_template( 'invoices/partials/notes', array( 'invoice' => $invoice ) ); ?>
			<?php eaccounting_get_admin_template( 'invoices/partials/payments', array( 'invoice' => $invoice ) ); ?>

		</div><!--.ea-admin-page__sidebar-->


	</div>
<?php
if ( ! $invoice->is_status( 'paid' ) ) {
	eaccounting_get_admin_template( 'js/modal-invoice-payment', array( 'invoice' => $invoice ) );
}
