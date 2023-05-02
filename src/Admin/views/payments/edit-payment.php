<?php
/**
 * View: Edit Payment
 *
 * @since       1.1.0
 * @subpackage  Admin/Views/Items
 * @package     EverAccounting
 * @var int $payment_id
 */

defined( 'ABSPATH' ) || exit();

$payment = new \EverAccounting\Models\Payment( $payment_id );
$title   = $payment->exists() ? __( 'Update Payment', 'wp-ever-accounting' ) : __( 'Add Payment', 'wp-ever-accounting' );
?>

<div class="eac-page__header">
	<div class="eac-page__header-col">
		<h2 class="eac-page__title"><?php echo esc_html( $title ); ?></h2>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-sales&tab=payments' ) ); ?>"><span class="dashicons dashicons-undo"></span></a>
	</div>
	<div class="eac-page__header-col">
		<?php if ( $payment->exists() ) : ?>
			<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=eac-sales&tab=payments&action=delete&payment_id=' . $payment->get_id() ), 'bulk-items' ) ); ?>" class="del">
				<?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?>
			</a>
			<!--view-->
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-sales&tab=payments&action=view&payment_id=' . $payment->get_id() ) ); ?>" class="button button-secondary">
				<?php esc_html_e( 'View Payment', 'wp-ever-accounting' ); ?>
			</a>
		<?php endif; ?>
		<?php submit_button( __( 'Save Payment', 'wp-ever-accounting' ), 'primary', 'submit', false, array( 'form' => 'eac-payment-form' ) ); ?>
	</div>
</div>
<?php
require __DIR__ . '/payment-form.php';
