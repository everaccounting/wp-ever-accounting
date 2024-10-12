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

<div class="eac-section-header">
	<h1 class="wp-heading-inline">
		<?php esc_html_e( 'View Payment', 'wp-ever-accounting' ); ?>
		<a href="<?php echo esc_attr( remove_query_arg( array( 'action', 'id' ) ) ); ?>" title="<?php esc_attr_e( 'Go back', 'wp-ever-accounting' ); ?>">
			<span class="dashicons dashicons-undo"></span>
		</a>
	</h1>
	<a href="<?php echo esc_url( $payment->get_edit_url() ); ?>" class="page-title-action"><?php esc_html_e( 'Edit Payment', 'wp-ever-accounting' ); ?></a>
</div>

<div class="eac-poststuff">

	<div class="column-1">
		<?php echo do_shortcode( '[eac_payment id="' . $payment->id . '"]' ); ?>
		<?php
		/**
		 * Fires action to inject custom meta boxes in the main column.
		 *
		 * @param Payment $payment Payment object.
		 *
		 * @since 1.0.0
		 */
		do_action( 'eac_payment_edit_core_meta_boxes', $payment );
		?>
	</div>

	<div class="column-2">
		<form name="payment-actions" class="eac-card" method="post">
			<div class="eac-card__header">
				<h2 class="eac-card__title"><?php esc_html_e( 'Actions', 'wp-ever-accounting' ); ?></h2>
			</div>
			<div class="eac-card__body">
				<?php
				// share URL.
				eac_form_field( array(
					'label'       => __( 'Share URL', 'wp-ever-accounting' ),
					'type'        => 'text',
					'id'          => 'share_url',
					'value'       => $payment->get_public_url(),
					'readonly'    => true,
				) );

				eac_form_field(
					array(
						'label'       => __( 'Status', 'wp-ever-accounting' ),
						'type'        => 'select',
						'id'          => 'status',
						'options'     => EAC()->payments->get_statuses(),
						'value'       => $payment->status,
						'placeholder' => __( 'Select status', 'wp-ever-accounting' ),
						'required'    => true,
					)
				);

				/**
				 * Fires to add custom actions.
				 *
				 * @param Payment $customer Payment object.
				 *
				 * @since 2.0.0
				 */
				do_action( 'eac_customer_misc_actions', $payment );
				?>
			</div>
			<div class="eac-card__footer">
				<?php if ( $payment->exists() ) : ?>
					<a class="eac_confirm_delete del" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'action', 'delete', admin_url( 'admin.php?page=eac-sales&tab=payments&id=' . $payment->id ) ), 'bulk-payments' ) ); ?>"><?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?></a>
					<button class="button button-primary"><?php esc_html_e( 'Update Payment', 'wp-ever-accounting' ); ?></button>
				<?php else : ?>
					<button class="button button-primary tw-w-[100%]"><?php esc_html_e( 'Add Payment', 'wp-ever-accounting' ); ?></button>
				<?php endif; ?>
			</div>
			<?php wp_nonce_field( 'eac_payment_actions' ); ?>
			<input type="hidden" name="action" value="eac_payment_actions"/>
			<input type="hidden" name="id" value="<?php echo esc_attr( $payment->id ); ?>"/>
		</form>

		<div class="eac-card">
			<div class="eac-card__header">
				<h3 class="eac-card__title"><?php esc_html_e( 'Attachment', 'wp-ever-accounting' ); ?></h3>
			</div>
			<div class="eac-card__body">
				<?php if ( $payment->attachment ) : ?>
					<a href="<?php echo esc_url( $payment->attachment->get_url() ); ?>" target="_blank"><?php esc_html_e( 'View Attachment', 'wp-ever-accounting' ); ?></a>
				<?php else : ?>
					<?php esc_html_e( 'No attachment found.', 'wp-ever-accounting' ); ?>
				<?php endif; ?>
			</div>
		</div>

		<?php
		/**
		 * Fires action to inject custom meta boxes in the side column.
		 *
		 * @param Payment $payment Payment object.
		 *
		 * @since 1.0.0
		 */
		do_action( 'eac_payment_view_side_meta_boxes', $payment );
		?>

	</div><!-- .column-2 -->

</div><!-- .eac-poststuff -->
