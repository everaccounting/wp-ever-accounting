<?php
/**
 * Admin payment view.
 *
 * @since 1.0.0
 *
 * @package EverAccounting
 * @var Payment $payment Payment.
 * @var string  $action Action.
 */

use EverAccounting\Models\Payment;

defined( 'ABSPATH' ) || exit;

wp_verify_nonce( '_wpnonce' );
$id      = isset( $_GET['id'] ) ? absint( wp_unslash( $_GET['id'] ) ) : 0;
$payment = EAC()->payments->get( $id );

?>

<h1 class="wp-heading-inline">
	<?php esc_html_e( 'View Payment', 'wp-ever-accounting' ); ?>
	<a href="<?php echo esc_attr( remove_query_arg( array( 'action', 'id' ) ) ); ?>" title="<?php esc_attr_e( 'Go back', 'wp-ever-accounting' ); ?>">
		<span class="dashicons dashicons-undo"></span>
	</a>
</h1>

<div class="eac-poststuff">

	<div class="column-1">
		<?php eac_get_template( 'payment.php', array( 'payment' => $payment ) ); ?>
		<?php
		/**
		 * Fires action to inject custom content in the main column.
		 *
		 * @param Payment $payment Payment object.
		 *
		 * @since 1.0.0
		 */
		do_action( 'eac_payment_edit_core_content', $payment );
		?>
	</div>

	<div class="column-2">
		<div class="eac-card">
			<div class="eac-card__header">
				<h2 class="eac-card__title"><?php esc_html_e( 'Actions', 'wp-ever-accounting' ); ?></h2>
				<a href="<?php echo esc_url( $payment->get_edit_url() ); ?>">
					<?php esc_html_e( 'Edit', 'wp-ever-accounting' ); ?>
				</a>
			</div>
			<div class="eac-card__body">
				<?php
				/**
				 * Fires to add custom actions.
				 *
				 * @param Payment $payment Payment object.
				 *
				 * @since 2.0.0
				 */
				do_action( 'eac_payment_view_misc_actions', $payment );
				?>
				<a href="#" class="button button-small button-block eac-payment-email">
					<span class="dashicons dashicons-email"></span> <?php esc_html_e( 'Email', 'wp-ever-accounting' ); ?>
				</a>
				<a href="#" class="button button-small button-block eac-print-this" data-target="#eac-payment>table">
					<span class="dashicons dashicons-printer"></span> <?php esc_html_e( 'Print', 'wp-ever-accounting' ); ?>
				</a>
				<a href="#" class="button button-small button-block">
					<span class="dashicons dashicons-share"></span> <?php esc_html_e( 'Share', 'wp-ever-accounting' ); ?>
				</a>
			</div>
			<div class="eac-card__footer">
				<a class="del del_confirm" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'action', 'delete', $payment->get_edit_url() ), 'bulk-payments' ) ); ?>">
					<?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?>
				</a>
			</div>
		</div>

		<?php
		/**
		 * Fires action to inject custom content in the side column.
		 *
		 * @param Payment $payment Payment object.
		 *
		 * @since 1.0.0
		 */
		do_action( 'eac_payment_view_sidebar_content', $payment );
		?>

	</div><!-- .column-2 -->

</div><!-- .eac-poststuff -->

<script type="text/html" id="tmpl-eac-payment-email">
	<form>
		<div class="eac-modal-header">
			<h2><?php esc_html_e( 'Email Payment', 'wp-ever-accounting' ); ?></h2>
		</div>
		<div class="eac-modal-body">
			<div class="eac-form-field">
				<label for="email"><?php esc_html_e( 'Email', 'wp-ever-accounting' ); ?></label>
				<input type="email" name="email" id="email" required value="<?php echo esc_attr( $payment->customer ? $payment->customer->email : '' ); ?>">
			</div>
			<div class="eac-form-field">
				<label for="subject"><?php esc_html_e( 'Subject', 'wp-ever-accounting' ); ?></label>
				<input type="text" name="subject" id="subject" required>
			</div>
			<div class="eac-form-field">
				<label for="message"><?php esc_html_e( 'Message', 'wp-ever-accounting' ); ?></label>
				<?php
				wp_editor(
					get_option( 'new_payment_email_content' ),
					'message',
					array(
						'textarea_name' => 'message',
						'editor_height' => 200,
					)
				);
				?>
			</div>
		</div>

		<div class="eac-modal-footer">
			<button class="button button-primary"><?php esc_html_e( 'Send', 'wp-ever-accounting' ); ?></button>
			<button class="button" data-modal-close><?php esc_html_e( 'Cancel', 'wp-ever-accounting' ); ?></button>
		</div>
	</form>
</script>

<script type="text/html" id="tmpl-eac-share-document">
	<div class="eac-modal-header">
		<h2><?php esc_html_e( 'Share Payment', 'wp-ever-accounting' ); ?></h2>
	</div>

