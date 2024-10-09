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
?>

<h1 class="wp-heading-inline">
	<?php if ( $payment->exists() ) : ?>
		<?php // translators: Payment number. ?>
		<?php printf( esc_html__( 'Payment: #%s', 'wp-ever-accounting' ), esc_html( $payment->number ) ); ?>
	<?php else : ?>
		<?php esc_html_e( 'Add Payment', 'wp-ever-accounting' ); ?>
	<?php endif; ?>
	<a href="<?php echo esc_attr( remove_query_arg( array( 'action', 'id' ) ) ); ?>" title="<?php esc_attr_e( 'Go back', 'wp-ever-accounting' ); ?>">
		<span class="dashicons dashicons-undo"></span>
	</a>
</h1>

<form id="eac-payment-form" method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
	<?php wp_nonce_field( 'eac_edit_payment' ); ?>
	<input type="hidden" name="action" value="eac_edit_payment"/>
	<input type="hidden" name="id" value="<?php echo esc_attr( $payment->id ); ?>"/>
	<input type="hidden" id="referredby" name="referredby" value="<?php echo esc_attr( wp_get_referer() ); ?>"/>

	<div class="eac-poststuff">

		<div class="column-1">
			<?php
			/**
			 * Fires action for registering meta boxes.
			 *
			 * @param Payment $payment Payment.
			 * @param string  $action Action.
			 *
			 * @since 1.0.0
			 */
			do_action( 'eac_payment_meta_boxes_primary', $payment, $action );
			?>
		</div>

		<div class="column-2">
			<?php
			/**
			 * Fires action for registering meta boxes.
			 *
			 * @param Payment $payment Payment.
			 * @param string  $action Action.
			 *
			 * @since 1.0.0
			 */
			do_action( 'eac_payment_meta_boxes_secondary', $payment );
			?>
		</div><!-- .column-2 -->

	</div>
</form>
