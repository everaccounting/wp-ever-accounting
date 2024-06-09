<?php
/**
 * Admin Veiw Payment.
 * Page: Sales
 * Tab: Payment
 *
 * @package EverAccounting
 * @version 1.0.0
 * @var $payment \EverAccounting\Models\Payment Payment object.
 */

defined( 'ABSPATH' ) || exit;
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
		<?php eac_display_payment( $payment_id ); ?>
	</div>
	<div class="eac-col-3">
		<div class="eac-card">
			<div class="eac-card__header">
				<h3 class="eac-card__title"><?php esc_html_e( 'Notes', 'wp-ever-accounting' ); ?></h3>
			</div>
			<div class="eac-card__body">
				<form action="">
					<div class="eac-form-field">
						<label for="note"><?php esc_html_e( 'Add Note', 'wp-ever-accounting' ); ?></label>
						<textarea name="note" id="note" cols="30" rows="2" required="required" placeholder="Enter Note"></textarea>
					</div>
					<input type="hidden" name="object_id" value="<?php echo esc_attr( $payment_id ); ?>">
					<input type="hidden" name="object_type" value="payment">
					<?php wp_nonce_field( 'wcsn_add_note' ); ?>
					<button class="button"><?php esc_html_e( 'Add Note', 'wp-ever-accounting' ); ?></button>
				</form>
			</div>

			<div class="eac-card__body">
				<ul id="payment-notes" class="eac-notes">
					<li class="note">
						<div class="note__header">
							<div class="note__author">
								<?php echo get_avatar( get_current_user_id(), 32 ); ?>
								<span
									class="note__author-name"><?php echo get_the_author_meta( 'display_name', get_current_user_id() ); ?></span>
							</div>
							<div class="note__date">
								<?php echo date_i18n( 'M d, Y', strtotime( current_time( 'mysql' ) ) ); ?>
							</div>
						</div>
						<div class="note__content">
							<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusantium, quibusdam.</p>
						</div>
						<div class="note__actions">
							<a href="#"
							   class="note__action"><?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?></a>
						</div>
					</li>
					<li class="note">
						<div class="note__header">
							<div class="note__author">
								<?php echo get_avatar( get_current_user_id(), 32 ); ?>
								<span
									class="note__author-name"><?php echo get_the_author_meta( 'display_name', get_current_user_id() ); ?></span>
							</div>
							<div class="note__date">
								<?php echo date_i18n( 'M d, Y', strtotime( current_time( 'mysql' ) ) ); ?>
							</div>
						</div>
						<div class="note__content">
							<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusantium, quibusdam.</p>
						</div>
						<div class="note__actions">
							<a href="#"
							   class="note__action"><?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?></a>
						</div>
					</li>
					<li class="note">
						<div class="note__header">
							<div class="note__author">
								<?php echo get_avatar( get_current_user_id(), 32 ); ?>
								<span
									class="note__author-name"><?php echo get_the_author_meta( 'display_name', get_current_user_id() ); ?></span>
							</div>
							<div class="note__date">
								<?php echo date_i18n( 'M d, Y', strtotime( current_time( 'mysql' ) ) ); ?>
							</div>
						</div>
						<div class="note__content">
							<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusantium, quibusdam.</p>
						</div>
						<div class="note__actions">
							<a href="#"
							   class="note__action"><?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?></a>
						</div>
					</li>
					<li class="note">
						<div class="note__header">
							<div class="note__author">
								<?php echo get_avatar( get_current_user_id(), 32 ); ?>
								<span
									class="note__author-name"><?php echo get_the_author_meta( 'display_name', get_current_user_id() ); ?></span>
							</div>
							<div class="note__date">
								<?php echo date_i18n( 'M d, Y', strtotime( current_time( 'mysql' ) ) ); ?>
							</div>
						</div>
						<div class="note__content">
							<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusantium, quibusdam.</p>
						</div>
						<div class="note__actions">
							<a href="#"
							   class="note__action"><?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?></a>
						</div>
					</li>
					<li class="note">
						<div class="note__header">
							<div class="note__author">
								<?php echo get_avatar( get_current_user_id(), 32 ); ?>
								<span
									class="note__author-name"><?php echo get_the_author_meta( 'display_name', get_current_user_id() ); ?></span>
							</div>
							<div class="note__date">
								<?php echo date_i18n( 'M d, Y', strtotime( current_time( 'mysql' ) ) ); ?>
							</div>
						</div>
						<div class="note__content">
							<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusantium, quibusdam.</p>
						</div>
						<div class="note__actions">
							<a href="#"
							   class="note__action"><?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?></a>
						</div>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>


