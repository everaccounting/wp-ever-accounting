<?php
/**
 * View: View Payment
 *
 * @since       1.1.0
 * @subpackage  Admin/Views/Items
 * @package     EverAccounting
 * @var int $payment_id
 */

defined( 'ABSPATH' ) || exit();

$payment = new \EverAccounting\Models\Payment( $payment_id );
?>
<div class="eac-page__header">
	<div class="eac-page__header-col">
		<h2 class="eac-page__title">
			<?php
			/* translators: %d: payment id */
			echo sprintf( esc_html__( 'Payment #%d', 'wp-ever-accounting' ), esc_html( $payment->get_id() ) );
			?>
		</h2>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-sales&tab=payments' ) ); ?>"><span class="dashicons dashicons-undo"></span></a>
	</div>
	<div class="eac-page__header-col">
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-sales&tab=payments&action=edit&payment_id=' . $payment->get_id() ) ); ?>" class="button button-secondary">
			<?php esc_html_e( 'Edit Payment', 'wp-ever-accounting' ); ?>
		</a>
		<?php submit_button( __( 'Save Payment', 'wp-ever-accounting' ), 'primary', 'submit', false, array( 'form' => 'eac-payment-form' ) ); ?>
	</div>
</div>
