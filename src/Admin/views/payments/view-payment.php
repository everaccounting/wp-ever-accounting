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
$actions = array(
	array(
		'url'  => admin_url( 'admin.php?page=eac-sales&tab=payments&action=edit&payment_id=' . $payment->get_id() ),
		'text' => __( 'Edit', 'wp-ever-accounting' ),
	),
	array(
		'url'  => wp_nonce_url( admin_url( 'admin.php?page=eac-sales&tab=payments&action=delete&payment_id=' . $payment->get_id() ), 'eac_delete_payment' ),
		'text' => __( 'Delete', 'wp-ever-accounting' ),
	),
);
$actions = apply_filters( 'eac_payment_actions', $actions, $payment_id );
?>
<div class="eac-section-header">
	<div>
		<h2>
			<?php echo esc_html( $payment->get_transaction_number() ); ?>
		</h2>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-sales&tab=payments' ) ); ?>"><span class="dashicons dashicons-undo"></span></a>
	</div>
	<div>
		<?php
		/**
		 * Action before payment actions.
		 *
		 * @param int $payment_id Payment ID.
		 *
		 * @since 1.1.0
		 */
		do_action( 'eac_payment_before_actions', $payment_id );
		?>
		<a href="<?php echo esc_url( eac_action_url( 'action=send_payment_receipt&id=' . $payment->get_id(), false ) ); ?>" class="button button-primary">
			<?php esc_html_e( 'Send Receipt', 'wp-ever-accounting' ); ?>
		</a>
		<?php eac_dropdown_menu( $actions ); ?>
		<?php
		/**
		 * Action after payment actions.
		 *
		 * @param int $payment_id Payment ID.
		 *
		 * @since 1.1.0
		 */
		do_action( 'eac_payment_after_actions', $payment_id );
		?>
	</div>
</div>
