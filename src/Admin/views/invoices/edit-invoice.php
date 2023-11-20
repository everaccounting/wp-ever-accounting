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

$document = new Invoice( $invoice_id );
$title    = $document->exists() ? __( 'Update Invoice', 'wp-ever-accounting' ) : __( 'Add Invoice', 'wp-ever-accounting' );
?>
	<div class="eac-section-header">
		<div>
			<h2><?php echo esc_html( $title ); ?></h2>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-sales&tab=invoices' ) ); ?>"><span class="dashicons dashicons-undo"></span></a>
		</div>
		<div>
			<?php if ( $document->exists() ) : ?>
				<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=eac-sales&tab=invoices&delete=' . $document->get_id() ), 'bulk-accounts' ) ); ?>" class="del">
					<?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?>
				</a>
				<!--view-->
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-sales&tab=invoices&action=view&invoice_id=' . $document->get_id() ) ); ?>">
					<?php esc_html_e( 'View', 'wp-ever-accounting' ); ?>
				</a>
			<?php endif; ?>
			<?php submit_button( __( 'Save Invoice', 'wp-ever-accounting' ), 'primary', 'submit', false, array( 'form' => 'eac-invoice-form' ) ); ?>
		</div>
	</div>
<?php
require __DIR__ . '/invoice-form.php';
