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
		 * Fires action to inject custom content in the main column.
		 *
		 * @param Bill $bill Bill object.
		 *
		 * @since 1.0.0
		 */
		do_action( 'eac_bill_edit_core_content', $bill );
		?>
	</div>

	<div class="column-2">

		<div class="eac-card">
			<div class="eac-card__header">
				<h2 class="eac-card__title"><?php esc_html_e( 'Actions', 'wp-ever-accounting' ); ?></h2>
			</div>
			<div class="eac-card__body">
				<a class="button button-block" href="<?php echo esc_url( $bill->get_edit_url() ); ?>"><?php esc_html_e( 'Mark Received', 'wp-ever-accounting' ); ?></a>
				<a class="button button-primary button-block" href="<?php echo esc_url( $bill->get_edit_url() ); ?>"><?php esc_html_e( 'Add Payment', 'wp-ever-accounting' ); ?></a>
				<?php
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
			</div>

			<?php wp_nonce_field( 'eac_update_bill' ); ?>
			<input type="hidden" name="bill_id" value="<?php echo esc_attr( $bill->id ); ?>">
			<input type="hidden" name="action" value="eac_bill_action">
		</div>

		<?php
		/**
		 * Fires action to inject custom content in the side column.
		 *
		 * @param Bill $bill Bill object.
		 *
		 * @since 1.0.0
		 */
		do_action( 'eac_bill_view_sidebar_content', $bill );
		?>

	</div><!-- .column-2 -->

</div><!-- .eac-poststuff -->
