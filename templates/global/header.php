<?php
/**
 * Displays header.
 *
 * This template can be overridden by copying it to yourtheme/eaccounting/global/header.php.
 *
 * @var $invoice
 * @version 1.1.0
 */
?>

<div class="ea-document_top">
	<div class="ea-row">
		<div class="ea-col-3">
			<img src="<?php echo eaccounting()->settings->get( 'company_logo', eaccounting()->plugin_url( '/assets/images/document-logo.png' ) ); ?>" alt="company-logo" class="company-logo">
		</div>
		<div class="ea-col-12">
			<div class="ea-document__header">
				<div class="ea-document__header-left">
					<div class="ea-document__status <?php echo sanitize_html_class( $invoice->get_status() ); ?>">
						<span><?php echo esc_html( $invoice->get_status_nicename() ); ?></span>
					</div>
				</div>
				<div class="ea-document__header-right">
					<button class="button button-default receive-payment">
						<span class="dashicons dashicons-printer"></span>
						<?php esc_html_e( 'Download', 'wp-ever-accounting' ); ?>
					</button>
					<?php if ( is_user_logged_in() && current_user_can( 'ea_manage_invoice' ) ) : ?>
						<a class="button button-primary edit" href="<?php echo admin_url( 'admin.php?page=ea-sales&tab=invoices&action=edit&invoice_id=3', 'admin' ); ?>">
							<span class="dashicons dashicons-money-alt"></span>
							<?php esc_html_e( 'Edit', 'wp-ever-accounting' ); ?>
						</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>
