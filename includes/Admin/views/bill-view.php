<?php
/**
 * Admin bill view.
 *
 * @since 1.0.0
 *
 * @package EverAccounting
 * @var Bill $bill Bill.
 * @var string  $action Action.
 */

use EverAccounting\Models\Bill;

defined( 'ABSPATH' ) || exit;

wp_verify_nonce( '_wpnonce' );
$id   = isset( $_GET['id'] ) ? absint( wp_unslash( $_GET['id'] ) ) : 0;
$bill = EAC()->bills->get( $id );

?>

<div class="eac-section-header">
	<h1 class="wp-heading-inline">
		<?php esc_html_e( 'View Bill', 'wp-ever-accounting' ); ?>
		<a href="<?php echo esc_attr( remove_query_arg( array( 'action', 'id' ) ) ); ?>" title="<?php esc_attr_e( 'Go back', 'wp-ever-accounting' ); ?>">
			<span class="dashicons dashicons-undo"></span>
		</a>
	</h1>
	<a href="<?php echo esc_url( $bill->get_edit_url() ); ?>" class="page-title-action"><?php esc_html_e( 'Edit Bill', 'wp-ever-accounting' ); ?></a>
</div>

<div class="eac-poststuff">

	<div class="column-1">
		<?php eac_get_template( 'bill.php', array( 'bill' => $bill ) ); ?>
		<?php
		/**
		 * Fires action to inject custom meta boxes in the main column.
		 *
		 * @param Bill $bill Bill object.
		 *
		 * @since 1.0.0
		 */
		do_action( 'eac_bill_edit_core_meta_boxes', $bill );
		?>
	</div>

	<div class="column-2">

		<form class="eac-card" method="post" action="<?php echo esc_url( add_query_arg( 'action', 'update', $bill->get_edit_url() ) ); ?>">
			<div class="eac-card__header">
				<h2 class="eac-card__title"><?php esc_html_e( 'Actions', 'wp-ever-accounting' ); ?></h2>
			</div>
			<div class="eac-card__body">
				<?php
				eac_form_field(
					array(
//						'label'       => __( 'Action', 'wp-ever-accounting' ),
						'type'        => 'select',
						'id'          => 'bill_action',
						'options'     => array(),
						'value'       => $bill->status,
						'placeholder' => __( 'Select action', 'wp-ever-accounting' ),
						'required'    => true,
					)
				);
				/**
				 * Fires to add custom actions.
				 *
				 * @param Bill $bill Bill object.
				 *
				 * @since 2.0.0
				 */
				do_action( 'eac_bill_view_misc_actions', $bill );
				?>
			</div>
			<div class="eac-card__footer">
				<a class="del del_confirm" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'action', 'delete', $bill->get_edit_url() ), 'bulk-bills' ) ); ?>"><?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?></a>
				<button type="submit" class="button button-primary"><?php esc_html_e( 'Submit', 'wp-ever-accounting' ); ?></button>
			</div>

			<?php wp_nonce_field( 'eac_update_bill' ); ?>
			<input type="hidden" name="bill_id" value="<?php echo esc_attr( $bill->id ); ?>">
			<input type="hidden" name="action" value="eac_bill_action">
		</form>

		<?php
		/**
		 * Fires action to inject custom meta boxes in the side column.
		 *
		 * @param Bill $bill Bill object.
		 *
		 * @since 1.0.0
		 */
		do_action( 'eac_bill_view_side_meta_boxes', $bill );
		?>

	</div><!-- .column-2 -->

</div><!-- .eac-poststuff -->
