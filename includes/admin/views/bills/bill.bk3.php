<?php
/**
 * Admin Bill Page.
 *
 * Page: Expenses
 * Tab: Bills
 *
 * @since       1.1.0
 * @subpackage  Admin/Views/Bills
 * @package     EverAccounting
 *
 * @var Bill $bill
 */

use EverAccounting\Models\Bill;

defined( 'ABSPATH' ) || exit();
if ( $bill->exists() ) {
	//add_meta_box( 'bill_actions', __( 'Bill Actions', 'wp-ever-accounting' ), false, 'ea_bill', 'side' );
	add_meta_box( 'bill_notes', __( 'Bill Notes', 'wp-ever-accounting' ), array( 'EAccounting_Admin_Bills', 'bill_notes' ), 'ea_bill', 'side' );
	add_meta_box( 'bill_payments', __( 'Bill Payments', 'wp-ever-accounting' ), '__return_null', 'ea_bill', 'side' );
}
$bill->maybe_set_document_number();
/**
 * Fires after all built-in meta boxes have been added, contextually for the given object.
 *
 * @since 1.1.0
 *
 * @param Bill $bill object.
 */
do_action( 'add_meta_boxes_ea_bill', $bill );
?>
<div class="ea-title-section">
	<div>
		<h1 class="wp-heading-inline"><?php esc_html_e( 'Bill', 'wp-ever-accounting' ); ?></h1>
		<?php if ( $bill->exists() ) : ?>
			<a href="<?php echo esc_url( 'admin.php?page=ea-expenses&tab=bills&action=add' ); ?>" class="page-title-action"><?php esc_html_e( 'Add New', 'wp-ever-accounting' ); ?></a>
		<?php else : ?>
			<a href="<?php echo remove_query_arg( array( 'action', 'id' ) ); ?>" class="page-title-action"><?php esc_html_e( 'View All', 'wp-ever-accounting' ); ?></a>
		<?php endif; ?>
	</div>
	<div>
		<button class="button-secondary"><span><?php esc_html_e( 'View Invoice', 'wp-ever-accounting' ); ?></span></button>
	</div>
</div>

<hr class="wp-header-end">

<?php if ( $bill->exists() && $bill->is_draft() ) : ?>
	<div class="notice error">
		<p><?php echo __( 'This is a <strong>DRAFT</strong> bill and will be reflected after it gets <strong>received</strong>.', 'wp-ever-accounting' ); ?></p>
	</div>
<?php endif; ?>

<form name="bill" method="post" id="ea-bill">
	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="post-body-content">
				<div class="ea-card">
					<div class="ea-card__inside">

						<?php if($bill->is_editable()): ?>
						<?php else: ?>
						<?php endif; ?>

					</div>
				</div><!-- /ea-card -->
			</div>

			<div id="postbox-container-1" class="postbox-container">
				<div class="ea-card">
					<div class="ea-card__header">
						<h3 class="ea-card__title">
							<?php esc_html_e( 'Bill Actions', 'wp-ever-accounting' ); ?>
						</h3>

					</div>
					<?php if($bill->is_editable()): ?>
					<?php else: ?>
					<?php endif; ?>
				</div>
				<?php eaccounting_do_meta_boxes( 'ea_bill', 'side', $bill ); ?>
			</div><!--/postbox-container-->

			<div id="postbox-container-2" class="postbox-container">
				<?php eaccounting_do_meta_boxes( 'ea_bill', 'normal', $bill ); ?>
				<?php eaccounting_do_meta_boxes( 'ea_bill', 'advanced', $bill ); ?>
			</div><!--/postbox-container-->

		</div>
	</div><!-- /poststuff -->
	<?php wp_nonce_field( 'ea_edit_bill' ); ?>
	<?php eaccounting_hidden_input( 'id', $bill->get_id() ); ?>
</form>

<?php
