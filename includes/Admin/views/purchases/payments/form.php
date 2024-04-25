<?php
/**
 * Admin Payments Form.
 * Page: Expenses
 * Tab: Payments
 *
 * @package EverAccounting
 * @version 1.0.0
 * @var $payment \EverAccounting\Models\Payment Payment object.
 */

defined( 'ABSPATH' ) || exit;
// $state = wp_interactivity_state( 'eac/payment', $payment->to_array() );
?>
<form id="eac-payment-form" method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>" data-wp-interactive="eac/payment">
	<span data-wp-text="name"></span>
	<div class="bkit-poststuff">
		<div class="column-1">
			<div class="bkit-card">
				<div class="bkit-card__header">
					<h2 class="bkit-card__title"><?php esc_html_e( 'Item details', 'wp-ever-accounting' ); ?></h2>
				</div>

				<div class="bkit-card__body grid--fields">
					<div class="bkit-form-group">
						<label for="name">
							<?php esc_html_e( 'Name', 'wp-ever-accounting' ); ?>
							<abbr title="required"></abbr>
						</label>
						<input type="text" name="name" id="name" value="<?php echo esc_attr( $payment->name ); ?>"/>
					</div>
				</div>
			</div>
		</div><!-- .column-1 -->

		<div class="column-2">
			<div class="bkit-card">
				<div class="bkit-card__header">
					<h2 class="bkit-card__title"><?php esc_html_e( 'Actions', 'wp-ever-accounting' ); ?></h2>
				</div>
				<div class="bkit-card__footer">
					<?php // if ( $payment->exists() ) : ?>
					<input type="hidden" name="id" value="<?php echo esc_attr( $payment->id ); ?>"/>
					<?php // endif; ?>
					<input type="hidden" name="action" value="eac_edit_payment"/>
					<?php wp_nonce_field( 'eac_edit_payment' ); ?>
					<?php // if ( $payment->exists() ) : ?>
					<a class="eac_confirm_delete del" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'action', 'delete', admin_url( 'admin.php?page=eac-expenses&tab=payments&id=' . $payment->id ) ), 'bulk-items' ) ); ?>"><?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?></a>
					<?php // endif; ?>
					<?php // if ( $payment->exists() ) : ?>
					<button class="button button-primary"><?php esc_html_e( 'Update Payment', 'wp-ever-accounting' ); ?></button>
					<?php // else : ?>
					<button class="button button-primary bkit-w-100"><?php esc_html_e( 'Add Payment', 'wp-ever-accounting' ); ?></button>
					<?php // endif; ?>
				</div>
			</div>
		</div><!-- .column-2 -->

	</div><!-- .bkit-poststuff -->
</form>
<?php
