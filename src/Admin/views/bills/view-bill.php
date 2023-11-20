<?php
/**
 * View bill page.
 *
 * @package EverAccounting
 * @subpackage Admin
 *
 * @since 1.0.0
 * @var int $bill_id Invoice ID.
 */

use EverAccounting\Models\Bill;

defined( 'ABSPATH' ) || exit;

$document = eac_get_bill( $bill_id );
if ( empty( $document ) ) {
	wp_safe_redirect( admin_url( 'admin.php?page=eac-purchases&tab=bills' ) );
	exit;
}
$actions = array(
	array(
		'url'  => admin_url( 'admin.php?page=eac-purchases&tab=bills&action=edit&bill_id=' . $document->get_id() ),
		'text' => __( 'Edit', 'wp-ever-accounting' ),
	),
	array(
		'url'  => wp_nonce_url( admin_url( 'admin.php?page=eac-purchases&tab=bills&action=delete&bill_id=' . $document->get_id() ), 'eac_delete_bill' ),
		'text' => __( 'Delete', 'wp-ever-accounting' ),
	),
	array(
		'url'  => wp_nonce_url( admin_url( 'admin.php?page=eac-purchases&tab=bills&action=clone&bill_id=' . $document->get_id() ), 'eac_clone_bill' ),
		'text' => __( 'Clone', 'wp-ever-accounting' ),
	),
);
$actions = apply_filters( 'eac_bill_actions', $actions, $bill_id );
?>
<div class="eac-section-header margin-bottom-4">
	<div>
		<h2>
			<?php echo esc_html( $document->get_number() ); ?>
		</h2>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-purchases&tab=bills' ) ); ?>">
			<span class="dashicons dashicons-undo"></span>
		</a>
	</div>
	<div>
		<?php
		/**
		 * Action before bill actions.
		 *
		 * @param int $bill_id Payment ID.
		 * @param Invoice $document Invoice object.
		 *
		 * @since 1.1.0
		 */
		do_action( 'ever_accounting_before_bill_actions', $document->get_id(), $document );
		?>
		<?php eac_dropdown_menu( $actions ); ?>
		<?php
		/**
		 * Action after bill actions.
		 *
		 * @param int $bill_id Payment ID.
		 * @param Invoice $document Invoice object.
		 *
		 * @since 1.1.0
		 */
		do_action( 'ever_accounting_after_bill_actions', $document->get_id(), $document );
		?>
	</div>
</div>
<div class="eac-columns">
	<div class="eac-col-9">
		<div id="bill">
			<?php eac_display_bill( $document->get_id() ); ?>
		</div>
		<div class="eac-card">
			<div class="eac-card__header">
				<h2 class="eac-card__title"><?php esc_html_e( 'Payments', 'wp-ever-accounting' ); ?></h2>
			</div>
			<div class="eac-card__body padding-0">
				<table class="widefat fixed striped">
					<thead>
					<tr>
						<th class="payment-number"><?php esc_html_e( 'Number', 'wp-ever-accounting' ); ?></th>
						<th class="payment-date"><?php esc_html_e( 'Date', 'wp-ever-accounting' ); ?></th>
						<th class="payment-amount"><?php esc_html_e( 'Amount', 'wp-ever-accounting' ); ?></th>
						<th class="payment-method"><?php esc_html_e( 'Method', 'wp-ever-accounting' ); ?></th>
						<th class="payment-reference"><?php esc_html_e( 'Reference', 'wp-ever-accounting' ); ?></th>
					</tr>
					</thead>
					<tbody>
					<?php if ( empty( $document->get_payments() ) ) : ?>
						<tr>
							<td colspan="5"><?php esc_html_e( 'No payments found.', 'wp-ever-accounting' ); ?></td>
						</tr>
					<?php else : ?>
						<?php foreach ( $document->get_payments() as $payment ) : ?>
							<tr>
								<td class="payment-number">
									<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-purchases&tab=payments&action=view&payment_id=' . $payment->get_id() ) ); ?>">
										<?php echo esc_html( $payment->get_number() ); ?>
									</a>
								</td>
								<td class="payment-date"><?php echo esc_html( $payment->get_date() ); ?></td>
								<td class="payment-amount"><?php echo esc_html( $payment->get_formatted_amount() ); ?></td>
								<td class="payment-method"><?php echo esc_html( $payment->get_payment_method() ); ?></td>
								<td class="payment-reference"><?php echo esc_html( $payment->get_reference() ); ?></td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="eac-col-3">
		<div class="eac-card">
			<div class="eac-card__header">
				<h2 class="eac-card__title"><?php esc_html_e( 'Status', 'wp-ever-accounting' ); ?></h2>
			</div>
			<div class="eac-card__body padding-0">
				<table class="widefat fixed striped">
					<tbody>
					<?php if ( ! empty( $document->get_date_created() ) ) : ?>
						<tr>
							<th class="history-action"><?php esc_html_e( 'Created', 'wp-ever-accounting' ); ?></th>
							<td class="history-date"><?php echo esc_html( $document->get_date_created() ); ?></td>
						</tr>
					<?php endif; ?>
					<?php if ( ! empty( $document->get_sent_date() ) ) : ?>
						<tr>
							<th class="history-action"><?php esc_html_e( 'Sent', 'wp-ever-accounting' ); ?></th>
							<td class="history-date"><?php echo esc_html( $document->get_sent_date() ); ?></td>
						</tr>
					<?php endif; ?>
					<?php if ( ! empty( $document->get_payment_date() ) ) : ?>
						<tr>
							<th class="history-action"><?php esc_html_e( 'Paid', 'wp-ever-accounting' ); ?></th>
							<td class="history-date"><?php echo esc_html( $document->get_payment_date() ); ?></td>
						</tr>
					<?php endif; ?>
					<tr>
						<th class="history-action"><?php esc_html_e( 'Status', 'wp-ever-accounting' ); ?></th>
						<td class="history-date"><?php echo esc_html( $document->get_status_label() ); ?></td>
					</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
