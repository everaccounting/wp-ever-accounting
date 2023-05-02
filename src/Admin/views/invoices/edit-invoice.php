<?php
/**
 * View: Edit Invoice
 *
 * @since    1.1.6
 * @package     EverAccounting
 * @subpackage  Admin/View/Invoices
 * @var int $invoice_id Invoice ID.
 */

defined( 'ABSPATH' ) || exit();
use EverAccounting\Models\Invoice;

defined( 'ABSPATH' ) || exit();

$invoice = new Invoice( $invoice_id );
$title   = $invoice->exists() ? __( 'Update Invoice', 'wp-ever-accounting' ) : __( 'Add Invoice', 'wp-ever-accounting' );
?>
<div class="eac-page__header">
	<div class="eac-page__header-col">
		<h2 class="eac-page__title"><?php echo esc_html( $title ); ?></h2>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-sales&tab=invoices' ) ); ?>"><span class="dashicons dashicons-undo"></span></a>
	</div>
	<div class="eac-page__header-col">
		<?php if ( $invoice->exists() ) : ?>
			<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=eac-sales&tab=invoices&delete=' . $invoice->get_id() ), 'bulk-accounts' ) ); ?>" class="del">
				<?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?>
			</a>
			<!--view-->
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-sales&tab=invoices&action=view&invoice_id=' . $invoice->get_id() ) ); ?>">
				<?php esc_html_e( 'View', 'wp-ever-accounting' ); ?>
			</a>
		<?php endif; ?>
		<?php submit_button( __( 'Save Invoice', 'wp-ever-accounting' ), 'primary', 'submit', false, array( 'form' => 'eac-invoice-form' ) ); ?>
	</div>
</div>
