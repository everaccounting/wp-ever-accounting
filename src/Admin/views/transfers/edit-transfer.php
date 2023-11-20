<?php
/**
 * View: Edit Transfer
 *
 * @since       1.1.0
 * @subpackage  Admin/Views/Items
 * @package     EverAccounting
 * @var int $transfer_id
 */

defined( 'ABSPATH' ) || exit();

$transfer = new \EverAccounting\Models\Transfer( $transfer_id );
$title    = $transfer->exists() ? __( 'Update Transfer', 'wp-ever-accounting' ) : __( 'Add Transfer', 'wp-ever-accounting' );
?>

<div class="eac-section-header">
	<div>
		<h2><?php echo esc_html( $title ); ?></h2>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-banking&tab=transfers' ) ); ?>"><span class="dashicons dashicons-undo"></span></a>
	</div>
	<div>
		<?php if ( $transfer->exists() ) : ?>
			<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=eac-banking&tab=transfers&action=delete&transfer_id=' . $transfer->get_id() ), 'bulk-transfers' ) ); ?>" class="del">
				<?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?>
			</a>
		<?php endif; ?>
		<?php submit_button( __( 'Save Transfer', 'wp-ever-accounting' ), 'primary', 'submit', false, array( 'form' => 'eac-transfer-form' ) ); ?>
	</div>
</div>
<?php
require __DIR__ . '/transfer-form.php';
