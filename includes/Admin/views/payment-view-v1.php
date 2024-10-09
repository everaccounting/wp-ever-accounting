<?php
/**
 * Admin payment view.
 *
 * @since 1.0.0
 *
 * @package EverAccounting
 * @var \EverAccounting\Models\Payment $payment Payment.
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="eac-section-header">
	<h1 class="wp-heading-inline">
		<?php // translators: Payment number. ?>
		<?php printf( esc_html__( 'Payment: #%s', 'wp-ever-accounting' ), esc_html( $payment->number ) ); ?>
		<a href="<?php echo esc_attr( remove_query_arg( 'action' ) ); ?>" title="<?php esc_attr_e( 'Go back', 'wp-ever-accounting' ); ?>">
			<span class="dashicons dashicons-undo"></span>
		</a>
	</h1>

	<div class="eac-button-group">
		<a class="button" href="<?php echo esc_url( add_query_arg( array( 'action' => 'edit' ) ) ); ?>">
			<?php esc_html_e( 'Send Receipt', 'wp-ever-accounting' ); ?>
		</a>
		<a class="button" href="https://manage.byteever.com/eaccounting/invoice/68/ea-invoice-8e92be02e3c6427994c" target="_blank"><span class="dashicons dashicons-printer"></span> Print</a>
	</div>
</div>

<form id="eac-payment-view" method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
	<div class="eac-poststuff">
		<div class="column-1">
			<?php echo do_shortcode( '[eac_payment id="' . absint( $payment->id ) . '"]' ); ?>
		</div>

		<div class="column-2">

			<div class="eac-card">
				<div class="eac-card__header">
					<h3 class="eac-card__title"><?php esc_html_e( 'Attachment', 'wp-ever-accounting' ); ?></h3>
				</div>
				<div class="eac-card__body">
					<?php eac_file_uploader( array( 'value' => $payment->attachment_id ) ); ?>
				</div>
			</div>

		</div><!-- .column-2 -->
	</div>
</form>
