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
<div class="eac-section-header margin-bottom-4">
	<div>
		<h2>
			<?php echo esc_html( $payment->get_number() ); ?>
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

<div class="eac-columns">
	<div class="eac-col-9">
		<div class="eac-document">
			<div class="eac-panel padding-6 margin-0">
				<div class="eac-document__title">Payment Receipt</div>

				<div class="eac-columns display-flex align-items-center border-bottom padding-bottom-3 margin-bottom-3">
					<div class="eac-col-6">
						<div class="eac-document__logo">
							<img src="https://byteever.com/wp-content/plugins/wp-ever-accounting/dist/images/document-logo.png" alt="ByteEver">
						</div>
					</div>
					<div class="eac-col-6 text-end-md">
						<address>
							<?php echo wp_kses_post( eac_get_formatted_company_address() ); ?>
						</address>
					</div>
				</div>


				<div class="eac-document__title text-center margin-y-3">Payment Receipt</div>

				<div class="eac-columns margin-bottom-6">
					<div class="eac-col-6">
						<strong>Received from:</strong><br>
						<div class="eac-document__address">
							<?php echo wp_kses_post( eac_get_formatted_company_address() ); ?>
						</div>
					</div>
					<div class="eac-col-6 text-end-md">
						<div class="eac-document__address">
							<?php echo wp_kses_post( eac_get_formatted_company_address() ); ?>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>
	<div class="eac-col-3">
		<div class="eac-card margin-top-0" style="margin-top: 0 !important;">
			<div class="eac-card__header">
				<div class="eac-card__title"><?php esc_html_e( 'Notes', 'wp-ever-accounting' ); ?></div>
			</div>
			<div class="eac-card__body">
				Lorem ipsum dolor sit amet, consectetur adipisicing elit. Perspiciatis, sed.
			</div>
		</div>
	</div>
</div>
