<?php
/**
 * View: Edit Bill
 *
 * @since    1.1.6
 * @package     EverAccounting
 * @subpackage  Admin/View/Bills
 * @var int $bill_id Bill ID.
 */

defined( 'ABSPATH' ) || exit();
use EverAccounting\Models\Bill;

defined( 'ABSPATH' ) || exit();

$document  = new Bill( $bill_id );
$title = $document->exists() ? __( 'Update Bill', 'wp-ever-accounting' ) : __( 'Add Bill', 'wp-ever-accounting' );
?>
<div class="eac-section-header">
	<div>
		<h2><?php echo esc_html( $title ); ?></h2>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-purchases&tab=bills' ) ); ?>"><span class="dashicons dashicons-undo"></span></a>
	</div>
	<div>
		<?php if ( $document->exists() ) : ?>
			<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=eac-purchases&tab=bills&delete=' . $document->get_id() ), 'bulk-accounts' ) ); ?>" class="del">
				<?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?>
			</a>
			<!--view-->
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-purchases&tab=bills&action=view&bill_id=' . $document->get_id() ) ); ?>">
				<?php esc_html_e( 'View', 'wp-ever-accounting' ); ?>
			</a>
		<?php endif; ?>
		<?php submit_button( __( 'Save Bill', 'wp-ever-accounting' ), 'primary', 'submit', false, array( 'form' => 'eac-bill-form' ) ); ?>
	</div>
</div>
<?php
require __DIR__ . '/bill-form.php';
